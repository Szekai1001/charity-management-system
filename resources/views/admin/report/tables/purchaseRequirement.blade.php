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
        </span>
    </div>
</div>



<!-- Table section -->
<div class="card border-0 shadow p-3 mt-5">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex gap-3 align-items-center">
            <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
            <h4 class="fw-semibold mb-0">Purchase Requirements records</h4>
        </div>
    </div>
    <div class="d-flex justify-content-end">
        @if($purchaseRequirements->isNotEmpty())
        <div class="text-end">
            <small class="text-uppercase text-secondary fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Total Estimated Cost
            </small>
            <h4 class="mb-0 fw-bolder text-primary">
                RM {{ number_format($purchaseRequirements->sum('subtotal'), 2) }}
            </h4>
        </div>
        @endif
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-uppercase small text-secondary fw-bold">
                            <tr>
                                <th scope="col" class="ps-4 text-center" style="width: 80px;">#</th>

                                <th scope="col">Item Name</th>

                                <th scope="col" class="text-center text-nowrap" style="width: 150px;">Qty</th>

                                <th scope="col" class="text-end pe-4 text-nowrap" style="width: 200px;">Subtotal (RM)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchaseRequirements as $row)
                            <tr>
                                <td class="ps-4 text-center text-muted small">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $row->item->name }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-secondary bg-opacity-10 text-dark border border-secondary border-opacity-25 px-3">
                                        {{ $row->total_quantity }}
                                    </span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-dark font-monospace">
                                    {{ number_format($row->subtotal, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted opacity-25 mb-3">
                                        <i class="bi bi-basket3 display-4"></i>
                                    </div>
                                    <h6 class="fw-semibold text-secondary">No requirements found</h6>
                                    <p class="small text-muted mb-0">Items will appear here once processed.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>