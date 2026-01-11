<?php

namespace App\Http\Controllers;

use App\Helpers\ReportService;
use App\Models\ActivityLog;
use App\Models\Notification;
use App\Models\Salary;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;

use function PHPUnit\Framework\isEmpty;

class SalaryController extends Controller
{
    public function index(Request $request)
    {
        // ====================================================
        // PHASE 1: CALCULATE & UPDATE DB
        // ====================================================

        // FIX: Prioritize Request -> Then Current Date. 
        // We removed 'session()' from the read logic so it doesn't get "stuck" on old dates.
        if ($request->has('salary_month_year')) {
            $calcDate = $request->salary_month_year;
        } else {
            $calcDate = now()->format('Y-m'); // Defaults to "2025-12"
        }

        // Optional: We can still save to session for other pages, but we won't read it here.
        session(['selected_month_year' => $calcDate]);

        $calcYear = Carbon::parse($calcDate)->year;
        $calcMonth = Carbon::parse($calcDate)->month;
        $payrate = session('payrate') ?? 0;

        // 2. Load Teachers & Calculate Attendance
        $teachers = Teacher::with(['teacher_attendances' => function ($query) use ($calcMonth, $calcYear) {
            $query->whereMonth('date', $calcMonth)->whereYear('date', $calcYear);
        }])->get();

        foreach ($teachers as $teacher) {
            $totalTime = 0;
            // -- Calculation --
            foreach ($teacher->teacher_attendances as $attendance) {
                if ($attendance->check_in_time && $attendance->check_out_time) {
                    $in = Carbon::parse($attendance->date . ' ' . $attendance->check_in_time);
                    $out = Carbon::parse($attendance->date . ' ' . $attendance->check_out_time);
                    if ($out->lt($in)) $out->addDay();
                    $totalTime += $in->diffInHours($out);
                }
            }

            $salaryAmt = $totalTime * $payrate;

            // -- DB Update --
            $existing = Salary::where('teacher_id', $teacher->id)
                ->where('year', $calcYear)->where('month', $calcMonth)->first();
            
            $salaryDate = Carbon::create($calcYear, $calcMonth, 1);

            if($teacher->created_at->gt($salaryDate)){
                return;
            }

            if (!$existing) {
                Salary::create([
                    'teacher_id' => $teacher->id,
                    'year' => $calcYear,
                    'month' => $calcMonth,
                    'salary' => $salaryAmt,
                    'hours_worked' => $totalTime,
                    'payment_status' => 'unpaid'
                ]);
            } elseif ($existing->payment_status == 'unpaid') {
                $existing->update(['hours_worked' => $totalTime, 'salary' => $salaryAmt]);
            }
        }

        // ====================================================
        // PHASE 2: PREPARE DISPLAY DATA
        // ====================================================

        // Check: Is the user actively filtering via the table filters?
        if ($request->hasAny(['salary_year', 'salary_month', 'salary_payment_status'])) {
            // YES: Use the user's specific filters
            $dataRequest = $request;
        } else {
            // NO (Initial Load): 
            // We must manually tell the Service to load the CURRENT month calculated above.
            $dataRequest = new Request();
            $dataRequest->merge([
                'salary_year' => $calcYear,
                'salary_month' => $calcMonth,
                'salary_payment_status' => null
            ]);
        }

        // Call the Service (File 1)
        $result = ReportService::getSalary($dataRequest);

        return view('admin.salaryManagement', [
            // Table Data
            'salaryDetails'     => $result['salaries'],
            'paidTeachers'      => $result['paid_count'],
            'unpaidTeachers'    => $result['unpaid_count'],
            'totalPaidSalary'   => $result['total_paid_salary'],
            'totalUnpaidSalary' => $result['total_unpaid_salary'],

            // Filter Inputs (Pass back so the view knows what is selected)
            'year'              => $result['year'],
            'month'             => $result['month'],
            'salary_payment_status' => $result['status'],

            // Config Data
            'payrate'           => $payrate,
            'selectedMonth'     => $calcDate
        ]);
    }



    public function filter(Request $request)
    {
        $result = ReportService::getSalary($request);
        $salaryDetails = $result['salaries'];
        $year = $result['year'];
        $month = $result['month'];
        $paidTeachers = $result['paid_count'];
        $unpaidTeachers = $result['unpaid_count'];
        $totalPaidSalary = $result['total_paid_salary'];
        $totalUnpaidSalary = $result['total_unpaid_salary'];

        return view('admin.report.tables.salary', compact('salaryDetails', 'year', 'month', 'paidTeachers', 'unpaidTeachers', 'totalPaidSalary', 'totalUnpaidSalary'))->render();
    }


    public function calculateSalary(Request $request)
    {
        $validated = $request->validate([
            'payrate' => 'required|numeric|min:1',
            'salary_month_year' => 'required|string',
        ]);

        $payrate = $validated['payrate'];
        $salary_year_month = $validated['salary_month_year'];

        $salary_year = \Carbon\Carbon::parse($salary_year_month)->year;
        $salary_month = \Carbon\Carbon::parse($salary_year_month)->month;

        $salaryDetails = Salary::where('year', $salary_year)
            ->where('month', $salary_month)
            ->get();

        if ($salaryDetails->isEmpty()) {
            return redirect()->back()->with('error', 'No salary records for the selected month and year');
        }

        foreach ($salaryDetails as $salary) {
            if ($salary->payment_status === 'paid') {
                continue;
            } else {
                $salaryCal = $salary->hours_worked * $payrate;

                $salary->update([
                    'salary' => $salaryCal,
                ]);
            }
        }

        // Store selected month/year and payrate in session so index can use it
        session([
            'payrate' => $payrate,
            'selected_month_year' => $salary_year_month
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'salary',
            'message' => "Salary successfully calculated for {$salary_month}-{$salary_year}.",
        ]);

        return redirect()->route('salary', ['salary_month_year' => $salary_year_month])
            ->with('success', 'Salaries calculated and stored successfully');
    }



    public function update(Request $request)
    {
        $paymentStatuses = $request->input('payment_status', []);

        $updatedStatusCount = 0;
        $paidCount = 0;

        foreach ($paymentStatuses as $salaryId => $newStatus) {
            $salary = Salary::find($salaryId);

            if ($salary) {
                // 1. Get the status currently in the database
                $currentDatabaseStatus = $salary->payment_status;

                // 2. CHECK: If the status in the form is the same as the database, STOP.
                // This prevents re-sending notifications to people who are already paid.
                if ($currentDatabaseStatus === $newStatus) {
                    continue; // Skip to the next teacher
                }

                // 3. Status is definitely different, so we update
                $salary->payment_status = $newStatus;
                $salary->payment_date = ($newStatus === 'paid') ? now() : null;
                $salary->save();

                $updatedStatusCount++;

                // 4. Send Notification (Only happens if we passed the check above)
                if ($newStatus === 'paid') {
                    $paidCount++;

                    $salaryForDate = \Carbon\Carbon::createFromDate($salary->year, $salary->month, 1)->format('F Y');

                    Notification::create([
                        'user_id' => $salary->teacher->user_id, // Ensure this maps to the correct teacher
                        'title'   => "Salary Paid",
                        'message' => "Your Salary for " . $salaryForDate . " has been paid.",
                        'type'    => "salary_payment",
                        'is_read' => 0,
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', "Updated {$updatedStatusCount} records.");
    }


    public function teacherSalaryView(Request $request)
    {
        $teacher = Auth::user()->teacher;

        // 1. Get Real Time Context (For "Current Salary" Card)
        $now = now();
        $currentYear = $now->year;
        $currentMonth = $now->month;

        // 2. Get Filter Input (For "History" Table)
        $filterYear = $request->input('salaryViewYear');

        // 3. Fetch "Current Month" specific record
        // (This shows the big card at the top for "This Month's Status")
        $currentMonthSalary = $teacher->salary()
            ->where('year', $currentYear)
            ->where('month', $currentMonth)
            ->first();

        // 4. Build History Query
        $historyQuery = $teacher->salary()
            ->orderByDesc('year')
            ->orderByDesc('month');

        // Apply Filter only if user selected a year
        if ($filterYear) {
            $historyQuery->where('year', $filterYear);
        }

        // 5. Paginate
        // IMPORTANT: use withQueryString() so filters persist on Page 2, 3, etc.
        $salaryDetails = $historyQuery->paginate(12)->withQueryString();

        return view('user.teacher.salary', [
            'currentMonthSalary' => $currentMonthSalary,
            'salaryDetails'      => $salaryDetails,

            // Pass these for "Current Status" display
            'currentYear'        => $currentYear,
            'currentMonthName'   => $now->format('F'), // e.g., "December"

            // Pass this back so the Filter Dropdown stays selected
            'selectedYear'       => $filterYear
        ]);
    }
}
