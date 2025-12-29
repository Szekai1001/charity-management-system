@forelse($applications as $application)
<tr class="align-middle border-bottom text-center">

    {{-- 1. Index --}}
    <td class="text-muted fw-bold" style="width: 50px;">
        {{ $loop->iteration }}
    </td>

    {{-- 2. User Profile (Aligned Left) --}}
    <td class="text-start ps-3">
        <div class="d-flex align-items-center">
            {{-- Initials Avatar --}}
            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                {{ substr($application->user->beneficiary->name ?? 'U', 0, 1) }}
            </div>

            {{-- Name & Email Stack --}}
            <div>
                <div class="fw-bold text-dark">
                    {{ $application->user->beneficiary->name ?? 'Unknown User' }}
                </div>
                <div class="small text-muted">
                    {{ $application->user->email }}
                </div>
            </div>
        </div>
    </td>

    {{-- 3. IC --}}
    <td>{{ $application->user->beneficiary->ic }}</td>

    {{-- 4. DSS Score --}}
    <td>
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
            Score: {{ $application->dss_score }}
        </span>
    </td>

    {{-- 5. Phone --}}
    <td class="text-nowrap text-muted small">
        <i class="bi bi-telephone me-1"></i>
        {{ $application->user->beneficiary->phone_number ?? '-' }}
    </td>

    {{-- 6. Status Dropdown --}}
    <td>
        <select name="statuses[{{ $application->id }}]"
            class="form-select form-select-sm fw-semibold text-capitalize mx-auto" style="max-width: 140px;">
            <option value="processing" {{ $application->status == 'processing' ? 'selected' : '' }}>Processing</option>
            <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
        </select>
    </td>

    {{-- 7. Action Button --}}
    <td>
        <button type="button"
            class="btn btn-sm btn-outline-primary shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#detailsModal{{ $application->id }}"
            title="View Details">
            <i class="bi bi-eye-fill me-1"></i> View
        </button>
    </td>
</tr>


<!-- Modal -->
<div class="modal fade" id="detailsModal{{ $application->id }}" tabindex="-1" aria-labelledby="detailsModalLabel{{ $application->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="detailsModalLabel{{ $application->id }}">
                    Beneficiary Application Details (ID: {{ $application->id }})
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                @if($application->application_type == 'Beneficiary' && $application->user->beneficiary)
                <!-- Personal Details -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold mb-3">Personal Details</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Name</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->name }}</dd>

                            <dt class="col-sm-3">IC</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->ic }}</dd>

                            <dt class="col-sm-3">Gender</dt>
                            <dd class="col-sm-9">{{ ucfirst($application->user->beneficiary->gender) }}</dd>

                            <dt class="col-sm-3">Birth Date</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->birth_date }}</dd>

                            <dt class="col-sm-3">Religion</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->religion }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->phone_number }}</dd>

                            <dt class="col-sm-3">Family Role</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->family_role }}</dd>

                            <dt class="col-sm-3">Occupation</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->occupation }}</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">
                                {{ collect([
                        $application->user->beneficiary->street ?? 'N/A',
                        $application->user->beneficiary->area,
                        $application->user->beneficiary->city,
                        $application->user->beneficiary->state,
                        $application->user->beneficiary->zip
                    ])->filter()->implode(', ') }}
                            </dd>

                            <dt class="col-sm-3">Residential Status</dt>
                            <dd class="col-sm-9">{{ $application->user->beneficiary->residential_status}}

                            <dt class="col-sm-3">Basic Amenities Access</dt>
                            <dd class="col-sm-9">
                                @if(!empty($application->user->beneficiary->basic_amenities_access))
                                {{ implode(', ', $application->user->beneficiary->basic_amenities_access) }}
                                @else
                                N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Family Members --}}
                @if($application->user->beneficiary->familyMember && $application->user->beneficiary->familyMember->count())
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Family Members</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($application->user->beneficiary->familyMember as $index => $member)
                            <div class="col-md-6">
                                <div class="p-3 border rounded bg-light h-100">
                                    <h6 class="fw-bold">Member {{ $index + 1 }}</h6>
                                    <dl class="row mb-0">
                                        <dt class="col-sm-5">Name</dt>
                                        <dd class="col-sm-7">{{ $member->name }}</dd>

                                        <dt class="col-sm-5">Birth Date</dt>
                                        <dd class="col-sm-7">{{ \Carbon\Carbon::parse($member->birth_date)->format('d M Y') }}</dd>

                                        <dt class="col-sm-5">Occupation</dt>
                                        <dd class="col-sm-7">{{ $member->occupation }}</dd>

                                        <dt class="col-sm-5">Relationship</dt>
                                        <dd class="col-sm-7">{{ $member->relationship }}</dd>
                                    </dl>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Incomes -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="fw-bold">Incomes</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Family Income</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->family_income > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->family_income, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Assist from Child</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->assist_from_child > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->assist_from_child, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Government Assist</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->government_assist > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->government_assist, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Insurance Pay</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->insurance_pay > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->insurance_pay, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    @foreach($application->user->beneficiary->otherIncome ?? [] as $income)
                                    <dt class="col-sm-6">{{ $income->other_income_resource }}</dt>
                                    <dd class="col-sm-6">
                                        RM {{ number_format((float) $income->other_income_source_value, 2) }}
                                    </dd>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Expenses -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="fw-bold">Expenses</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row">
                                    <dt class="col-sm-6">Mortgage / Rent</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->mortgage_expense > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->mortgage_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Transport Loan</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->transport_loan > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->transport_loan, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Utility Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->utility_expense > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->utility_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Education Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->education_expense > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->education_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Family Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->beneficiary->family_expense > 0)
                                        RM {{ number_format((float) $application->user->beneficiary->family_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    @foreach($application->user->beneficiary->otherExpense ?? [] as $expense)
                                    <dt class="col-sm-6">{{ $expense->other_expense }}</dt>
                                    <dd class="col-sm-6">
                                        {{-- Cast to float to avoid errors --}}
                                        RM {{ number_format((float) $expense->other_expense_value, 2) }}
                                    </dd>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0">Application Reason</h6>
                    </div>
                    <div class="card-body">
                        {{$application->user->beneficiary->application_reason ?? 'N/A'}}
                    </div>
                </div>


                {{-- Documents --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Documents</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            @foreach($application->documents as $doc)
                            <dt class="col-sm-4">{{ $doc->type }}</dt>
                            <dd class="col-sm-8">
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-decoration-none">
                                    View Document
                                </a>
                            </dd>
                            @endforeach
                        </dl>
                    </div>
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@empty
<tr>
    <td colspan="9" class="text-center py-4">
        <div class="d-flex flex-column align-items-center text-muted">
            <i class="bi bi-box-seam display-6 mb-2"></i>
            <span class="fw-semibold">No application for beneficiaries yet</span>
            <small class="text-secondary">New appllication will appear here once received.</small>
        </div>
    </td>
</tr>
@endforelse