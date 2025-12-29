<?php

namespace App\Http\Controllers;

use App\Helpers\ReportService;
use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; //date time handling library

class StudentAttendanceController extends Controller
{

    public function index()
    {
        // 1. Rename $today to $date so it matches your View
        $date = Carbon::today()->toDateString();


        // 2. Update query to use $date
        $studentAttendances = StudentAttendance::with('student')
            ->where('date', $date)
            ->simplePaginate(20);

        $students = Student::select('id', 'name')->orderBy('name')->get();

        $studentsCount = Student::whereHas('user.applications', function ($query) {
            $query->where('status', 'approved');
        })->count();

        // 3. Update the counts to use $date
        $present = StudentAttendance::where('status', 'present')
            ->where('date', $date)->count();

        $absent = StudentAttendance::where('status', 'absent')
            ->where('date', $date)->count();

        $excused = StudentAttendance::where('status', 'excused')
            ->where('date', $date)->count();

        // 4. Pass 'date' instead of 'today' in compact
        return view('admin.studentAttendance', compact('studentAttendances', 'students', 'studentsCount', 'present', 'absent', 'excused', 'date'));
    }


    public function teacherIndex(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $activeTab = $request->get('tab', 'studentAttendanceToggle');

        $teacherId = $user->teacher->id;

        // Get all student attendance records for today for this teacher
        $studentAttendances = StudentAttendance::with('student')
            ->whereHas('student', function ($query) use ($teacherId) {
                $query->where('teacher_id', $teacherId);
            })
            ->where('date', $today)
            ->simplePaginate(20);

        $studentsCount = Student::whereHas('teacher', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->count();

        $students = Student::select('id', 'name')->where('teacher_id', $teacherId)->orderBy('name')->get();

        // Statistics by status
        $stats = StudentAttendance::select('status')
            ->selectRaw('COUNT(*) as total_students')
            ->where('date', $today)
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        return view('user.teacher.studentAttendance', [
            'date' => $today,
            'studentAttendances' => $studentAttendances,
            'students'           => $students,
            'studentsCount'      => $studentsCount,
            'present'            => $stats['present']->total_students ?? 0,
            'absent'             => $stats['absent']->total_students ?? 0,
            'excused'            => $stats['excused']->total_students ?? 0,
            'activeTab' => $activeTab
        ]);
    }


    public function startAttendance(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        if ($user->role === 'teacher') {
            $teacherId = $user->teacher->id;
            $students = Student::where('teacher_id', $teacherId)->get();
        } else {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $existing = StudentAttendance::whereIn('student_id', $students->pluck('id'))
            ->where('date', $today)
            ->exists();

        if ($existing) {
            return redirect()->back()->with('info', 'Attendance for today has already been started.');
        }
        // Create attendance records with default Absent
        foreach ($students as $student) {
            StudentAttendance::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'date' => $today,
                ],
                [
                    'status' => 'absent',
                ]
            );
        }

        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'attendance',
            'message' => 'Started student attendance for today.',
        ]);

        // ✅ Redirect to index so data is always loaded there
        return redirect()->route('attendance.student.teacher')
            ->with('success', 'Attendance started for today. All students are Absent by default.');
    }

    public function resetAttendance()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        if ($user->role !== 'teacher') {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $teacherId = $user->teacher->id;

        // Delete today's attendance for all students under this teacher
        StudentAttendance::whereHas('student', function ($query) use ($teacherId) {
            $query->where('teacher_id', $teacherId);
        })->where('date', $today)->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'activity_type' => 'attendance',
            'message' => 'Reset all student attendance records for today.',
        ]);


        return redirect()->back()->with('success', 'Attendance for today has been reset.');
    }

    public function scanner()
    {
        return view('user.teacher.qrCodeScanner'); // create this blade file
    }

    public function scan(Request $request)
    {
        $qrCode = trim($request->scanDetails);
        $action = $request->action; // check_in or check_out
        $today  = now()->toDateString();

        // Try to match a Student first
        $student = Student::where('qr_code', $qrCode)->first();

        if ($student) {

            // Check if attendance for today has started
            $attendanceStarted = StudentAttendance::where('date', $today)->exists();

            if (!$attendanceStarted) {
                return redirect()->back()->with('error', 'Attendance for today has not started yet.');
            }

            $attendance = StudentAttendance::firstOrCreate(
                ['student_id' => $student->id, 'date' => $today],
                ['status' => 'absent']
            );

            if ($action === 'check_in') {
                if (is_null($attendance->check_in_time)) {
                    $attendance->update([
                        'check_in_time' => now()->toTimeString(),
                        'status' => 'present',
                    ]);

                    ActivityLog::create([
                        'user_id' => Auth::id(),
                        'activity_type' => 'attendance',
                        'message' => "{$student->name} checked in successfully at " . now()->format('h:i A'),
                    ]);

                    return back()->with('success', 'Student checked in successfully.');
                }
                return back()->with('info', 'Student already checked in today.');
            }

            if ($action === 'check_out') {
                if (is_null($attendance->check_out_time)) {
                    $attendance->update([
                        'check_out_time' => now()->toTimeString(),
                    ]);

                    ActivityLog::create([
                        'user_id' => Auth::id(),
                        'activity_type' => 'attendance',
                        'message' => "{$student->name} checked out successfully.",
                    ]);

                    return back()->with('success', 'Student checked out successfully.');
                }
                return back()->with('info', 'Student already checked out today.');
            }

            return back()->with('error', 'Invalid action.');
        }

        // Try to match a Teacher next
        $teacher = Teacher::where('qr_code', $qrCode)->first();

        if ($teacher) {

            // Check if attendance for today has started
            $attendanceStarted = TeacherAttendance::where('date', $today)->exists();

            if (!$attendanceStarted) {
                return redirect()->back()->with('error', 'Teacher Attendance for today has not started yet.');
            }

            $attendance = TeacherAttendance::firstOrCreate(
                ['teacher_id' => $teacher->id, 'date' => $today],
                ['status' => 'absent']
            );

            if ($action === 'check_in') {
                if (is_null($attendance->check_in_time)) {
                    $attendance->update([
                        'check_in_time' => now()->toTimeString(),
                        'status' => 'present',
                    ]);
                    return back()->with('success', 'Teacher checked in successfully.');
                }
                return back()->with('info', 'Teacher already checked in today.');
            }

            if ($action === 'check_out') {
                if (is_null($attendance->check_out_time)) {
                    $attendance->update([
                        'check_out_time' => now()->toTimeString(),
                    ]);
                    return back()->with('success', 'Teacher checked out successfully.');
                }
                return back()->with('info', 'Teacher already checked out today.');
            }

            return back()->with('error', 'Invalid action.');
        }

        // If neither student nor teacher matched
        return back()->with('error', 'Invalid QR Code.');
    }




    public function update(Request $request)
    {
        $statuses = $request->input('status');

        if (!$statuses || !is_array($statuses)) {
            return redirect()->back()->with('error', 'No status changes were submitted.');
        }

        $updatedCount = 0;

        foreach ($statuses as $attendanceId => $status) {
            $attendance = StudentAttendance::find($attendanceId);

            if ($attendance && in_array($status, ['present', 'absent', 'excused'])) {
                if ($attendance->status !== $status) {
                    $attendance->status = $status;
                    $attendance->save();
                    $updatedCount++;
                }
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'attendance_update',
            'message' => "Updated {$updatedCount} student attendance statuses.",
        ]);

        // 🔹 Determine redirect route based on user role
        $user = Auth::user();
        $redirectRoute = $user->role === 'teacher' ? 'attendance.student.teacher' : 'attendance.student';

        return redirect()->route($redirectRoute, ['tab' => 'viewStudentAttendance'])
            ->with('success', "Successfully updated {$updatedCount} attendance statuses.");
    }


    public function filter(Request $request)
    {
        // 1. SECURITY CHECK: If user is a teacher, inject their ID into the request
        if (Auth::user()->teacher) {
            $request->merge(['teacher_id' => Auth::user()->teacher->id]);
        }

        $attendanceType = 'student';

        // 2. Call the service. It will now see 'teacher_id' and filter automatically
        $result = ReportService::getAttendance($request, $attendanceType);

        $studentAttendances = $result['attendances'];
        $year = $result['year'];
        $month = $result['month'];
        $date = $result['date'];

        $present = $result['present'];
        $absent = $result['absent'];
        $excused = $result['excused'];

        // 3. Render Views
        // Both views now receive the filtered data if the user was a teacher.
        // If the user was Admin, $request->teacher_id was null, so they see everything.

        $adminView = view(
            'admin.report.tables.studentAttendance',
            compact('studentAttendances', 'year', 'month', 'date', 'present', 'absent', 'excused')
        )->render();

        $teacherView = view(
            'user.teacher.table.studentAttendance',
            compact('studentAttendances', 'year', 'month', 'date', 'present', 'absent', 'excused')
        )->render();

        return response()->json([
            'adminHtml' => $adminView,
            'teacherHtml' => $teacherView,
        ]);
    }
}
