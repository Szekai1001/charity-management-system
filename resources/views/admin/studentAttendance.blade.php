@extends('layout.admin')

@section('content')
@include('components.alerts')

{{-- ========================================== --}}
{{-- TOOLBAR: SEARCH & ACTIONS           --}}
{{-- ========================================== --}}
<div class="card p-3 border-0 shadow-sm mb-4">
    <div class="d-flex flex-wrap flex-md-nowrap justify-content-between align-items-center gap-3">

        {{-- 1. LEFT SIDE: Student Search --}}
        <div class="flex-grow-1" style="max-width: 400px; min-width: 250px;">
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
                    placeholder="Search Student Name or IC"
                    autocomplete="off">

                <datalist id="studentOptions">
                    @foreach($students as $student)
                    <option value="{{ $student->name }}"></option>
                    @endforeach
                </datalist>
            </div>
        </div>

        {{-- 2. RIGHT SIDE: Action Buttons --}}
        <div class="d-flex flex-wrap justify-content-end align-items-center gap-2">

            {{-- Excel Button --}}
            <a id="btn-export-excel" href="#" class="btn btn-success d-flex align-items-center px-3 shadow-sm">
                <i class="bi bi-file-earmark-excel-fill me-2"></i> Excel
            </a>

            {{-- PDF Button --}}
            <a id="btn-export-pdf" href="#" class="btn btn-danger d-flex align-items-center px-3 shadow-sm">
                <i class="bi bi-file-earmark-pdf-fill me-2"></i> PDF
            </a>

            {{-- Filter Dropdown --}}
            <div class="dropdown">
                <button class="btn btn-light border d-flex align-items-center px-3 shadow-sm"
                    type="button" id="studentAttendanceFilterDropdown" data-bs-toggle="dropdown"
                    data-bs-display="static" aria-expanded="false">
                    <i class="bi bi-sliders me-2"></i> Filter
                </button>

                {{-- Dropdown Menu (Contains the Filters) --}}
                <div class="dropdown-menu dropdown-menu-end p-3 shadow rounded-3"
                    aria-labelledby="studentAttendanceFilterDropdown"
                    style="min-width: 320px;">

                    <form id="filterForm" onsubmit="return false;">
                        <div class="row g-3">

                            {{-- View Mode --}}
                            <div class="col-12">
                                <label class="form-label fw-bold small text-muted">Filter Mode</label>
                                <select id="studentFilterOption" class="form-select filter-trigger">
                                    <option value="date" selected>Daily View</option>
                                    <option value="month">Monthly View</option>
                                </select>
                            </div>

                            {{-- Date Input (Default) --}}
                            <div class="col-12" id="filterDateGroup">
                                <label class="form-label fw-bold small text-muted">Select Date</label>
                                <input type="date" id="sa_date" class="form-control filter-trigger"
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            {{-- Month/Year Inputs (Hidden Initially) --}}
                            <div class="col-12 d-none" id="filterMonthGroup">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">Year</label>
                                        <input type="number" id="sa_year" class="form-control filter-trigger"
                                            value="{{ date('Y') }}">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold small text-muted">Month</label>
                                        <select id="sa_month" class="form-select filter-trigger">
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
                                <select id="sa_status" class="form-select filter-trigger">
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

{{-- ========================================== --}}
{{-- TABLE WRAPPER                    --}}
{{-- ========================================== --}}
<div id="studentAttendanceTableWrapper">
    <div id="studentAttendanceTable">
        @include('admin.report.tables.studentAttendance')
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {

        //   Short cut function
        const getEl = (id) => document.getElementById(id);

        // 2. CONFIGURATION
        const ROUTES = {
            filter: "{{ route('attendance.studentFilter') }}",
            excel: "{{ route('report.export') }}",
            pdf: "{{ route('report.export.pdf') }}"
        };

        const els = {
            mode: getEl('studentFilterOption'),
            dateGroup: getEl('filterDateGroup'),
            monthGroup: getEl('filterMonthGroup'),
            details: getEl('sa_student_details'),
            date: getEl('sa_date'),
            year: getEl('sa_year'),
            month: getEl('sa_month'),
            status: getEl('sa_status'),
            table: getEl('studentAttendanceTable'), // The container for AJAX results
            wrapper: getEl('studentAttendanceTableWrapper'), // The static wrapper for Event Delegation
            btnExcel: getEl('btn-export-excel'),
            btnPdf: getEl('btn-export-pdf')
        };

        // 3. TOGGLE VISIBILITY (Date vs Month)
        function toggleViewMode() {
            if (els.mode.value === 'month') {
                els.monthGroup.classList.remove('d-none');
                els.dateGroup.classList.add('d-none');
                els.date.value = ''; // Clear date to avoid conflict
            } else {
                els.monthGroup.classList.add('d-none');
                els.dateGroup.classList.remove('d-none');
                els.month.value = ''; // Clear month to avoid conflict
                if (!els.date.value) els.date.value = new Date().toISOString().split('T')[0];
            }
            fetchData();
        }

        // 4. FETCH DATA (The Engine)
        function fetchData() {
            if (!els.table) return;

            els.table.style.opacity = '0.5';

            const params = new URLSearchParams({
                saStudentDetails: els.details.value,
                saYear: els.year.value,
                saMonth: els.month.value,
                saDate: els.date.value,
                saStatus: els.status.value,
                report: 'attendance',
                type: 'student'
            });

            const queryString = params.toString();

            if (els.btnExcel) els.btnExcel.href = `${ROUTES.excel}?${queryString}`;
            if (els.btnPdf) els.btnPdf.href = `${ROUTES.pdf}?${queryString}`;

            fetch(`${ROUTES.filter}?${queryString}`)
                .then(response => response.json())
                .then(data => {
                    els.table.innerHTML = data.adminHtml;
                    els.table.style.opacity = '1';
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    els.table.style.opacity = '1';
                });
        }

        // 5. EVENT LISTENERS

        // A. View Mode Toggle
        if (els.mode) els.mode.addEventListener('change', toggleViewMode);

        // B. Search Bar (Debounced)
        let debounceTimer;
        if (els.details) {
            els.details.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(fetchData, 500);
            });
        }

        // C. Other Filters
        [els.date, els.year, els.month, els.status].forEach(input => {
            if (input) input.addEventListener('change', fetchData);
        });

        // ============================================================
        // 6. BULK ACTIONS (Fixed Logic)
        // ============================================================

        if (els.wrapper) {

            // A. "Select All" Checkbox
            els.wrapper.addEventListener('change', function(e) {
                if (e.target.classList.contains('select-studentAttendance')) {
                    const isChecked = e.target.checked;
                    els.wrapper.querySelectorAll('input[name="studentAttendance_ids[]"]').forEach(cb => {
                        cb.checked = isChecked;
                    });
                }
            });

            // B. Apply Bulk Status
            els.wrapper.addEventListener('click', function(e) {

                // FIX: Use .closest() to handle clicks on the ICON inside the button
                const applyBtn = e.target.closest('#apply_td_bulk_status');

                if (applyBtn) {
                    e.preventDefault();

                    const statusVal = getEl('td_bulk_status')?.value;
                    if (!statusVal) return alert('Please select a status to apply');

                    const checkedRows = els.wrapper.querySelectorAll('input[name="studentAttendance_ids[]"]:checked');
                    if (checkedRows.length === 0) return alert('Please select at least one student');

                    checkedRows.forEach(chk => {
                        const row = chk.closest('tr');
                        const select = row.querySelector('select.form-select');
                        if (select) {
                            select.value = statusVal;
                            // Visual feedback
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
            const container = document.getElementById('studentAttendanceTable');

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

        // 7. INITIAL RUN
        fetchData();
    });
</script>
@endsection