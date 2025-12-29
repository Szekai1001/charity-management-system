@extends('layout.beneficiary')
@include('components.alerts')

@section('content')

<style>
    /* Mobile: Show border */
    .mobile-border-top {
        border-top: 1px solid #dee2e6 !important;
    }

    /* Web (768px and up): Hide border */
    @media (min-width: 768px) {
        .mobile-border-top {
            border-top: 0 !important;
        }
    }
</style>

@if ($errors->any())
<div class="alert alert-danger border-0 border-start border-4 border-danger shadow-sm rounded-3 mb-4" role="alert">
    <div class="d-flex">
        <div class="me-3">
            <i class="bi bi-exclamation-triangle-fill fs-4 text-danger"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1 text-danger">Please fix the following errors:</h6>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            {{-- 1. ALREADY APPLIED --}}
            @if($alreadyApplied)
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                <div class="mb-3 text-success">
                    <i class="bi bi-check-circle-fill display-4"></i>
                </div>
                <h4 class="fw-bold">Request Submitted</h4>
                <p class="text-muted">You have already applied for this month.</p>
                <a href="{{ route('beneficiary.dashboard') }}" class="btn btn-outline-dark rounded-pill mt-2 w-100">Back to Dashboard</a>
            </div>

            {{-- 2. CLOSED --}}
            @elseif(!$formControl)
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-light">
                <h4 class="fw-bold text-secondary">Applications Closed</h4>
                <p class="text-muted">This form is currently unavailable.</p>
            </div>

            {{-- 3. ACTIVE FORM --}}
            @else

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                    <h3 class="fw-bold text-dark mb-1">Monthly Supply Request Form</h3>
                    <p class="text-muted small">Please select your preferred package below</p>
                </div>

                <div class="card-body p-3 p-md-4">

                    {{-- GUIDELINES SECTION --}}
                    <div class="card border-0 bg-info-subtle rounded-4 mb-4">
                        <div class="card-body p-3 p-md-4">
                            <h6 class="fw-bold text-info mb-3">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>Important Instructions
                            </h6>

                            <div class="row g-3">
                                {{-- Column 1 --}}
                                <div class="col-12 col-md-4">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 text-primary"><i class="bi bi-1-circle-fill fs-4"></i></div>
                                        <div>
                                            <strong class="d-block text-dark small mb-1">One Request Only</strong>
                                            <p class="small text-muted mb-0">One application per month.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 pt-3 pt-md-0 mobile-border-top">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 text-primary"><i class="bi bi-person-badge-fill fs-4"></i></div>
                                        <div>
                                            <strong class="d-block text-dark small mb-1">Self Pickup</strong>
                                            <p class="small text-muted mb-0">9 AM - 5 PM.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Column 3 - Added border-top for mobile --}}
                                <div class="col-12 col-md-4 pt-3 pt-md-0 mobile-border-top">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3 text-primary"><i class="bi bi-house-door-fill fs-4"></i></div>
                                        <div>
                                            <strong class="d-block text-dark small mb-1">Home Delivery</strong>
                                            <p class="small text-muted mb-0">Someone must be home.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <form id="supplyRequestForm" action="{{ route('supplyRequest.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="control_id" value="{{ $formControl->id }}">

                        {{-- STEP 1: PACKAGES --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase small ls-1 mb-3">1. Select Package</label>
                            <div class="d-flex flex-column gap-2">
                                @foreach ($packages as $package)
                                <div class="position-relative">
                                    <input type="radio" class="btn-check package-radio" name="package_id" id="pkg_{{ $package->id }}" value="{{ $package->id }}" required>
                                    <label class="btn btn-outline-light w-100 text-start p-3 border shadow-sm rounded-3 d-flex align-items-center package-label text-dark" for="pkg_{{ $package->id }}">
                                        <div class="me-3 text-secondary check-icon">
                                            <i class="bi bi-circle fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1" style="min-width: 0;"> {{-- min-width fix for text wrapping --}}
                                            <div class="fw-bold fs-6">{{ $package->name }}</div>
                                            <div class="small text-muted text-wrap"> {{-- Changed truncate to wrap for mobile --}}
                                                {{ $package->description ?? 'Standard Supply Set' }}
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- STEP 2: COLLECTION METHOD --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary text-uppercase small ls-1 mb-3">2. Collection Method</label>
                            <div class="row g-2"> {{-- Tightened gap --}}
                                <div class="col-12 col-sm-6"> {{-- Full width on mobile --}}
                                    <input type="radio" class="btn-check" name="distribution_method" id="pickup" value="Pickup">
                                    <label class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold" for="pickup">
                                        <i class="bi bi-shop me-2"></i> Pickup
                                    </label>
                                </div>
                                <div class="col-12 col-sm-6"> {{-- Full width on mobile --}}
                                    <input type="radio" class="btn-check" name="distribution_method" id="delivery" value="Delivery">
                                    <label class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold" for="delivery">
                                        <i class="bi bi-truck me-2"></i> Delivery
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- STEP 3: DATE SECTION --}}
                        <div class="deliverySection mb-4 p-3 bg-light rounded-3 border" style="display:none;">
                            <label class="form-label fw-bold text-dark small">Preferred Delivery Slot</label>
                            <select name="date_id" id="date_id" class="form-select border-secondary-subtle bg-white">
                                <option value="" selected disabled>Select date...</option>
                                @foreach ($dates as $date)
                                <option value="{{ $date->id }}">{{ $date->date }} ({{ $date->session }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- SUBMIT --}}
                        <div class="d-grid pt-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm py-3">
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- VISUAL LOGIC FOR PACKAGE SELECTION ---
        const packageRadios = document.querySelectorAll('.package-radio');

        function updatePackageVisuals() {
            packageRadios.forEach(radio => {
                const label = document.querySelector(`label[for="${radio.id}"]`);
                const icon = label.querySelector('.check-icon i');

                if (radio.checked) {
                    // Active State Styling
                    label.classList.remove('btn-outline-light', 'text-dark', 'bg-white');
                    label.classList.add('btn-primary-subtle', 'border-primary', 'text-primary');

                    // Change Icon to Checkmark
                    icon.classList.remove('bi-circle');
                    icon.classList.add('bi-check-circle-fill', 'text-primary');
                } else {
                    // Inactive State Styling
                    label.classList.add('btn-outline-light', 'text-dark', 'bg-white');
                    label.classList.remove('btn-primary-subtle', 'border-primary', 'text-primary');

                    // Change Icon back to Circle
                    icon.classList.add('bi-circle');
                    icon.classList.remove('bi-check-circle-fill', 'text-primary');
                }
            });
        }

        // Attach listener to all radios
        packageRadios.forEach(radio => {
            radio.addEventListener('change', updatePackageVisuals);
        });
        // ------------------------------------------

        const deliveryRadio = document.getElementById('delivery');
        const pickupRadio = document.getElementById('pickup');
        const deliverySection = document.querySelector('.deliverySection');
        const dateInput = document.getElementById('date_id');

        function toggleDelivery() {
            if (deliveryRadio && pickupRadio) {
                if (deliveryRadio.checked) {
                    deliverySection.style.display = 'block';
                } else {
                    deliverySection.style.display = 'none';
                    if (dateInput) dateInput.value = "";
                }
            }
        }

        if (deliveryRadio) deliveryRadio.addEventListener('change', toggleDelivery);
        if (pickupRadio) pickupRadio.addEventListener('change', toggleDelivery);

        const form = document.getElementById('supplyRequestForm');
        if (form) {
            form.addEventListener("submit", function(e) {
                let valid = true;

                // Package Validation
                const pkgError = document.getElementById('package-error');
                if (!Array.from(packageRadios).some(r => r.checked)) {
                    valid = false;
                    pkgError.style.display = 'block';
                } else {
                    pkgError.style.display = 'none';
                }

                // Method Validation
                if (!(deliveryRadio.checked || pickupRadio.checked)) {
                    valid = false;
                    alert("Please select a collection method");
                }

                // Date Validation
                if (deliveryRadio.checked && !dateInput.value) {
                    valid = false;
                    dateInput.classList.add('is-invalid');
                } else if (dateInput) {
                    dateInput.classList.remove('is-invalid');
                }

                if (!valid) {
                    e.preventDefault();
                } else {
                    if (!confirm('Are you sure you want to submit?')) e.preventDefault();
                }
            });
        }
    });
</script>
@endsection