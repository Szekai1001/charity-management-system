@extends('layout.admin')
@include('components.alerts')
@section('content')

<ul class="nav nav-tabs mb-4" id="supplyRequestTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'viewSupplyRequest' ? 'active' : '' }}" href="?tab=viewSupplyRequest" role="tab">
            View Supply Request
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ $activeTab == 'viewPurchaseRequirement' ? 'active' : '' }}" href="?tab=viewPurchaseRequirement" role="tab">
            View Purchase Requirement
        </a>
    </li>
</ul>

<div class="tab-content" id="packageTabContent">

    <div class="tab-pane fade {{ $activeTab == 'viewSupplyRequest' ? 'show active' : '' }}" id="supplyRequestSection" role="tabpanel">
        <div class="card p-3 border-0 shadow">
            <div class="d-flex justify-content-between align-items-center gap-2">

                <div class="flex-grow-1" style="max-width: 400px; min-width: 250px;">
                    <div class="input-group bg-white border rounded-pill shadow-sm overflow-hidden">

                        {{-- Icon: Transparent background to blend in --}}
                        <span class="input-group-text border-0 bg-transparent ps-3">
                            <i class="bi bi-search text-muted"></i>
                        </span>

                        {{-- Input: No border, no focus shadow (so the ring doesn't cut the pill) --}}
                        <input type="text"
                            class="form-control border-0 shadow-none"
                            id="beneficiary_details"
                            list="beneficiaryOptions"
                            placeholder="Search Beneficiary Name or IC..."
                            autocomplete="off">

                        <datalist id="beneficiaryOptions">
                            @foreach($beneficiaries as $beneficiary)
                            <option value="{{ $beneficiary->name }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>
                    
                <div class="d-flex gap-3">

                    {{-- IDs added for JS targeting --}}
                    <a id="sr-export-excel-link" href="{{ route('report.export', array_merge(request()->query(), ['report' => 'supplyDistribution'])) }}" class="btn btn-success">
                        <i class="bi bi-file-earmark-excel-fill me-2"></i>Excel
                    </a>
                    <a id="sr-export-pdf-link" href="{{ route('report.export.pdf', array_merge(request()->query(), ['report' => 'supplyDistribution'])) }}" class="btn btn-danger">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i> PDF
                    </a>
    
                    <div class="dropdown">
                        <button class="btn btn-light border shadow-sm dropdown-toggle" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                            <i class="bi bi-sliders me-1"></i> Filter
                        </button>
    
                        <div class="dropdown-menu p-3 shadow" aria-labelledby="filterDropdown"
                            style="min-width: 600px; max-height: 500px; overflow-y: auto; right: 0; left: auto;">
                            <form method="GET" action="{{ route('supplyRequest.filter') }}">
                                <input type="hidden" name="type" value="monthly_supply">
    
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="sd_year" class="form-label">Year:</label>
                                        <input type="number" id="sd_year" name="sd_year" class="form-control filter-sd"
                                            value="{{ $year ?? '' }}" min="2000" max="2100">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="sd_month" class="form-label">Month:</label>
                                        <select name="sd_month" id="sd_month" class="form-select filter-sd">
                                            <option value="">--All Months--</option>
                                            @foreach (range(1, 12) as $m)
                                            <option value="{{ $m }}" {{ (int)$m === (int)($month ?? 0) ? 'selected' : '' }}>
                                                {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="package" class="form-label">Package:</label>
                                        <select name="package" id="package" class="form-select filter-sd">
                                            <option value="">-- All Packages --</option>
                                            @foreach($allPackages as $package)
                                            <option value="{{ $package->id }}" {{ (int)$package->id === (int)(request('package')) ? 'selected' : '' }}>
                                                {{ $package->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="session" class="form-label">Session:</label>
                                        <select name="session" id="session" class="form-select filter-sd">
                                            <option value="">-- All Sessions --</option>
                                            @foreach($allSessions as $session)
                                            <option value="{{ $session->session }}"
                                                {{ $session->session === request('session') ? 'selected' : '' }}>
                                                {{ $session->session }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="distribution_method" class="form-label">Distribution Method:</label>
                                        <select name="distribution_method" id="distribution_method" class="form-select filter-sd">
                                            <option value="">-- All distribution methods --</option>
                                            @foreach($allMethods as $method)
                                            <option value="{{ $method }}" {{ $method === request('distribution_method') ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="distribution_status" class="form-label">Status:</label>
                                        <select name="distribution_status" id="distribution_status" class="form-select filter-sd">
                                            <option value="">-- All statuses --</option>
                                            @foreach($allStatuses as $status)
                                            <option value="{{ $status }}" {{ $status === request('distribution_status') ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>  
                </div>
            </div>
        </div>

        <div id="supplyDistributionTable">
            @include('admin.report.tables.supplyDistribution')
        </div>
    </div>


    <div class="tab-pane fade {{ $activeTab == 'viewPurchaseRequirement' ? 'show active' : '' }}" id="purchaseRequirementSection" role="tabpanel">

        <div class="card p-3 shadow border-0">
            <div class="d-flex justify-content-end align-items-center gap-2">

                {{-- ADDED IDs: pr-export-excel-link & pr-export-pdf-link --}}
                <a id="pr-export-excel-link" href="{{ route('report.export', array_merge(request()->query(), ['report' => 'purchaseRequirement'])) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel-fill me-2"></i>Excel
                </a>
                <a id="pr-export-pdf-link" href="{{ route('report.export.pdf', array_merge(request()->query(), ['report' => 'purchaseRequirement'])) }}" class="btn btn-danger">
                    <i class="bi bi-file-earmark-pdf-fill me-2"></i> PDF
                </a>

                <div class="dropdown">
                    <button class="btn btn-light border shadow-sm dropdown-toggle" type="button" id="filterDropdown"
                        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <i class="bi bi-sliders me-1"></i> Filter
                    </button>

                    <div class="dropdown-menu p-3 shadow" aria-labelledby="filterDropdown"
                        style="min-width: 600px; max-height: 500px; overflow-y: auto; right: 0; left: auto;">

                        <form method="GET" action="{{ route('purchaseRequirement.filter') }}">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="pr_year" class="form-label">Year:</label>
                                    <input type="number" id="pr_year" name="pr_year" class="form-control filter-pr"
                                        value="{{ old('year', request('year') ?? $year) }}" min="2000" max="2100">
                                </div>

                                <div class="col-md-6">
                                    <label for="pr_month" class="form-label">Month:</label>
                                    <select name="pr_month" id="pr_month" class="form-select filter-pr">
                                        <option value="">-- Select Month --</option>
                                        @foreach (range(1, 12) as $m)
                                        <option value="{{ $m }}"
                                            {{ (string)$m === (string)(request('month') ?? $month) ? 'selected' : '' }}>
                                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="purchaseRequirementTable">
            @include('admin.report.tables.purchaseRequirement')
        </div>
    </div>
</div>


<script>
    // =========================================================
    // GLOBAL HELPER FUNCTION (Must be outside DOMContentLoaded)
    // =========================================================
    function prepareItemsForSubmit(formType) {
        if (formType === 'supply') {
            const form = document.getElementById("availablePackage");
            let allfilled = true;

            // Check Checkboxes
            const packageFormCheckbox = form.querySelectorAll('input[type="checkbox"][name="active_packages[]"]');
            const anyPackageChecked = Array.from(packageFormCheckbox).some(cb => cb.checked);
            
            if (!anyPackageChecked) {
                allfilled = false;
                packageFormCheckbox.forEach(cb => cb.classList.add('is-invalid'));
            } else {
                packageFormCheckbox.forEach(cb => cb.classList.remove('is-invalid'));
            }

            // Check JSON Dates
            const dateEmptyCheck = document.getElementById('delivery_dates_json').value;
            if (!dateEmptyCheck || dateEmptyCheck === "[]" || dateEmptyCheck.trim() === "") {
                allfilled = false;
            }

            if (!allfilled) {
                alert('Please enter all required fields');
                return false;
            }
        } else {
            return false;
        }
        return true;
    }

    document.addEventListener('DOMContentLoaded', function() {

        // =========================================================
        // 1. GLOBAL UI HANDLERS (Checkboxes & Bulk Status)
        // =========================================================

        // Handle "Select All" Checkbox
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('select-all')) {
                const isChecked = e.target.checked;
                // Select all checkboxes inside the specific table context if needed, 
                // or globally with the class .supply-checkbox
                document.querySelectorAll('.supply-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                });
            }
        });

        // Handle "Apply Bulk Status" Button
        document.addEventListener('click', function(e) {
            if (e.target && e.target.id === 'apply_sd_bulk_status') {
                e.preventDefault();

                const bulkStatusSelect = document.getElementById('sd_bulk_status');
                const selectedStatus = bulkStatusSelect ? bulkStatusSelect.value : '';

                if (!selectedStatus) {
                    alert('Please select a status to apply.');
                    return;
                }

                // Ensure selector matches your specific row checkboxes
                const checkedRows = document.querySelectorAll('.supply-checkbox:checked'); 

                if (checkedRows.length === 0) {
                    alert('Please select at least one row.');
                    return;
                }

                checkedRows.forEach(cb => {
                    const row = cb.closest('tr');
                    const rowSelect = row.querySelector('select.form-select');
                    if (rowSelect) {
                        rowSelect.value = selectedStatus;
                    }
                });
            }
        });


        // =========================================================
        // 2. SUPPLY REQUEST (SR) LOGIC
        // =========================================================

        const collectParamsSR = () => {
            return {
                sd_year: document.getElementById('sd_year')?.value || '',
                sd_month: document.getElementById('sd_month')?.value || '',
                package: document.getElementById('package')?.value || '',
                session: document.getElementById('session')?.value || '',
                distribution_method: document.getElementById('distribution_method')?.value || '',
                distribution_status: document.getElementById('distribution_status')?.value || '',
                // NEW: Added Search Input
                beneficiary_details: document.getElementById('beneficiary_details')?.value || '', 
            };
        };

        const updateExportLinkSR = () => {
            const params = collectParamsSR();
            const queryString = new URLSearchParams(params).toString();

            const excelLink = document.getElementById('sr-export-excel-link');
            const pdfLink = document.getElementById('sr-export-pdf-link');

            // Define Base Routes
            const baseExcelRoute = "{{ route('report.export', ['report' => 'supplyDistribution']) }}";
            const basePdfRoute = "{{ route('report.export.pdf', ['report' => 'supplyDistribution']) }}";

            if (excelLink) excelLink.href = `${baseExcelRoute}&${queryString}`;
            if (pdfLink) pdfLink.href = `${basePdfRoute}&${queryString}`;
        };

        const sendRequestSR = () => {
            const params = collectParamsSR();

            fetch("{{ route('supplyRequest.filter') }}?" + new URLSearchParams(params))
                .then(response => response.text())
                .then(html => {
                    document.getElementById('supplyDistributionTable').innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        };

        const handleFilterChangeSR = () => {
            sendRequestSR();
            updateExportLinkSR();
        };

        // A. Event Listeners for Dropdowns/Inputs
        document.querySelectorAll('input.filter-sd, select.filter-sd').forEach(el => {
            el.removeEventListener('change', handleFilterChangeSR);
            el.addEventListener('change', handleFilterChangeSR);

            if (el.tagName === 'INPUT') {
                el.removeEventListener('keyup', handleFilterChangeSR);
                el.addEventListener('keyup', handleFilterChangeSR);
            }
        });

        // B. Event Listener for Search Input (With Debounce)
        const beneficiaryInput = document.getElementById('beneficiary_details');
        let debounceTimer;

        if (beneficiaryInput) {
            beneficiaryInput.addEventListener('input', function() {
                // Clear previous timer to reset the clock
                clearTimeout(debounceTimer);
                
                // Set new timer: wait 500ms after user stops typing
                debounceTimer = setTimeout(() => {
                    handleFilterChangeSR();
                }, 500);
            });
        }

        // Initialize SR links on load
        updateExportLinkSR();


        // =========================================================
        // 3. PURCHASE REQUIREMENT (PR) LOGIC
        // =========================================================

        const collectParamsPR = () => {
            return {
                pr_year: document.getElementById('pr_year')?.value || '',
                pr_month: document.getElementById('pr_month')?.value || '',
            };
        };

        const updateExportLinkPR = () => {
            const params = collectParamsPR();
            const queryString = new URLSearchParams(params).toString();

            const excelLink = document.getElementById('pr-export-excel-link');
            const pdfLink = document.getElementById('pr-export-pdf-link');

            const baseExcelRoute = "{{ route('report.export', ['report' => 'purchaseRequirement']) }}";
            const basePdfRoute = "{{ route('report.export.pdf', ['report' => 'purchaseRequirement']) }}";

            if (excelLink) excelLink.href = `${baseExcelRoute}&${queryString}`;
            if (pdfLink) pdfLink.href = `${basePdfRoute}&${queryString}`;
        };

        const sendRequestPR = () => {
            const params = collectParamsPR();

            fetch("{{ route('purchaseRequirement.filter') }}?" + new URLSearchParams(params))
                .then(response => response.text())
                .then(html => {
                    document.getElementById('purchaseRequirementTable').innerHTML = html;
                })
                .catch(error => console.error('Error:', error));
        };

        const handleFilterChangePR = () => {
            sendRequestPR();
            updateExportLinkPR();
        };

        // Attach Events for PR
        document.querySelectorAll('input.filter-pr, select.filter-pr').forEach(el => {
            el.removeEventListener('change', handleFilterChangePR);
            el.addEventListener('change', handleFilterChangePR);

            if (el.tagName === 'INPUT') {
                el.removeEventListener('keyup', handleFilterChangePR);
                el.addEventListener('keyup', handleFilterChangePR);
            }
        });

        // Initialize PR links on load
        updateExportLinkPR();
    });
</script>
@endsection