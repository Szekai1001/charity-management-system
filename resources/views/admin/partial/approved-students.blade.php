<form id="assignTeacherForm" method="POST" action="{{ route('students.assignTeacher') }}">
    @csrf
    <div class="card shadow-sm">

        <div class="table-responsive">

            <table class="table table-hover align-middle">
                <thead class="table-light text-center text-uppercase small">
                    <tr>
                        <th><input type="checkbox" id="selectAllStudents" class="select-student"></th>
                        <th class="text-start ps-3">Student Name</th>
                        <th>Phone Number</th>
                        <th>Gender</th>
                        <th>Teacher Name</th>
                        <th>QR code</th>
                        <th>Grade Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    @if(!empty($approvedStudents) && $approvedStudents->count())
                    @forelse($approvedStudents as $approvedStudent)
                    <tr>
                        <td><input type="checkbox" name="student_ids[]" class="student-checkbox" value="{{$approvedStudent->user->student->id}}"></td>
                        <td class="text-start ps-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                                    style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                                    {{ substr($approvedStudent->user->student->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">
                                        {{$approvedStudent->user->student->name}}
                                    </div>
                                    <div class="text-muted small">
                                        {{$approvedStudent->user->email}}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="text-nowrap text-muted small">
                            <i class="bi bi-telephone me-1"></i>{{$approvedStudent->user->student->phone}}
                        </td>
                        <td>{{$approvedStudent->user->student->gender}}</td>
                        <td>{{$approvedStudent->user->student->teacher ? $approvedStudent->user->student->teacher->name : 'Not Assigned'}}</td>
                        <td><a href="#" data-bs-toggle="modal" data-bs-target="#qrModalstudent-{{$approvedStudent->user->student->id}}">
                                <i class="bi bi-qr-code fs-4"></i>
                            </a></td>
                        <td>{{$approvedStudent->user->student->grade}}</td>
                        <td class="text-center">
                            <!-- View Details button -->
                            <a href="#"
                                data-bs-toggle="modal"
                                data-bs-target="#details{{ $approvedStudent->user->student->id }}"
                                class="btn btn-primary btn-sm mx-1"
                                title="View Details">
                                View
                            </a>

                            <!-- Delete button -->
                            <a href="#"
                                data-id="{{ $approvedStudent->user->student->id }}"
                                data-type="student"
                                class="btn btn-danger btn-sm mx-1 del-row"
                                title="Delete Row">
                                Delete
                            </a>

                            <a href="#"
                                data-bs-toggle="modal"
                                data-bs-target="#studentEdit{{ $approvedStudent->user->student->id }}"
                                class="btn btn-warning btn-sm mx-1">
                                Edit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <i class="bi bi-box-seam display-6 mb-2"></i>
                                <span class="fw-semibold">No students yet</span>
                                <small class="text-secondary">New records will appear here once received.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <button type="button" class="btn btn-primary mt-3" id="openAssignModal">Assign Teacher</button>

    <div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-labelledby="assignTeacherLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Assign Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="teacher_id">Select Teacher:</label>
                    <select name="teacher_id" class="form-control" required>
                        <option value="">-- Choose Teacher --</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->teacher->id }}">{{ $teacher->teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Assign</button>
                </div>
            </div>
        </div>
    </div>
</form>

@foreach($approvedStudents as $approvedStudent)
<!-- Update Student Modal -->
<div class="modal fade" id="studentEdit{{ $approvedStudent->user->student->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('users.update') }}">
                @csrf

                <!-- REQUIRED -->
                <input type="hidden" name="id" value="{{ $approvedStudent->user->student->id }}">
                <input type="hidden" name="type" value="student">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                            name="student_email"
                            class="form-control @error('student_email') is-invalid @enderror"
                            value="{{ old('student_email', $approvedStudent->user->email) }}">
                        @error('student_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Grade -->
                    <div class="mb-3">
                        <label class="form-label">Grade</label>
                        <input type="text"
                            name="grade"
                            class="form-control @error('grade') is-invalid @enderror"
                            value="{{ old('grade', $approvedStudent->user->student->grade) }}">
                        @error('grade')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- School -->
                    <div class="mb-3">
                        <label class="form-label">School</label>
                        <input type="text"
                            name="school"
                            class="form-control @error('school') is-invalid @enderror"
                            value="{{ old('school', $approvedStudent->user->student->school) }}">
                        @error('school')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $approvedStudent->user->student->phone) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Guardian Phone -->
                    <div class="mb-3">
                        <label class="form-label">Guardian Phone Number</label>
                        <input type="text"
                            name="guardian_phone"
                            class="form-control @error('guardian_phone') is-invalid @enderror"
                            value="{{ old('guardian_phone', $approvedStudent->user->student->guardian->phone ?? '') }}">
                        @error('guardian_phone')
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

@foreach($approvedStudents as $approvedStudent)
<div class="modal fade" id="details{{ $approvedStudent->user->student->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    Student Details #{{ $approvedStudent->user->student->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Personal Info --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">IC</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->ic }}</dd>

                            <dt class="col-sm-3">Gender</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->gender }}</dd>

                            <dt class="col-sm-3">Birth Date</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->birth_date }}</dd>

                            <dt class="col-sm-3">Religion</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->religion }}</dd>

                            <dt class="col-sm-3">School</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->school }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->phone }}</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">
                                {{ collect([
                                        $approvedStudent->user->student->street ?? 'N/A',
                                        $approvedStudent->user->student->area,
                                        $approvedStudent->user->student->city,
                                        $approvedStudent->user->student->state,
                                        $approvedStudent->user->student->zip
                                    ])->filter()->implode(', ') }}
                            </dd>

                            <dt class="col-sm-3">Application Reason</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->reason ?? 'N/A' }}</dd>

                            <dt class="col-sm-3">Residential Status</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->residential_status }}</dd>

                            <dt class="col-sm-3">Basic Amenities Access</dt>
                            <dd class="col-sm-9">
                                @if(!empty($approvedStudent->user->student->basic_amenities_access))
                                {{ implode(', ', $approvedStudent->user->student->basic_amenities_access) }}
                                @else
                                N/A
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>

                {{-- Guardian Info --}}
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="fw-bold">Guardian Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">Name</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->guardian->name }}</dd>

                            <dt class="col-sm-3">IC</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->guardian->ic }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->guardian->phone }}</dd>

                            <dt class="col-sm-3">Relationship</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->guardian->relationship }}</dd>

                            <dt class="col-sm-3">Occupation</dt>
                            <dd class="col-sm-9">{{ $approvedStudent->user->student->guardian->occupation }}</dd>
                        </dl>
                    </div>
                </div>

                {{-- Family Members --}}
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">Family Members</h6>
                    </div>
                    <div class="card-body">
                        @if($approvedStudent->user->student->familyMember && $approvedStudent->user->student->familyMember->count())
                        <div class="row g-3">
                            @foreach($approvedStudent->user->student->familyMember as $index => $member)
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
                        <p class="text-muted">No family members recorded.</p>
                        @endif
                    </div>
                </div>

                {{-- Incomes --}}
                <div class="row">
                    {{-- Income --}}
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h6 class="fw-bold mb-0">Incomes</h6>
                            </div>
                            <div class="card-body">
                                <dl class="row mb-0">
                                    <dt class="col-sm-6">Family Income</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->family_income > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->family_income, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Assist from Child</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->assist_from_child > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->assist_from_child, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Government Assist</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->government_assist > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->government_assist, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Insurance Pay</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->insurance_pay > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->insurance_pay, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    @foreach($approvedStudent->user->student->otherIncome ?? [] as $income)
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
                                        @if($approvedStudent->user->student->mortgage_expense > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->mortgage_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Transport Loan</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->transport_loan > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->transport_loan, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Utility Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->utility_expense > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->utility_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Education Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->education_expense > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->education_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>

                                    <dt class="col-sm-6">Family Expense</dt>
                                    <dd class="col-sm-6">
                                        @if($approvedStudent->user->student->family_expense > 0)
                                        RM {{ number_format((float) $approvedStudent->user->student->family_expense, 2) }}
                                        @else
                                        No
                                        @endif
                                    </dd>


                                    @foreach($approvedStudent->user->student->otherExpense ?? [] as $expense)
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
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
</div>
@endforeach



<!-- Include modal -->
@foreach($approvedStudents as $approvedStudent)
<x-qr-modal
    :id="'student-'.$approvedStudent->user->student->id"
    :name="$approvedStudent->user->student->name"
    :qr_code="$approvedStudent->user->student->qr_code" />
@endforeach


<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
        console.log("Validation errors detected."); // Debug check 1

        var type = "{{ old('type') }}";
        var id = "{{ old('id') }}";

        console.log("Type:", type, "ID:", id); // Debug check 2

        if (type === 'student' && id) {
            var modalId = 'studentEdit' + id;
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