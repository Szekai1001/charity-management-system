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
                {{ substr($application->user->student->name ?? 'U', 0, 1) }}
            </div>

            {{-- Name & Email Stack --}}
            <div>
                <div class="fw-bold text-dark">
                    {{ $application->user->student->name ?? 'Unknown User' }}
                </div>
                <div class="small text-muted">
                    {{ $application->user->email }}
                </div>
            </div>
        </div>
    </td>

    {{-- 3. IC --}}
    <td>{{ $application->user->student->ic }}</td>

    {{-- 4. DSS Score --}}
    <td>
        <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
            Score: {{ $application->dss_score }}
        </span>
    </td>

    {{-- 5. Phone --}}
    <td class="text-nowrap text-muted small">
        <i class="bi bi-telephone me-1"></i>
        {{ $application->user->student->phone ?? '-' }}
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


<!-- Modal for each application -->
<div class="modal fade" id="detailsModal{{ $application->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Student Application Details #{{ $application->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Student Section --}}
                @if($application->application_type == 'Student' && $application->user->student)
                {{-- Personal Info --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold pb-2">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8">{{ $application->user->student->name }}</dd>

                            <dt class="col-sm-4">IC</dt>
                            <dd class="col-sm-8">{{ $application->user->student->ic }}</dd>

                            <dt class="col-sm-4">Gender</dt>
                            <dd class="col-sm-8">{{ $application->user->student->gender }}</dd>

                            <dt class="col-sm-4">Birth Date</dt>
                            <dd class="col-sm-8">{{ $application->user->student->birth_date }}</dd>

                            <dt class="col-sm-4">Grade</dt>
                            <dd class="col-sm-8">{{ $application->user->student->grade }}</dd>

                            <dt class="col-sm-4">Religion</dt>
                            <dd class="col-sm-8">{{ $application->user->student->religion }}</dd>

                            <dt class="col-sm-4">School</dt>
                            <dd class="col-sm-8">{{ $application->user->student->school }}</dd>

                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ $application->user->student->phone }}</dd>

                            <dt class="col-sm-4">Address</dt>
                            <dd class="col-sm-8">
                                {{ collect([
                    $application->user->student->street ?? 'N/A',
                    $application->user->student->area,
                    $application->user->student->city,
                    $application->user->student->state,
                    $application->user->student->zip
                ])->filter()->implode(', ') }}
                            </dd>

                            <dt class="col-sm-4">Residential Status</dt>
                            <dd class="col-sm-8">{{ $application->user->student->residential}}

                            <dt class="col-sm-4">Basic Amenities Access</dt>
                            <dd class="col-sm-8">
                                @if(!empty($application->user->student->amenities))
                                {{ implode(', ', $application->user->student->amenities) }}
                                @else
                                N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                @if($application->user->student->guardian)
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-light">
                        <h6 class="fw-bold mb-0">Guardian Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-4">Name</dt>
                            <dd class="col-sm-8">{{ $application->user->student->guardian->name }}</dd>

                            <dt class="col-sm-4">IC</dt>
                            <dd class="col-sm-8">{{ $application->user->student->guardian->ic }}</dd>

                            <dt class="col-sm-4">Phone</dt>
                            <dd class="col-sm-8">{{ $application->user->student->guardian->phone }}</dd>

                            <dt class="col-sm-4">Relationship</dt>
                            <dd class="col-sm-8">{{ $application->user->student->guardian->relationship }}</dd>

                            <dt class="col-sm-4">Occupation</dt>
                            <dd class="col-sm-8">{{ $application->user->student->guardian->occupation }}</dd>
                        </dl>
                    </div>
                </div>
                @endif

                {{-- Family Members --}}
                @if($application->user->student->familyMember && $application->user->student->familyMember->count())
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Family Members</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($application->user->student->familyMember as $index => $member)
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

                <div class="row">
                    {{-- Income --}}
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Income</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6">Family Income</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->family_income > 0)
                                        RM {{ number_format((float) $application->user->student->family_income, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Assist from Child</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->assist_from_child > 0)
                                        RM {{ number_format((float) $application->user->student->assist_from_child, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Government Assist</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->government_assist > 0)
                                        RM {{ number_format((float) $application->user->student->government_assist, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Insurance Pay</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->insurance_pay > 0)
                                        RM {{ number_format((float) $application->user->student->insurance_pay, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    @foreach($application->user->student->otherIncome ?? [] as $income)
                                    <dt class="col-sm-6">{{ $income->other_income_resource }}</dt>
                                    <dd class="col-sm-6">
                                        @if($income->other_income_source_value > 0)
                                        RM {{ number_format((float) $income->other_income_source_value, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    </div>

                    {{-- Expenses --}}
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Expenses</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6">Mortgage / Rent</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->mortgage_expense > 0)
                                        RM {{ number_format((float) $application->user->student->mortgage_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Transport Loan</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->transport_loan > 0)
                                        RM {{ number_format((float) $application->user->student->transport_loan, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Utility Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->utility_expense > 0)
                                        RM {{ number_format((float) $application->user->student->utility_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Education Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($application->user->student->education_expense > 0)
                                        RM {{ number_format((float) $application->user->student->education_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                        <dt class="col-sm-6">Family Expense</dt>
                                        <dd class="col-sm-6">
                                            @if($application->user->student->family_expense > 0)
                                            RM {{ number_format((float) $application->user->student->family_expense, 2) }}
                                            @else
                                            No
                                            @endif
                                        </dd>

                                    @foreach($application->user->student->otherExpense ?? [] as $expense)
                                    <dt class="col-sm-6">{{ $expense->other_expense }}</dt>
                                    <dd class="col-sm-6">
                                        @if($expense->other_expense_value > 0)
                                        RM {{ number_format((float) $expense->other_expense_value, 2) }}
                                        @else
                                        No
                                        @endif
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
                        {{$application->user->student->reason ?? 'N/A'}}
                    </div>
                </div>

                @endif

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

            </div>
        </div>
    </div>
</div>
<!-- End Modal -->
@empty
<tr>
    <td colspan="9" class="text-center py-4">
        <div class="d-flex flex-column align-items-center text-muted">
            <i class="bi bi-box-seam display-6 mb-2"></i>
            <span class="fw-semibold">No application for students yet</span>
            <small class="text-secondary">New applications will appear here once received.</small>
        </div>
    </td>
</tr>
@endforelse