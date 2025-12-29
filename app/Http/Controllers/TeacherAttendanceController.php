<?php

namespace App\Http\Controllers;

use App\Helpers\ReportService;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schedule;

class TeacherAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = now()->toDateString();

        if ($user->role == 'teacher') {
            $teacherId = $user->teacher->id;
            $current = now();
            $year = $current->year;
            $month = $current->month;
            $date = null;
        

            // Paginated teacher attendances
           $teacherAttendances = TeacherAttendance::where('teacher_id', $teacherId)
                ->whereYear('date', $year)   // <--- Added
                ->whereMonth('date', $month) // <--- Added
                ->orderBy('date', 'desc')
                ->simplePaginate(20);

            $totalDays = TeacherAttendance::where('teacher_id', $teacherId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->count();

            $present = TeacherAttendance::where('teacher_id', $teacherId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'present')
                ->count();

            $absent = $totalDays - $present;
            $progress = $totalDays > 0 ? ($present / $totalDays) * 100 : 0;

            return view('user.teacher.teacherAttendance', compact(
                'teacherAttendances',
                'progress',
                'year',
                'month',
                'totalDays',
                'present',
                'absent'
            ));
        } elseif ($user->role == 'admin') {

            $activeTab = $request->get('tab', 'viewTeacherAttendanceToggle');

            // All teacher attendances (consider pagination if large)
            $teacherAttendances = TeacherAttendance::orderBy('date', 'desc')->simplePaginate(20);

            // Counts for today
            $teachersCount = TeacherAttendance::whereDate('date', $today)->count();
            $teachers = Teacher::select('id', 'name')->orderBy('name')->get();
            $present = TeacherAttendance::where('status', 'present')->whereDate('date', $today)->count();
            $absent = TeacherAttendance::where('status', 'absent')->whereDate('date', $today)->count();
            $excused = TeacherAttendance::where('status', 'excused')->whereDate('date', $today)->count();
            $date = now()->toDateString();


            $teacherTodayAttendances = TeacherAttendance::where('date', $today)->get();

            return view('admin.teacherAttendance', compact(
                'teacherAttendances',
                'teachers',
                'teachersCount',
                'present',
                'absent',
                'excused',
                'activeTab',
                'date',
                'teacherTodayAttendances'
            ));
        } else {
            return redirect()->back()->with('error', 'Unauthorized');
        }
    }

    public function startAttendance()
    {
        $today = Carbon::today()->toDateString();
        $existing = TeacherAttendance::where('date', $today)->exists();

        if ($existing) {
            return redirect()->back()->with('info', 'Teacher Attendance for today has already been started.');
        }

        $activeTeachers = User::where('role', 'teacher')
            ->where('status', 'active')
            ->get();

        $title = "Attendance Started";
        $message = "The daily attendance session has started. Please clock in now.";

        // 3. Loop ONCE to do both tasks
        foreach ($activeTeachers as $teacher) {

            TeacherAttendance::create([
                'teacher_id' => $teacher->teacher->id, // Use the User_ID to be safe
                'date'       => $today,
                'status'     => 'absent'
            ]);

            // B. Create Notification
            Notification::create([
                'user_id'    => $teacher->id,
                'title'      => $title,
                'message'    => $message,
                'type'       => 'warning',
                'is_read'    => 0,
            ]);
        }

        ActivityLog::create([
            'user_id'       => Auth::id(),
            'activity_type' => 'teacher_attendance',
            'message'       => 'Started teacher attendance for today.',
        ]);

        return redirect()
            ->route('attendance.teacher', ['tab' => 'viewTeacherAttendanceToggle'])
            ->with('success', 'Attendance started! Notifications sent to ' . $activeTeachers->count() . ' active teachers.');
    }

    public function resetAttendance()
    {
        $today = Carbon::today()->toDateString();

        TeacherAttendance::where('date', $today)->delete();

        Notification::whereDate('created_at', $today)
            ->where('title', 'Attendance Started') // Must match the title in startAttendance exactly
            ->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'teacher_attendance',
            'message' => 'Reset teacher attendance for today.',
        ]);


        return redirect()
            ->route('attendance.teacher', ['tab' => 'viewTeacherAttendanceToggle'])
            ->with('success', 'Attendance for today has been reset.');
    }



    public function update(Request $request)
    {

        $statuses = $request->input('status');

        if (!$statuses || !is_array($statuses)) {
            return redirect()->back()->with('error', 'No status changed was submittted');
        }

        $updatedCount = 0;

        foreach ($statuses as $attendanceId => $status) {
            $attendance = TeacherAttendance::find($attendanceId);

            if ($attendance || in_array($status, ['present', 'absent', 'excused'])) {
                if ($attendance->status != $status) {
                    $attendance->status = $status;
                    $attendance->save();
                    $updatedCount++;
                }
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'teacher_attendance',
            'message' => "Updated {$updatedCount} teacher attendance statuses.",
        ]);


        return redirect()->back()->with('success', "Successfully updated {$updatedCount} attendance statuses");
    }

    public function filter(Request $request)
    {
        $user = Auth::user();

        // ------------------------------------------------------------
        // 1. DETERMINE SPECIFIC ID (Role Check)
        // ------------------------------------------------------------
        $attendanceType = 'teacher';
        $specificTeacherId = null;

        // If the logged-in user is a Teacher, force the service to filter by their ID
        if ($user->role === 'teacher') {
            if (!$user->teacher) {
                return response()->json(['error' => 'Teacher profile not found'], 404);
            }
            $specificTeacherId = $user->teacher->id;
        }

        // ------------------------------------------------------------
        // 2. CALL SERVICE
        // ------------------------------------------------------------
        // NOTE: We do NOT need to map inputs manually because your 
        // JavaScript sends 'taYear', 'taMonth', etc. automatically.
        
        $result = ReportService::getAttendance($request, $attendanceType, $specificTeacherId);

        // ------------------------------------------------------------
        // 3. EXTRACT DATA
        // ------------------------------------------------------------
        $teacherAttendances = $result['attendances'];
        
        $year    = $result['year'];
        $month   = $result['month'];
        $date    = $result['date'];
        
        $present = $result['present'];
        $absent  = $result['absent'];
        $excused = $result['excused'];

        $html = '';

        // ------------------------------------------------------------
        // 4. RENDER HTML
        // ------------------------------------------------------------
        if ($user->role === 'admin') {
            // --- ADMIN VIEW ---
            $html = view('admin.report.tables.teacherAttendance', compact(
                'teacherAttendances', 
                'year', 
                'month', 
                'date', 
                'present', 
                'absent', 
                'excused'
            ))->render();

        } elseif ($user->role === 'teacher') {
            // --- TEACHER VIEW ---
            
            // Calculate Progress Bar Stats
            $totalDays = $present + $absent + $excused;
            $progress = $totalDays > 0 ? ($present / $totalDays) * 100 : 0;

            $html = view('user.teacher.table.teacherAttendanceTable', compact(
                'teacherAttendances',
                'year',
                'month',
                'progress',
                'present',
                'absent',
                'totalDays'
            ))->render();
        }

        // ------------------------------------------------------------
        // 5. RETURN JSON
        // ------------------------------------------------------------
        return response()->json(['adminHtml' => $html]);
    }
}
