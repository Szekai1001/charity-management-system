@extends('layout.admin')
@include('components.alerts')
@section('content')
<div class="col-12 mb-4">
    <div class="card border border-light shadow-sm rounded-3">

        {{-- Header: Clean split between Title and Data --}}
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center">

            <div class="d-flex align-items-center gap-3">
                <h6 class="mb-0 fw-bold text-dark text-nowrap">Salary Configuration</h6>

                <div class="vr text-secondary opacity-25 d-none d-md-block" style="height:45px;"></div>

                <div class="d-flex align-items-center bg-warning-subtle text-warning-emphasis rounded-3 px-3 py-2">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <span class="small lh-sm mb-0">
                        <strong>Teachers with a paid status will not be recalculated.</strong>
                    </span>
                </div>
            </div>

            <div class="text-end">
                <div class="text-uppercase text-secondary fw-bold mb-1" style="font-size: 10px; letter-spacing: 0.8px;">
                    Current Rate
                </div>
                <div class="fs-5 fw-bold text-primary lh-1">
                    RM {{ $payrate ?? '0.00' }} <span class="fs-6 text-secondary fw-normal">/hr</span>
                </div>
            </div>

        </div>

        {{-- Body: Standard, clean form --}}
        <div class="card-body px-4 py-4">
            <form action="{{ route('salary.calculate') }}" method="POST" id="payrateForm">
                @csrf
                <div class="row g-3 align-items-end">

                    <div class="col-md-4">
                        <label class="form-label fw-medium mb-1">New Hourly Rate <span class="text-danger">*</span></label>
                        <div class="input-group">

                            <span class="input-group-text bg-white text-muted border-end-0">RM</span>
                            <input type="number" step="0.01" id="payrate" name="payrate"
                                class="form-control border-start-0 ps-0"
                                placeholder="0.00" value="{{ old('payrate') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label  fw-medium mb-1">Select Month <span class="text-danger">*</span></label>
                        <input type="month" name="salary_month_year" id="selected_month_year"
                            class="form-control"
                            value="{{ \Carbon\Carbon::create($year, $month)->format('Y-m') }}">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-sm w-100 fw-medium"
                            onclick="return confirm('Are you sure? After clicking this, the salary will be visible to the teacher.');">
                            Calculate
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="col-12 mb-3">
    <div class="card p-3 shadow-sm border-0">

        {{-- Main Flex Container: Splits Export Buttons (Left) and Filters (Right) --}}
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

            {{-- LEFT SIDE: Export Actions --}}
            <div class="d-flex gap-2">
                <a id="excel-export-link" href="{{ route('report.export', array_merge(request()->query(), ['report' => 'salary'])) }}"
                    class="btn btn-success btn-sm d-flex align-items-center shadow-sm px-3">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i> Excel
                </a>

                <a id="pdf-export-link" href="{{ route('report.export.pdf', array_merge(request()->query(), ['report' => 'salary'])) }}"
                    class="btn btn-danger btn-sm d-flex align-items-center shadow-sm px-3">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> PDF
                </a>
            </div>

            {{-- RIGHT SIDE: Filters (Aligned nicely) --}}
            <div class="d-flex flex-wrap align-items-center gap-2">

                {{-- Year Filter --}}
                <div class="input-group input-group-sm" style="width: 140px;">
                    <span class="input-group-text bg-light fw-bold text-secondary">Year</span>
                    {{-- FIX: Use $year directly. If it's null, the placeholder shows. --}}
                    <input type="number" name="salary_year" id="year"
                        class="form-control filter-salary text-center"
                        value="{{ $year }}" placeholder="YYYY">
                </div>

                {{-- Month Filter --}}
                <div class="input-group input-group-sm" style="width: 180px;">
                    <span class="input-group-text bg-light fw-bold text-secondary">Month</span>
                    <select name="salary_month" id="month" class="form-select filter-salary">
                        <option value="">-- All Months --</option>
                        @foreach (range(1, 12) as $m)
                        {{-- FIX: Strict comparison. If $month is null/empty, nothing is selected (showing All Months) --}}
                        <option value="{{ $m }}" {{ (string)$m === (string)$month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('M') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div class="input-group input-group-sm" style="width: 160px;">
                    <span class="input-group-text bg-light fw-bold text-secondary">Status</span>
                    <select name="salary_payment_status" id="payment_status" class="form-select filter-salary">
                        <option value="">All</option>
                        {{-- FIX: Use the variable passed from controller --}}
                        <option value="unpaid" {{ ($salary_payment_status ?? '') === 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="paid" {{ ($salary_payment_status ?? '') === 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>

            </div>

        </div>
    </div>
</div>



<div class="col-12">
    <div id="salary-table">
        @include('admin.report.tables.salary')
    </div>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. Central function to get all current filter values
        const collectParams = () => {
            return {
                salary_year: document.getElementById('year')?.value || '',
                salary_month: document.getElementById('month')?.value || '',
                salary_payment_status: document.getElementById('payment_status')?.value || '',
            };
        };

        // 2. Update the Excel/PDF buttons so they include current filters
        const updateExportLink = () => {
            const params = collectParams();
            // Create a clean query string (removes empty values)
            const queryString = new URLSearchParams(
                Object.fromEntries(Object.entries(params).filter(([_, v]) => v != ''))
            ).toString();

            const excelLink = document.getElementById('excel-export-link');
            const pdfLink = document.getElementById('pdf-export-link');

            const baseExcelRoute = "{{ route('report.export', ['report' => 'salary']) }}";
            const basePdfRoute = "{{ route('report.export.pdf', ['report' => 'salary']) }}";

            if (excelLink) excelLink.href = `${baseExcelRoute}&${queryString}`;
            if (pdfLink) pdfLink.href = `${basePdfRoute}&${queryString}`;
        }

        // 3. MAIN FILTER LOGIC (Page Reload Strategy)
        const handleFilterChange = () => {
            const params = collectParams();
            const url = new URL("{{ route('salary') }}"); // Ensure this matches your route name

            // Loop through inputs and add them to URL if they have values
            Object.keys(params).forEach(key => {
                if (params[key]) {
                    url.searchParams.set(key, params[key]);
                }
            });

            // Reload the page with new filters
            window.location.href = url.toString();
        }

        // 4. Attach Event Listeners
        const inputs = document.querySelectorAll('.filter-salary');

        inputs.forEach(el => {
            // Remove any existing listeners to be safe
            el.removeEventListener('change', handleFilterChange);

            // Add CHANGE listener. 
            // This triggers when:
            // - You select an option from a dropdown
            // - You type in a number box and click outside (blur)
            // - You press ENTER inside the number box
            el.addEventListener('change', handleFilterChange);

            // CRITICAL FIX: DO NOT use 'keyup' for page reloads.
            // It interrupts typing. We removed that block.
        });

        // Initialize links on load
        updateExportLink();


        // --- EXISTING FORM VALIDATION CODE ---
        document.getElementById('payrateForm').addEventListener('submit', function(e) {
            let isValid = true;
            let payrate = this.querySelector('input[name="payrate"]');

            if (!payrate.value.trim()) {
                isValid = false;
                payrate.classList.add('is-invalid');
            } else {
                payrate.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                alert('Please fill all required fields');
            }
        });

        // --- CHECKBOX LOGIC ---
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('select-all')) {
                const isChecked = e.target.checked;
                document.querySelectorAll('.salary-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                });
            }
        });

        // --- BULK APPLY LOGIC ---
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'apply_bulk_status') {
                e.preventDefault();
                const bulkSelect = document.getElementById('bulk_status');
                const statusVal = bulkSelect ? bulkSelect.value : '';

                if (!statusVal) return alert('Please select a status to apply.');

                const checkedBoxes = document.querySelectorAll('.salary-checkbox:checked');
                if (checkedBoxes.length === 0) return alert('Please select at least one teacher record.');

                checkedBoxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const rowSelect = row.querySelector('select[name^="payment_status"]');
                    if (rowSelect) {
                        rowSelect.value = statusVal.toLowerCase();
                        row.classList.add('table-warning');
                        setTimeout(() => row.classList.remove('table-warning'), 600);
                    }
                });
            }
        });
    });
</script>
@endsection