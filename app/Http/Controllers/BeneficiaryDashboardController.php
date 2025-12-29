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

        // --- 1. Existing Logic: Form Control Timer ---
        $formControl = FormControl::where('form_type', 'monthly_supply')
            ->whereDate('open_date', '<=', now())
            ->whereDate('close_date', '>=', now())
            ->first();

        $remainDays = null;
        $remainInTime = null;
        $progressPercentage = null;
        $now = Carbon::now();

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

        // --- 2. Existing Logic: Fetch Latest Request ---
        $supplyRequest = SupplyRequest::where('beneficiary_id', $beneficiary->id)
            ->with(['package.items'])
            ->latest()
            ->first();

        // --- 3. NEW LOGIC: Delivery/Pickup Reminder ---
        $deliveryReminder = null;

        // Find an active request that is approved but not yet completed
        // We assume 'delivery_date' is a relationship. We check if it exists.
        $upcomingRequest = SupplyRequest::where('beneficiary_id', $beneficiary->id)
            ->where('distribution_status', 'approved') // Only check approved requests
            ->whereHas('delivery_date') // Ensure there is a date attached
            ->with('delivery_date')
            ->latest()
            ->first();

        if ($upcomingRequest && $upcomingRequest->delivery_date) {
            // Assuming the relationship 'delivery_date' has a column named 'date' or 'schedule_date'
            // Adjust 'date' below to match your actual database column name in the delivery_dates table
            $dateValue = $upcomingRequest->delivery_date->date ?? $upcomingRequest->delivery_date->created_at;

            $scheduleDate = Carbon::parse($dateValue);

            // Check if the date is in the future or today
            if ($scheduleDate->isToday() || $scheduleDate->isFuture()) {
                $diffInDays = $now->diffInDays($scheduleDate, false);

                // Define "Near" as within 3 days
                if ($diffInDays <= 5) {
                    $type = ucfirst($upcomingRequest->distribution_method ?? 'Delivery/Pickup'); // delivery or pickup

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
            'deliveryReminder' // <--- Pass the new variable here
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
