@extends('layout.admin')
@include('components.alerts')
@section('content')

<!-- Form Control -->
<div class="row g-4">
    <div class="col-8">
        <div class="card mb-5 p-3 border-0 shadow">
            <div class="d-flex gap-2 align-items-center mb-4">
                <i class="bi bi-file-earmark-plus me-2 fs-4 text-primary"></i>
                <h4 class="fw-semibold mb-0">Create Form Control</h4>
            </div>
            <form action="{{ route('formControl.store') }}" method="POST" id="formControlForm">
                @csrf
                <div class="row mb-3">
                    <div class="col-5 mb-3">
                        <label for="formType" class="form-label fw-semibold">Form Type <span class="text-danger">*</span></label>
                        <select name="formType" id="formType" class="form-select">
                            <option value="">-- Select Form Type --</option>
                            <option value="student">Student</option>
                            <option value="beneficiary">Beneficiary</option>
                            <option value="monthly_supply">Monthly Supply</option>
                        </select>
                    </div>
                    <div class="col-5">
                        <label for="openDate" class="form-label fw-semibold">Open Date <span class="text-danger">*</span></label>
                        <input type="date" id="openDate" name="openDate" class="form-control">
                    </div>
                    <div class="col-5">
                        <label for="closeDate" class="form-label fw-semibold">Close Date <span class="text-danger">*</span></label>
                        <input type="date" id="closeDate" name="closeDate" class="form-control">
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card p-3 border-0 shasow-sm bg-info-subtle">
                            <div class="d-flex align-items-center gap-3 ">
                                <i class="bi bi-exclamation-circle-fill fs-6"></i>
                                The form will only be available for submissions between the selected dates.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button id="formControlButton" type="submit" class="btn btn-primary">Create Form Control</button>
                </div>
            </form>

        </div>
    </div>

    <div class="col-12">
        <div class="card p-3 border-0 shadow">
            <h4 class="fw-semibold mb-4">Active Form Controls</h4>
            <table class="table">
                <thead>
                    <tr class="text-center">
                        <th>#</th>
                        <th>Form Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($formControls as $formControl)
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $formControl->form_type }}</td>
                        <td>{{ $formControl->open_date }}</td>
                        <td>{{ $formControl->close_date }}</td>
                        @if ($formControl->open_date <= now() && $formControl->close_date >= now())
                            <td><span class="badge bg-success">Active</span></td>
                            @else
                            <td><span class="badge bg-warning text-dark">Inactive</span></td>
                            @endif
                          
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <i class="bi bi-file-earmark-text display-6 mb-2"></i>
                                <span class="fw-semibold">No form control yet</span>
                                <small class="text-secondary">New form control will appear here once submitted.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('formControlForm');
    const typeSelect = document.getElementById('type');
    const applicationTable = document.getElementById('applicationTable');

    // Handle form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            let isValid = true;

            const openDate = form.querySelector('input[name="openDate"]');
            const closeDate = form.querySelector('input[name="closeDate"]');
            const formType = form.querySelector('select[name="formType"]');

            // Validate form type
            if (!formType.value.trim()) {
                isValid = false;
                formType.classList.add('is-invalid');
            } else {
                formType.classList.remove('is-invalid');
            }

            // Validate open date
            if (!openDate.value.trim()) {
                isValid = false;
                openDate.classList.add('is-invalid');
            } else {
                openDate.classList.remove('is-invalid');
            }

            // Validate close date
            if (!closeDate.value.trim()) {
                isValid = false;
                closeDate.classList.add('is-invalid');
            } else {
                closeDate.classList.remove('is-invalid');
            }

            if (!isValid) {
                e.preventDefault();
                // Use a Bootstrap toast or inline error instead of alert
                alert('Please fill all required fields');
            }
        });
    }
</script>

@endsection