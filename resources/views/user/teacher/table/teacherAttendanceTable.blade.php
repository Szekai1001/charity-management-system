<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">

    {{-- Left Side: Date Badge --}}
    <div class="bg-light border rounded px-4 py-2 d-flex align-items-center shadow-sm">
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

    {{-- Right Side: Note (Compacted) --}}
    <div class="bg-info-subtle border border-info-subtle text-info-emphasis rounded px-3 py-2 shadow-sm d-flex align-items-center">
        <i class="bi bi-exclamation-circle-fill me-2"></i>
        <small class="mb-0">
            <strong>Note:</strong> Data defaults to current month. Click Filter to view past records.
        </small>
    </div>

</div>

<div class="row g-4 mb-4 align-items-stretch">

    {{-- Left: Performance Card --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex flex-column justify-content-center">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Attendance Performance</h5>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ $year }} &bull; {{ \Carbon\Carbon::create()->month((int)$month)->format('F') }}
                        </p>
                    </div>
                    <span class="badge {{ $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-warning text-dark' : 'bg-danger') }} bg-opacity-10 text-reset px-3 py-2 rounded-pill fs-6 fw-bold">
                        {{ round($progress) }}%
                    </span>
                </div>

                <div class="progress" style="height: 12px; border-radius: 10px; background-color: #f0f2f5;">
                    <div class="progress-bar {{ $progress >= 80 ? 'bg-success' : ($progress >= 50 ? 'bg-warning' : 'bg-danger') }}"
                        role="progressbar"
                        style="width: {{ $progress }}%; border-radius: 10px;"
                        aria-valuenow="{{ round($progress) }}"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3 small text-muted fw-medium">
                    <span>0%</span>
                    <span>
                        Attended: <strong class="text-dark">{{ $present ?? '0' }}</strong> / {{ $totalDays ?? '0' }} days
                    </span>
                    <span>100%</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Stats Cards (Stacked) --}}
    <div class="col-lg-5">
        <div class="row g-3 h-100">
            {{-- Present Card --}}
            {{-- col-lg-12 h-50 makes it stack vertically and take half height on desktop --}}
            <div class="col-6 col-lg-12 h-50">
                <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-success">
                    <div class="card-body px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Present</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ $present ?? '0' }} <small class="fs-6 text-muted fw-normal">days</small></h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-2 rounded-circle text-success">
                            <i class="bi bi-check-lg fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Absent Card --}}
            <div class="col-6 col-lg-12 h-50">
                <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-4 border-danger">
                    <div class="card-body px-4 d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-uppercase text-muted small fw-bold mb-1">Absent</h6>
                            <h3 class="fw-bold text-dark mb-0">{{ $absent ?? '0' }} <small class="fs-6 text-muted fw-normal">days</small></h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-2 rounded-circle text-danger">
                            <i class="bi bi-x-lg fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Attendance Table Card --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">

    <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
        <h5 class="fw-bold text-dark mb-0">Attendance History</h5>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-secondary small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Date</th>
                    <th class="text-center py-3">Check In</th>
                    <th class="text-center py-3">Check Out</th>
                    <th class="text-center py-3">Duration</th>
                    <th class="text-center py-3 pe-4">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($teacherAttendances as $attendance)
                <tr>
                    <td class="ps-4 py-3">{{ $attendance->date }}</td>

                    <td class="text-center py-3">
                        <span class="text-success mb-1"><i class="bi bi-clock text-success me-1"></i>{{ $attendance->check_in_time ?? '--:--' }}</span>
                    </td>

                    <td class="text-center py-3">
                        <span class="text-danger"><i class="bi bi-clock text-danger me-1"></i>{{ $attendance->check_out_time ?? '--:--' }}</span>
                    </td>

                    <td class="text-center py-3">
                        @if($attendance->check_in_time && $attendance->check_out_time)
                        {{
            \Carbon\Carbon::parse($attendance->check_in_time)
            ->diffAsCarbonInterval(\Carbon\Carbon::parse($attendance->check_out_time))
            ->seconds(0) 
            ->forHumans(['parts' => 2, 'short' => true]) 
        }}
                        @else
                        -
                        @endif
                    </td>
                    <td class="text-center py-3 pe-4">
                        @if($attendance->status == 'present')
                        <span class="badge bg-success">Present</span>
                        @elseif($attendance->status == 'absent')
                        <span class="badge bg-danger">Absent</span>
                        @else
                        <span class="badge bg-warning text-dark">Excused</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="d-flex flex-column align-items-center text-muted">
                            <i class="bi bi-box-seam display-6 mb-2"></i>
                            <span class="fw-semibold">No attendance record yet</span>
                            <small class="text-secondary">New record will appear here once recorded.</small>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer bg-white border-top py-3 px-4 rounded-bottom-4">
        <div class="d-flex justify-content-end">
            {{ $teacherAttendances->appends(request()->query())->links() }}
        </div>
    </div>


</div>