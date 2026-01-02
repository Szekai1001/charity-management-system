<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FormControl;
use App\Models\Notification;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiaryDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $beneficiary = $user->beneficiary;

        if (!$beneficiary) {
            abort(403, 'Unauthorized access');
        }

        $now = Carbon::now(); // Capture current time once to ensure consistency

        // --- 1. Existing Logic: Form Control Timer ---
        $formControl = FormControl::where('form_type', 'monthly_supply')
            ->whereDate('open_date', '<=', $now)
            ->whereDate('close_date', '>=', $now)
            ->first();

        $remainDays = null;
        $remainInTime = null;
        $progressPercentage = null;

        if ($formControl) {
            $closeDate = Carbon::parse($formControl->close_date);
            $openDate = Carbon::parse($formControl->open_date);
            $diffHours = $now->diffInHours($closeDate, false);

            if ($diffHours < 24 && $diffHours >= 0) {
                $remainInTime = (int) $diffHours . ' hour' . ($diffHours !== 1 ? 's' : '') .  ' left';
            } else {
                $remainDays = $now->diffInDays($closeDate, false);
                $remainInTime = (int) $remainDays . ' day' . ($remainDays !== 1 ? 's' : '') . ' left';
            }

            $totalDays = $openDate->diffInDays($closeDate);
            $passedDays = $openDate->diffInDays($now);
            $progressPercentage = $totalDays > 0 ? round(($passedDays / $totalDays) * 100) : 0;
        }

        // --- 2. UPDATED LOGIC: Fetch Current Month's Request ---
        // We add whereMonth and whereYear to ensure we only get a request for THIS month.
        $supplyRequest = SupplyRequest::where('beneficiary_id', $beneficiary->id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->with(['package.items'])
            ->first();

        // Note: If your application cycle crosses months (e.g. 25th to 5th), 
        // you should check 'created_at' against the $formControl dates instead.
        // Assuming standard monthly calendar:

        // --- 3. Existing Logic: Delivery/Pickup Reminder ---
        $deliveryReminder = null;

        // We keep this query loose (latest approved) just in case a delivery 
        // from late last month is scheduled for the 1st of this month.
        $upcomingRequest = SupplyRequest::where('beneficiary_id', $beneficiary->id)
            ->where('distribution_status', 'approved')
            ->whereHas('delivery_date')
            ->with('delivery_date')
            ->latest()
            ->first();

        if ($upcomingRequest && $upcomingRequest->delivery_date) {
            $dateValue = $upcomingRequest->delivery_date->date ?? $upcomingRequest->delivery_date->created_at;
            $scheduleDate = Carbon::parse($dateValue);

            if ($scheduleDate->isToday() || $scheduleDate->isFuture()) {
                $diffInDays = $now->diffInDays($scheduleDate, false);

                if ($diffInDays <= 5) {
                    $type = ucfirst($upcomingRequest->distribution_method ?? 'Delivery/Pickup');

                    if ($scheduleDate->isToday()) {
                        $deliveryReminder = "🔔 Reminder: Your $type is scheduled for TODAY!";
                    } elseif ($scheduleDate->isTomorrow()) {
                        $deliveryReminder = "🔔 Reminder: Your $type is scheduled for tomorrow.";
                    } else {
                        $deliveryReminder = "📅 Upcoming: Your $type is in " . (int)$diffInDays . " days (" . $scheduleDate->format('M d') . ").";
                    }
                }
            }
        }

        // --- 4. Existing Logic: Activities ---
        $activities = ActivityLog::where('user_id', Auth::id())
            ->latest()
            ->simplePaginate(5, ['*'], 'log_page');

        return view('user.beneficiary.beneficiaryDashboard', compact(
            'formControl',
            'supplyRequest',
            'remainInTime',
            'progressPercentage',
            'deliveryReminder'
        ));
    }
    public function viewPastApplication(Request $request)
    {
        $beneficiary = Auth::user()->beneficiary;
        $beneficiaryId = $beneficiary->id;
        $current = now();
        $year = $current->year;
        $month = $current->month;

        // 1. All applications for the current year (History List)
        $applications = SupplyRequest::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Count of applications this year (e.g., "5 / 12")
        $applicationsCount = $applications->count();

        // 3. Check if they have already applied THIS month (Boolean)
        $hasCurrentMonthApp = SupplyRequest::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->exists();

        // 4. Total approved/delivered packages this year
        $approvedCount = SupplyRequest::where('beneficiary_id', $beneficiaryId)
            ->whereYear('created_at', $year)
            ->whereIn('distribution_status', ['approved', 'delivered'])
            ->count();

        return view('user.beneficiary.pastApplication', compact(
            'applications',
            'applicationsCount',
            'hasCurrentMonthApp',
            'approvedCount'
        ));
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

        return view('user.beneficiary.notification', compact('activitiesType', 'tab'));
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
            $activity = Notification::where('user_id', Auth::id())->where('is_read', false)->update([
                'is_read' => true,
            ]);
        }

        return redirect()->route('beneficiary.notification', ['tab' => $currentTab]);
    }
}
