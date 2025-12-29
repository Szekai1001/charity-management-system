<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Salary;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherDashboardController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        $teacherId = $teacher->id;
        $current = now();
        $year = $current->year;
        $month = $current->month;

        // Get all students under this teacher
        $studentsPag = Student::where('teacher_id', $teacherId)->simplePaginate(5, ['*'], 'stu_page');
        $students = Student::where('teacher_id', $teacherId)->get();


        // Get students name first letter
        $initials = [];
        foreach ($studentsPag as $student) {
            $words = explode(' ', $student->name);
            $abbr = '';
            foreach ($words as $word) {
                $abbr .= strtoupper(substr($word, 0, 1));
            }
            $initials[$student->id] = $abbr; // store initials by student id
        }

        // Get only 5 student first for student attendance part
        $studentAttendances = Student::where('teacher_id', $teacherId)->simplePaginate(5, ['*'], 'att_page');


        // ✅ Calculate attendance percentage for each student
        foreach ($studentAttendances as $student) {
            $totalDays = StudentAttendance::where('student_id', $student->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->count();

            $presentDays = StudentAttendance::where('student_id', $student->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->where('status', 'present')
                ->count();

            $student->attendance_percent = $totalDays > 0
                ? round(($presentDays / $totalDays) * 100)
                : 0;
        }

        // ✅ Calculate teacher’s attendance percentage
        $teacherTotalDays = TeacherAttendance::where('teacher_id', $teacherId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->count();

        $teacherPresentDays = TeacherAttendance::where('teacher_id', $teacherId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 'present')
            ->count();

        $teacherAbsentDays = $teacherTotalDays - $teacherPresentDays;

        $teacherAttendancePercent = $teacherTotalDays > 0
            ? round(($teacherPresentDays / $teacherTotalDays) * 100)
            : 0;


        // Get activity log
        $activitiesTeacherDashboard = ActivityLog::where('user_id', Auth::id())
            ->latest()
            ->simplePaginate(5, ['*'], 'log_page'); // show 5 per page


        // Get salary
        $salaries = Salary::where('teacher_id', $teacherId)
            ->orderByDesc('year')   // 1. Sort by Year (2025, 2024...)
            ->orderByDesc('month')  // 2. Sort by Month (12, 11...) within that year
            ->simplePaginate(5, ['*'], 'salary_page');



        // ✅ Return all data to the view
        return view('user.teacher.dashboard', compact('students', 'studentsPag', 'studentAttendances', 'initials', 'teacherAttendancePercent', 'teacherTotalDays', 'teacherAbsentDays', 'teacherPresentDays', 'activitiesTeacherDashboard', 'salaries', 'year', 'month'));
    }

    public function notification(Request $request)
    {
        $tab = $request->query('tab', 'unread');

        $query = Notification::where('user_id', Auth::id());

        if ($tab === 'read') {
            $query->where('is_read', 1);
        } elseif ($tab === 'unread') {
            $query->where('is_read', 0);
        }

        // Add ->appends(['tab' => $tab]) OR ->withQueryString()
        $activitiesType = $query->latest()
            ->simplePaginate(5)
            ->appends(['tab' => $tab]);

        return view('user.teacher.notification', compact('activitiesType', 'tab'));
    }


    public function updateNotification(Request $request)
    {
        $notificationId = $request->input('notification');
        $allRead = $request->input('allRead');
        $currentTab = $request->input('tab', 'unread');

        if ($notificationId) {
            $activity = Notification::find($notificationId);

            if ($activity) {
                $activity->is_read = !$activity->is_read;
                $activity->save();
            }
        }

        if ($allRead) {
            $activity =  Notification::where('user_id', Auth::id())->where('is_read', false)->update([
                'is_read' => true,
            ]);
        }

        return redirect()->route('teacher.notification', ['tab' => $currentTab]);
    }
}
