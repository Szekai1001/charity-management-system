@extends('layout.admin')
@include('components.alerts')
@section('content')
<style>
    /* Only show QR code when printing */
    @media print {
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

        #print-area {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            /* center perfectly */
            background: white;
            padding: 20px;
            text-align: center;
        }
    }
</style>

<ul class="nav nav-tabs" id="userTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'students' ? 'active' : '' }}" href="?tab=students" role="tab">
            Students
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'teachers' ? 'active' : '' }}" href="?tab=teachers" role="tab">
            Teachers
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'beneficiaries' ? 'active' : '' }}" href="?tab=beneficiaries" role="tab">
            Beneficiaries
        </a>
    </li>

</ul>

<div class="tab-content mt-5" id="userTabContent">

    {{-- Students Tab --}}
    <div class="tab-pane fade {{ $activeTab == 'students' ? 'show active' : '' }}" id="students" role="tabpanel">
        <div class="card p-3 border-0 shadow">

            <div class="d-flex gap-3 align-items-center mb-2">
                <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-semibold mb-0">Student records</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                        {{ $approvedStudents->count() }} students
                    </span>
                </div>
            </div>

            <div class="card p-3 border-0 shasow-sm bg-info-subtle mb-5">
                <div class="d-flex align-items-center gap-3 ">
                    <i class="bi bi-exclamation-circle-fill fs-6"></i>
                    The available QR code is for attendance tracking and can be printed for student use. Please also ensure that each student is assigned to a teacher accordingly.
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text" id="search-addon">
                            <i class="bi bi-search"></i> <!-- Needs Bootstrap Icons -->
                        </span>
                        <input type="text" data-type="student" data-target="student-table" placeholder="Search by email, name, or IC ..." class="form-control filter-input">
                    </div>
                </div>

                <div class="col-md-6 d-flex justify-content-end mb-3">
                    <!-- Filter Dropdown -->
                    <div class="dropdown me-3">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-sliders me-1"></i> Filter
                        </button>
                        <div class="dropdown-menu p-3 shadow" aria-labelledby="filterDropdown" style="min-width: 250px;">

                            <!-- Grade filter -->
                            <div class="mb-3">
                                <label for="grade" class="form-label">Select grade</label>
                                <select name="grade" id="grade" class="form-select filter-input">
                                    <option value="" selected>--All grades--</option>
                                    @foreach($approvedStudents->pluck('user.student.grade')->unique() as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Teacher filter -->
                            <div class="mb-3">
                                <label for="assignedTeacher" class="form-label">Select Teacher</label>
                                <select class="form-select filter-input" id="assignedTeacher" name="assignedTeacher">
                                    <option value="" selected>--All teachers--</option>
                                    @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->teacher->id }}">
                                        {{ $teacher->teacher->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="student-table">
                @include('admin.partial.approved-students', ['approvedStudents' => $approvedStudents, 'teachers' => $teachers])
            </div>
        </div>

    </div>


    {{-- Teachers Tab --}}
    <div class="tab-pane fade {{ $activeTab == 'teachers' ? 'show active' : '' }}" id="teachers" role="tabpanel">

        <div class="card p-3 border-0 shadow">


            <div class="d-flex gap-3 align-items-center mb-2">
                <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-semibold mb-0">Teachers records</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                        {{ $teachers->count() }} teachers
                    </span>
                </div>
            </div>
            <div class="card p-3 border-0 shasow-sm bg-info-subtle mb-5">
                <div class="d-flex align-items-center gap-3 ">
                    <i class="bi bi-exclamation-circle-fill fs-6"></i>
                    The available QR code is for attendance tracking and can be printed for teacher use.
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="search-addon">
                            <i class="bi bi-search"></i> <!-- Needs Bootstrap Icons -->
                        </span>
                        <input type="text" data-type="teacher" data-target="teacher-table" placeholder="Search by email, name, or IC ..." class="form-control search-input">
                    </div>
                </div>
            </div>
            <div id="teacher-table">
                @include('admin.partial.approved-teachers', ['teachers' => $teachers])
            </div>
        </div>
    </div>

    {{-- Beneficiaries Tab --}}
    <div class="tab-pane fade {{ $activeTab == 'beneficiaries' ? 'show active' : '' }}" id="beneficiaries" role="tabpanel">
        <div class="card p-3 border-0 shadow">

            <div class="d-flex gap-3 align-items-center mb-5">
                <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                <div class="d-flex align-items-center gap-2">
                    <h4 class="fw-semibold mb-0">Beneficiary records</h4>
                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">
                        {{ $approvedBeneficiaries->count() }} beneficiaries
                    </span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group mb-3">
                        <span class="input-group-text" id="search-addon">
                            <i class="bi bi-search"></i> <!-- Needs Bootstrap Icons -->
                        </span>
                        <input type="text" data-type="beneficiary" data-target="beneficiary-table" placeholder="Search by email, name, or IC ..." class="form-control search-input">
                    </div>
                </div>
            </div>
            <div id="beneficiary-table">
                @include('admin.partial.approved-beneficiary', ['approvedBeneficiaries' => $approvedBeneficiaries])
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendRequest = (type) => {
            const params = {
                query: document.querySelector(`input[data-type="${type}"]`)?.value || '',
                grade: document.getElementById('grade')?.value || '',
                assignedTeacher: document.getElementById('assignedTeacher')?.value || '',
                type: type
            };

            fetch("{{ route('users.filter') }}?" + new URLSearchParams(params)) //- Converts the params object into a URL query string
                .then(response => response.text())
                .then(html => {
                    document.getElementById(`${type}-table`).innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        };

        // Handle typing in search
        document.querySelector('input[data-type="student"]')?.addEventListener('keyup', () => sendRequest('student'));
        document.querySelector('input[data-type="teacher"]')?.addEventListener('keyup', () => sendRequest('teacher'));
        document.querySelector('input[data-type="beneficiary"]')?.addEventListener('keyup', () => sendRequest('beneficiary'));



        // Handle dropdown changes
        document.querySelectorAll('select.filter-input').forEach(select => {
            select.addEventListener('change', () => sendRequest('student'));
        });


        document.addEventListener('click', function(e) {
            // Check if the clicked element has the class 'del-row'
            if (e.target && e.target.classList.contains('del-row')) {
                e.preventDefault();

                const btn = e.target; // The clicked button
                const id = btn.dataset.id;
                const type = btn.dataset.type;

                if (!confirm('Are you sure you want to delete this record?')) return;

                fetch(`{{ route('users.delete') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            id: id,
                            type: type
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove the row
                            btn.closest('tr').remove();
                            alert('Record deleted successfully');
                        } else {
                            alert('Delete failed. Please try again.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        });

    });
</script>


@endsection