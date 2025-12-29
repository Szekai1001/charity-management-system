@extends('layout.app')
@include('components.alerts')
@section('title', 'beneficiary Form')
@section('content')

@php
// Page 1: Family Members Count
$countFM = old('memberName') ? count(old('memberName')) : 1;

// Page 2: Other Income Count
$countIncome = old('otherIncomeName') ? count(old('otherIncomeName')) : 1;

// Page 2: Other Expenses Count
$countExpense = old('otherExpensesName') ? count(old('otherExpensesName')) : 1;
@endphp

@if($alreadyApplied)

<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-md-6 col-lg-5 my-5">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5 text-center ">

                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-envelope-check-fill text-primary" viewBox="0 0 16 16">
                            <path d="M.05 3.555A2 2 0 0 1 2 2h12a2 2 0 0 1 1.95 1.555L8 8.414.05 3.555ZM0 4.697v7.104l5.803-3.558L0 4.697ZM6.761 8.83l-6.57 4.026A2 2 0 0 0 2 14h6.256A4.493 4.493 0 0 1 8 12.5a4.49 4.49 0 0 1 1.606-3.446l-.367-.225L8 9.586l-1.239-.757ZM16 4.697v4.974A4.491 4.491 0 0 0 12.5 8a4.49 4.49 0 0 0-1.965.45l-.338-.207L16 4.697Z" />
                            <path d="M16 12.5a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Zm-1.993-1.679a.5.5 0 0 0-.686.172l-1.17 1.95-.547-.547a.5.5 0 0 0-.708.708l.9 9a.5.5 0 0 0 .752-.044l1.504-2.5a.5.5 0 0 0-.15-.639Z" />
                        </svg>
                    </div>
                </div>

                <h3 class="fw-bold text-dark mb-3">Application Already Received</h3>

                <p class="text-muted mb-4">
                    Our records show you have already submitted this form. You do not need to apply again.
                </p>

                <div class="bg-light rounded p-3 mb-4">
                    <p class="mb-0 small text-secondary fw-semibold">
                        <i class="bi bi-info-circle me-1"></i>
                        Please pay attention to your email inbox for confirmation and further updates.
                    </p>
                </div>

                <div class="d-grid">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">Back to Home</a>
                </div>

            </div>
        </div>
    </div>
</div>

@else

@if($formControl)
<div class="beneficiaryFormContainer d-flex flex-column justify-content-center align-items-center mx-auto" style="max-width: 1000px; min-height: 100vh;">

    <div class="my-5 text-center">
        <h2 class="fw-bold">Beneficiary Application Form</h2>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-body p-4">

            <h5 class="fw-bold text-dark mb-4 text-center">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>Before You Start
            </h5>

            <div class="row g-4">

                <div class="col-md-6 d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <span class="badge rounded-pill bg-warning text-dark p-2">
                            <i class="bi bi-geo-alt-fill fs-5"></i>
                        </span>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold text-dark mb-1">Eligibility Check</h6>
                        <p class="text-muted small mb-0">
                            Strictly for residents residing in <strong class="text-dark">Batu Pahat</strong>.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 d-flex align-items-start border-start-md border-secondary-subtle">
                    <div class="flex-shrink-0">
                        <span class="badge rounded-pill bg-danger p-2">
                            <i class="bi bi-shield-exclamation fs-5"></i>
                        </span>
                    </div>
                    <div class="ms-3">
                        <h6 class="fw-bold text-danger mb-1">Declaration of Truth</h6>
                        <p class="text-muted small mb-0">
                            Discovery of <strong>false information</strong> or fake documents will result in <span class="text-dark fw-bold">immediate disqualification</span> and blacklisting.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm w-100" role="alert">
        <div class="d-flex align-items-start">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-3 mt-1" viewBox="0 0 16 16">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </svg>

            <div class="w-100">
                <h5 class="alert-heading fw-bold mb-1">Submission Failed</h5>
                <p class="mb-2">
                    Please check the fields highlighted in <strong class="text-danger">red</strong> below.
                </p>

                <div class="p-2 rounded bg-white bg-opacity-50 border border-danger border-opacity-25">
                    <small class="fw-bold text-danger text-uppercase d-block mb-1" style="font-size: 0.75rem;">System Error Log:</small>
                    <ul class="mb-0 text-danger small ps-3">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Start Form -->
    <form data-multi-step action="{{route('beneficiary.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="control_id" value="{{ $formControl->id }}">
        <!-- Personal Details (First Page)-->
        <div id="page1">

            <div class="bg-white p-4 rounded shadow my-3" style="width: fit-content; max-width: 100%;">
                <h3 class="mt-2 fw-bold">Personal Details</h3>
                <div class="row g-3 mb-5 d-flex justify-content-center">
                    <div class="col-md-6">
                        <label for="bName" class="form-label">Beneficiary Name</label>
                        <input type="text" class="form-control @error('bName') is-invalid @enderror" id="bName" name="bName" placeholder="Enter your full name" value="{{ old('bName') }}" required>
                        @error('bName')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="bIC" class="form-label">IC Number</label>
                        <input type="text" class="form-control @error('bIC') is-invalid @enderror" id="bIC" name="bIC" placeholder="Enter your IC number (e.g., 010203-10-1234)" value="{{ old('bIC') }}" required>
                        @error('bIC')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Gender</label><br>
                        <input class="form-check-input @error('bGender') is-invalid @enderror" type="radio" id="male" name="bGender" value="male" {{ old('bGender') == 'male' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="male">Male</label>
                        <input class="form-check-input @error('bGender') is-invalid @enderror" type="radio" id="female" name="bGender" value="female" {{ old('bGender') == 'female' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="female">Female</label>
                        @error('bGender')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="bBirthDate" class="form-label">Birth Date</label>
                        <input type="date" class="form-control @error('bBirthDate') is-invalid @enderror" id="bBirthDate" name="bBirthDate" value="{{ old('bBirthDate') }}" required>
                        @error('bBirthDate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="bReligion" class="form-label">Religion:</label>
                        <select class="form-select other @error('bReligion') is-invalid @enderror" id="bReligion" name="bReligion" required>
                            <option value="" selected disabled>-- Select Religion --</option>
                            @foreach($religions as $religion)
                            <option value="{{ $religion }}" {{ old('bReligion') == $religion ? 'selected' : '' }}>{{ $religion }}</option>
                            @endforeach
                        </select>
                        @error('bReligion')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        {{-- Checks if old input was filled or specific value was selected to keep it visible --}}
                        <div class="other-container {{ old('bReligion') == 'Other' || old('otherReligion') ? '' : 'd-none' }} mt-2">
                            <label for="otherReligion"> If others,Please specify</label>
                            <input type="text" class="form-control @error('otherReligion') is-invalid @enderror" id="otherReligion" name="otherReligion" value="{{ old('otherReligion') }}">
                            @error('otherReligion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="familyRole" class="form-label">Family Role</label>
                        <select name="familyRole" class="form-select other @error('familyRole') is-invalid @enderror" id="familyRole" required>
                            <option value="" disabled selected>--Select Family Role--</option>
                            @foreach($familyRoles as $familyRole)
                            <option value="{{ $familyRole }}" {{ old('familyRole') == $familyRole ? 'selected' : '' }}>{{ $familyRole }}</option>
                            @endforeach
                        </select>
                        @error('familyRole')<div class="invalid-feedback">{{ $message }}</div>@enderror

                        <div class="other-container {{ old('familyRole') == 'Other' || old('otherFamilyRole') ? '' : 'd-none' }} mt-2">
                            <label for="otherFamilyRole"> If others,Please specify</label>
                            <input type="text" class="form-control @error('otherFamilyRole') is-invalid @enderror" id="otherFamilyRole" name="otherFamilyRole" value="{{ old('otherFamilyRole') }}">
                            @error('otherFamilyRole')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="bPhone">Phone Number</label>
                        <input type="tel" class="form-control @error('bPhone') is-invalid @enderror" id="bPhone" name="bPhone" placeholder="Enter your phone number (e.g., 012-3466789)" value="{{ old('bPhone') }}" required>
                        @error('bPhone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="occupation" class="form-label">Occupation</label>
                        <select name="occupation" id="occupation" class="form-select @error('occupation') is-invalid @enderror" required>
                            <option value="" disabled selected>-- Select Occupation --</option>
                            @foreach($employmentTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('occupation') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="street" class="form-label">Street Address:</label>
                        <input type="text" class="form-control @error('street') is-invalid @enderror" id="street" name="street" placeholder="Street address, house number, etc." value="{{ old('street') }}" required>
                        @error('street')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="area" class="form-label">Area:</label>
                        <input type="text" class="form-control @error('area') is-invalid @enderror" id="area" name="area" placeholder="e.g., Taman Bukit Perdana" value="{{ old('area') }}" required>
                        @error('area')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="city" class="form-label">City:</label>
                        <input type="text" class="form-control" id="city" name="city" value="Batu Pahat" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="state" class="form-label">State:</label>
                        <input type="text" class="form-control" id="state" name="state" value="Johor" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="zip" class="form-label">Postal Code:</label>
                        <input type="text" class="form-control @error('zip') is-invalid @enderror" id="zip" name="zip" pattern="[0-9]{5}" placeholder="e.g.,83000" value="{{ old('zip') }}" required>
                        @error('zip')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <!-- Family Member Details -->
            <div class="bg-white p-4 rounded shadow my-3" style="max-width: 100%;">
                <div class="extra-option">
                    <h3 class="mt-2 fw-bold">Other Family Members Details</h3>
                    <i>"You may add details of other family members (siblings, grandparents, etc.) if applicable"</i>

                    @for($i=0; $i < $countFM; $i++)
                        <div class="card p-3 my-3 extra-options family-member position-relative bg-light border" style="max-width: 1000px;">
                        <span class="remove-btn position-absolute top-0 end-0 mt-2 me-2" role="button" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-x-lg ms-auto text-danger" viewBox="0 0 16 16">
                                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                            </svg>
                        </span>
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="memberName_{{$i}}" class="form-label">Family Member Name</label>
                                <input type="text" class="form-control @error('memberName.'.$i) is-invalid @enderror" id="memberName_{{$i}}" name="memberName[{{ $i }}]" placeholder="Enter your family member's full name" value="{{ old('memberName.'.$i) }}">
                                @error('memberName.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mBirthDate_{{$i}}" class="form-label">Birth Date</label>
                                <input type="date" class="form-control @error('mBirthDate.'.$i) is-invalid @enderror" id="mBirthDate_{{$i}}" name="mBirthDate[{{ $i }}]" value="{{ old('mBirthDate.'.$i) }}">
                                @error('mBirthDate.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mOccupation_{{$i}}" class="form-label">Occupation</label>
                                <select name="mOccupation[{{ $i }}]" id="mOccupation_{{$i}}" class="form-select @error('mOccupation.'.$i) is-invalid @enderror">
                                    <option value="" hidden {{ old('mOccupation.'.$i) ? '' : 'selected' }}>-- Select Occupation --</option>
                                    @foreach($employmentTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('mOccupation.'.$i) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('mOccupation.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="mRelationship_{{$i}}" class="form-label">Relationship</label>
                                <select name="mRelationship[{{ $i }}]" id="mRelationship_{{$i}}" class="form-select other rel-select @error('mRelationship.'.$i) is-invalid @enderror">
                                    <option value="" hidden {{ old('mRelationship.'.$i) ? '' : 'selected' }}>-- Select relationship --</option>
                                    @foreach($relations as $relation)
                                    <option value="{{ $relation }}" {{ old('mRelationship.'.$i) == $relation ? 'selected' : '' }}>{{ $relation }}</option>
                                    @endforeach
                                </select>
                                @error('mRelationship.'.$i)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror

                                <div class="other-container mt-2 {{ old('mRelationship.'.$i) == 'Other' ? '' : 'd-none' }}">
                                    <label for="otherRelationship_{{$i}}">If others, please specify</label>
                                    <input type="text" class="form-control @error('otherRelationship.'.$i) is-invalid @enderror" id="otherRelationship_{{$i}}" name="otherRelationship[{{ $i }}]" value="{{ old('otherRelationship.'.$i) }}">
                                    @error('otherRelationship.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                </div>
                @endfor
                <button type="button" class="insert-before btn btn-secondary mt-3">Add Family Member</button>
            </div>
        </div>
        <!-- Navigation Button (Next Page) -->
        <div class="mb-5 text-end">
            <button type="button" class=" next-page btn btn-primary">Next</button>
        </div>
</div>

<!--Living Conditions -->
<div id="page2">
    <div class="bg-white p-4 rounded shadow my-3" style="width: fit-content; max-width: 100%;">
        <h3 class="mt-2 fw-bold">Living Conditions</h3>
        <div class="row g-3 mb-5 d-flex justify-content-center">

            <div class="col-md-12">
                <label for="residential" class="form-label">Residential Status</label>
                <select id="residential" class="form-select @error('residential') is-invalid @enderror" name="residential" required>
                    <option value="" selected disabled>-- Select Status --</option>
                    @foreach($housingTypes as $value => $label)
                    <option value="{{ $value }}" {{ old('residential') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('residential')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            <div class="col-md-12">
                <label class="form-label d-block mb-2">Basic Amenities Access</label>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('amenities') is-invalid @enderror" type="checkbox" id="electricity" name="amenities[]" value="electricity" {{ is_array(old('amenities')) && in_array('electricity', old('amenities')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="electricity">Electricity</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('amenities') is-invalid @enderror" type="checkbox" id="internet" name="amenities[]" value="internet" {{ is_array(old('amenities')) && in_array('internet', old('amenities')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="internet">Internet/Wi-Fi</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('amenities') is-invalid @enderror" type="checkbox" id="cooler" name="amenities[]" value="cooler" {{ is_array(old('amenities')) && in_array('cooler', old('amenities')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="cooler">Fan/Cooler</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('amenities') is-invalid @enderror" type="checkbox" id="water" name="amenities[]" value="water" {{ is_array(old('amenities')) && in_array('water', old('amenities')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="water">Clean Water</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input @error('amenities') is-invalid @enderror" type="checkbox" id="device" name="amenities[]" value="device" {{ is_array(old('amenities')) && in_array('device', old('amenities')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="device">Smartphone/Computer</label>
                </div>

                {{-- Error message for the group --}}
                @error('amenities')
                <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <h5 class="fw-bold mt-5">Monthly household income sources (currently)</h5>

            <!-- Family Income -->
            <div class="col-md-6">
                <label class="form-label">Family Income:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('income') is-invalid @enderror" type="radio" id="noIncome" name="income" value="no" {{ old('income') == 'no' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="noIncome">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('income') is-invalid @enderror" type="radio" id="hasIncome" name="income" value="yes" {{ old('income') == 'yes' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="hasIncome">Yes</label>
                </div>
                @error('income')<div class="text-danger small">{{ $message }}</div>@enderror

                <div class="other-container d-none mt-2">
                    <label class="form-label" for="monthlyIncome">Please enter the value</label>
                    <div class="input-group">
                        <span class="input-group-text">RM</span>
                        <input type="number" step="0.01" class="form-control @error('monthly_income') is-invalid @enderror" id="monthlyIncome" name="monthly_income" placeholder="0.00" value="{{ old('monthly_income') }}">
                        @error('monthly_income')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <!-- Assistance from child -->
            <div class="col-md-6">
                <label class="form-label">Assistance from children and relatives:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('childAssist') is-invalid @enderror" type="radio" id="noChildAssist" name="childAssist" value="no" {{ old('childAssist') == 'no' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="noChildAssist">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('childAssist') is-invalid @enderror" type="radio" id="hasChildAssist" name="childAssist" value="yes" {{ old('childAssist') == 'yes' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="hasChildAssist">Yes</label>
                </div>
                @error('childAssist')<div class="text-danger small">{{ $message }}</div>@enderror

                <div class="other-container d-none mt-2">
                    <label class="form-label" for="childAssistValue">Please enter the value</label>
                    <div class="input-group">
                        <span class="input-group-text">RM</span>
                        <input type="number" step="0.01" class="form-control @error('childAssistValue') is-invalid @enderror" id="childAssistValue" name="childAssistValue" placeholder="0.00" value="{{ old('childAssistValue') }}">
                        @error('childAssistValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Government assistance:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('govAssist') is-invalid @enderror" type="radio" id="noGovAssist" name="govAssist" value="no" {{ old('govAssist') == 'no' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="noGovAssist">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('govAssist') is-invalid @enderror" type="radio" id="hasGovAssist" name="govAssist" value="yes" {{ old('govAssist') == 'yes' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="hasGovAssist">Yes</label>
                </div>
                @error('govAssist')<div class="text-danger small">{{ $message }}</div>@enderror

                <div class="other-container d-none mt-2">
                    <label class="form-label" for="govAssistValue">Please enter the value</label>
                    <div class="input-group">
                        <span class="input-group-text">RM</span>
                        <input type="number" step="0.01" class="form-control @error('govAssistValue') is-invalid @enderror" id="govAssistValue" name="govAssistValue" placeholder="0.00" value="{{ old('govAssistValue') }}">
                        @error('govAssistValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Insurance Payout:</label><br>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('insurancePay') is-invalid @enderror" type="radio" id="noInsurancePay" name="insurancePay" value="no" {{ old('insurancePay') == 'no' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="noInsurancePay">No</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input other @error('insurancePay') is-invalid @enderror" type="radio" id="hasInsurancePay" name="insurancePay" value="yes" {{ old('insurancePay') == 'yes' ? 'checked' : '' }} required>
                    <label class="form-check-label" for="hasInsurancePay">Yes</label>
                </div>
                @error('insurancePay')<div class="text-danger small">{{ $message }}</div>@enderror

                <div class="other-container d-none mt-2">
                    <label class="form-label" for="insurancePayValue">Please enter the value</label>
                    <div class="input-group">
                        <span class="input-group-text">RM</span>
                        <input type="number" step="0.01" class="form-control @error('insurancePayValue') is-invalid @enderror" id="insurancePayValue" name="insurancePayValue" placeholder="0.00" value="{{ old('insurancePayValue') }}">
                        @error('insurancePayValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="col-md-12 extra-option">
                <label class="form-label fw-bold">Other Income Sources</label>

                @for($i=0; $i < $countIncome; $i++)
                    <div class="card p-3 my-3 extra-options filledAll position-relative bg-light border">
                    <span class="remove-btn position-absolute top-0 end-0 mt-2 me-2" role="button" title="Remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg text-danger" viewBox="0 0 16 16">
                            <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                        </svg>
                    </span>

                    <label class="form-label" for="otherIncomeName_{{$i}}">Income Source Name</label>
                    <input class="form-control @error('otherIncomeName.'.$i) is-invalid @enderror" type="text" id="otherIncomeName_{{$i}}" name="otherIncomeName[{{ $i }}]" value="{{ old('otherIncomeName.' . $i) }}">
                    @error('otherIncomeName.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <label class="form-label mt-2" for="otherIncomeValue_{{$i}}">Value</label>
                    <div class="input-group">
                        <span class="input-group-text">RM</span>
                        <input type="number" step="0.01" class="form-control @error('otherIncomeValue.'.$i) is-invalid @enderror" id="otherIncomeValue_{{$i}}" name="otherIncomeValue[{{ $i }}]" placeholder="0.00" value="{{ old('otherIncomeValue.' . $i) }}">
                        @error('otherIncomeValue.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
            </div>
            @endfor
            <button type="button" class="insert-before btn btn-secondary mt-3">Add others income source</button>
        </div>

        <div class="col-12 mt-5">
            <h5 class="fw-bold">Monthly household expenses (currently)</h5>
        </div>

        <div class="col-md-6">
            <label class="form-label">Mortgage or rent:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('mortgage') is-invalid @enderror" type="radio" id="noMortgage" name="mortgage" value="no" {{ old('mortgage') == 'no' ? 'checked' : '' }} required>
                <label class="form-check-label" for="noMortgage">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('mortgage') is-invalid @enderror" type="radio" id="hasMortgage" name="mortgage" value="yes" {{ old('mortgage') == 'yes' ? 'checked' : '' }} required>
                <label class="form-check-label" for="hasMortgage">Yes</label>
            </div>
            @error('mortgage')<div class="text-danger small">{{ $message }}</div>@enderror

            <div class="other-container d-none mt-2">
                <label class="form-label" for="mortgageValue">Please enter the value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('mortgageValue') is-invalid @enderror" id="mortgageValue" name="mortgageValue" placeholder="0.00" value="{{ old('mortgageValue') }}">
                    @error('mortgageValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Transportation loan:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('transportLoan') is-invalid @enderror" type="radio" id="noTransportLoan" name="transportLoan" value="no" {{ old('transportLoan') == 'no' ? 'checked' : '' }} required>
                <label class="form-check-label" for="noTransportLoan">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('transportLoan') is-invalid @enderror" type="radio" id="hasTransportLoan" name="transportLoan" value="yes" {{ old('transportLoan') == 'yes' ? 'checked' : '' }} required>
                <label class="form-check-label" for="hasTransportLoan">Yes</label>
            </div>
            @error('transportLoan')<div class="text-danger small">{{ $message }}</div>@enderror

            <div class="other-container d-none mt-2">
                <label class="form-label" for="transportLoanValue">Please enter the value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('transportLoanValue') is-invalid @enderror" id="transportLoanValue" name="transportLoanValue" placeholder="0.00" value="{{ old('transportLoanValue') }}">
                    @error('transportLoanValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Educational expenses:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('eduExpense') is-invalid @enderror" type="radio" id="noEduExpense" name="eduExpense" value="no" {{ old('eduExpense') == 'no' ? 'checked' : '' }} required>
                <label class="form-check-label" for="noEduExpense">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('eduExpense') is-invalid @enderror" type="radio" id="hasEduExpense" name="eduExpense" value="yes" {{ old('eduExpense') == 'yes' ? 'checked' : '' }} required>
                <label class="form-check-label" for="hasEduExpense">Yes</label>
            </div>
            @error('eduExpense')<div class="text-danger small">{{ $message }}</div>@enderror

            <div class="other-container d-none mt-2">
                <label class="form-label" for="eduExpenseValue">Please enter the value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('eduExpenseValue') is-invalid @enderror" id="eduExpenseValue" name="eduExpenseValue" placeholder="0.00" value="{{ old('eduExpenseValue') }}">
                    @error('eduExpenseValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Utility expenses (electricity/water):</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('utilityExpenses') is-invalid @enderror" type="radio" id="noUtilityExpenses" name="utilityExpenses" value="no" {{ old('utilityExpenses') == 'no' ? 'checked' : '' }} required>
                <label class="form-check-label" for="noUtilityExpenses">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('utilityExpenses') is-invalid @enderror" type="radio" id="hasUtilityExpenses" name="utilityExpenses" value="yes" {{ old('utilityExpenses') == 'yes' ? 'checked' : '' }} required>
                <label class="form-check-label" for="hasUtilityExpenses">Yes</label>
            </div>
            @error('utilityExpenses')<div class="text-danger small">{{ $message }}</div>@enderror

            <div class="other-container d-none mt-2">
                <label class="form-label" for="utilityExpensesValue">Please enter the value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('utilityExpensesValue') is-invalid @enderror" id="utilityExpensesValue" name="utilityExpensesValue" placeholder="0.00" value="{{ old('utilityExpensesValue') }}">
                    @error('utilityExpensesValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <label class="form-label">Family Expenses:</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('familyExpenses') is-invalid @enderror" type="radio" id="nofamilyExpenses" name="familyExpenses" value="no" {{ old('familyExpenses') == 'no' ? 'checked' : '' }} required>
                <label class="form-check-label" for="nofamilyExpenses">No</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input other @error('familyExpenses') is-invalid @enderror" type="radio" id="hasFamilyExpenses" name="familyExpenses" value="yes" {{ old('familyExpenses') == 'yes' ? 'checked' : '' }} required>
                <label class="form-check-label" for="hasFamilyExpenses">Yes</label>
            </div>
            @error('familyExpenses')<div class="text-danger small">{{ $message }}</div>@enderror

            <div class="other-container d-none mt-2">
                <label class="form-label" for="familyExpensesValue">Please enter the value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('familyExpensesValue') is-invalid @enderror" id="familyExpensesValue" name="familyExpensesValue" placeholder="0.00" value="{{ old('familyExpensesValue') }}">
                    @error('familyExpensesValue')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-md-12 extra-option">
            <label class="form-label fw-bold">Other Expenses</label>

            @for($i=0; $i < $countExpense; $i++)
                <div class="card p-3 my-3 extra-options filledAll position-relative bg-light border">
                <span class="remove-btn position-absolute top-0 end-0 mt-2 me-2" role="button" title="Remove">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-x-lg text-danger" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                    </svg>
                </span>

                <label class="form-label" for="otherExpensesName_{{$i}}">Expenses Name</label>
                <input class="form-control @error('otherExpensesName.'.$i) is-invalid @enderror" type="text" id="otherExpensesName_{{$i}}" name="otherExpensesName[{{ $i }}]" value="{{ old('otherExpensesName.' . $i) }}">
                @error('otherExpensesName.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror

                <label class="form-label mt-2" for="otherExpensesValue_{{$i}}">Value</label>
                <div class="input-group">
                    <span class="input-group-text">RM</span>
                    <input type="number" step="0.01" class="form-control @error('otherExpensesValue.'.$i) is-invalid @enderror" id="otherExpensesValue_{{$i}}" name="otherExpensesValue[{{ $i }}]" placeholder="0.00" value="{{ old('otherExpensesValue.' . $i) }}">
                    @error('otherExpensesValue.'.$i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
        </div>
        @endfor
        <button type="button" class="insert-before btn btn-secondary mt-3">Add others expenses</button>
    </div>
</div>
</div>
<div class="bg-white p-4 rounded shadow my-3">
    <!-- Application Reason -->
    <h3 class="mt-2 fw-bold">Application Reason</h3>
    <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" style="height: 150px;" placeholder="Explain why monthly supply support is needed (e.g., facing financial hardship, supporting a large family, or lacking access to basic necessities)">{{ old('reason') }}</textarea>
    @error('reason')
    <div class="invalid-feedback">
        {{ $message }}
    </div>
    @enderror
    <div class="col-md-10 mb-5">
    </div>
</div>
<!-- Navigation button (prev or next page) -->
<div class="mt-3 d-flex justify-content-between mb-5">
    <button type="button" class="prev-page btn btn-secondary">Back</button>
    <button type="button" class="next-page btn btn-primary">Next</button>
</div>
</div>

<!-- Upload Document (third page) -->
<div class="BeneficiaryDocument" id="page3">
    <div class="bg-white p-4 rounded shadow my-3" style="width: fit-content; max-width: 100%;">
        <h3 class="fw-bold">Required Document</h3>
        <p class="text-muted" style="font-size: 0.9rem;">
            Accepted formats: <strong>PDF, JPG, JPEG, PNG</strong>.
            Maximum file size: <strong>2MB</strong>.
            Please ensure the document is clear, readable, and visibly contains the text
            <strong>/For Official Use/</strong> before uploading, as this involves sensitive information.
        </p>

        <label for="iC_copy" class="form-label"> IC copy</label>
        <input type="file" id="iC_copy" name="iC_copy" class="form-control @error('iC_copy') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
        @error('iC_copy')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <br><br>

        <label for="utility" class="form-label">Utility Bills</label>
        <input type="file" id="utility" name="utility" class="form-control @error('utility') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
        @error('utility')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <br><br>

        <label for="payslip" class="form-label">Payslip (Last 3 months)</label>
        <input type="file" id="payslip" name="payslip" class="form-control @error('payslip') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png" required>
        @error('payslip')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <!-- Navigation Button (next and prev) -->
    <div class="mt-3 d-flex justify-content-between mb-5">
        <button type="button" class=" prev-page btn btn-secondary">Back</button>
        <button type="submit" class="btn btn-success submit" onclick="return confirm('Are you sure you want to submit this form?')">
            Submit
        </button>
    </div>
</div>
</form>
</div>
<!-- End Form -->
@else
<div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-md-6 col-lg-5 my-5">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-5 text-center">

                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle" style="width: 80px; height: 80px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-slash-circle-fill text-danger" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-4.646-2.646a.5.5 0 0 0-.708-.708l-6 6a.5.5 0 0 0 .708.708l6-6z" />
                        </svg>
                    </div>
                </div>

                <h3 class="fw-bold text-dark mb-3">Application Unavailable</h3>

                <p class="text-muted mb-4">
                    We are currently not accepting new responses for this form.
                </p>

                <div class="d-grid col-8 mx-auto">
                    <a href="{{ route('home') }}" class="btn btn-danger">Return to Home</a>
                </div>

            </div>
        </div>
    </div>
</div>

@endif
@endif

@endsection