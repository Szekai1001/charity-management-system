@extends('layout.admin')
@include('components.alerts')
@section('content')

<style>
    body {
        background-color: #f8f9fa;
    }

    /* Soft background utility for icons */
    .bg-soft-primary {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
    }

    .bg-soft-success {
        background-color: rgba(25, 135, 84, 0.1);
        color: #198754;
    }

    .bg-soft-warning {
        background-color: rgba(255, 193, 7, 0.1);
        color: #ffc107;
    }

    .bg-soft-info {
        background-color: rgba(13, 202, 240, 0.1);
        color: #0dcaf0;
    }

    .bg-soft-danger {
        background-color: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    /* Card Hover Effect */
    .card {
        transition: transform 0.2s;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>



<div class="container-fluid">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Dashboard Overview</h3>
            <p class="text-muted mb-0">Welcome back, Admin! Here's what's happening today.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button class="btn btn-white bg-white border shadow-sm fw-medium text-secondary">
                <i class="bi bi-calendar-event me-2"></i> {{ date('M d, Y') }}
            </button>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Students</h6>
                            <h3 class="fw-bold mb-0">{{ $studentApplications['approved']}}</h3>
                        </div>
                        <div class="p-3 rounded-3 bg-soft-primary">
                            <i class="bi bi-mortarboard-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-soft-success rounded-pill"><i class="bi bi-arrow-up"></i> {{ $studentPercentage }}%</span>
                        <span class="text-muted small ms-1">vs last month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Total Teachers</h6>
                            <h3 class="fw-bold mb-0">{{ $teachersCount }}</h3>
                        </div>
                        <div class="p-3 rounded-3 bg-soft-success">
                            <i class="bi bi-person-workspace fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-success small fw-bold">
                            <i class="bi bi-person-check-fill me-1"></i>
                            {{ $teacherPresentCount }} Present
                        </span>

                        <span class="text-danger small ms-1">
                            ({{ $teachersCount - $teacherPresentCount }} Absent)
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Beneficiaries</h6>
                            <h3 class="fw-bold mb-0">{{ $beneficiaryApplications['approved']}}</h3>
                        </div>
                        <div class="p-3 rounded-3 bg-soft-warning">
                            <i class="bi bi-people-fill fs-4 text-warning"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="badge bg-soft-success rounded-pill"><i class="bi bi-plus"></i>{{ $beneficiaryNewThisMonth }}</span>
                        <span class="text-muted small ms-1">New this month</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small fw-bold mb-1">Open Application</h6>
                            <h5 class="fw-bold text-dark mb-0">{{ $formType }}</h5>
                        </div>
                        <div class="p-3 rounded-3 bg-soft-info">
                            <i class="bi bi-file-earmark-check-fill fs-4"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <span class="text-danger small fw-bold"><i class="bi bi-clock"></i> Ends in {{ $remaining }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <form action="{{ route('dashboardAttendance.filter') }}" method="GET">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-secondary">Attendance Trends</h6>
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.dashboard') }}"
                                class="btn btn-sm {{ !request()->has('lastWeek') ? 'btn-primary' : 'btn-outline-primary' }}">
                                This Week
                            </a>

                            <a href="{{ route('admin.dashboard', ['lastWeek' => 1]) }}"
                                class="btn btn-sm {{ request()->has('lastWeek') ? 'btn-primary' : 'btn-outline-primary' }}">
                                Last Week
                            </a>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    <canvas id="attendanceChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <form action="{{ route('dashboardApplication.filter') }}" method="GET">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-secondary">Application Overview</h6>
                        <div>
                            <button type="submit" class="btn btn-primary btn-sm" id="btnStudent" name="student">Student</button>
                            <button type="submit" class="btn btn-primary btn-sm" name="beneficiary" id="btnBeneficiary">Beneficiary</button>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                        <div style="width: 100%; max-width: 250px;">
                            <canvas id="appChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <span class="badge bg-success me-2">Approved</span>
                            <span class=" badge bg-warning text-dark me-2">Processing</span>
                            <span class="badge bg-danger">Rejected</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-secondary">Recent Supply Requests</h6>
                <a href="{{ route('supplyRequest.show') }}" class="btn btn-sm btn-light text-primary fw-bold">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-center text-uppercase small text-muted">
                        <tr>
                            <th>Package Name</th>
                            <th>Beneficiary</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach($recentSupplyRequests as $request)
                        <tr>
                            <td class="fw-medium">{{ $request->package->name }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ $request->beneficiary ? substr($request->beneficiary->name, 0, 2) : 'NA' }}
                                    </div>
                                    {{ $request->beneficiary->name}}
                                </div>
                            </td>
                            <td class="text-muted small">{{ $request->created_at }}</td>
                            <td>
                                @if($request->distribution_status === 'approved')
                                <span class="badge bg-success">Approved</span>
                                @elseif($request->distribution_status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($request->distribution_status === 'rejected')
                                <span class="badge bg-danger">Rejected</span>
                                @else
                                <span class="badge bg-info">Delivered</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold text-secondary">Activity Log</h6>
            </div>

            <div class="card-body p-3">
                <div class="vstack gap-2" style="max-height: 400px; overflow-y: auto;">

                    @foreach ($activity_logs as $activity)
                    <div class="d-flex align-items-center p-3 rounded bg-light border-start border-4 shadow-sm">

                        <div class="flex-grow-1 lh-1">
                            <p class="mb-1 text-dark fw-bold small">
                                {{ $activity->message }}
                            </p>
                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock-history me-1"></i>{{ $activity->created_at->diffForHumans() }}
                            </small>
                        </div>

                        <div class="text-muted opacity-25">
                            <i class="bi bi-chevron-right small"></i>
                        </div>

                    </div>
                    @endforeach

                    @if($activity_logs->isEmpty())
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted py-5">
                        <i class="bi bi-inbox fs-1 opacity-25 mb-2"></i>
                        <p class="small">No Activity Found</p>
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Line Chart (Attendance)
    const ctxLine = document.getElementById('attendanceChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: @json($weeksLabel),
            datasets: [{
                    label: 'Student Attendance',
                    data: @json($studentAttendances),
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4
                },
                {
                    label: 'Teacher Attendance',
                    data: @json($teacherAttendances),
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.05)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true, // Starts at 0
                    ticks: {
                        stepSize: 1, // Since you only have 5 students, show 1, 2, 3...
                        precision: 0 // Don't show decimals like 1.5
                    },
                    border: {
                        dash: [4, 4]
                    }
                }
            }
        }
    });

    const studentData = @json($studentApplications);
    const beneficiaryData = @json($beneficiaryApplications);

    // 1. Doughnut Chart (Applications)
    const ctxDoughnut = document.getElementById('appChart').getContext('2d');

    // Save the chart instance in a variable
    const chart = new Chart(ctxDoughnut, {
        type: 'doughnut',
        data: {
            labels: ['Approved', 'Processing', 'Rejected'],
            datasets: [{
                data: [studentData.approved, studentData.processing, studentData.rejected],
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // When clicking "Student"
    document.getElementById('btnStudent').addEventListener('click', (e) => {
        e.preventDefault(); // prevent form submission if inside a <form>
        chart.data.datasets[0].data = [
            studentData.approved,
            studentData.processing,
            studentData.rejected
        ];
        chart.update();
    });

    // When clicking "Beneficiary"
    document.getElementById('btnBeneficiary').addEventListener('click', (e) => {
        e.preventDefault(); // prevent form submission if inside a <form>
        chart.data.datasets[0].data = [
            beneficiaryData.approved,
            beneficiaryData.processing,
            beneficiaryData.rejected
        ];
        chart.update();
    });
</script>





@endsection