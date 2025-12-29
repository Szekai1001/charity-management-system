<?php

namespace App\Helpers;

use App\Models\Item;
use App\Models\Salary;
use App\Models\StudentAttendance;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use App\Models\TeacherAttendance;

class ReportService
{
    public static function getAttendance($request, $attendanceType, $specificId = null)
    {
        // ... (Your existing input mapping) ...
        $filterYear     = $request->input($attendanceType === 'teacher' ? 'taYear' : 'saYear');
        $filterMonth    = $request->input($attendanceType === 'teacher' ? 'taMonth' : 'saMonth');
        $filterDate     = $request->input($attendanceType === 'teacher' ? 'taDate' : 'saDate');
        $filterStatus   = $request->input($attendanceType === 'teacher' ? 'taStatus' : 'saStatus');
        $filterDetails  = $request->input($attendanceType === 'teacher' ? 'taTeacherDetails' : 'saStudentDetails');

        // NEW: Check if a specific teacher scope is requested
        $filterTeacherId = $request->input('teacher_id');

        // Model selection
        if ($attendanceType === 'student') {
            $query       = StudentAttendance::with('student');
            $statsQuery  = StudentAttendance::query();
            $relation    = 'student';
            $foreignKey  = 'student_id';
        } else {
            $query       = TeacherAttendance::with('teacher');
            $statsQuery  = TeacherAttendance::query();
            $relation    = 'teacher';
            $foreignKey  = 'teacher_id';
        }

        if ($specificId) {
            $query->where($foreignKey, $specificId);
            $statsQuery->where($foreignKey, $specificId);
        }

        // Apply filters
        foreach ([$query, $statsQuery] as $q) {
            if ($filterYear)  $q->whereYear('date', $filterYear);
            if ($filterMonth) $q->whereMonth('date', $filterMonth);
            if ($filterDate)  $q->whereDate('date', $filterDate);
            if ($filterStatus) $q->where('status', strtolower($filterStatus));

            // Existing Detail Filter
            if ($filterDetails) {
                $q->whereHas($relation, function ($subQ) use ($filterDetails) {
                    $subQ->where(function ($query) use ($filterDetails) {
                        $query->where('name', 'like', '%' . $filterDetails . '%')
                            ->orWhere('ic', 'like', '%' . $filterDetails . '%');
                    });
                });
            }

            // NEW: Apply Teacher Scope Filter (Only for student attendance)
            if ($filterTeacherId && $attendanceType === 'student') {
                $q->whereHas('student', function ($subQ) use ($filterTeacherId) {
                    $subQ->where('teacher_id', $filterTeacherId);
                });
            }
        }

        // ... (Rest of your stats calculation and return) ...

        $stats = $statsQuery
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $present = $stats->firstWhere('status', 'present')->total ?? 0;
        $absent  = $stats->firstWhere('status', 'absent')->total ?? 0;
        $excused = $stats->firstWhere('status', 'excused')->total ?? 0;

        return [
            'type'          => $attendanceType,
            'attendances'   => $query->orderBy('date', 'desc')->simplePaginate(20),
            'year'          => $filterYear,
            'month'         => $filterMonth,
            'date'          => $filterDate,
            'status'        => $filterStatus,
            'present'       => $present,
            'absent'        => $absent,
            'excused'       => $excused,
        ];
    }

    public static function getReportingAttendance($request, $attendanceType)
    {
        // Map inputs depending on type
        $filterYear     = $request->filled('year') ? $request->year : now()->year;
        $filterMonth    = $request->filled('month') ? $request->month : now()->month;
        $filterAbsence  = (int) $request->input('absentCount', 3);

        // Model selection
        if ($attendanceType === 'student') {
            $query       = StudentAttendance::with('student');
            $statsQuery  = StudentAttendance::query();
            $dailyQuery  = StudentAttendance::query();
            $absentQuery = StudentAttendance::query();
            $idKey       = 'student_id';
        } else {
            $query       = TeacherAttendance::with('teacher');
            $statsQuery  = TeacherAttendance::query();
            $dailyQuery  = TeacherAttendance::query();
            $absentQuery = TeacherAttendance::query();
            $idKey       = 'teacher_id';
        }

        // Apply filters
        foreach ([$query, $statsQuery, $dailyQuery, $absentQuery] as $q) {
            if ($filterYear)  $q->whereYear('date', $filterYear);
            if ($filterMonth) $q->whereMonth('date', $filterMonth);
        }

        // Compute overall stats
        $stats = $statsQuery
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->get();

        $present = $stats->firstWhere('status', 'present')->total ?? 0;
        $absent  = $stats->firstWhere('status', 'absent')->total ?? 0;
        $excused = $stats->firstWhere('status', 'excused')->total ?? 0;

        $total = $present + $absent + $excused;
        $attendanceRate = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        // Daily chart
        $attendanceDaily = $dailyQuery
            ->selectRaw("DATE_FORMAT(date, '%Y-%m-%d') as attendance_date, COUNT(*) as total_present")
            ->where('status', 'present')
            ->groupBy('date')
            ->pluck('total_present', 'attendance_date');

        // ============================================
        // ABSENCE THRESHOLD (BEST VERSION)
        // ============================================

        $absentDataFinal = collect();

        if ($filterAbsence !== null) {

            // Step 1: Find IDs that meet the absence limit
            $absentCount = $absentQuery
                ->select($idKey)
                ->selectRaw("COUNT(*) as absent_count")
                ->where("status", "absent")
                ->groupBy($idKey)
                ->having("absent_count", ">=", $filterAbsence)
                ->get();

            // Step 2: Loop through each ID
            foreach ($absentCount as $recordGroup) {

                $id = $recordGroup->$idKey;

                // Step 3: Fetch all filtered attendance records for this person
                $records = ($attendanceType === 'student'
                    ? StudentAttendance::with('student')
                    : TeacherAttendance::with('teacher'))
                    ->when($filterYear, fn($q) => $q->whereYear('date', $filterYear))
                    ->when($filterMonth, fn($q) => $q->whereMonth('date', $filterMonth))
                    ->where($idKey, $id)
                    ->get();

                // Step 4: Calculate stats
                $presentDay = $records->where('status', 'present')->count();
                $absentDay  = $records->where('status', 'absent')->count();
                $excusedDay = $records->where('status', 'excused')->count();

                $totalDays = $presentDay + $absentDay + $excusedDay;

                $rate = $totalDays > 0
                    ? round(($presentDay / $totalDays) * 100, 2)
                    : 0;

                // Step 5: Push to final output
                $absentDataFinal->push([
                    'name' => $records->first()->{$attendanceType === 'student' ? 'student' : 'teacher'}->name,
                    'absent_count' => $absentDay,
                    'rate'       => $rate,
                ]);
            }
        }

        // Return output
        return [
            'type'               => $attendanceType,
            'attendances'        => $query->get(),
            'year'               => $filterYear,
            'month'              => $filterMonth,
            'present'            => $present,
            'absent'             => $absent,
            'excused'            => $excused,
            'attendanceRate'     => $attendanceRate,
            'attendanceDaily'    => $attendanceDaily,
            'absentDataFinal'    => $absentDataFinal
        ];
    }


    public static function getSupplyDistribution($request)
    {
        $query = SupplyRequest::with('package', 'delivery_date', 'beneficiary');

        $filterYear = $request->filled('sd_year') ? (int) $request->sd_year : null;
        $filterMonth = $request->filled('sd_month') ? (int) $request->sd_month : null;

        if ($filterYear) {
            $query->whereYear('created_at', $filterYear);
        }
        if ($filterMonth) {
            $query->whereMonth('created_at', $filterMonth);
        }

        if ($request->filled('package')) {
            $query->where('package_id', $request->package);
        }
        if ($request->filled('session')) {
            $query->whereHas('delivery_date', function ($q) use ($request) {
                $q->where('session', $request->session);
            });
        }

        if ($request->filled('distribution_method')) {
            $query->where('distribution_method', $request->distribution_method);
        }
        if ($request->filled('distribution_status')) {
            $query->where('distribution_status', $request->distribution_status);
        }

        if ($request->filled('beneficiary_details')) {
            $query->whereHas('beneficiary', function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->beneficiary_details . '%')
                        ->orwhere('ic', 'like', '%' . $request->beneficiary_details . '%');
                });
            });
        }

        $stats = (clone $query)
            ->select('distribution_status')
            ->selectRaw('COUNT(*) as total_supply_request')
            ->groupBy('distribution_status')
            ->get()
            ->keyBy('distribution_status');

        $paginatedResults = $query->simplePaginate(20)->withQueryString();

        return [
            'supplyRequests'    => $paginatedResults,
            'year'              => $filterYear,
            'month'             => $filterMonth,
            'total_supply_request' => $query->count(),
            'approved_request'  => $stats['approved']->total_supply_request ?? 0,
            'rejected_request'    => $stats['rejected']->total_supply_request ?? 0,
            'delivered_request' => $stats['delivered']->total_supply_request ?? 0,
            'pending_request'   => $stats['pending']->total_supply_request ?? 0,
        ];
    }

    public static function getPurchaseRequirement($year = null, $month = null)
    {
        $query = SupplyRequestItem::query()
            ->join('supply_requests', 'supply_requests.id', '=', 'supply_request_items.supply_request_id')
            ->join('items', 'items.id', '=', 'supply_request_items.item_id')
            ->selectRaw('
            supply_request_items.item_id, 
            items.name as item_name, 
            items.estimated_price, 
            SUM(supply_request_items.quantity) as total_quantity,
            SUM(supply_request_items.quantity * items.estimated_price) as subtotal
        ')
            ->whereIn('supply_requests.distribution_status', ['approved', 'delivered']);


        if ($year) {
            $query->whereYear('supply_requests.created_at', $year);
        }

        if ($month) {
            $query->whereMonth('supply_requests.created_at', $month);
        }
        return $query->groupBy('supply_request_items.item_id', 'items.name', 'items.estimated_price')->get();
    }



    public static function getSalary($request)
    {
        // 1. Get vars directly (Do not use ?: now() here)
        // We allow these to be null so we can fetch "All" records.
        $year = $request->salary_year;
        $month = $request->salary_month;
        $status = $request->salary_payment_status;

        // 2. Start Query
        $query = Salary::with('teacher');

        // 3. Apply Filters ONLY if variables are not empty
        if (!empty($year)) {
            $query->where('year', $year);
        }
        
        if (!empty($month)) {
            $query->where('month', $month);
        }

        if (!empty($status)) {
            $query->where('payment_status', $status);
        }

        // 4. Sort and Get Data
        $salaryDetails = $query->orderBy('year', 'desc')
                               ->orderBy('month', 'desc')
                               ->get();

        // 5. Calculate Stats & Return
        return [
            'year' => $year,
            'month' => $month,
            'status' => $status,
            'salaries' => $salaryDetails,
            'paid_count' => $salaryDetails->where('payment_status', 'paid')->count(),
            'unpaid_count' => $salaryDetails->where('payment_status', 'unpaid')->count(),
            'total_paid_salary' => $salaryDetails->where('payment_status', 'paid')->sum('salary'),
            'total_unpaid_salary' => $salaryDetails->where('payment_status', 'unpaid')->sum('salary'),
        ];
    }
}
