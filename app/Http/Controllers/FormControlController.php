<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\DeliveryDate;
use App\Models\FormControl;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormControlController extends Controller
{

    public function index()
    {
        $formControls = FormControl::where('open_date', '<=', now())
            ->where('close_date', '>=', now())
            ->get();

        return view('admin.formControl', compact('formControls'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'formType' => 'required|string',
            'openDate' => 'required|date',
            'closeDate' => 'required|date',
        ]);

        $hadSimilarActiveForm = FormControl::where('form_type', $request->formType)
            ->where('open_date', '<=', now())
            ->where('close_date', '>=', now())
            ->exists();

        if ($hadSimilarActiveForm) {
            return back()->with('error', 'A form of this type is already active.');
        }



        // Check if closeDate is earlier than openDate
        if ($request->closeDate < $request->openDate) {
            return back()->with('error', 'Close date cannot be earlier than the open date.')->withInput();
        }

        if ($request->openDate < today()->toDateString()) {
            return back()->with('error', 'Open date cannot be in the past.')->withInput();
        }

        if (strtolower($request->formType) === 'monthly_supply') {
            // Must have at least one active package
            $hasPackages = Package::where('is_active', '1')->exists();

            if (!$hasPackages) {
                return back()->with('error', 'You must set up at least one active package before creating a Supply Request form.')->withInput();
            }

            // Must have at least one available delivery date (future)
            $hasDeliveryDates = DeliveryDate::where('date', '>=', today())->exists();

            if (!$hasDeliveryDates) {
                return back()->with('error', 'You must set up at least one available delivery date before creating a Supply Request form.')->withInput();
            }
        }

        FormControl::create([
            'form_type' => $request->formType,
            'open_date' => $request->openDate,
            'close_date' => $request->closeDate,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'form_control',
            'message' => "Form Control {$request->formType} created successfully",
        ]);


        return back()->with('success', 'Form control created successfully!');
    }
}
