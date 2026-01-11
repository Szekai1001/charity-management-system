@extends('layout.teacher')
@include('components.alerts')

@section('content')

<style>
    :root {
        --primary-soft: #e0e7ff;
        --primary-main: #4f46e5;
        --success-soft: #dcfce7;
        --success-main: #16a34a;
        --danger-soft: #fee2e2;
        --danger-main: #dc2626;
        --card-radius: 16px;
    }

    .text-primary-main {
        color: var(--primary-main);
    }

    .bg-primary-soft {
        background-color: var(--primary-soft);
        color: var(--primary-main);
    }

    .card {
        border: 1px solid rgba(0, 0, 0, 0.04);
        border-radius: var(--card-radius);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .dashboard-header {
        background: linear-gradient(120deg, #4f46e5 0%, #818cf8 100%);
        color: white;
        border-radius: var(--card-radius);
        position: relative;
        overflow: hidden;
    }

    .dashboard-header::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    /* Scrollable areas for widgets */
    .widget-scroll {
        max-height: 320px;
        overflow-y: auto;
        scrollbar-width: thin;
    }

    .widget-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .widget-scroll::-webkit-scrollbar-thumb {
        background-color: #ddd;
        border-radius: 4px;
    }

    /* Mobile Tweaks */
    @media (max-width: 768px) {
        .dashboard-header {
            text-align: center;
        }

        .dashboard-header .d-flex {
            justify-content: center !important;
        }

        .stats-card-body {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
    }
</style>

<div class="row g-3 g-lg-4 mb-3 mb-lg-4">
    <div class="col-lg-8">
        <div class="dashboard-header p-3 p-lg-4 h-100 d-flex flex-column justify-content-center">
            <div class="d-flex justify-content-between align-items-center position-relative z-1">
                <div class="w-100 w-md-auto">
                    <span class="badge bg-white text-primary mb-2 bg-opacity-75 backdrop-blur">
                        <i class="bi bi-calendar3 me-1"></i> {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </span>
                    <h2 class="fw-bold mb-1 fs-3 fs-lg-2">
                        Hello, {{ explode(' ', Auth::user()->teacher->name ?? Auth::user()->name)[0] }}!
                    </h2>
                    <p class="mb-0 opacity-75 small">Ready to inspire young minds today?</p>
                </div>
                <div class="d-none d-md-block text-end">
                    <h1 class="display-6 fw-bold mb-0">{{ \Carbon\Carbon::now()->format('h:i A') }}</h1>
                    <small class="opacity-75">Academic Year: {{ $year }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 bg-white border-0">
            <div class="card-body d-flex align-items-center justify-content-between stats-card-body p-3 p-lg-4">
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1">My Attendance</h6>
                    <h3 class="fw-bold mb-0 display-6">
                        @php
                        $total = $teacherPresentDays + $teacherAbsentDays;
                        $rate = $total > 0 ? round(($teacherPresentDays / $total) * 100) : 0;
                        @endphp
                        {{ $rate }}%
                    </h3>
                    <small class="text-{{ $rate >= 90 ? 'success' : 'warning' }} fw-medium">
                        <i class="bi bi-check-circle-fill me-1"></i> Presence Rate
                    </small>
                </div>
                <div style="width: 90px; height: 90px; min-width: 90px;">
                    <canvas id="teacherDonut"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4 mb-3 mb-lg-4">
    <div class="col-lg-8">
        <div class="card h-100 border-0 bg-white">
            <div class="card-header bg-transparent border-0 pt-3 pt-lg-4 px-3 px-lg-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0 text-dark fs-6 fs-lg-5">Attendance Overview</h5>
                    <small class="text-muted d-none d-sm-block">Performance for current list</small>
                </div>
                <a href="{{ route('attendance.student.teacher') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold">View List</a>
            </div>
            <div class="card-body px-3 px-lg-4 pb-4">
                <div style="height: 250px; width: 100%;">
                    <canvas id="studentBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100 border-0 bg-white">
            <div class="card-header bg-transparent border-0 pt-3 pt-lg-4 px-3 px-lg-4 d-flex justify-content-between">
                <h5 class="fw-bold mb-0 fs-6 fs-lg-5">My Students</h5>
                <span class="badge bg-primary-soft rounded-pill">{{ $students->count() }} Total</span>
            </div>
            <div class="card-body p-0 widget-scroll">
                <ul class="list-group list-group-flush">
                    @forelse($studentsPag as $student)
                    <li class="list-group-item border-0 px-3 px-lg-4 py-2">
                        <div class="d-flex align-items-center p-2 rounded hover-bg-light" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#dashboardStudentInfo{{ $student->id }}">
                            <div class="rounded-circle bg-primary-soft text-primary-main fw-bold d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; font-size: 0.9rem;">
                                {{ strtoupper(substr(trim($student->name), 0, 1)) }}
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <h6 class="mb-0 text-dark fw-semibold text-truncate" style="font-size: 0.95rem;">{{ $student->name }}</h6>
                                <small class="text-muted" style="font-size: 0.8rem;">Grade {{ $student->grade }}</small>
                            </div>
                            <i class="bi bi-chevron-right text-muted opacity-25 small"></i>
                        </div>
                    </li>
                    @empty
                    <li class="text-center p-4 text-muted small">No students assigned.</li>
                    @endforelse
                </ul>
            </div>
            <div class="card-footer bg-white border-0 text-center py-2">
                {{ $studentsPag->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<div class="row g-3 g-lg-4">
    <div class="col-lg-6">
        <div class="card h-100 border-0 bg-white">
            <div class="card-header bg-transparent border-0 pt-3 pt-lg-4 px-3 px-lg-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-wallet2 text-warning me-2"></i>Salary History</h6>
                <a href="{{ route('teacher.salaryView') }}" class="small text-decoration-none fw-bold text-muted">See All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="bg-light small text-muted text-center">
                            <tr>
                                <th class="py-2">Month</th>
                                <th class="py-2">Amount</th>
                                <th class="py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @forelse($salaries->take(5) as $salary)
                            <tr>
                                <td class="fw-medium text-dark py-3">{{ \Carbon\Carbon::create()->month($salary->month)->format('M')  }}</td>
                                <td class="text-muted py-3">RM {{ $salary->salary }}</td>
                                <td class="py-3">
                                    @if(strtolower($salary->payment_status) == 'paid')
                                    <span class="badge bg-success-soft text-success rounded-pill px-2">Paid</span>
                                    @else
                                    <span class="badge bg-danger-soft text-danger rounded-pill px-2">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted small">No data available.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100 border-0 bg-white">
            <div class="card-header bg-transparent border-0 pt-3 pt-lg-4 px-3 px-lg-4">
                <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-activity text-primary me-2"></i>Activity Log</h6>
            </div>
            <div class="card-body px-3 px-lg-4 widget-scroll">
                <div class="border-start border-2 border-light ms-2">
                    @forelse ($activitiesTeacherDashboard as $activity)
                    <div class="position-relative ps-4 mb-4">
                        <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white border border-2" style="width: 10px; height: 10px; margin-top: 5px"></div>
                        <p class="mb-0 fw-semibold text-dark small">{{ $activity->message }}</p>
                        <small class="text-muted" style="font-size: 0.7rem;">{{ $activity->created_at->diffForHumans() }}</small>
                    </div>
                    @empty
                    <div class="ps-4 text-muted small">No recent activities.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@foreach($students as $student)
<div class="modal fade" id="dashboardStudentInfo{{$student->id}}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header border-0 bg-primary-soft py-3 px-4">
                <h6 class="modal-title fw-bold text-primary-main">Student Profile</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <div class="text-center mb-4">
                    <div class="rounded-circle bg-white shadow-sm text-primary fw-bold d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 80px; height: 80px; font-size: 26px;">
                        {{ strtoupper(substr(trim($student->name), 0, 1)) }}
                    </div>
                    <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                    <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">Grade {{ $student->grade }}</span>
                </div>

                <div class="row g-3">

                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">IC Number</small>
                            <span class="fw-bold text-dark">{{ $student->ic }}</span>
                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Contact</small>
                            <span class="fw-bold text-dark">{{ $student->phone }}</span>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1">Guardian Contact</small>
                            <span class="fw-bold text-dark">{{ $student->guardian->phone }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. Teacher Attendance Donut ---
        const teacherCtx = document.getElementById('teacherDonut').getContext('2d');
        const teacherPresent = @json($teacherPresentDays);
        const teacherAbsent = @json($teacherAbsentDays);

        new Chart(teacherCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [teacherPresent, teacherAbsent],
                    backgroundColor: ['#4f46e5', '#f3f4f6'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '75%',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                }
            }
        });

        // --- 2. Student Attendance Bar Chart ---
        const studentData = @json($studentAttendances->items());
        const labels = studentData.map(s => {
            // Shorten names for mobile legibility
            let name = s.name.split(' ')[0];
            return name.length > 8 ? name.substring(0, 8) + '..' : name;
        });
        const percentages = studentData.map(s => s.attendance_percent);
        const backgroundColors = percentages.map(val => val >= 80 ? '#10b981' : (val >= 50 ? '#f59e0b' : '#ef4444'));

        const studentCtx = document.getElementById('studentBarChart').getContext('2d');

        new Chart(studentCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Attendance %',
                    data: percentages,
                    backgroundColor: backgroundColors,
                    borderRadius: 4,
                    barPercentage: 0.6, // Slimmer bars for mobile
                }]
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
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            borderDash: [5, 5],
                            color: '#f8f9fa'
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            },
                            maxRotation: 45,
                            minRotation: 45
                        }
                    }
                }
            }
        });
    });
</script>

@endsection