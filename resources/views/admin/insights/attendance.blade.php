@extends('layout.admin')
@section('content')

<form action="{{ route('attendance.filter') }}" method="POST">
@csrf
<div class="row g-3 mb-4 align-items-end">
        <div class="col-md-2">
            <label for="year" class="form-label">Year:</label>
            <input type="number" id="year" name="year"
                class="form-control filter shadow-sm"
                value="{{ request('year', now()->year) ?? '' }}" min="2000" max="2100">
        </div>
    
        <div class="col-md-2">
            <label for="month" class="form-label">Month:</label>
            <select name="month" id="month" class="form-select filter shadow-sm">
                <option value="">-- Select Month --</option>
                @foreach (range(1, 12) as $m)
                <option value="{{ $m }}"
                    {{ (int) request('month', now()->month) === (int)$m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <input type="submit" class="btn btn-primary">
        </div>
    </div>
</form>


<div class="row g-4 justify-content-center">

    <div class="col-md-3">
        <div class="card text-center p-4 rounded-4 bg-success-subtle border-0 shadow-sm h-100">
            <i class="bi bi-person-check-fill text-success fs-1 mb-2"></i>
            <p class="fw-semibold text-uppercase small text-muted">Student Attendance Rate</p>
            <h3 class="fw-bold text-success" id="studentRate">{{ $studentAttendanceRate }}%</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-4 rounded-4 bg-warning-subtle border-0 shadow-sm h-100">
            <i class="bi bi-person-x-fill text-warning fs-1 mb-2"></i>
            <p class="fw-semibold text-uppercase small text-muted">Teacher Attendance Rate</p>
            <h3 class="fw-bold text-warning" id="teacherRate">{{ $teacherAttendanceRate }}%</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-4 rounded-4 bg-danger-subtle border-0 shadow-sm h-100"
            data-bs-toggle="tooltip" title="Student absent: {{ $studentAbsent }}, Teacher absent: {{ $teacherAbsent }}">
            <i class="bi bi-person-dash-fill text-danger fs-1 mb-2"></i>
            <p class="fw-semibold text-uppercase small text-muted">Total Absences</p>
            <h3 class="fw-bold text-danger" id="totalAbsent">{{ $totalAbsent }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-4 rounded-4 bg-info-subtle border-0 shadow-sm h-100"
            data-bs-toggle="tooltip" title="Student excused: {{ $studentExcused }}, Teacher excused: {{ $teacherExcused }}">
            <i class="bi bi-person-dash-fill text-info fs-1 mb-2"></i>
            <p class="fw-semibold text-uppercase small text-muted">Total Excused</p>
            <h3 class="fw-bold text-info" id="totalExcused">{{ $totalExcused }}</h3>
        </div>
    </div>


    <div class="col-md-6">
        <div class="card p-3 shadow-sm rounded-4 h-100">
            <h5 class="fw-semibold mb-3">Student Attendance Trends Over Time</h5>
            <canvas id="studentAttendanceChart" height="200"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card p-3 shadow-sm rounded-4 h-100">
            <h5 class="fw-semibold mb-3">Teacher Attendance Trends Over Time</h5>
            <canvas id="teacherAttendanceChart" height="200"></canvas>
        </div>
    </div>


    <div class="col-12">
        <div class="card p-4 shadow-sm rounded-4 mt-3">
            <h5 class="fw-semibold mb-3">Absences Record</h5>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="absentCount" class="form-label">Absent more than</label>
                    <input type="number" name="absentCount" id="absentCount"
                        class="form-control absentFilter"
                        value="{{ request('absentCount', 3) }}">
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4 p-3 h-100">
                        <h5 class="fw-semibold mb-3">Student Absences</h5>
                        {{-- **FIX 3: Added ID to the container for AJAX update** --}}
                        <div class="table-responsive" id="studentAbsentTable">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Absences</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($studentAbsentData as $student)
                                    <tr>
                                        <td>{{ $student['name'] }}</td>
                                        <td><span class="badge bg-danger">{{ $student['absent_count'] }}</span></td>
                                        <td>{{ $student['rate'] }}%</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No students meet the criteria</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm rounded-4 p-3 h-100">
                        <h5 class="fw-semibold mb-3">Teacher Absences</h5>
                        {{-- **FIX 3: Added ID to the container for AJAX update** --}}
                        <div class="table-responsive" id="teacherAbsentTable">
                            <table class="table table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Teacher Name</th>
                                        <th>Absences</th>
                                        <th>Attendance %</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($teacherAbsentData as $teacher)
                                    <tr>
                                        <td>{{ $teacher['name'] }}</td>
                                        <td><span class="badge bg-danger">{{ $teacher['absent_count'] }}</span></td>
                                        <td>{{ $teacher['rate'] }}%</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No teachers meet the criteria</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>


    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        // Ensure bootstrap is available here
        // return new bootstrap.Tooltip(tooltipTriggerEl, {
        //     customClass: 'tooltip-white'
        // });
        // NOTE: Commented out bootstrap dependent code if library is not loaded globally
    });

    // **FIX 4: Chart Update Functions Defined**
    const updateChart = (chartInstance, dailyData) => {
        chartInstance.data.labels = Object.keys(dailyData);
        chartInstance.data.datasets[0].data = Object.values(dailyData);
        chartInstance.update();
    };

    // Student attendance chart
    const studentCtx = document.getElementById('studentAttendanceChart').getContext('2d');
    const studentChart = new Chart(studentCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($studentAttendanceDaily->toArray())),
            datasets: [{
                label: 'Present',
                data: @json(array_values($studentAttendanceDaily->toArray())),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Teacher attendance chart
    const teacherCtx = document.getElementById('teacherAttendanceChart').getContext('2d');
    const teacherChart = new Chart(teacherCtx, {
        type: 'line',
        data: {
            labels: @json(array_keys($teacherAttendanceDaily->toArray())),
            datasets: [{
                label: 'Present',
                data: @json(array_values($teacherAttendanceDaily->toArray())),
                borderColor: 'rgba(54, 162, 235, 1)',
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                tension: 0.2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {

        

        const buildTableHTML = (dataArray) => {
            if (!dataArray || dataArray.length === 0) {
                return `
            <tr>
                <td colspan="3" class="text-center text-muted">No records meet the criteria</td>
            </tr>
        `;
            }

            let html = '';
            dataArray.forEach(item => {
                // Assuming item has 'name', 'absent_count', and 'rate' keys
                html += `
            <tr>
                <td>${item.name}</td>
                <td><span class="badge bg-danger">${item.absent_count}</span></td>
                <td>${item.rate}%</td>
            </tr>
        `;
            });
            return html;
        };


        const absentRequest = () => {

            const year = document.getElementById('year')?.value || '';
            const month = document.getElementById('month')?.value || '';

            const params = {
                absentCount: document.getElementById('absentCount')?.value,
                year: year,
                month: month
            };

            fetch("{{ route('absent.data') }}?" + new URLSearchParams(params))
                // **FIX 2: Corrected typo 'reponse' to 'response'**
                .then(response => response.json())
                .then(data => {
                    
                    const studentTableBody = document.querySelector('#studentAbsentTable table tbody');
                    const teacherTableBody = document.querySelector('#teacherAbsentTable table tbody');

                    if(studentTableBody){
                        studentTableBody.innerHTML = buildTableHTML(data.studentAbsentData); 
                    }

                    if(teacherTableBody){
                        teacherTableBody.innerHTML = buildTableHTML(data.teacherAbsentData);
                    }
                })
                .catch(error => console.error('Error in absent request:', error));
        };

        // **FIX 3: Changed querySelectorAll and added listener to button**
        const absentFilterInput = document.querySelector('input.absentFilter');

        if (absentFilterInput) {
            // Optional: Add listener to input as well for immediate feedback (e.g., hitting enter)
            absentFilterInput.addEventListener('change', absentRequest);
            absentFilterInput.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    absentRequest();
                }
            });
        }
    });
</script>

@endsection