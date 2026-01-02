@extends('layout.beneficiary')
@include('components.alerts')

@section('content')

<style>
    /* Default (Mobile) Styles */
    .avatar-responsive {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }

    .greeting-text {
        font-size: 1.25rem;
        /* Smaller font for mobile */
    }

    /* Desktop Styles (md and up) */
    @media (min-width: 768px) {
        .avatar-responsive {
            width: 70px;
            height: 70px;
            font-size: 1.75rem;
        }

        .greeting-text {
            font-size: 2rem;
            /* Big font for desktop */
        }
    }
</style>

@php
// Dynamic Greeting Logic
$hour = now()->hour;
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening' );
    $firstName=explode(' ', Auth::user()->beneficiary->name)[0]; // Get just the first name
@endphp

<div class="container-fluid px-4 py-4" style="max-width: 1400px;">
    
    <div class="row mb-4">
    <div class="col-12">
        <div class="card rounded-4 border-0 shadow-sm bg-white position-relative overflow-hidden">
            <div class="card-body p-3 p-lg-5"> 
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    
                    <div class="d-flex align-items-center gap-3 mb-2 mb-md-0">
                        
                        <div class="avatar-responsive d-flex align-items-center justify-content-center text-white rounded-circle shadow-sm flex-shrink-0" 
                             style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                            {{ substr(Auth::user()->beneficiary->name, 0, 1) }}
                        </div>

                        <div> 
                            <h6 class="text-uppercase text-muted small fw-bold mb-0 mb-md-1" style="font-size: 0.75rem;">
                                {{ now()->format('l, d M Y') }}
                            </h6>
                            
                            <h2 class="greeting-text fw-bold text-dark mb-0 lh-sm">
                                {{ $greeting }}, {{ $firstName }}!
                            </h2>
                            
                            <p class="text-secondary mb-0 small d-none d-sm-block mt-1"> 
                                Here is what' s happening today.
    </p>
    </div>
    </div>

    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">

        @if($formControl)
        <div class="d-flex justify-content-between w-100 w-md-auto align-items-center gap-3">
            <div class="text-start text-md-end">
                <span class="d-block fw-bold text-dark h6 mb-0">{{ $remainInTime }}</span>
                <span class="text-muted" style="font-size: 0.75rem">Closes In</span>
            </div>
            <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary btn-sm rounded-pill d-md-none">
                <i class="bi bi-person-gear"></i>
            </a>
        </div>
        @endif

        <a href="{{ route('user.profile') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-semibold d-none d-md-flex align-items-center">
            <i class="bi bi-person-gear me-2"></i> Profile
        </a>
    </div>

    </div>
    </div>
    </div>
    </div>
    </div>

    @if(isset($deliveryReminder) && $deliveryReminder)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-warning border-0 shadow-sm rounded-4 d-flex align-items-center" role="alert">
                <div class="bg-warning bg-opacity-25 text-warning rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-bell-fill fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0 text-dark">Upcoming Action Required</h6>
                    <p class="mb-0 text-muted small">{{ $deliveryReminder }}</p>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    @endif

    <div class="row g-4 mb-4">

        <div class="col-md-6 col-xl-4">
            <div class="card rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white;">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="text-white-50 text-uppercase small fw-bold mb-1">Current Status</h6>
                            <h2 class="fw-bold mb-0">
                                @if(!$supplyRequest) Not Applied
                                @else {{ ucfirst($supplyRequest->distribution_status) }}
                                @endif
                            </h2>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-2">
                            <i class="bi bi-activity fs-4 text-white"></i>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if(!$supplyRequest)
                        @if($formControl)
                        <p class="small text-white-50 mb-3">Applications are open for this month.</p>
                        <a href="{{ route('supplyRequest.create') }}" class="btn btn-light text-primary fw-bold w-100 rounded-3 shadow-sm">
                            Apply Now
                        </a>
                        @else
                        <p class="small text-white-50 mb-0">Applications are currently closed.</p>
                        @endif
                        @else
                        <p class="small text-white-50 mb-3">Last updated: {{ $supplyRequest->updated_at->diffForHumans() }}</p>
                        <a href=" {{ route('beneficiary.viewPastApplication') }}" class="btn btn-outline-light w-100 rounded-3 text-white">View Details</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-4">
            <div class="card rounded-4 h-100 bg-white shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                        <h6 class="fw-bold text-dark mb-0">Application Window</h6>
                    </div>

                    @if($formControl)
                    <div class="text-center py-2">
                        <h3 class="fw-bold text-dark mb-0">{{ $remainInTime }}</h3>
                        <span class="text-muted small">Remaining time</span>
                    </div>
                    <div class="progress mt-3 rounded-pill" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $progressPercentage }}%"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>Open</span>
                        <span>Close: {{ \Carbon\Carbon::parse($formControl->close_date)->format('d M') }}</span>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <h5 class="text-muted">No Active Cycle</h5>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-12 col-xl-4">
            <div class="card rounded-4 border-0 shadow-sm h-100" style="background: #ffffff;">
                <div class="card-body p-3 p-md-4"> {{-- Reduced padding on mobile --}}

                    {{-- Header: Stacked on mobile, side-by-side on desktop --}}
                    <div class="d-flex flex-row justify-content-between align-items-start mb-3 mb-md-4">
                        <div class="flex-grow-1">
                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill fw-bold mb-2" style="font-size: 0.7rem;">
                                MONTHLY ALLOTMENT
                            </span>
                            @if($supplyRequest && $supplyRequest->package)
                            <h4 class="fw-bold text-dark mb-0 fs-5 fs-md-4">{{ $supplyRequest->package->name }}</h4>
                            @endif
                        </div>
                        <div class="text-end ms-2 d-none d-md-block">
                            <i class="bi bi-calendar3 text-muted"></i>
                            <div class="small text-muted fw-bold" style="font-size: 0.75rem;">{{ date('M Y') }}</div>
                        </div>
                    </div>

                    @if($supplyRequest && $supplyRequest->package)
                    {{-- Description Box: More compact on mobile --}}
                    <div class="p-3 rounded-4 bg-light border-0 mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-info-circle-fill text-primary me-2"></i>
                            <span class="text-uppercase text-dark fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Package Description</span>
                        </div>
                        <p class="text-secondary mb-0 lh-sm" style="font-size: 0.85rem;">
                            {{ $supplyRequest->package->description ?? 'Standard monthly support package.' }}
                        </p>
                    </div>

                    {{-- Status Footer: Mobile friendly alignment --}}
                    <div class="d-flex align-items-center justify-content-between mt-auto pt-2">
                        <div class="small">
                            <span class="text-muted">Status:</span>
                            @php
                            $statusColor = match($supplyRequest->distribution_status) {
                            'approved' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                            'delivered' => 'info',
                            default => 'secondary'
                            };
                            @endphp
                            <span class="text-{{ $statusColor }} fw-bold ms-1 text-capitalize">{{ $supplyRequest->distribution_status }}</span>
                        </div>
                    </div>
                    @else
                    {{-- Mobile Empty State --}}
                    <div class="text-center py-4 py-md-5">
                        <div class="bg-light rounded-circle d-inline-flex p-3 mb-3">
                            <i class="bi bi-plus-lg text-primary fs-4"></i>
                        </div>
                        <p class="text-muted small px-3">No request found for this cycle. Apply now to receive your supplies.</p>
                        <a href="{{ route('supplyRequest.create') }}" class="btn btn-primary rounded-pill px-4 w-100 d-md-inline-block w-md-auto mt-2">
                            Request Now
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($supplyRequest)
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card rounded-4 shadow-sm bg-white border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold m-0">Tracking History</h6>
                </div>
                <div class="card-body">
                    @php
                    $step = 0;
                    if($supplyRequest->distribution_status == 'approved') $step = 1;
                    if($supplyRequest->distribution_status == 'delivered') $step = 2;
                    if($supplyRequest->distribution_status == 'rejected') $step = -1;
                    @endphp

                    {{-- DESKTOP VIEW: Horizontal Stepper --}}
                    <div class="d-none d-md-block position-relative my-4 mx-4">
                        <div class="progress bg-light" style="height: 3px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $step >= 2 ? '100%' : ($step == 1 ? '50%' : '0%') }}"></div>
                        </div>

                        <div class="d-flex justify-content-between position-absolute w-100 top-0 start-0 translate-middle-y">
                            <div class="bg-white p-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white bg-success" style="width: 32px; height: 32px;">
                                    <i class="bi bi-check"></i>
                                </div>
                            </div>
                            <div class="bg-white p-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white {{ $step >= 1 ? 'bg-success' : ($step == -1 ? 'bg-danger' : 'bg-secondary bg-opacity-25') }}" style="width: 32px; height: 32px;">
                                    @if($step == -1) <i class="bi bi-x"></i> @elseif($step >= 1) <i class="bi bi-check"></i> @else 2 @endif
                                </div>
                            </div>
                            <div class="bg-white p-1">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white {{ $step >= 2 ? 'bg-success' : 'bg-secondary bg-opacity-25' }}" style="width: 32px; height: 32px;">
                                    @if($step >= 2) <i class="bi bi-check"></i> @else 3 @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-3">
                            <div class="text-center ms-n3">
                                <span class="d-block fw-bold text-dark small">Submitted</span>
                                <small class="text-muted" style="font-size: 0.7rem">{{ $supplyRequest->created_at->format('d M') }}</small>
                            </div>
                            <div class="text-center">
                                <span class="d-block fw-bold text-dark small">Processed</span>
                                <small class="text-muted" style="font-size: 0.7rem">{{ $step != 0 ? 'Done' : 'Pending' }}</small>
                            </div>
                            <div class="text-center me-n3">
                                <span class="d-block fw-bold text-dark small">Delivered</span>
                                <small class="text-muted" style="font-size: 0.7rem">{{ $step == 2 ? 'Done' : 'Wait' }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- MOBILE VIEW: Vertical Stepper --}}
                    <div class="d-block d-md-none py-3">
                        <div class="position-relative">
                            {{-- The Continuous Vertical Line --}}
                            <div class="position-absolute start-0 top-0 bottom-0 border-start border-2 ms-3"
                                style="height: calc(100% - 32px); margin-top: 16px; border-color: #dee2e6 !important; z-index: 0;">
                                {{-- Progress Highlight Line (Green part) --}}
                                <div class="border-start border-2 border-success"
                                    style="height: {{ $step >= 2 ? '100%' : ($step == 1 ? '50%' : '0%') }}; margin-left: -2px;">
                                </div>
                            </div>

                            {{-- Step 1 --}}
                            <div class="d-flex mb-4 position-relative" style="z-index: 1;">
                                <div class="me-3 text-center" style="width: 32px;">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        <i class="bi bi-check"></i>
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <span class="d-block fw-bold text-dark small">Submitted</span>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">Application received on {{ $supplyRequest->created_at->format('d M Y') }}</p>
                                </div>
                            </div>

                            {{-- Step 2 --}}
                            <div class="d-flex mb-4 position-relative" style="z-index: 1;">
                                <div class="me-3 text-center" style="width: 32px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm {{ $step >= 1 ? 'bg-success' : ($step == -1 ? 'bg-danger' : 'bg-secondary-subtle') }}" style="width: 32px; height: 32px;">
                                        @if($step == -1) <i class="bi bi-x"></i> @elseif($step >= 1) <i class="bi bi-check"></i> @else <small>2</small> @endif
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <span class="d-block fw-bold text-dark small">Processed</span>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                        @if($step == -1) Request was rejected @elseif($step >= 1) Application approved @else Awaiting admin review @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Step 3 --}}
                            <div class="d-flex position-relative" style="z-index: 1;">
                                <div class="me-3 text-center" style="width: 32px;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm {{ $step >= 2 ? 'bg-success' : 'bg-secondary-subtle' }}" style="width: 32px; height: 32px;">
                                        @if($step >= 2) <i class="bi bi-check"></i> @else <small>3</small> @endif
                                    </div>
                                </div>
                                <div class="pt-1">
                                    <span class="d-block fw-bold text-dark small">Delivered</span>
                                    <p class="text-muted mb-0" style="font-size: 0.75rem;">
                                        @if($step >= 2) Items collected/delivered @else Final step of the process @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        {{-- Your Existing Delivery Details Code --}}
        <div class="col-lg-4">
            <div class="card rounded-4 shadow-sm bg-white border-0 h-100">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <h6 class="fw-bold m-0">Delivery Details</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start mb-3">
                        <div class="me-3 mt-1 text-primary"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <span class="d-block small text-muted text-uppercase fw-bold">Method</span>
                            <span class="fw-bold text-dark">{{ ucfirst($supplyRequest->distribution_method) }}</span>
                        </div>
                    </div>
                    @if($supplyRequest->distribution_method === 'Delivery')
                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border-0">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-truck text-primary"></i>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <span class="d-block small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Preferred Date</span>
                            <span class="fw-bold text-dark d-block text-truncate">
                                {{ \Carbon\Carbon::parse($supplyRequest->delivery_date->date)->format('d M Y') }}
                            </span>
                        </div>
                    </div>
                    @else
                    <div class="d-flex align-items-center p-3 rounded-4 bg-light border-0">
                        <div class="flex-shrink-0 me-3">
                            <div class="rounded-circle bg-info bg-opacity-10 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-clock-history text-info"></i>
                            </div>
                        </div>
                        <div class="min-w-0">
                            <span class="d-block small text-muted text-uppercase fw-bold" style="font-size: 0.65rem; letter-spacing: 0.5px;">Available Time</span>
                            <span class="fw-bold text-dark d-block">
                              Morning (9AM - 12PM)
                            </span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @else
    {{-- NEW: Empty State Design --}}
    <div class="card rounded-4 shadow-sm border-0 py-4 py-md-5">
        <div class="card-body text-center p-3 p-md-4">
            <div class="mb-3">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                    <i class="bi bi-box-seam text-secondary" style="font-size: 2rem;"></i>
                </div>
            </div>
            <h5 class="fw-bold text-dark">No Active Request</h5>
            <p class="text-muted mb-4 small px-0 px-md-5">You haven't submitted a supply request for this month yet. <br class="d-none d-md-block">Apply now to receive your monthly support.</p>

            {{-- Make sure this route matches your actual route name --}}
            <a href="{{ route('supplyRequest.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold w-100 w-md-auto">
                <i class="bi bi-plus-lg me-2"></i>Apply Now
            </a>
        </div>
    </div>
    @endif
    </div>
    @endsection