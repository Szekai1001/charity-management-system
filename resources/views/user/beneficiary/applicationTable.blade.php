@forelse ($supplyRequests as $index => $request)
<tr class="align-middle">
    <td class="text-secondary small fw-bold ps-4">{{ $index + 1 }}</td>

    <td>
        <div class="d-flex align-items-center">
            <div class="avatar bg-light text-primary rounded-3 p-2 me-3 d-none d-md-block">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <span class="d-block fw-bold text-dark">
                    {{ $request->package->name ?? 'Unknown Package' }}
                </span>
                </div>
        </div>
    </td>

    <td class="text-center text-secondary">
        @if(optional($request->delivery_date)->date)
            <i class="bi bi-calendar3 me-1 small"></i> 
            {{ $request->delivery_date->date }}
        @else
            <span class="text-muted fst-italic">--</span>
        @endif
    </td>

    <td class="text-center">
        @php $method = strtolower($request->distribution_method ?? ''); @endphp
        
        @if($method === 'Delivery')
            <span class="text-dark small fw-semibold">
                <i class="bi bi-truck text-primary me-1"></i> Delivery
            </span>
        @elseif($method === 'Pickup')
            <span class="text-dark small fw-semibold">
                <i class="bi bi-shop text-info me-1"></i> Pickup
            </span>
        @else
            <span class="text-muted">N/A</span>
        @endif
    </td>

    <td class="text-center">
        @php $status = $request->distribution_status; @endphp

        @if($status === 'approved')
            <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-3">
                <i class="bi bi-check-circle-fill me-1"></i> Approved
            </span>

        @elseif($status === 'rejected')
            <span class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-3">
                <i class="bi bi-x-circle-fill me-1"></i> Rejected
            </span>

        @elseif($status === 'delivered')
            <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-3">
                <i class="bi bi-box-seam-fill me-1"></i> Delivered
            </span>

        @else
            <span class="badge rounded-pill bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3">
                <i class="bi bi-hourglass-split me-1"></i> Pending
            </span>
        @endif
    </td>
</tr>

@empty
<tr>
    <td colspan="5" class="text-center py-5">
        <div class="d-flex flex-column align-items-center justify-content-center">
            <div class="bg-light rounded-circle p-4 mb-3">
                <i class="bi bi-inbox text-secondary fs-1"></i>
            </div>
            <h6 class="fw-bold text-dark">No Records Found</h6>
            <p class="text-muted small mb-0">
                We couldn't find any applications matching your filters.
            </p>
        </div>
    </td>
</tr>
@endforelse