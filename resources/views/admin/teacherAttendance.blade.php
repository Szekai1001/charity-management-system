@extends('layout.admin')
@section('content')
@include('components.alerts')

{{-- TABS --}}
<ul class="nav nav-tabs mb-4" id="teacherAttendanceTab" role="tabList">
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'viewTeacherAttendanceToggle' ? 'active' : '' }}" href="?tab=viewTeacherAttendanceToggle">
            Daily Attendance Control
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $activeTab === 'viewTeacherAttendance' ? 'active' : '' }}" href="?tab=viewTeacherAttendance">
            View Teacher Attendance
        </a>
    </li>
</ul>

<div class="tab-content">

    {{-- ================= TAB 1: DAILY CONTROL ================= --}}
    <div class="tab-pane fade {{ $activeTab == 'viewTeacherAttendanceToggle' ? 'show active' : '' }}" id="teacherAttendanceToggleSection">
        <div class="card border-0 shadow-sm mb-4 p-2">
            <div class="card-header bg-white p-3 d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h5 class="mb-0"><i class="bi bi-qr-code-scan text-primary me-2"></i> Daily Attendance Session</h5>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('admin.startTeacherAttendance') }}" method="POST">
                        @csrf
                        <button class="btn btn-primary shadow-sm" onclick="return confirm('Start new session?')">
                            <i class="bi bi-play-circle-fill me-2"></i> Open Session
                        </button>
                    </form>
                    <form action="{{ route('admin.deleteTeacherAttendance') }}" method="POST">
                        @csrf
                        <button class="btn btn-outline-danger shadow-sm" onclick="return confirm('Wipe today\'s data?')">
                            <i class="bi bi-arrow-counterclockwise me-2"></i> Reset
                        </button>
                    </form>
                </div>
            </div>

            @if($teacherTodayAttendances->count() > 0)
            <div class="alert alert-success mx-3"><strong>Session Live:</strong> Teachers can scan now.</div>
            @else
            <div class="alert alert-warning mx-3"><strong>Session Not Started:</strong> Click Open Session.</div>
            @endif

            <div class="card-body p-0">
                <form action="{{ route('attendance.teacherUpdate') }}" method="POST">
                    @csrf @method('PUT')
                    <div class="d-flex justify-content-end mb-3 me-3">
                        <button type="submit" class="btn btn-primary shadow-sm">Save All Changes</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-center">
                                <tr>
                                    <th style="width:40px">#</th>
                                    <th class="text-start ps-3">Teacher Name</th>
                                    <th>Check In</th>
                                    <th>Check Out</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @forelse($teacherTodayAttendances as $attendance)
                                <tr>
                                    <td style="width:40px;">{{ $loop->iteration }}</td>
                                    <td class="text-start ps-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                                                style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                                                {{ substr($attendance->teacher->name ?? 'U', 0, 1) }}
                                            </div>
                                            {{ $attendance->teacher->name }}
                                        </div>
                                    </td>
                                    <td><span class="mb-1 small"><i class="bi bi-clock text-success me-1"></i>{{ $attendance->check_in_time ?? '--:--' }}</td>
                                    <td><span class="small"><i class="bi bi-clock text-danger me-1"></i>{{ $attendance->check_out_time ?? '--:--' }}</td>
                                    <td>
                                        @if($attendance->status === 'present')
                                        <span class="badge bg-success">Present</span>
                                        @elseif($attendance->status === 'absent')
                                        <span class="badge bg-warning text-dark">Absent</span>
                                        @else
                                        <span class="badge bg-danger">Excused</span>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="status[{{ $attendance->id }}]" class="form-select form-select-sm" style="width: 120px; margin:auto;">
                                            <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Present</option>
                                            <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Absent</option>
                                            <option value="excused" {{ $attendance->status == 'excused' ? 'selected' : '' }}>Excused</option>
                                        </select>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="p-4 text-muted">No session started yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- ================= TAB 2: HISTORY & FILTER ================= --}}
    <div class="tab-pane fade {{ $activeTab == 'viewTeacherAttendance' ? 'show active' : '' }}" id="teacherAttendanceSection">

        <div class="card p-3 border-0 shadow-sm mb-4">
            <div class="d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center gap-3">
                <div class="flex-grow-1" style="max-width: 400px; min-width: 250px;">

                    <div class="input-group bg-white border rounded-pill shadow-sm overflow-hidden">

                        <span class="input-group-text border-0 bg-transparent ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        <input type="text"
                            class="form-control border-0 shadow-none"
                            id="ta_teacher_details"
                            list="teacherOptions"
                            placeholder="Search Teacher Name or IC..."
                            autocomplete="off">

                        <datalist id="teacherOptions">
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">

                    {{-- Export Buttons --}}
                    <div class="d-flex align-items-center gap-2">
                        <a id="btn-export-excel" href="{{ route('report.export', array_merge(request()->query(), ['report' => 'attendance', 'type' => 'teacher'])) }}" class="btn btn-success shadow-sm">
                            <i class="bi bi-file-earmark-excel-fill"></i> Excel
                        </a>
                        <a id="btn-export-pdf" href="{{ route('report.export.pdf', array_merge(request()->query(), ['report' => 'attendance', 'type' => 'teacher'])) }}" class="btn btn-danger shadow-sm">
                            <i class="bi bi-file-earmark-pdf-fill"></i> PDF
                        </a>

                        {{-- Filter Dropdown --}}
                        <div class="dropdown">
                            <button class="btn btn-light border dropdown-toggle d-flex align-items-center px-3 shadow-sm" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                <i class="bi bi-sliders me-2"></i> Filter
                            </button>

                            <div class="dropdown-menu dropdown-menu-end p-3 shadow rounded-3"
                                aria-labelledby="teacherAttendanceFilterDropdown"
                                style="min-width: 320px;">

                                <form id="filterForm" onsubmit="return false;">
                                    <div class="row g-3">

                                        {{-- View Mode --}}
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Filter Mode</label>
                                            <select id="teacherFilterOption" class="form-select filter-trigger">
                                                <option value="date" selected>Daily View</option>
                                                <option value="month">Monthly View</option>
                                            </select>
                                        </div>

                                        {{-- Date Input (Default) --}}
                                        <div class="col-12" id="filterDateGroup">
                                            <label class="form-label fw-bold small text-muted">Select Date</label>
                                            <input type="date" id="ta_date" class="form-control filter-trigger"
                                                value="{{ date('Y-m-d') }}">
                                        </div>

                                        {{-- Month/Year Inputs (Hidden Initially) --}}
                                        <div class="col-12 d-none" id="filterMonthGroup">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="form-label fw-bold small text-muted">Year</label>
                                                    <input type="number" id="ta_year" class="form-control filter-trigger"
                                                        value="{{ date('Y') }}">
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label fw-bold small text-muted">Month</label>
                                                    <select id="ta_month" class="form-select filter-trigger">
                                                        <option value="">--All Months--</option>
                                                        @foreach (range(1, 12) as $m)
                                                        <option value="{{ $m }}" {{ $m == date('n') ? 'selected' : '' }}>
                                                            {{ \Carbon\Carbon::create()->month($m)->format('M') }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Status --}}
                                        <div class="col-12">
                                            <label class="form-label fw-bold small text-muted">Status</label>
                                            <select id="ta_status" class="form-select filter-trigger">
                                                <option value="">All Statuses</option>
                                                <option value="present">Present</option>
                                                <option value="absent">Absent</option>
                                                <option value="excused">Excused</option>
                                            </select>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> {{-- End Wrapper --}}

        {{-- TABLE CONTAINER --}}
        <div id="teacherAttendanceTableWrapper">
            <div id="teacherAttendanceTable">
                @include('admin.report.tables.teacherAttendance')
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. CONFIGURATION
        const getEl = (id) => document.getElementById(id);

        const ROUTES = {
            filter: "{{ route('attendance.teacherFilter') }}",
            excel: "{{ route('report.export') }}",
            pdf: "{{ route('report.export.pdf') }}"
        };

        const els = {
            mode: getEl('teacherFilterOption'),
            dateGroup: getEl('filterDateGroup'),
            monthGroup: getEl('filterMonthGroup'),
            details: getEl('ta_teacher_details'),
            date: getEl('ta_date'),
            year: getEl('ta_year'),
            month: getEl('ta_month'),
            status: getEl('ta_status'),
            table: getEl('teacherAttendanceTable'),
            // FIX 1: Ensure this ID matches the HTML
            wrapper: getEl('teacherAttendanceTableWrapper'),
            btnExcel: getEl('btn-export-excel'),
            btnPdf: getEl('btn-export-pdf')
        };

        function toggleViewMode() {
            if (els.mode.value === 'month') {
                els.monthGroup.classList.remove('d-none');
                els.dateGroup.classList.add('d-none');
                els.date.value = '';
            } else {
                els.monthGroup.classList.add('d-none');
                els.dateGroup.classList.remove('d-none');
                els.month.value = '';
                if (!els.date.value) els.date.value = new Date().toISOString().split('T')[0];
            }
            fetchData();
        }

        // 2. MAIN REFRESH LOGIC
        function fetchData() {
            if (!els.table) return;

            els.table.style.opacity = '0.5';

            const params = new URLSearchParams({
                taTeacherDetails: els.details.value,
                taYear: els.year.value,
                taMonth: els.month.value,
                taDate: els.date.value,
                taStatus: els.status.value,
                report: 'attendance',
                type: 'teacher'
            });
            const queryString = params.toString();

            if (els.btnExcel) els.btnExcel.href = `${ROUTES.excel}?${queryString}`;
            if (els.btnPdf) els.btnPdf.href = `${ROUTES.pdf}?${queryString}`;

            fetch(`${ROUTES.filter}?${queryString}`)
                .then(response => response.json()) // FIX 2: Use .json() like the Student module
                .then(data => {
                    // FIX 2: Access .adminHtml property
                    if (els.table) els.table.innerHTML = data.adminHtml;
                    els.table.style.opacity = '1';
                })
                .catch(err => {
                    console.error('Error loading teacher table:', err);
                    els.table.style.opacity = '1';
                });
        }

        // 3. FILTER EVENT LISTENERS
        if (els.mode) els.mode.addEventListener('change', toggleViewMode);

        let debounceTimer;
        if (els.details) {
            els.details.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchData, 500);
            });
        }

        [els.date, els.year, els.month, els.status].forEach(input => {
            if (input) input.addEventListener('change', fetchData);
        });

        // 4. BULK ACTIONS (Event Delegation)
        if (els.wrapper) {

            // A. Handle "Select All" Checkbox
            els.wrapper.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-all')) { // Ensure your TH checkbox has class 'select-all'
                    const isChecked = e.target.checked;
                    // FIX 3: Use 'els.wrapper', not 'wrapper'
                    els.wrapper.querySelectorAll('input[name="teacherAttendance_ids[]"]').forEach(cb => {
                        cb.checked = isChecked;
                    });
                }
            });

            // B. Handle "Apply Bulk Status" Button
            els.wrapper.addEventListener('click', function(e) {

                // FIX 4: Use closest() for Icon clicks
                const applyBtn = e.target.closest('#apply_td_bulk_status');

                if (applyBtn) {
                    e.preventDefault();

                    const statusVal = getEl('td_bulk_status')?.value;
                    if (!statusVal) return alert('Please select a status first.');

                    // FIX 3: Use 'els.wrapper', not 'wrapper'
                    const checkedRows = els.wrapper.querySelectorAll('input[name="teacherAttendance_ids[]"]:checked');

                    if (checkedRows.length === 0) return alert('Please select at least one teacher.');

                    checkedRows.forEach(chk => {
                        const row = chk.closest('tr');
                        const select = row.querySelector('select.form-select');
                        if (select) {
                            select.value = statusVal;
                            row.classList.add('table-warning');
                            setTimeout(() => row.classList.remove('table-warning'), 500);
                        }
                    });
                }
            });
        }

        document.addEventListener('click', function(e) {
            // 1. Check if the clicked element is inside a pagination link
            const link = e.target.closest('.pagination a');
            const container = document.getElementById('teacherAttendanceTable');

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
                        if (data.adminHtml) {
                            container.innerHTML = data.adminHtml;
                        }
                        container.style.opacity = '1';
                    })
                    .catch(error => {
                        console.error('Pagination Error:', error);
                        container.style.opacity = '1';
                    });
            }
        });


        // 5. INITIAL RUN
        fetchData();
    });
</script>
@endsection