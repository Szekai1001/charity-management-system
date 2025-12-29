<table class="table table-hover align-middle">
    <thead class="table-light text-center text-uppercase small">
        <tr>
            <th><input type="checkbox" class="select-beneficiary"></th>
            <th class="text-start ps-3">Beneficiary Name</th>
            <th>Phone Number</th>
            <th>Gender</th>
            <th>Birth Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @forelse($approvedBeneficiaries as $approvedBeneficiary)
        <tr>
            <td><input type="checkbox" name="beneficiary_ids[]" class="beneficiary-checkbox" value="{{$approvedBeneficiary->user->beneficiary->id}}"></td>
            <td class="text-start ps-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                        style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                        {{ substr($approvedBeneficiary->user->beneficiary->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">
                            {{$approvedBeneficiary->user->beneficiary->name}}
                        </div>

                        <div class="small text-muted">
                            {{$approvedBeneficiary->user->email}}
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-nowrap text-muted small">
                <i class="bi bi-telephone me-1"></i>{{$approvedBeneficiary->user->beneficiary->phone_number}}
            </td>
            <td>{{$approvedBeneficiary->user->beneficiary->gender}}</td>
            <td>{{$approvedBeneficiary->user->beneficiary->birth_date}}</td>
            <td class="text-center">
                <!-- View Details button -->
                <a href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#beneficiaryDetails{{ $approvedBeneficiary->user->beneficiary->id }}"
                    class="btn btn-primary btn-sm mx-1"
                    title="View Details">
                    View
                </a>

                <!-- Delete button -->
                <a href="#"
                    data-id="{{ $approvedBeneficiary->user->beneficiary->id }}"
                    data-type="beneficiary"
                    class="btn btn-danger btn-sm mx-1 del-row"
                    title="Delete Row">
                    Delete
                </a>

                <a href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#beneficiaryEdit{{ $approvedBeneficiary->user->beneficiary->id }}"
                    class="btn btn-warning btn-sm mx-1">
                    Edit
                </a>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center py-4">
                <div class="d-flex flex-column align-items-center text-muted">
                    <i class="bi bi-box-seam display-6 mb-2"></i>
                    <span class="fw-semibold">No beneficiaries yet</span>
                    <small class="text-secondary">New records will appear here once received.</small>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@foreach($approvedBeneficiaries as $approvedBeneficiary)
<!-- Update Student Modal -->
<div class="modal fade" id="beneficiaryEdit{{ $approvedBeneficiary->user->beneficiary->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('users.update') }}">
                @csrf

                <!-- REQUIRED -->
                <input type="hidden" name="id" value="{{ $approvedBeneficiary->user->beneficiary->id }}">
                <input type="hidden" name="type" value="beneficiary">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Beneficiary</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                            name="beneficiary_email"
                            class="form-control @error('beneficiary_email') is-invalid @enderror"
                            value="{{ old('beneficiary_email', $approvedBeneficiary->user->email) }}">
                        @error('beneficiary_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- phone -->
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $approvedBeneficiary->user->beneficiary->phone_number) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach



@foreach($approvedBeneficiaries as $approvedBeneficiary)
<div class="modal fade" id="beneficiaryDetails{{ $approvedBeneficiary->user->beneficiary->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="fw-bold mb-0">Beneficiary Details #{{ $approvedBeneficiary->user->beneficiary->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- Personal Information --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-sm-3">IC</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->ic }}</dd>

                            <dt class="col-sm-3">Gender</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->gender }}</dd>

                            <dt class="col-sm-3">Birth Date</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->birth_date }}</dd>

                            <dt class="col-sm-3">Religion</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->religion }}</dd>

                            <dt class="col-sm-3">Family Role</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->family_role }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->phone_number }}</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">
                                {{ collect([
                                        $approvedBeneficiary->user->beneficiary->street ?? 'N/A',
                                        $approvedBeneficiary->user->beneficiary->area,
                                        $approvedBeneficiary->user->beneficiary->city,
                                        $approvedBeneficiary->user->beneficiary->state,
                                        $approvedBeneficiary->user->beneficiary->zip
                                    ])->filter()->implode(', ') }}
                            </dd>

                            <dt class="col-sm-3">Application Reason</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->application_reason ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Residential Status</dt>
                            <dd class="col-sm-9">{{ $approvedBeneficiary->user->beneficiary->residential_status }}</dd>

                            <dt class="col-sm-3">Basic Amenities Access</dt>
                            <dd class="col-sm-9">
                                @if(!empty($approvedBeneficiary->user->beneficiary->basic_amenities_access))
                                {{ implode(', ', $approvedBeneficiary->user->beneficiary->basic_amenities_access) }}
                                @else
                                N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Family Members --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Family Members</h6>
                    </div>
                    <div class="card-body">
                        @if($approvedBeneficiary->user->beneficiary->familyMember && $approvedBeneficiary->user->beneficiary->familyMember->count())
                        <div class="row g-3">
                            @foreach($approvedBeneficiary->user->beneficiary->familyMember as $index => $member)
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
                        @else
                        <p class="text-muted mb-0">No family members recorded.</p>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        {{-- Incomes --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Incomes</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6">Family Income</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->family_income > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->family_income, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Assist from Child</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->assist_from_child > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->assist_from_child, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Government Assist</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->government_assist > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->government_assist, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Insurance Pay</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->insurance_pay > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->insurance_pay, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    {{-- Other Incomes Loop --}}
                                    @if($approvedBeneficiary->user->beneficiary->otherIncome->count() > 0)
                                    @foreach($approvedBeneficiary->user->beneficiary->otherIncome as $income)
                                    <dt class="col-sm-6">{{ $income->other_income_resource }}</dt>
                                    <dd class="col-sm-6">
                                        @if($income->other_income_source_value > 0)
                                        RM {{ number_format((float)$income->other_income_source_value, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>
                                    @endforeach
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        {{-- Expenses --}}
                        <div class="card mb-0">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Expenses</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6">Mortgage / Rent</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->mortgage_expense > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->mortgage_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Transport Loan</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->transport_loan > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->transport_loan, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Utility Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->utility_expense > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->utility_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Education Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedBeneficiary->user->beneficiary->education_expense > 0)
                                        RM {{ number_format((float)$approvedBeneficiary->user->beneficiary->education_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    {{-- Other Expenses Loop --}}
                                    @if($approvedBeneficiary->user->beneficiary->otherExpense->count() > 0)
                                    @foreach($approvedBeneficiary->user->beneficiary->otherExpense as $expense)
                                    <dt class="col-sm-6">{{ $expense->other_expense }}</dt>
                                    <dd class="col-sm-6">
                                        @if($expense->other_expense_value > 0)
                                        RM {{ number_format((float)$expense->other_expense_value, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>
                                    @endforeach
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
</div>
@endforeach

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
        console.log("Validation errors detected."); // Debug check 1

        var type = "{{ old('type') }}";
        var id = "{{ old('id') }}";

        console.log("Type:", type, "ID:", id); // Debug check 2

        if (type === 'beneficiary' && id) {
            var modalId = 'beneficiaryEdit' + id;
            var modalElement = document.getElementById(modalId);

            if (modalElement) {
                console.log("Modal found. Opening..."); // Debug check 3
                var myModal = new bootstrap.Modal(modalElement);
                myModal.show();
            } else {
                console.error("Modal element not found: " + modalId);
            }
        } else {
            console.warn("Type or ID is missing. Check Controller withInput() or Hidden Inputs.");
        }
        @endif
    });
</script>