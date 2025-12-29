<div class="border-bottom pb-3 mb-4 mt-3">

    <div class="bg-light border rounded px-4 py-2 d-inline-flex align-items-center shadow-sm">
        <i class="bi bi-calendar3 text-primary fs-5 me-2"></i>

        <span class="fw-semibold fs-6 text-dark">
            Showing data for:
            <span class="text-primary">
                @if(!empty($date))
                {{-- Case 1: Specific Date (if used) --}}
                {{ \Carbon\Carbon::parse($date)->format('d F Y') }}

                @elseif(!empty($month) && !empty($year))
                {{-- Case 2: Specific Month & Year Selected --}}
                {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}

                @elseif(!empty($year))
                {{-- Case 3: "All Months" Selected (Month is null, Year is set) --}}
                All Months in {{ $year }}

                @else
                {{-- Case 4: Everything is null --}}
                All Records
                @endif
            </span>
    </div>
</div>

<div class="row g-2 mt-3 d-flex flex-nowrap overflow-x-auto">

    <div class="col">
        <div class="card shadow-sm border-0 text-center h-100 py-3 px-3">
            <i class="bi bi-bag-fill text-primary fs-3 mb-2"></i>
            <p class="text-muted text-uppercase small fw-semibold mb-0">Total Supply Requests</p>
            <h5 class="fw-bold mt-1">{{ $supplyRequestsCount }}</h5>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-0 text-center h-100 py-3 px-3">
            <i class="bi bi-check-circle-fill text-success fs-3 mb-2"></i>
            <p class="text-muted text-uppercase small fw-semibold mb-0">Total Approved</p>
            <h5 class="fw-bold text-success mt-1">{{ $approvedCount }}</h5>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-0 text-center h-100 py-3 px-3">
            <i class="bi bi-hourglass-split text-warning fs-3 mb-2"></i>
            <p class="text-muted text-uppercase small fw-semibold mb-0">Total Pending</p>
            <h5 class="fw-bold text-warning mt-1">{{ $pendingCount }}</h5>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-0 text-center h-100 py-3 px-3">
            <i class="bi bi-x-circle-fill text-danger fs-3 mb-2"></i>
            <p class="text-muted text-uppercase small fw-semibold mb-0">Total Rejected</p>
            <h5 class="fw-bold text-danger mt-1">{{ $rejectedCount }}</h5>
        </div>
    </div>

    <div class="col">
        <div class="card shadow-sm border-0 text-center h-100 py-3 px-3">
            <i class="bi bi-truck text-info fs-3 mb-2"></i>
            <p class="text-muted text-uppercase small fw-semibold mb-0">Total Delivered</p>
            <h5 class="fw-bold text-info mt-1">{{ $deliveredCount }}</h5>
        </div>
    </div>

</div>

<div class="card border-0 p-3 shadow mt-3">
    <div class="d-flex align-items-center justify-content-between mb-4">

        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary-subtle text-primary rounded p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-calendar-check-fill fs-5"></i>
            </div>
            <h5 class=" mb-0">Supply Request Records</h5>
        </div>

        <div class="mb-0">
            {{ $supplyRequests->links() }}
        </div>

    </div>

    <form action="{{route('supply.update' , ['id' => 'bulk'])}}" method="POST">
        @csrf
        @method('PUT')
        <!-- Table section -->
        <div class="row mt-4">
            <div class="col-md-12"> <!-- adjust table width here -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 bg-light p-2 rounded border">
                    <div class="d-flex align-items-center gap-2">
                        <span class="small fw-bold text-muted me-1">Bulk Action:</span>
                        <select id="sd_bulk_status" class="form-select form-select-sm w-auto">
                            <option value="">-- Select Status --</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="pending">Pending</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        <button type="button" id="apply_sd_bulk_status" class="btn btn-sm btn-outline-primary">Apply</button>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm"><i class="bi bi-save me-1"></i>Save All</button>
                </div>

                <div class="card shadow-sm">

                    <table class="table table-hover align-middle">
                        <thead class="table-light text-center  text-uppercase small">
                            <tr class="align-middle"> {{-- Added align-middle here --}}

                                <th scope="col" style="width: 50px;">
                                    <input type="checkbox" class="select-all form-check-input">
                                </th>

                                <th class="text-start ps-3">Beneficiary Name</th>
                                <th scope="col">Package</th>
                                <th scope="col">Distribution Method</th>
                                <th scope="col">Date</th>
                                <th scope="col">Distribution Status</th>
                                <th scope="col">Action</th>

                            </tr>
                        </thead>
                        <tbody class="text-center align-middle">
                            @forelse($supplyRequests as $request)
                            <tr>
                                {{-- Checkbox for Bulk Actions (Centered) --}}
                                <td>
                                    <input type="checkbox" name="request_ids[]" class="supply-checkbox" value="{{$request->id}}">
                                </td>

                                <td class="text-start ps-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                                            style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                                            {{ substr($request->beneficiary->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">
                                                {{$request->beneficiary->name}}
                                            </div>
                                            <div class="text-muted small">
                                                {{$request->beneficiary->user->email}}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Package Name --}}
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded bg-light text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="bi bi-box-seam"></i>
                                        </div>
                                        <div>
                                            <span class="fw-semibold text-dark">{{ $request->package->name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Distribution Method --}}
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @php
                                        $isPickup = strtolower($request->distribution_method) === 'pickup';
                                        @endphp
                                        <div class="avatar-sm rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <i class="bi {{ $isPickup ? 'bi-shop text-info' : 'bi-truck text-primary' }}"></i>
                                        </div>
                                        <span class="fw-medium text-dark">{{ ucfirst($request->distribution_method) }}</span>
                                    </div>
                                </td>

                                {{-- Delivery Date & Session --}}
                                <td class="align-middle">
                                    <div class="lh-1">
                                        @if($request->delivery_date && $request->delivery_date->date)
                                        <div class="fw-bold text-dark mb-1">
                                            {{ \Carbon\Carbon::parse($request->delivery_date->date)->format('d M Y') }}
                                        </div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $request->delivery_date->session ?? 'Not set' }}
                                        </div>
                                        @else
                                        <span class="text-muted small">Not Scheduled</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Current Status Badge (Read-only view) --}}
                                <td class="text-center">
                                    @php
                                    $status = $request->distribution_status;
                                    $badgeClass = match($status) {
                                    'approved' => 'bg-success',
                                    'delivered' => 'bg-primary',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-warning text-dark', // pending
                                    };
                                    $statusText = ucfirst($status);
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                                </td>

                                {{-- Status Update Dropdown (Actionable) --}}
                                <td>
                                    <select name="distribution_statuses[{{ $request->id }}]" class="form-select form-select-sm">
                                        <option value="approved" {{ $request->distribution_status == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="pending" {{ $request->distribution_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="rejected" {{ $request->distribution_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        <option value="delivered" {{ $request->distribution_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    </select>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center text-muted">
                                        <i class="bi bi-box-seam display-5 mb-3 text-secondary"></i>
                                        <h5 class="fw-bold mb-1">No Supply Requests Found</h5>
                                        <small class="text-secondary">It looks like there are no matching requests for the current filters.</small>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </form>
</div>