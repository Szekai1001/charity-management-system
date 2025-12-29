<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\FormControl;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\SupplyRequest;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // ------------------------------------------------------------
        // APPLICATION COUNTS (GLOBAL)
        // ------------------------------------------------------------ 
        $studentApplications = [
            'approved'   => Application::where('application_type', 'student')->where('status', 'approved')->count(),
            'processing' => Application::where('application_type', 'student')->where('status', 'processing')->count(),
            'rejected'   => Application::where('application_type', 'student')->where('status', 'rejected')->count(),
        ];

        $beneficiaryApplications = [
            'approved'   => Application::where('application_type', 'Beneficiary')->where('status', 'approved')->count(),
            'processing' => Application::where('application_type', 'Beneficiary')->where('status', 'processing')->count(),
            'rejected'   => Application::where('application_type', 'Beneficiary')->where('status', 'rejected')->count(),
        ];
        $beneficiaryNewThisMonth = Beneficiary::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ------------------------------------------------------------
        // Teachers count
        // ------------------------------------------------------------
        $teachersCount = Teacher::count();
        $teacherPresentCount = TeacherAttendance::where('date', today())->where('status', 'present')->count();

        // ------------------------------------------------------------
        // FILTER: Student / Beneficiary (for Doughnut Chart)
        // ------------------------------------------------------------
        $selected = $request->filled('student') ? 'student' : ($request->filled('beneficiary') ? 'beneficiary' : 'student');

        $chartData = $selected === 'student' ? $studentApplications : $beneficiaryApplications;


        // ------------------------------------------------------------
        // MONTHLY STUDENT APPROVED COMPARISON
        // ------------------------------------------------------------
        $now = now();
        $lastMonth = now()->subMonth();

        // 2. Get Current Count
        $current = Student::whereHas('user.applications', fn($q) => $q->where('status', 'approved'))
            ->whereYear('created_at', $now->year)   // <--- Added Year Check
            ->whereMonth('created_at', $now->month)
            ->count();

        // 3. Get Previous Count
        $previous = Student::whereHas('user.applications', fn($q) => $q->where('status', 'approved'))
            ->whereYear('created_at', $lastMonth->year) // <--- Added Year Check
            ->whereMonth('created_at', $lastMonth->month)
            ->count();

        $studentPercentage = ($previous == 0) ? ($current > 0 ? 100 : 0) : (($current - $previous) / $previous) * 100;

        // Format it to 1 decimal place (optional)
        $studentPercentage = number_format($studentPercentage, 1);


        // ------------------------------------------------------------
        // WEEKLY ATTENDANCE FILTER: thisWeek / lastWeek
        // ------------------------------------------------------------
        $start = $request->filled('thisWeek')
            ? now()->startOfWeek()
            : now()->subWeek()->startOfWeek();

        $weekDates = collect();
        for ($i = 0; $i < 5; $i++) {
            $date = $start->copy()->addDays($i);
            $weekDates->push([
                'label' => $date->format('D'),
                'date'  => $date->toDateString()
            ]);
        }

        $weeksLabel = $weekDates->pluck('label')->toArray();

        $studentAttendances = $weekDates->map(
            fn($day) =>
            StudentAttendance::whereDate('date', $day['date'])
                ->whereNotNull('check_in_time')->count()
        )->toArray();

        $teacherAttendances = $weekDates->map(
            fn($day) =>
            TeacherAttendance::whereDate('date', $day['date'])
                ->whereNotNull('check_in_time')->count()
        )->toArray();


        // ------------------------------------------------------------
        // CURRENT OPEN APPLICATION (SAFE CHECK)
        // ------------------------------------------------------------
        $currentOpenApplication = FormControl::where('open_date', '<=', today())
            ->where('close_date', '>=', today())
            ->orderBy('close_date')
            ->first();

        if ($currentOpenApplication) {
            $due = Carbon::parse($currentOpenApplication->close_date);
            $diff = now()->diff($due);

            $remaining = sprintf('%d days %d hours %d min', $diff->d, $diff->h, $diff->i);
            $formType = $currentOpenApplication->form_type;
        } else {
            $remaining = "No active form";
            $formType = null;
        }

        // ------------------------------------------------------------
        // SUPPLY REQUEST VIEW
        // ------------------------------------------------------------
        $recentSupplyRequests = SupplyRequest::with(['package', 'beneficiary'])->latest()->take(6)->get();

        // ------------------------------------------------------------
        // ACTIVITY LOG
        // ------------------------------------------------------------
        $activity_logs = ActivityLog::where('user_id', Auth::id())->latest()->take(7)->get();

        // ------------------------------------------------------------
        // RETURN VIEW
        // ------------------------------------------------------------
        return view('admin.dashboard', compact(
            'studentApplications',
            'beneficiaryApplications',
            'beneficiaryNewThisMonth',
            'teachersCount',
            'teacherPresentCount',
            'studentAttendances',
            'teacherAttendances',
            'weeksLabel',
            'formType',
            'remaining',
            'studentPercentage',
            'chartData',
            'selected',
            'recentSupplyRequests',
            'activity_logs'
        ));
    }
}
