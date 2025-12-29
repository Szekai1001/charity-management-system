<table class="table table-hover align-middle">
    <thead class="table-light text-center text-uppercase small">
        <tr>
            <th><input type="checkbox" class="select-teacher"></th>
            <th class="text-start ps-3">Teacher Name</th>
            <th>Phone Number</th>
            <th>Gender</th>
            <th>Responsible Students</th>
            <th>QR code</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
        @forelse($teachers as $teacher)
        <tr>
            <td><input type="checkbox" name="teacher_ids[]" class="teacher-checkbox" value="{{$teacher->teacher->id}}"></td>
            <td class="text-start ps-3">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                        style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                        {{ substr($teacher->teacher->name ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold text-dark">
                            {{$teacher->teacher->name}}
                        </div>

                        <div class="small text-muted">
                            {{$teacher->email}}
                        </div>
                    </div>
                </div>
            </td>
            <td class="text-nowrap text-muted small">
                <i class="bi bi-telephone me-1"></i>{{$teacher->teacher->phone_number}}
            </td>
            <td>{{$teacher->teacher->gender}}</td>
            <td>
                @php
                $currentStudents = $teacher->teacher->student;
                @endphp

                @if($currentStudents->isNotEmpty())
                <div class="d-flex flex-wrap gap-1">

                    {{-- 2. Use $currentStudents here instead of just $students --}}
                    @foreach($currentStudents->take(2) as $student)
                    <span class="badge bg-light text-dark border">
                        {{ $student->name }}
                    </span>
                    @endforeach

                    {{-- 3. Use $currentStudents here as well --}}
                    @if($currentStudents->count() > 2)
                    <span class="badge bg-primary rounded-pill"
                        title="{{ $currentStudents->pluck('name')->implode(', ') }}"
                        style="cursor: help;">
                        +{{ $currentStudents->count() - 2 }} more
                    </span>
                    @endif
                </div>
                @else
                <span class="text-muted small fst-italic">Not Assigned</span>
                @endif
            </td>
            <td><a href="#" data-bs-toggle="modal" data-bs-target="#qrModalteacher-{{$teacher->teacher->id}}">
                    <i class="bi bi-qr-code fs-4"></i>
                </a></td>
            <td class="text-center">
                <!-- View Details button -->
                <a href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#teacherDetails{{ $teacher->teacher->id }}"
                    class="btn btn-primary btn-sm mx-1"
                    title="View Details">
                    View
                </a>

                <!-- Delete button -->
                <a href="#"
                    data-id="{{ $teacher->teacher->id }}"
                    data-type="teacher"
                    class="btn btn-danger btn-sm mx-1 del-row"
                    title="Delete Row">
                    Delete
                </a>

                <a href="#"
                    data-bs-toggle="modal"
                    data-bs-target="#teacherEdit{{ $teacher->teacher->id }}"
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
                    <span class="fw-semibold">No teachers yet</span>
                    <small class="text-secondary">New records will appear here once received.</small>
                </div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

@foreach($teachers as $teacher)
<!-- Update Student Modal -->
<div class="modal fade" id="teacherEdit{{ $teacher->teacher->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('users.update') }}">
                @csrf

                <!-- REQUIRED -->
                <input type="hidden" name="id" value="{{ $teacher->teacher->id }}">
                <input type="hidden" name="type" value="teacher">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email"
                            name="teacher_email"
                            class="form-control @error('teacher_email') is-invalid @enderror"
                            value="{{ old('teacher_email', $teacher->email) }}">
                        @error('teacher_email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- phone -->
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text"
                            name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $teacher->teacher->phone_number) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- education_level -->
                    <div class="mb-3">
                        <label class="form-label">Education Level</label>
                        <input type="text"
                            name="education_level"
                            class="form-control @error('education_level') is-invalid @enderror"
                            value="{{ old('education_level', $teacher->teacher->education_level) }}">
                        @error('education_level')
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

@foreach($teachers as $teacher)
<div class="modal fade" id="teacherDetails{{ $teacher->teacher->id }}" tabindex="-1" aria-labelledby="teacherDetailsLabel{{ $teacher->teacher->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="teacherDetailsLabel{{ $teacher->teacher->id }}">
                    Teacher Details #{{ $teacher->teacher->id }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <h6 class="fw-bold">Personal Information</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-3">IC</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->ic }}</dd>

                            <dt class="col-sm-3">Gender</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->gender }}</dd>

                            <dt class="col-sm-3">Birth Date</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->birth_date }}</dd>

                            <dt class="col-sm-3">Phone</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->phone_number }}</dd>

                            <dt class="col-sm-3">Address</dt>
                            <dd class="col-sm-9">
                                {{ collect([
                                        $teacher->teacher->street ?? 'N/A',
                                        $teacher->teacher->area,
                                        $teacher->teacher->city,
                                        $teacher->teacher->state,
                                        $teacher->teacher->zip
                                    ])->filter()->implode(', ') }}
                            </dd>

                            <dt class="col-sm-3">Education Level</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->education_level }}</dd>

                            <dt class="col-sm-3">Field of Expertise</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->field_of_expertise }}</dd>

                            <dt class="col-sm-3">Experience Years</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->experience_years }}</dd>

                            <dt class="col-sm-3">Experience Details</dt>
                            <dd class="col-sm-9">{{ $teacher->teacher->experience_details }}</dd>
                        </dl>
                    </div>
                </div>

                <div class="card shadow-sm border mt-3">
                    <div class="card-header">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-people-fill me-2"></i>Responsible Students
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($teacher->teacher->student as $student)
                            <div class="list-group-item d-flex align-items-center px-4 py-3">
                                <div class="bg-light rounded-circle p-2 me-3 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <span class="fw-bold small">{{ substr($student->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ $student->name }}</h6>
                                    <small class="text-muted" style="font-size: 0.8rem;">Grade: {{ $student->grade ?? 'N/A' }}</small>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-person-x display-6 mb-2"></i>
                                <p class="mb-0">No students assigned yet.</p>
                            </div>
                            @endforelse
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


@foreach($teachers as $teacher)
<x-qr-modal
    :id="'teacher-'.$teacher->teacher->id"
    :name="$teacher->teacher->name"
    :qr_code="$teacher->teacher->qr_code" />
@endforeach

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->any())
        console.log("Validation errors detected."); // Debug check 1    

        var type = "{{ old('type') }}";
        var id = "{{ old('id') }}";

        console.log("Type:", type, "ID:", id); // Debug check 2

        if (type === 'teacher' && id) {
            var modalId = 'teacherEdit' + id;
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