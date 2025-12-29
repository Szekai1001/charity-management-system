@extends('layout.beneficiary')
@include('components.alerts')

@section('content')
<div class="container py-4">

    {{-- Header Section --}}
    <div class="d-flex flex-column flex-md-row justify-content-end align-items-md-center mb-4 gap-3">
        <div>
            @if(!$hasCurrentMonthApp)
                <a href="{{ route('supplyRequest.create') }}" class="btn btn-primary shadow-sm px-4">
                    <i class="bi bi-plus-lg me-1"></i> New Request
                </a>
            @else
                <button class="btn btn-outline-secondary px-4" disabled>
                    <i class="bi bi-check2-all me-1"></i> {{ date('F') }} Request Submitted
                </button>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        {{-- Yearly Progress --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Yearly Progress</p>
                            <h4 class="fw-bold mb-0">{{ $applicationsCount }} / 12</h4>
                            <small class="text-muted">Total Requests</small>
                        </div>
                        <div class="bg-primary-subtle text-primary rounded-circle p-3">
                            <i class="bi bi-calendar3 fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Monthly Status Card --}}
        @php
            // Get current month's app status if it exists
            $currentMonthApp = $applications->first(fn($app) => $app->created_at->month == date('n'));
            $status = $currentMonthApp->distribution_status ?? 'no_request';
            
            $statusConfig = match($status) {
                'pending'   => ['color' => 'warning', 'icon' => 'hourglass-split', 'label' => 'Pending Review'],
                'approved'  => ['color' => 'success', 'icon' => 'check-circle', 'label' => 'Approved'],
                'delivered' => ['color' => 'info', 'icon' => 'box-seam', 'label' => 'Delivered'],
                'rejected'  => ['color' => 'danger', 'icon' => 'x-circle', 'label' => 'Rejected'],
                default     => ['color' => 'secondary', 'icon' => 'dash-circle', 'label' => 'Not Applied'],
            };
        @endphp

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-{{ $statusConfig['color'] }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">{{ date('F') }} Status</p>
                            <h4 class="fw-bold mb-0 text-capitalize">{{ $statusConfig['label'] }}</h4>
                            <small class="text-{{ $statusConfig['color'] }}">
                                @if($status == 'no_request') 
                                    Action required
                                @else 
                                    Updated {{ $currentMonthApp->updated_at->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                        <div class="bg-{{ $statusConfig['color'] }}-subtle text-{{ $statusConfig['color'] }} rounded-circle p-3">
                            <i class="bi bi-{{ $statusConfig['icon'] }} fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Approved Count --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted small mb-1 text-uppercase fw-bold">Total Approved</p>
                            <h4 class="fw-bold mb-0">{{ $approvedCount }}</h4>
                            <small class="text-muted">Successful applications</small>
                        </div>
                        <div class="bg-info-subtle text-info rounded-circle p-3">
                            <i class="bi bi-patch-check fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Annual Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold text-dark mb-0">Annual Records</h5>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
               <table class="table table-hover align-middle mb-0">
    <thead class="bg-light text-secondary small text-uppercase">
        <tr>
            <th class="py-3 ps-4">Month</th>
            <th class="py-3">Package Category</th>
            <th class="py-3 text-center">Submission Date</th>
            <th class="py-3 text-center">Method</th> {{-- New Column --}}
            <th class="py-3 text-center">Status</th>
            <th class="py-3 ps-4">Message</th> {{-- New Column --}}
        </tr>
    </thead>
    <tbody>
        @forelse($applications as $app)
        <tr>
            <td class="ps-4 fw-bold text-dark">
                {{ \Carbon\Carbon::parse($app->created_at)->format('F') }}
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="avatar-sm bg-light rounded p-2 me-2">
                        <i class="bi bi-box2-heart text-primary"></i>
                    </span>
                    <span>{{ $app->package->name ?? 'Monthly Assistance' }}</span>
                </div>
            </td>
            <td class="text-center">{{ $app->created_at->format('d M Y') }}</td>
            
            {{-- Distribution Method Column --}}
            <td class="text-center">
                <span class="small text-dark fw-medium">
                    @if($app->distribution_method == 'Pickup')
                        <i class="bi bi-shop me-1 text-muted"></i> Self Pickup
                    @else
                        <i class="bi bi-truck me-1 text-muted"></i> Home Delivery
                    @endif
                </span>
            </td>

            <td class="text-center">
                @php
                    $color = match($app->distribution_status) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        'delivered' => 'info',
                        default => 'secondary'
                    };
                @endphp
                <span class="badge bg-{{ $color }}-subtle text-{{ $color }} px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.65rem;">
                    {{ $app->distribution_status }}
                </span>
            </td>

            {{-- Message Column --}}
            <td class="ps-4 small text-secondary">
                @switch($app->distribution_status)
                    @case('pending')
                        Your application is being reviewed. We will notify you once accepted.
                        @break
                    @case('approved')
                        Accepted! Your package is being prepared for {{ $app->distribution_method == 'Pickup' ? 'pickup' : 'delivery to your home' }}.
                        @break
                    @case('delivered')
                        Package successfully {{ $app->distribution_method == 'Pickup' ? 'collected by you' : 'delivered to your home' }}.
                        @break
                    @case('rejected')
                        Application not accepted. Contact support for more details.
                        @break
                    @default
                        Processing status update...
                @endswitch
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" class="text-center py-5">
                <i class="bi bi-inbox text-muted mb-3 d-block fs-1 opacity-25"></i>
                <p class="text-muted mb-0">No application records found for {{ date('Y') }}</p>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>    
            </div>
        </div>
    </div>
</div>
@endsection