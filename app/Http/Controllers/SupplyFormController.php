<?php

namespace App\Http\Controllers;

use App\Helpers\ReportService;
use App\Models\ActivityLog;
use App\Models\Beneficiary;
use App\Models\DeliveryDate;
use App\Models\FormControl;
use App\Models\Notification;
use App\Models\Package;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDO;

//  Admin submit the available package and dates for the supply request form
class SupplyFormController extends Controller
{

    // Route: supplyRequest.show
    public function supplyRequestShow(Request $request)
    {
        $activeTab = $request->get('tab', session('active_tab', 'viewSupplyRequest'));
        $allPackages = Package::all();
        $allSessions = DeliveryDate::select('session')
            ->distinct()
            ->orderBy('session')
            ->get();

        $current = now();
        $year = $current->year;
        $month = $current->month;

        $supplyRequests = SupplyRequest::with('package', 'delivery_date', 'beneficiary') // Good to add 'with' for performance
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->simplePaginate(20)
            ->withQueryString();

        $allMethods  = SupplyRequest::select('distribution_method')->distinct()->pluck('distribution_method');
        $allStatuses = SupplyRequest::select('distribution_status')->distinct()->pluck('distribution_status');
        $beneficiaries = Beneficiary::select('id', 'name')->orderBy('name')->get();
        $purchaseRequirements = ReportService::getPurchaseRequirement($year, $month);
        $totalPrice = $purchaseRequirements->sum('subtotal');


        // Grouped stats for current month
        $stats = SupplyRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select('distribution_status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('distribution_status')
            ->get()
            ->keyBy('distribution_status');

        $supplyRequestsCount = $stats->sum('total'); // total requests
        $approvedCount       = $stats['approved']->total ?? 0;
        $rejectedCount         = $stats['rejected']->total ?? 0;
        $deliveredCount      = $stats['delivered']->total ?? 0;
        $pendingCount        = $stats['pending']->total ?? 0;

        return view('admin.supplyView', compact(
            'allPackages',
            'allSessions',
            'allMethods',
            'allStatuses',
            'supplyRequests',
            'year',
            'month',
            'purchaseRequirements',
            'activeTab',
            'supplyRequestsCount',
            'approvedCount',
            'deliveredCount',
            'rejectedCount',
            'pendingCount',
            'totalPrice',
            'beneficiaries'
        ));
    }


    public function filterSupplyRequest(Request $request)
    {
        $result = ReportService::getSupplyDistribution($request);
        $supplyRequests = $result['supplyRequests'];
        $year = $result['year'];
        $month = $result['month'];
        $supplyRequestsCount = $result['total_supply_request'];
        $approvedCount = $result['approved_request'];
        $rejectedCount = $result['rejected_request'];
        $deliveredCount = $result['delivered_request'];
        $pendingCount = $result['pending_request'];
        return view('admin.report.tables.supplyDistribution', compact('supplyRequests', 'year', 'month', 'supplyRequestsCount', 'approvedCount', 'deliveredCount', 'rejectedCount', 'pendingCount'));
    }

    public function filterPurchaseRequirement(Request $request)
    {
        $year = $request->filled('pr_year') ? (int) $request->pr_year : now()->year;
        $month = $request->filled('pr_month') ? (int) $request->pr_month : now()->month;

        $purchaseRequirements = ReportService::getPurchaseRequirement($year, $month);

        return view('admin.report.tables.purchaseRequirement', compact('purchaseRequirements', 'year', 'month'));
    }

    public function store(Request $request)
    {
        // --- STEP 1: Validate the request ---
        $validated = $request->validate([
            // active_packages is REQUIRED IF delivery_dates is NOT present
            'active_packages' => 'array|nullable|required_without:delivery_dates',
            'active_packages.*' => 'exists:packages,id',

            // delivery_dates is REQUIRED IF active_packages is NOT present
            'delivery_dates' => [
                'nullable',
                'string',
                'required_without:active_packages',
                // Custom rule to ensure the JSON array is not empty when provided
                // Custom rule on the delivery_dates field
                function ($attribute, $value, $fail) use ($request) {

                    // 1. Check if the 'Packages' action was taken (packages are selected)
                    $packagesPresent = $request->has('active_packages') && count($request->input('active_packages', [])) > 0;

                    // 2. Check if the 'Delivery Dates' action was taken (JSON decodes to a non-empty array)
                    $dates = json_decode($value, true);
                    $datesPresent = is_array($dates) && !empty($dates);

                    // 3. The ONLY time it Fails is if NEITHER action was taken.
                    if (!$packagesPresent && !$datesPresent) {
                        $fail('You must select at least one package or add at least one delivery date.');
                    }
                },
            ],
        ]);

        if ($request->has('active_packages') && !empty($request->active_packages)) {
            Package::whereIn('id', $request->active_packages)->update(['is_active' => true]);
        }


        if ($request->filled('delivery_dates')) {
            $deliveryDates = json_decode($request->delivery_dates, true);


            // Step 6: Insert new delivery dates (No change needed)
            foreach ($deliveryDates as $entry) {
                // Ensure the keys exist before accessing them, though validation should cover this
                if ($entry['session'] === 'Both') {
                    DeliveryDate::create([
                        'date' => $entry['date'],
                        'session' => 'Morning',
                        'is_active' => true,
                    ]);
                    DeliveryDate::create([
                        'date' => $entry['date'],
                        'session' => 'Afternoon',
                        'is_active' => true,
                    ]);
                } else {
                    DeliveryDate::create([
                        'date' => $entry['date'],
                        'session' => $entry['session'],
                        'is_active' => true,
                    ]);
                }
            }
        }


        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'monthly_supply',
            'message' => 'Updated monthly supply settings.',
        ]);


        return redirect()->back()->with('success', 'Packages and delivery dates updated.');
    }

    public function resetForm()
    {
        // Step 1: Deactivate all packages
        Package::query()->update(['is_active' => false]);

        // Step 2: Deactivate all delivery dates
        DeliveryDate::query()->update(['is_active' => false]);

        // Step 3: Log the destructive action
        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'configuration_reset', // Use a dedicated type
            'message' => 'The monthly supply configuration was fully reset (All packages and dates deactivated).',
        ]);

        // Step 4: Redirect with a success message
        return redirect()->back()->with('success', 'Configuration has been successfully reset.');
    }


    // Route: supply.update
    public function update(Request $request, $id)
    {
        if ($id === 'bulk') {
            $statuses = $request->input('distribution_statuses');

            if (!$statuses || !is_array($statuses)) {
                return redirect()->back()->with('error', 'No status changes were submitted.');
            }

            $updatedCount = 0;

            foreach ($statuses as $supplyRequestId => $status) {
                $supplyRequest = \App\Models\SupplyRequest::find($supplyRequestId);

                if ($supplyRequest && in_array($status, ['approved', 'pending', 'delivered', 'rejected'])) {
                    $supplyRequest->distribution_status = $status;
                    $supplyRequest->save();

                    // Send notification only to the specific beneficiary
                    if ($supplyRequest->beneficiary) {
                        Notification::create([
                            'user_id' => $supplyRequest->beneficiary->user_id,
                            'title' => 'Supply Request Update',
                            'message' => "Your monthly supply application status has been updated to " . ucfirst($status) . ".",
                            'type' => 'supply_request',
                            'is_read' => 0,
                        ]);
                    }

                    $updatedCount++;
                }
            }

            // Recalculate totals using the first supply request in the batch
            $firstRequestId = array_key_first($statuses);
            $firstRequest = \App\Models\SupplyRequest::find($firstRequestId);
            if ($firstRequest) {
                $year  = $firstRequest->created_at->year;
                $month = $firstRequest->created_at->month;
                $purchaseRequirements = ReportService::getPurchaseRequirement($year, $month);
                $totalPrice = $purchaseRequirements->sum('subtotal');
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'monthly_supply',
                'message' => "Successfully updated {$updatedCount} supply request statuses.",
            ]);

            return redirect()->back()->with([
                'success' => "Successfully updated {$updatedCount} supply request statuses.",
            ]);
        }

        return redirect()->back()->with('error', 'Invalid request type.');
    }



    public function delete(Request $request)
    {
        // --- Validation (Ensures we get a valid ID for one of the types) ---
        $request->validate([
            'package_id_to_delete' => 'nullable|exists:packages,id',
            'date_id_to_delete' => 'nullable|exists:delivery_dates,id',
        ]);

        $deleted = false;
        $message = '';

        // --- 1. Delete Delivery Date ---
        if ($request->filled('date_id_to_delete')) {
            $dateId = $request->input('date_id_to_delete');

            // Note: Using findOrFail() will throw an exception if the ID is invalid, 
            // which is often desired behavior, but since we validated 'exists', we can use find().
            $date = DeliveryDate::find($dateId);

            if ($date) {
                // Instead of deleting the record, we should simply deactivate it, 
                // matching the rest of your supply form architecture (which uses is_active).
                // If you truly want to DELETE the record, use $date->delete();
                $date->update(['is_active' => false]);
                $deleted = true;
                $message = 'Delivery date successfully deactivated.';
            }
        }

        // --- 2. Delete Package ---
        if ($request->filled('package_id_to_delete')) {
            $packageId = $request->input('package_id_to_delete');
            $package = Package::find($packageId);

            if ($package) {
                // Again, deactivate the package instead of deleting the whole record.
                $package->update(['is_active' => false]);
                $deleted = true;
                $message = 'Package successfully deactivated.';
            }
        }

        if ($deleted) {
            // Log activity here if needed
            // ActivityLog::create(...); 
            return redirect()->back()->with('success', $message);
        }

        // If no action was taken (e.g., both hidden fields were null)
        return redirect()->back()->with('error', 'No package or delivery date was selected for deletion.');
    }
}
