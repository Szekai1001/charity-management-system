@extends('layout.teacher')
@include('components.alerts')
@section('content')

<style>

</style>
<div id="notification-area"></div>

{{-- Restored standard nav-tabs. Added overflow-auto for mobile horizontal scrolling --}}
<div class="mb-4 overflow-auto">
    <ul class="nav nav-tabs flex-nowrap text-nowrap" id="teacherAttendanceTab" role="tabList">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'studentAttendanceToggle' ? 'active' : '' }}" href="?tab=studentAttendanceToggle">
                Daily Attendance Control
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'viewStudentAttendance' ? 'active' : '' }}" href="?tab=viewStudentAttendance">
                View Student Attendances
            </a>
        </li>
    </ul>
</div>

<div class="tab-content">

    <div class="tab-pane fade {{ $activeTab == 'studentAttendanceToggle' ? 'show active' : '' }}" id="studentAttendanceToggle">


        <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">

            {{-- Remove w-100 and w-md-auto from the form tag --}}
            <form action="{{ route('attendance.start') }}" method="POST" id="formStart"
                onsubmit="return confirm('Start attendance for today?')">
                @csrf
                {{-- Keep w-100 on the button so it fills the form width --}}
                <button type="submit" class="btn btn-success px-4 py-2 rounded-3 w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-play-circle me-2 fs-5"></i> Start Attendance
                </button>
            </form>

            <form action="{{ route('attendance.reset') }}" method="POST"
                onsubmit="return confirm('Are you sure you want to delete attendance for today? This cannot be undone.')">
                @csrf
                <button type="submit" class="btn btn-danger px-4 py-2 rounded-3 w-100 d-flex align-items-center justify-content-center">
                    <i class="bi bi-trash3 me-2 fs-5"></i> Delete Attendance
                </button>
            </form>

        </div>

        <div class="row g-3 mb-4">
            {{-- Kept your original column sizes, they were good --}}
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Total Students</p>
                                <h3 class="fw-bold mb-0">{{ $studentAttendances->count() }}</h3>
                            </div>
                            <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Present</p>
                                <h3 class="fw-bold mb-0 text-dark">{{ $present }}</h3>
                            </div>
                            <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                                <i class="bi bi-check-lg fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Absent</p>
                                <h3 class="fw-bold mb-0 text-dark">{{ $absent }}</h3>
                            </div>
                            <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-x-lg fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-uppercase text-muted small fw-bold mb-1">Excused</p>
                                <h3 class="fw-bold mb-0 text-dark">{{ $excused }}</h3>
                            </div>
                            <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                                <i class="bi bi-envelope-paper-fill fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{route('attendance.studentUpdate') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                {{-- Card Header: Use flex-wrap to handle smaller screens without breaking layout --}}
                <div class="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">

                    <h5 class="fw-bold text-dark mb-0">Attendance List</h5>

                    {{-- Actions: Stack on mobile, inline on desktop --}}
                    {{-- Parent: d-grid stretches items on mobile. d-sm-flex makes them natural width on desktop. --}}
                    <div class="d-grid gap-2 d-sm-flex align-items-sm-center">

                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" id="allPresent" class="btn btn-outline-success fw-medium btn-sm">
                                <i class="bi bi-check-all me-1"></i> All Present
                            </button>
                            <button type="button" id="allAbsent" class="btn btn-outline-danger fw-medium btn-sm">
                                <i class="bi bi-x-circle me-1"></i> All Absent
                            </button>
                        </div>

                        {{-- Removed w-100, w-sm-auto, and ms-sm-2 (gap-2 handles the spacing now) --}}
                        <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                            <i class="bi bi-save me-1"></i> Save Changes
                        </button>

                    </div>
                </div>

                {{-- Table Responsive Wrapper: Only shows scrollbar on mobile if needed --}}
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="text-secondary small text-uppercase fw-bold">
                                <th class="ps-4 py-3" style="width: 50px;">#</th>
                                <th class="py-3">Student</th>
                                <th class="py-3 text-center">Date</th>
                                <th class="py-3 text-center">Check In Time</th>
                                <th class="py-3 text-center">Check Out Time</th>
                                <th class="py-3 text-center">Current Status</th>
                                <th class="py-3 text-center pe-4" style="width: 200px;">Update Action</th>
                            </tr>
                        </thead>
                        <tbody class="border-top-0">
                            @forelse($studentAttendances as $studentAttendance)
                            <tr>
                                <td class="ps-4 text-muted">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-3 flex-shrink-0" style="width: 40px; height: 40px;">
                                            {{ substr($studentAttendance->student->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-dark fw-bold">{{ $studentAttendance->student->name }}</div>
                                            <div class="text-muted small">Email: {{ $studentAttendance->student->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center text-muted small text-nowrap">
                                    {{ $studentAttendance->date }}
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="text-success mb-1"><i class="bi bi-clock text-success me-1"></i>{{ $studentAttendance->check_in_time ?? '--:--' }}</span>
                                </td>
                                <td class="text-center text-nowrap">
                                    <span class="text-danger"><i class="bi bi-clock text-danger me-1"></i>{{ $studentAttendance->check_out_time ?? '--:--' }}</span>
                                </td>
                                <td class="text-center">
                                    @if($studentAttendance->status === 'present')
                                    <span class="badge bg-success">Present</span>
                                    @elseif($studentAttendance->status === 'absent')
                                    <span class="badge bg-danger">Absent</span>
                                    @else
                                    <span class="badge bg-warning text-dark">Excused</span>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <select name="status[{{ $studentAttendance->id }}]" class="form-select form-select-sm shadow-sm border-0 bg-light fw-medium">
                                        <option value="present" {{$studentAttendance->status === 'present' ? 'selected' : ''}}>Present</option>
                                        <option value="absent" {{$studentAttendance->status === 'absent' ? 'selected' : ''}}>Absent</option>
                                        <option value="excused" {{$studentAttendance->status === 'excused' ? 'selected' : ''}}>Excused</option>
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <div class="bg-light rounded-circle p-3 mb-3">
                                            <i class="bi bi-clipboard-x display-6 text-secondary opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold text-dark">No Records Found</h6>
                                        <p class="small mb-0">Adjust your filters to see student data.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-light py-2 px-4">
                    <small class="text-muted fst-italic">* Click "Save Changes" to update the database.</small>
                </div>
            </div>

        </form>

    </div>

    <div class="tab-pane fade {{ $activeTab == 'viewStudentAttendance' ? 'show active' : '' }}" id="viewStudentAttendance">

        <div class="card shadow-sm border-0 rounded-4 p-3 col-md-12 mt-3 mb-5">
            <div class="row gy-3 justify-content-between align-items-end">

                <div class="col-12 col-md-5" style="max-width: 400px;">
                    {{-- Container: White background, rounded pill shape, subtle shadow --}}
                    <div class="input-group bg-white border rounded-pill shadow-sm overflow-hidden">

                        {{-- Icon: Transparent background to blend in --}}
                        <span class="input-group-text border-0 bg-transparent ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        {{-- Input: No border, no focus shadow (so the ring doesn't cut the pill) --}}
                        <input type="text"
                            class="form-control border-0 shadow-none"
                            id="sa_student_details"
                            list="studentOptions"
                            placeholder="Search Student Name or IC..."
                            autocomplete="off">

                        <datalist id="studentOptions">
                            @foreach($students as $student)
                            <option value="{{ $student->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div class="col-auto dropdown">
                    <button class="btn btn-light border d-flex align-items-center px-3 shadow-sm"
                        type="button" id="studentAttendanceFilterDropdown" data-bs-toggle="dropdown"
                        data-bs-display="static" aria-expanded="false">
                        <i class="bi bi-sliders me-2"></i> Filter
                    </button>

                    {{-- Dropdown Menu --}}
                    <div class="dropdown-menu dropdown-menu-end p-3 shadow rounded-3"
                        aria-labelledby="studentAttendanceFilterDropdown"
                        style="min-width: 320px;">

                        {{-- 1. Filter Mode (Added mb-3) --}}
                        <div class="col-12 mb-3">
                            <label for="studentFilterOption" class="form-label fw-semibold">
                                <i class="bi bi-funnel me-1 text-primary"></i> Filter Mode
                            </label>
                            <select name="studentfilterOption" id="studentFilterOption" class="form-select">
                                <option value="" disabled selected>-- Select Filter Option --</option>
                                <option value="month">Month</option>
                                <option value="date">Date</option>
                            </select>
                        </div>

                        {{-- 2. Date/Month Inputs (Added mb-3) --}}
                        <div class="col-12 mb-3">
                            <div id="filterMonthGroup" class="d-none">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="sa_year" class="form-label fw-semibold">Year</label>
                                        <input type="number" id="sa_year" name="sa_year" class="form-control filter-trigger"
                                            value="{{ $sa_year ?? '' }}" min="2000" max="2100"
                                            placeholder="e.g. 2025">
                                    </div>
                                    <div class="col-6">
                                        <label for="sa_month" class="form-label fw-semibold">Month</label>
                                        <select name="sa_month" id="sa_month" class="form-select filter-trigger">
                                            <option value="">-- All --</option>
                                            @foreach (range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ (int)$m === (int)($month ?? 0) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="filterDateGroup" class="d-none">
                                <label for="sa_date" class="form-label fw-semibold">Date</label>
                                <input type="date" name="sa_date" id="sa_date" class="form-control filter-trigger">
                            </div>
                        </div>

                        {{-- 3. Status (Last item, no margin needed) --}}
                        <div class="col-12">
                            <label for="sa_status" class="form-label fw-semibold">
                                <i class="bi bi-clipboard-check me-1 text-success"></i> Status
                            </label>
                            <select name="sa_status" id="sa_status" class="form-select filter-trigger">
                                <option value="">-- All Statuses --</option>
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="excused">Excused</option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div id="teacherViewstudentAttendanceTable">
            @include('user.teacher.table.studentAttendance')
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- 1. Quick Actions (Mark All Present/Absent) ---
            const btnPresent = document.getElementById('allPresent');
            const btnAbsent = document.getElementById('allAbsent');

            if (btnPresent) {
                btnPresent.addEventListener('click', function() {
                    if (confirm('Are you sure you want to mark all Student as Present?')) {
                        document.querySelectorAll('select.form-select').forEach(select => {
                            select.value = 'present';
                        });
                    }
                });
            }

            if (btnAbsent) {
                btnAbsent.addEventListener('click', function() {
                    if (confirm('Are you sure you want to mark all Student as Absent?')) {
                        document.querySelectorAll('select.form-select').forEach(select => {
                            select.value = 'absent';
                        });
                    }
                });
            }


            // --- 2. AJAX Filter Logic ---
            const refreshStudentTable = () => {
                const yearVal = document.getElementById('sa_year')?.value || '';
                const monthVal = document.getElementById('sa_month')?.value || '';
                const dateVal = document.getElementById('sa_date')?.value || '';
                const statusVal = document.getElementById('sa_status')?.value || '';
                const student_details = document.getElementById('sa_student_details')?.value || '';

                const params = new URLSearchParams({
                    saYear: yearVal,
                    saMonth: monthVal,
                    saDate: dateVal,
                    saStatus: statusVal,
                    saStudentDetails: student_details,
                    report: 'attendance',
                    type: 'student'
                });

                fetch("{{ route('attendance.studentFilter') }}?" + params.toString())
                    .then(response => {
                        if (!response.ok) throw new Error('Network response was not ok');
                        return response.json();
                    })
                    .then(data => {
                        const container = document.getElementById('teacherViewstudentAttendanceTable');
                        if (container && data.teacherHtml) {
                            container.innerHTML = data.teacherHtml;
                        }
                    })
                    .catch(error => console.error('Student Filter Error:', error));
            };


            // --- 3. Event Listeners for Filters ---
            const filterOption = document.getElementById('studentFilterOption');
            const mGroup = document.getElementById('filterMonthGroup');
            const dGroup = document.getElementById('filterDateGroup');

            if (filterOption) {
                filterOption.addEventListener('change', function() {
                    const val = this.value;

                    if (val === 'month') {
                        mGroup.classList.remove('d-none');
                        dGroup.classList.add('d-none');
                        if (document.getElementById('sa_date')) document.getElementById('sa_date').value = '';
                    } else if (val === 'date') {
                        mGroup.classList.add('d-none');
                        dGroup.classList.remove('d-none');
                        if (document.getElementById('sa_month')) document.getElementById('sa_month').value = '';
                    }

                    refreshStudentTable();
                });
            }

            // --- 4. PAGINATION INTERCEPTOR (Add this) ---
            document.addEventListener('click', function(e) {
                // 1. Check if the clicked element is inside a pagination link
                const link = e.target.closest('.pagination a');
                const container = document.getElementById('teacherViewstudentAttendanceTable');

                // 2. Only run if a link was clicked AND it is inside YOUR specific table container
                if (link && container && container.contains(link)) {
                    e.preventDefault(); // STOP the browser from following the link normally

                    const url = link.href; // Get the URL (e.g. ...?page=2&saYear=2025...)

                    container.style.opacity = '0.5'; // Visual feedback that it's loading

                    fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest' // Tell Laravel this is an AJAX request
                            }
                        })
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(data => {
                            // Update the HTML
                            if (data.teacherHtml) {
                                container.innerHTML = data.teacherHtml;
                            }
                            container.style.opacity = '1';
                        })
                        .catch(error => {
                            console.error('Pagination Error:', error);
                            container.style.opacity = '1';
                        });
                }
            });

            document.querySelectorAll('.filter-trigger').forEach(el => {
                el.addEventListener('change', refreshStudentTable);
                if (el.tagName === 'INPUT' && el.type === 'number') {
                    el.addEventListener('keyup', refreshStudentTable);
                }
            });

            const searchInput = document.getElementById('sa_student_details');
            let debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(refreshStudentTable, 500);
                });
            }

        });
    </script>

    @endsection