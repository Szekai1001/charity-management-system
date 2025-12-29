<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Application;
use App\Models\DeliveryDate;
use App\Models\FormControl;
use App\Models\Item;
use App\Models\Package;
use App\Models\Package_Item;
use App\Models\PackageItem;
use App\Models\SupplyRequest;
use App\Models\SupplyRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Admin add or edit the packages and items 
class SupplyRequestController extends Controller
{

    // Route: supplyRequest.create
    public function create()
    {
        $beneficiary = Auth::user()->beneficiary;
        $formControl = FormControl::where('form_type', 'monthly_supply') //Check form type = student
            ->whereDate('open_date', '<=', now())
            ->whereDate('close_date', '>=', now())
            ->first(); //retrieve the first matching record  

        $alreadyApplied = false;

        if ($formControl) {
            $alreadyApplied = SupplyRequest::where('beneficiary_id', $beneficiary->id)
                ->where('control_id', $formControl->id)
                ->exists();
        }

        $packages = Package::where('is_active', true)->get();
        $dates = DeliveryDate::where('is_active', true)->orderBy('date', 'asc')->get(); // You can filter by future dates if needed

        return view('user.beneficiary.supplyApplication', compact('packages', 'dates', 'formControl', 'alreadyApplied'));
    }


    // Route: supplyRequest.store
    public function store(Request $request)
    {
        $validated = $request->validate(
            [
                'package_id' => 'required|exists:packages,id',
                'distribution_method' => 'required|in:Delivery,Pickup',
                'date_id' => 'required_if:distribution_method,Delivery|exists:delivery_dates,id',
            ],
            [],
            [
                'date_id' => 'Delivery Date',
            ]
        );

        // Auto-assign the logged-in beneficiary
        $beneficiary = Auth::user()->beneficiary;

        // Save the request
        $supplyRequest = SupplyRequest::create([
            'beneficiary_id' => $beneficiary->id,
            'package_id' => $request->package_id,
            'control_id' => $request->control_id,
            'date_id' => $request->distribution_method === 'Delivery' ? $request->date_id : null,
            'distribution_method' => $request->distribution_method,
            'distribution_status' => 'pending'
        ]);

        $packageItems = PackageItem::where('package_id', $request->package_id)->get();


        foreach ($packageItems as $packageItem) {
            SupplyRequestItem::create([
                'supply_request_id' => $supplyRequest->id,
                'item_id' => $packageItem->item_id,
                'quantity' => $packageItem->quantity
            ]);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'monthly_supply',
            'message' => 'You have successfully submitted your monthly supply application for ' . now()->format('F Y') . '.',
        ]);


        return redirect()->back()->with('success', 'Your request has been submitted.');
    }
}
