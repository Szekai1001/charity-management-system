<?php

namespace App\Http\Controllers;

use App\Helpers\DssHelper;
use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Student;
use App\Models\FamilyMember;
use App\Models\Guardian;
use App\Models\Document;
use App\Models\FormControl;
use App\Models\Notification;
use App\Models\OtherExpense;
use App\Models\OtherIncome;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function create()
    {
        $userID = Auth::id();

        $formControl = FormControl::where('form_type', 'beneficiary')
            ->whereDate('open_date', '<=', now())
            ->whereDate('close_date', '>=', now())
            ->first(); //retrieve the first matching record  

        $alreadyApplied = false;

        if ($formControl) {
            $alreadyApplied = Application::where('user_id', $userID)
                ->where('control_id', $formControl->id)
                ->exists();
        }

        $religions = ['Christianity', 'Islam', 'Hinduism', 'Buddhism', 'Other'];

        $housingTypes = [
            'own_house' => 'Own House',
            'rent_house' => 'Rented House',
            'rent_room' => 'Rented Room (Bilik Sewa)',
            'ppr_gov' => 'PPR / Government Housing',
            'quarters' => 'Employer Quarters / Hostel',
            'squatter' => 'Squatter / Informal Area',
            'homeless' => 'Homeless'
        ];


        $familyRoles = ['Father', 'Mother', 'Grandfather', 'Grandmother', 'Other'];

        $relations = ['Father', 'Mother', 'Grandfather', 'Grandmother', 'Other'];

        $employmentTypes = [
            'full_time'     => 'Full-Time Employee',
            'part_time'     => 'Part-Time Employee',
            'self_employed' => 'Self-Employed / Gig Economy',
            'contract'      => 'Contract / Temp Worker',
            'housewife'     => 'Housewife / Homemaker',
            'retired'       => 'Retiree',

            // 1. For school-going kids (Needs textbooks, uniforms)
            'student'       => 'Student',

            // 2. NEW: For babies/toddlers (Needs diapers, milk)
            'child_infant'  => 'Child / Infant (Below School Age)',

            'unemployed'    => 'Unemployed (Looking for work)',
            'unable_work'   => 'Medically Unfit to Work'
        ];

        $incomeRanges = [
            'below_1000' => 'Below RM 1000',
            '1000_2999' => 'RM 1000–RM 2999',
            '3000_4999' => 'RM 3000–RM 4999',
            '5000_6999' => 'RM 5000–RM 6999',
            '7000_9999' => 'RM 7000–RM 9999',
            '10000_above' => 'RM 10,000 and above'
        ];

        return view('form.beneficiaryForm', compact('formControl', 'alreadyApplied', 'religions', 'housingTypes', 'familyRoles', 'relations', 'employmentTypes', 'incomeRanges'));
    }



    // Storing the input 
    public function store(Request $request)
    {
        // Validate form data
        $validated = $request->validate(
            [
                'bName' => 'required|string|max:255',
                'bIC' => 'required|string|max:20',
                'bGender' => 'required|string',
                'bBirthDate' => 'required|date',
                'bReligion' => 'required|string',
                'otherReligion' => 'nullable|required_if:bReligion,Other|string|max:255',
                'familyRole' => 'required|string',
                'otherFamilyRole' => 'nullable|required_if:familyRole,Other|string|max:255',
                'bPhone' => 'required|string|max:20',
                'occupation' => 'required|string',
                'street' => 'required|string',
                'area' => 'required|string',
                'city' => 'required|string',
                'state' => 'required|string',
                'zip' => 'required|string',

                'memberName' => 'nullable|array',
                'memberName.*' => 'nullable|string|max:255|required_with:mBirthDate.*,mOccupation.*,mRelationship.*',

                'mBirthDate' => 'nullable|array',
                'mBirthDate.*' => 'nullable|date|required_with:memberName.*,mOccupation.*,mRelationship.*',

                'mOccupation' => 'nullable|array',
                'mOccupation.*' => 'nullable|string|max:255|required_with:memberName.*,mBirthDate.*,mRelationship.*',

                'mRelationship' => 'nullable|array',
                'mRelationship.*' => 'nullable|string|required_with:memberName.*,mBirthDate.*,mOccupation.*',

                // Only required if mRelationship.* = "Other"
                'otherRelationship' => 'nullable|array',
                'otherRelationship.*' => 'nullable|string|max:255|required_if:mRelationship.*,"Other"',

                'residential' => 'required|string',
                'amenities' => 'array',
                'reason' => 'nullable|string',

                // income resource
                'income' => 'required|in:yes,no',
                'monthly_income' => 'nullable|required_if:income,yes|numeric|min:0',
                'childAssist' => 'required|in:yes,no',
                'childAssistValue' => 'nullable|required_if:childAssist,yes|numeric|min:0',
                'govAssist' => 'required|in:yes,no',
                'govAssistValue' => 'nullable|required_if:govAssist,yes|numeric|min:0',
                'insurancePay' => 'required|in:yes,no',
                'insurancePayValue' => 'nullable|required_if:insurancePay,yes|numeric|min:0',

                'otherIncomeName' => 'nullable|array',
                'otherIncomeName.*' => 'nullable|string|max:255|required_with:otherIncomeValue.*',

                'otherIncomeValue' => 'nullable|array',
                'otherIncomeValue.*' => 'nullable|numeric|min:0|required_with:otherIncomeName.*',

                // expenses
                'mortgage' => 'required|in:yes,no',
                'mortgageValue' => 'nullable|required_if:mortgage,yes|numeric|min:0',
                'transportLoan' => 'required|in:yes,no',
                'transportLoanValue' => 'nullable|required_if:transportLoan,yes|numeric|min:0',
                'eduExpense' => 'required|in:yes,no',
                'eduExpenseValue' => 'nullable|required_if:eduExpense,yes|numeric|min:0',
                'utilityExpenses' => 'required|in:yes,no',
                'utilityExpensesValue' => 'nullable|required_if:utilityExpenses,yes|numeric|min:0',
                'familyExpenses' => 'required|in:yes,no',
                'familyExpensesValue' => 'nullable|required_if:familyExpenses,yes|numeric|min:0',

                'otherExpensesName' => 'nullable|array',
                'otherExpensesName.*' => 'nullable|string|max:255|required_with:otherExpensesValue.*',

                'otherExpensesValue' => 'nullable|array',
                'otherExpensesValue.*' => 'nullable|numeric|min:0|required_with:otherExpensesName.*',


                'utility' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'iC_copy' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'payslip' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ],

            [
                // You can leave empty if you don’t add custom messages
            ],

            [
                'memberName.*' => 'Family Member Name',
                'mBirthDate.*' => 'Family Member Birth Date',
                'mOccupation.*' => 'Family Member Occupation',
                'mRelationship.*' => 'Family Member Relationship',
                'otherRelationship.*' => 'Other Relationship',

                'otherIncomeName.*' => 'Other Income Name',
                'otherIncomeValue.*' => 'Other Income Amount',
                'otherExpensesName.*' => 'Other Expense Name',
                'otherExpensesValue.*' => 'Other Expense Amount',
            ]
        );

        $userId = Auth::id();

        // Store beneficiary
        $beneficiary = Beneficiary::create([
            'user_id' => $userId,
            'name' => $request->bName,
            'ic' => $request->bIC,
            'gender' => $request->bGender,
            'birth_date' => $request->bBirthDate,
            'religion' => $request->bReligion === 'Other' ? $request->otherReligion : $request->bReligion,
            'family_role' => $request->familyRole === 'Other' ? $request->otherFamilyRole : $request->familyRole,
            'phone_number' => $request->bPhone,
            'street' => $request->street,
            'area' => $request->area,
            'city' => 'Batu Pahat',
            'state' => 'Johor',
            'zip' => $request->zip,
            'occupation' => $request->occupation,
            'residential_status' => $request->residential,
            'application_reason' => $request->reason,
            'basic_amenities_access' => $request->input('amenities', []),
            'family_income' => $request->income === 'yes' ? $request->monthly_income : 'no',
            'assist_from_child' => $request->childAssist === 'yes' ? $request->childAssistValue : 'no',
            'government_assist' => $request->govAssist === 'yes' ? $request->govAssistValue : 'no',
            'insurance_pay' => $request->insurancePay === 'yes' ? $request->insurancePayValue : 'no',
            'mortgage_expense' => $request->mortgage === 'yes' ? $request->mortgageValue : 'no',
            'transport_loan' => $request->transportLoan === 'yes' ? $request->transportLoanValue : 'no',
            'utility_expense' => $request->utilityExpenses === 'yes' ? $request->utilityExpensesValue : 'no',
            'education_expense' => $request->eduExpense === 'yes' ? $request->eduExpenseValue : 'no',
            'family_expense' => $request->familyExpenses === 'yes' ? $request->familyExpensesValue : 'no',
        ]);

        $application = Application::create([
            'user_id' => $userId,
            'control_id' => $request->control_id,
            'application_type' => 'Beneficiary',
            'dss_score' => 0,
            'status' => 'processing',
        ]);

        // Store family members
        if ($request->memberName) {
            foreach ($request->memberName as $index => $name) {

                if (
                    empty($name) &&
                    empty($request->mBirthDate[$index]) &&
                    empty($request->mOccupation[$index]) &&
                    empty($request->mRelationship[$index])
                ) {
                    continue;
                }

                $relationship = $request->mRelationship[$index] ?? null;

                if ($relationship === 'Other') {
                    // Use the matching "other" input if it exists
                    $relationship = $request->otherRelationship[$index] ?? 'Other';
                }

                $beneficiary->familyMember()->create([
                    'name' => $name,
                    'birth_date' => $request->mBirthDate[$index] ?? null,
                    'occupation' => $request->mOccupation[$index] ?? null,
                    'relationship' => $relationship,
                ]);
            }
        }


        if ($request->has('otherIncomeName')) {
            foreach ($request->otherIncomeName as $index => $name) {
                $value = $request->otherIncomeValue[$index] ?? null;
                if ($name && $value !== null && $value !== '') {
                    OtherIncome::create([
                        'beneficiary_id' => $beneficiary->id,
                        'other_income_resource' => $name,
                        'other_income_source_value' => $value,
                    ]);
                }
            }
        }

        if ($request->has('otherExpensesName')) {
            foreach ($request->otherExpensesName as $index => $name) {
                $value = $request->otherExpensesValue[$index] ?? null;

                if ($name && $value !== null && $value !== '') {
                    OtherExpense::create([
                        'beneficiary_id' => $beneficiary->id,
                        'other_expense' => $name,
                        'other_expense_value' => $value,
                    ]);
                }
            }
        }


        $documentTypes = [
            'utility' => 'Utility Bills',
            'iC_copy' => 'IC Copy',
            'payslip' => 'PaySlip'
        ];

        foreach ($documentTypes as $field => $type) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $fileName = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('uploads/documents', $fileName, 'public');

                Document::create([
                    'application_id' => $application->id,
                    'type' => $type,
                    'file_name' => $fileName,
                    'file_type' => $file->getClientMimeType(),
                    'file_path' => $filePath,
                ]);
            }
        }

        $beneficiary->load(['familyMember', 'otherIncome', 'otherExpense']);

        // Call DSS helper using the beneficiary instance directly
        $scores = DssHelper::calculateScores($beneficiary);

        $application->update(['dss_score' => $scores]);

        return redirect()->route('application.success');
    }
}
