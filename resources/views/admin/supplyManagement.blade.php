@extends('layout.admin')
@include('components.alerts')
@section('content')

<div class="card p-4 border-0 shadow mb-5">
    <div class="row text-center">

        <div class="col-md-4">
            <div class="text-primary mb-1">
                <i class="bi bi-box-seam fs-3"></i>
            </div>
            <h4 class="fw-bold mb-0">{{ $packagesCount }}</h4>
            <p class="text-muted small mb-0">Total Packages</p>
        </div>

        <div class="col-md-4 border-start border-end">
            <div class="text-success mb-1">
                <i class="bi bi-check-circle-fill fs-3"></i>
            </div>
            <h4 class="fw-bold mb-0">{{ $activePackagesCount }}</h4>
            <p class="text-muted small mb-0">Active Packages</p>
        </div>

        <div class="col-md-4">
            <div class="text-warning mb-1">
                <i class="bi bi-calendar-event-fill fs-3"></i>
            </div>
            <h4 class="fw-bold mb-0">{{ $activeDeliveryDatesCount }}</h4>
            <p class="text-muted small mb-0">Active Delivery Sessions</p>
        </div>

    </div>
</div>

<!-- Set supply form -->

<form id="availablePackage" action="{{ route('supplyForm.store') }}" method="POST">
    @csrf

    {{-- Hidden field required by the controller for the custom OR check (assuming you updated the controller) --}}
    <input type="hidden" name="base_config" value="1">

    <div class="alert alert-info border-0 shadow-sm mb-4 py-3">
        <i class="bi bi-info-circle-fill me-2"></i>
        <strong>Configuration Required:</strong> Please choose **Available Packages** OR **Add Delivery Dates**. You must complete at least one section to save.
    </div>

    {{-- Global Error for the main OR condition --}}
    @error('base_config')
    <div class="alert alert-danger mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <strong>Action Missing:</strong> {{ $message }}
    </div>
    @enderror

    <div class="container-fluid">
        <div class="row g-4">

            {{-- ----------------- COLUMN 1: PACKAGES (ACTION A) ----------------- --}}
            <div class="col-lg-4">
                {{-- (Package card code remains unchanged) --}}
                <div class="card border-primary border-3 border-bottom-0 border-top-0 border-end-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex gap-3 align-items-center">
                            <i class="bi bi-box-seam me-2 text-primary"></i>
                            <h6 class="fw-bold mb-0 text-uppercase">
                                Available Packages
                            </h6>
                            <span class="badge bg-primary-subtle text-primary ms-auto">OPTION 1</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($packages as $package)
                            <label class="list-group-item list-group-item-action d-flex gap-3 align-items-center py-3 px-4 cursor-pointer">
                                <input class="form-check-input flex-shrink-0 my-0 border-2"
                                    type="checkbox"
                                    name="active_packages[]"
                                    value="{{ $package->id }}"
                                    {{ in_array($package->id, old('active_packages', [])) ? 'checked' : '' }}
                                    style="transform: scale(1.2);">
                                <span class="fw-medium text-dark">{{ $package->name }}</span>
                            </label>
                            @empty
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-box2 display-6 opacity-25"></i>
                                <p class="small mt-2 mb-0">No packages found.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @error('active_packages')
                    <div class="p-3 mx-3 mb-3 text-danger border border-danger rounded bg-danger-subtle fw-medium shadow-sm">
                        <i class="bi bi-x-octagon-fill me-2"></i>
                        <strong>Selection Required:</strong> {{ $message }}
                    </div>
                    @enderror

                    <div class="card-footer bg-light text-center py-2">
                        <a href="{{ route('admin.packages') }}" class="text-decoration-none small fw-bold text-primary">
                            Manage Packages
                        </a>
                    </div>
                </div>
            </div>

            {{-- ----------------- COLUMN 2: DELIVERY DATES (ACTION B) ----------------- --}}
            <div class="col-lg-8">
                <div class="card border-info border-3 border-bottom-0 border-top-0 border-end-0 shadow-sm h-100">

                    <div class="card-body bg-light border-bottom p-3">
                        <div class="d-flex gap-3 mb-3 align-items-center">
                            <i class="bi bi-calendar-plus text-primary"></i>
                            <h6 class="fw-bold mb-0 text-uppercase">
                                Add Delivery Dates
                            </h6>
                            <span class="badge bg-info-subtle text-info ms-auto">OPTION 2</span>
                        </div>

                        @if ($errors->has('deliveryDate') || $errors->has('session'))
                        <div class="alert alert-warning border-0 mb-3 py-2 fw-medium">
                            <i class="bi bi-exclamation-circle-fill me-2"></i>
                            **Input Required:** Please select a Date and a Session before clicking 'Add Date'.
                        </div>
                        @endif

                        {{-- Visually group the inputs for adding a date --}}
                        <div class="p-3 mb-3 bg-white rounded shadow-sm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted">Date</label>
                                    {{-- is-invalid class triggers red border --}}
                                    <input type="date" id="deliveryDate" name="deliveryDate"
                                        class="form-control @error('deliveryDate') is-invalid @enderror"
                                        value="{{ old('deliveryDate') }}">
                                    {{-- Individual error message remains for precise context --}}
                                    @error('deliveryDate')
                                    <div class="text-danger small mt-1 py-1 px-2 border border-danger-subtle bg-danger-subtle rounded fw-medium">
                                        <i class="bi bi-calendar-x me-1"></i> {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">Session</label>
                                    <div class="btn-group w-100 shadow-sm rounded" role="group">
                                        {{-- ... your session radio buttons ... --}}
                                        <input type="radio" class="btn-check" name="session" id="morning" value="Morning" autocomplete="off" {{ old('session') == 'Morning' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary" for="morning">Morning</label>

                                        <input type="radio" class="btn-check" name="session" id="afternoon" value="Afternoon" autocomplete="off" {{ old('session') == 'Afternoon' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary border-start" for="afternoon">Afternoon</label>

                                        <input type="radio" class="btn-check" name="session" id="both" value="Both" autocomplete="off" {{ old('session') == 'Both' ? 'checked' : '' }}>
                                        <label class="btn btn-outline-secondary border-start" for="both">Both</label>
                                    </div>
                                    {{-- Individual error message for session is clearly placed beneath the control --}}
                                    @error('session')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary w-100 fw-bold" onclick="addDate()">
                                        <i class="bi bi-plus-lg"></i> Add Date
                                    </button>
                                </div>
                            </div>
                        </div> {{-- End of p-3 mb-3 bg-white rounded shadow-sm --}}
                    </div>

                    <div class="card-body">
                        <h6 class="fw-bold text-muted text-uppercase small mb-3">CURRENT DELIVERY SCHEDULE</h6>
                        <div class="card shadow-sm">
                            <div class="table-responsive" style="min-height: 250px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light text-center border-bottom text-uppercase small">
                                        <tr>
                                            <th>#</th>
                                            <th>Selected Date</th>
                                            <th>Session</th>
                                            <th>Remove</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dateList" class="text-center">
                                        <tr id="emptyPlaceholder">
                                            <td colspan="4" class="text-center py-5 text-muted">
                                                <div class="py-4">
                                                    <i class="bi bi-calendar4-week display-4 opacity-25"></i>
                                                    <p class="fw-medium mt-3 mb-0">Your schedule is empty.</p>
                                                    <small>Use the form above to add dates.</small>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                                <input type="hidden" name="delivery_dates" id="delivery_dates_json">
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success px-5 fw-bold shadow-sm">
                            Save Configuration <i class="bi bi-check-lg ms-2"></i>
                        </button>
                    </div>

                </div>
            </div>

        </div>
    </div>
</form>

<div class="alert alert-warning border-0 shadow-sm mb-4 py-3 mt-5" role="alert">
    {{-- Using the exclamation icon for clear warning status --}}
    <i class="bi bi-exclamation-triangle-fill me-2"></i>

    {{-- Strong emphasis on the instruction/warning --}}
    <strong>Warning: Start New Configuration Required!</strong>

    <p class="mb-0 mt-1">
        To create a completely new configuration, you must first click the
        <span class="text-danger fw-bold">"Reset Configuration"</span> button to clear all existing active packages and delivery dates.
    </p>
</div>

<!-- Active Packages & Dates -->

<div class="card p-3 border-0 shadow">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex gap-3">
            <div class="d-flex justify-content-center align-items-center">
                <i class="bi bi-gear-fill fs-3 text-primary"></i>
            </div>

            <div class="d-flex flex-column justify-content-center">
                <h4 class="m-0 fw-semibold">Active Configuration</h4>
                <p class="m-0 text-muted">View all active packages and scheduled delivery dates</p>
            </div>
        </div>
        <form action="{{ route('supplyForm.reset') }}" method="POST">
            @csrf
            <button class="btn btn-danger fw-bold shadow-sm" type="submit"
                onclick="return confirm('WARNING: Are you sure you want to completely reset the supply configuration? This will deactivate ALL packages and delete ALL scheduled delivery dates.')">
                <i class="bi bi-trash-fill me-2"></i> Reset Configuration
            </button>
        </form>
    </div>

    <div class="row mt-3 g-4">
        <!-- Active Packages -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Active Packages</h5>
                </div>
                <div class="card-body">
                    @if($activePackages->isEmpty())
                    <p class="text-muted">No active packages available.</p>
                    @else
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Package Name</th>
                                <th>Items</th>
                                <th>Item Details</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($activePackages as $index => $package)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $package->name }}</td>
                                <td>
                                    {{ $package->items->count()}}
                                </td>
                                <td>
                                    {{-- Reduced size and complexity: using btn-sm and a simple eye icon --}}
                                    <button type="button"
                                        class="btn btn-primary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#itemDetails-{{ $package->id }}"
                                        title="View Package Items">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>

                                {{-- ----------------- DEACTIVATE BUTTON ----------------- --}}
                                <td>
                                    <form action="{{ route('singleSupply.delete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="package_id_to_delete" value="{{ $package->id }}">

                                        {{-- Reduced size and simplified to a single trash/deactivate icon --}}
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Deactivate Package"
                                            onclick="return confirm('Are you sure you want to DEACTIVATE the package: {{ $package->name }}?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>

        <!-- Delivery Dates -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Delivery Dates & Sessions</h5>
                </div>
                <div class="card-body">
                    @if($activeDeliveryDates->isEmpty())
                    <p class="text-muted">No delivery dates scheduled.</p>
                    @else
                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center">
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Session</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach($activeDeliveryDates as $index => $date)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td name="deliveryDate{{$date->id}}">{{ \Carbon\Carbon::parse($date->date)->format('d M Y') }}</td>
                                <td>
                                    <span class="badge 
                                                @if($date->session == 'Morning') bg-primary
                                                @elseif($date->session == 'Afternoon') bg-warning text-dark
                                                @else bg-dark @endif">
                                        {{ $date->session }}
                                    </span>
                                </td>
                                <td>
                                    <form action="{{ route('singleSupply.delete') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="date_id_to_delete" value="{{ $date->id }}">

                                        {{-- Simplified button: uses outline style and trash icon for minimalism --}}
                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Deactivate Date"
                                            onclick="return confirm('Are you sure you want to DEACTIVATE the date: {{ \Carbon\Carbon::parse($date->date)->format('d M Y') }} ({{ $date->session }})?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

@foreach($activePackages as $package)
<div class="modal fade" id="itemDetails-{{ $package->id }}" tabindex="-1" aria-labelledby="itemDetailsLabel-{{ $package->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="itemDetailsLabel-{{ $package->id }}">{{ $package->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <!-- Package Summary -->
                <div class="d-flex justify-content-around align-items-center bg-info-subtle p-3 rounded-3 shadow-sm mb-3">
                    <div class="text-center">
                        <p class="mb-1 fw-semibold text-muted">TOTAL ITEMS</p>
                        <h4 class="fw-bold">{{ $package->items->count() }}</h4>
                    </div>
                    <div class="text-center">
                        <p class="mb-1 fw-semibold text-muted">STATUS</p>
                        <h4 class="fw-bold {{ $package->is_active ? 'text-success' : 'text-danger' }}">
                            {{ $package->is_active ? 'Active' : 'Inactive' }}
                        </h4>
                    </div>
                </div>

                <!-- Items List -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-light d-flex align-items-center gap-2">
                        <i class="bi bi-bag-check-fill fs-5"></i>
                        <h6 class="mb-0 fw-semibold">Items Included</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($package->items as $item)
                            <div class="border rounded-3 px-3 py-2 bg-white shadow-sm">
                                <span class="fw-semibold">{{ $item->name }}</span>
                                <span class="badge bg-primary ms-2">{{ $item->pivot->quantity }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach



<script>
    let dates = [];

    document.addEventListener('DOMContentLoaded', function() {
        const supplyForm = document.getElementById("availablePackage");
        supplyForm.addEventListener('submit', function(e) {
            if (!prepareItemsForSubmit('supply')) {
                e.preventDefault();
            }
        });
    });

    function addDate() {
        const date = document.getElementById('deliveryDate').value;
        const sessionInput = document.querySelector('input[name="session"]:checked');

        if (!date || !sessionInput) {
            alert('Please select both a date and a session.');
            return;
        }

        // Get today's date (YYYY-MM-DD)
        const today = new Date().toISOString().split('T')[0];

        // Check date must be after today
        if (date <= today) {
            alert('Please select a date after today.');
            return;
        }


        const session = sessionInput.value;

        // Prevent duplicates (optional)
        if (dates.some(d => d.date === date && d.session === session)) {
            alert('This date and session is already added.');
            return;
        }

        dates.push({
            date,
            session
        });
        updateDateList();
        syncHiddenInput();


    }

    function updateDateList() {
        const container = document.getElementById('dateList');

        if (dates.length === 0) {
            container.innerHTML = `
            <td colspan="4" class="text-center py-5 text-muted">
                <div class="py-4">
                <i class="bi bi-calendar4-week display-4 opacity-25"></i>
                <p class="fw-medium mt-3 mb-0">Your schedule is empty.</p>
                <small>Use the form above to add dates.</small>
                </div>
            </td>
        `;
            document.getElementById('delivery_dates_json').value = "";
            return;
        }

        const rows = dates.map((d, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${d.date}</td>
            <td>${d.session}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeDate(${index})">Remove</button>
            </td>
        </tr>
    `).join('');

        container.innerHTML = rows;

        // ✅ Keep hidden input updated
        syncHiddenInput();
    }

    function removeDate(index) {
        dates.splice(index, 1);
        updateDateList();
        syncHiddenInput();
    }

    function syncHiddenInput() {
        // Convert to json format
        document.getElementById('delivery_dates_json').value = JSON.stringify(dates);
    }
</script>

@endsection