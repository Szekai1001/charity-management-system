@extends('layout.teacher')
@include('components.alerts')

@section('content')

<div class="container-fluid px-0">

    <div class="row g-4 mb-4">

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Current Month</span>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold text-dark mb-0">
                        RM {{ number_format($currentMonthSalary->salary ?? 0, 2) }}
                    </h3>
                    <small class="text-muted">{{ $currentMonthName }} {{ $currentYear }}</small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-success rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Payment Date</span>
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-calendar-check fs-5"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-dark mb-0">
                        {{ $currentMonthSalary?->payment_date ? \Carbon\Carbon::parse($currentMonthSalary->payment_date)->format('d M, Y') : '--' }}
                    </h4>
                    <small class="text-success fw-bold">
                        {{ $currentMonthSalary?->payment_date ? \Carbon\Carbon::parse($currentMonthSalary->payment_date)->diffForHumans() : '' }}
                    </small>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info rounded-3">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-uppercase text-muted small fw-bold">Status</span>
                        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-receipt fs-5"></i>
                        </div>
                    </div>
                    <div>
                        @if(($currentMonthSalary->payment_status ?? '') == 'paid')
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-check-circle-fill me-1"></i> Paid
                        </span>
                        @elseif(($currentMonthSalary->payment_status ?? '') == 'unpaid')
                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-2 fs-6">
                            <i class="bi bi-clock-fill me-1"></i> Pending
                        </span>
                        @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 fs-6">
                            - No Record -
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

        <div class="card-header bg-white py-3 px-4 border-bottom">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="fw-bold text-dark mb-0">Payment History</h5>
                </div>

                <div class="col-md-6">
                    <form action="{{ route('teacher.salaryView') }}" method="GET">
                        <div class="d-flex justify-content-md-end align-items-center gap-2">
                            <label for="salaryViewYear" class="small fw-bold text-muted text-uppercase mb-0">Filter Year:</label>
                            <div class="input-group input-group-sm" style="max-width: 150px;">
                                <input type="number" id="salaryViewYear" name="salaryViewYear"
                                    class="form-control"
                                    value="{{ request('salaryViewYear', $selectedYear) }}"
                                    min="2000" max="2100">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="ps-4 py-3">Month</th>
                            <th class="text-center py-3">Hours Worked</th>
                            <th class="text-end py-3">Salary (RM)</th>
                            <th class="text-center py-3">Payment Date</th>
                            <th class="text-center py-3 pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaryDetails as $salary)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">{{ \Carbon\Carbon::create()->month($salary->month)->format('F') }}</span>

                            </td>

                            <td class="text-center text-muted">
                                @php
                                $hours = floor($salary->hours_worked);
                                $minutes = ($salary->hours_worked - $hours) * 60;
                                @endphp

                                {{ $hours }} hrs
                                @if($minutes > 0)
                                {{ round($minutes) }} mins
                                @endif
                            </td>

                            <td class="text-end fw-bold text-dark">
                                {{ number_format($salary->salary, 2) }}
                            </td>
                            <td class="text-center text-muted small">
                                {{ $salary->payment_date ? \Carbon\Carbon::parse($salary->payment_date)->format('d M Y') : '-' }}
                            </td>

                            <td class="text-center pe-4">
                                @if(strtolower($salary->payment_status) == 'paid')
                                <span class="badge bg-success">Paid</span>
                                @elseif(strtolower($salary->payment_status) == 'unpaid')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @else
                                <span class="badge bg-secondary">{{ ucfirst($salary->payment_status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-wallet2 display-6 opacity-25"></i>
                                    <p class="mt-2 mb-0 fw-medium">No salary records found for {{ request('salaryViewYear', $selectedYear) }}</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($salaryDetails->hasPages())
        <div class="card-footer bg-white border-top py-3 px-4">
            <div class="d-flex justify-content-end">
                {{ $salaryDetails->links() }}
            </div>
        </div>
        @endif

    </div>

</div>

@endsection