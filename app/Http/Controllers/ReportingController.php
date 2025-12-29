<?php

namespace App\Http\Controllers;

use App\Helpers\ReportService;
use App\Models\Item;
use App\Models\Package;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function attendanceReporting(Request $request)
    {
        // Get student stats
        $student = ReportService::getReportingAttendance($request, 'student');

        // Get teacher stats
        $teacher = ReportService::getReportingAttendance($request, 'teacher');

        // Combine absence + excused
        $totalAbsent  = $student['absent']  + $teacher['absent'];
        $studentAbsent = $student['absent'];
        $teacherAbsent = $teacher['absent'];

        $totalExcused = $student['excused'] + $teacher['excused'];
        $studentExcused = $student['excused'];
        $teacherExcused = $teacher['excused'];

        $studentAttendanceRate = $student['attendanceRate'];
        $teacherAttendanceRate = $teacher['attendanceRate'];

        $studentAttendanceDaily = $student['attendanceDaily'];
        $teacherAttendanceDaily = $teacher['attendanceDaily'];

        $studentAbsentData = $student['absentDataFinal'];
        $teacherAbsentData = $teacher['absentDataFinal'];


        return view('admin.insights.attendance', compact(
            'student',
            'teacher',
            'totalAbsent',
            'teacherAbsent',
            'studentAbsent',
            'totalExcused',
            'studentExcused',
            'teacherExcused',
            'studentAttendanceRate',
            'teacherAttendanceRate',
            'studentAttendanceDaily',
            'teacherAttendanceDaily',
            'studentAbsentData',
            'teacherAbsentData'
        ));
    }

    public function absentThreshold(Request $request)
    {
        // Get student stats
        $student = ReportService::getReportingAttendance($request, 'student');

        // Get teacher stats
        $teacher = ReportService::getReportingAttendance($request, 'teacher');

        return response()->json([
            'studentAbsentData' => $student['absentDataFinal'],
            'teacherAbsentData' => $teacher['absentDataFinal']
        ]);
    }

    public function supplyRequestReporting(Request $request)
{
    $filterYear = $request->input('year') ?? now()->year;
    $filterMonth = $request->filled('month') ? $request->month : null;

    // --- Helper for Date Filtering ---
    $dateFilter = function ($q) use ($filterYear, $filterMonth) {
        if ($filterYear) $q->whereYear('created_at', $filterYear);
        if ($filterMonth) $q->whereMonth('created_at', $filterMonth);
    };

    // 1. Top/Bottom Cards (No changes needed here, these looked okay)
    $mostDemandedPackages = Package::withCount(['supply_requests as total_packages' => $dateFilter])
        ->orderByDesc('total_packages')->take(3)->get();

    $mostDemandedItems = Item::withSum(['supply_request_items as total_items' => function ($q) use ($dateFilter) {
        $q->whereHas('supply_request', $dateFilter);
    }], 'quantity')->orderByDesc('total_items')->take(3)->get();

    $mostLeastDemandedPackages = Package::withCount(['supply_requests as total_least_packages' => $dateFilter])
        ->orderBy('total_least_packages', 'asc')->take(3)->get();

    $mostLeastDemandedItems = Item::withSum(['supply_request_items as total_least_items' => function ($q) use ($dateFilter) {
        $q->whereHas('supply_request', $dateFilter);
    }], 'quantity')->orderBy('total_least_items', 'asc')->take(3)->get();

    // ==========================================
    // 2. Monthly Table Data 
    // ==========================================

    // A. Applications per Month (Keyed by month for easy access)
    $appsPerMonth = SupplyRequest::whereYear('created_at', $filterYear)
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
        ->groupBy('month')
        ->pluck('count', 'month'); // Result: [1 => 50, 2 => 30...]

    // B. Financials (Cost & Items) per Month
    $monthlyFinancials = \Illuminate\Support\Facades\DB::table('supply_request_items')
        ->join('supply_requests', 'supply_requests.id', '=', 'supply_request_items.supply_request_id') // FIXED JOIN
        ->join('items', 'items.id', '=', 'supply_request_items.item_id')
        ->whereYear('supply_requests.created_at', $filterYear)
        ->whereIn('supply_requests.distribution_status', ['approved', 'delivered'])
        ->selectRaw('
            MONTH(supply_requests.created_at) as month,
            SUM(supply_request_items.quantity * items.estimated_price) as total_cost,
            SUM(supply_request_items.quantity) as total_items
        ')
        ->groupBy('month')
        ->get()
        ->keyBy('month'); // Key by month to access like $monthlyFinancials[1]

    // C. Highest Cost Item Per Month (Corrected Logic)
    // We group by month AND item to find the winner for each specific month
    $topItemsPerMonth = \Illuminate\Support\Facades\DB::table('supply_request_items')
        ->join('supply_requests', 'supply_requests.id', '=', 'supply_request_items.supply_request_id')
        ->join('items', 'items.id', '=', 'supply_request_items.item_id')
        ->whereYear('supply_requests.created_at', $filterYear)
        ->selectRaw('MONTH(supply_requests.created_at) as month, items.name, SUM(supply_request_items.quantity * items.estimated_price) as cost')
        ->groupBy('month', 'items.name')
        ->get()
        ->groupBy('month')
        ->map(fn($group) => $group->sortByDesc('cost')->first()->name ?? '-');

    // D. Most Requested Package Per Month (Corrected Logic)
    $topPackagesPerMonth = \Illuminate\Support\Facades\DB::table('supply_requests')
        ->join('packages', 'packages.id', '=', 'supply_requests.package_id')
        ->whereYear('supply_requests.created_at', $filterYear)
        ->selectRaw('MONTH(supply_requests.created_at) as month, packages.name, COUNT(*) as count')
        ->groupBy('month', 'packages.name')
        ->get()
        ->groupBy('month')
        ->map(fn($group) => $group->sortByDesc('count')->first()->name ?? '-');

    // ==========================================
    // 3. Construct Table Data
    // ==========================================
    $tableData = [];
    $runningYtd = 0;
    
    // Grand Totals
    $grandTotalApps = 0;
    $grandTotalItems = 0;
    $grandTotalCost = 0;

    foreach (range(1, 12) as $m) {
        $monthName = \Carbon\Carbon::create()->month($m)->format('F');
        
        // Safe Access using Null Coalescing Operator (??)
        $appCount = $appsPerMonth[$m] ?? 0;
        
        $financials = $monthlyFinancials->get($m);
        $monthCost = $financials->total_cost ?? 0;
        $itemCount = $financials->total_items ?? 0;

        $runningYtd += $monthCost;

        // Skip empty months
        if ($appCount == 0 && $monthCost == 0) {
            continue;
        }

        $grandTotalApps += $appCount;
        $grandTotalItems += $itemCount;
        $grandTotalCost += $monthCost;

        $tableData[] = [
            'month_name' => $monthName,
            'applications' => $appCount,
            'items_distributed' => $itemCount,
            'cost' => $monthCost,
            'average_cost' => $appCount > 0 ? $monthCost / $appCount : 0,
            'highest_cost_item' => $topItemsPerMonth[$m] ?? '-', // Now this works!
            'top_package' => $topPackagesPerMonth[$m] ?? '-',     // Now this works!
            'ytd' => $runningYtd
        ];
    }

    // --- Chart Data Preparation (If needed for frontend charts) ---
    // Note: We use values() to reset keys to 0,1,2... for JS arrays
    $supplyApplicationLabel = $appsPerMonth->keys()->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'))->toArray();
    $supplyApplicationData = $appsPerMonth->values()->toArray();
    
    // For package application chart (Optimized N+1)
    $packageApplicationData = [];
    $packageCounts = Package::with(['supply_requests' => function($q) use ($filterYear) {
        $q->selectRaw('package_id, MONTH(created_at) as month, count(*) as total')
          ->whereYear('created_at', $filterYear)
          ->groupBy('package_id', 'month');
    }])->get();

    $packageApplicationLabel = array_map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'), range(1,12));
    
    foreach($packageCounts as $pkg) {
        $data = [];
        // Map the relation data to a simple [0, 0, 5, ...] array
        $counts = $pkg->supply_requests->pluck('total', 'month'); 
        foreach(range(1,12) as $m) {
            $data[] = $counts[$m] ?? 0;
        }
        $packageApplicationData[] = [
            'label' => $pkg->name,
            'data' => $data
        ];
    }
    
    // Total Cost Charts
    $totalCostLabel = $monthlyFinancials->keys()->map(fn($m) => \Carbon\Carbon::create()->month($m)->format('F'))->toArray();
    $totalCostData = $monthlyFinancials->pluck('total_cost')->values()->toArray();

    return view('admin.insights.supplyRequest', compact(
        'mostDemandedPackages',
        'mostDemandedItems',
        'mostLeastDemandedPackages',
        'mostLeastDemandedItems',
        'tableData',
        'supplyApplicationLabel',
        'supplyApplicationData',
        'packageApplicationLabel',
        'packageApplicationData',
        'totalCostLabel',
        'totalCostData',
        'grandTotalApps',
        'grandTotalItems',
        'grandTotalCost',
        'runningYtd',
        'filterYear',
        'filterMonth'
    ));
}
}
