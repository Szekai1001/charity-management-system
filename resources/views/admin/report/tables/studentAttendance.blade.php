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
            <strong>Note:</strong> Data defaults to Today. Click Filter to view past dates.
        </small>
    </div>

</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3 text-success"><i class="bi bi-person-check-fill fs-2"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Present</div>
                    <h3 class="mb-0 fw-bold">{{ $present }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3 text-warning"><i class="bi bi-person-x-fill fs-2"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Absent</div>
                    <h3 class="mb-0 fw-bold">{{ $absent }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="flex-shrink-0 me-3 text-danger"><i class="bi bi-person-dash-fill fs-2"></i></div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Excused</div>
                    <h3 class="mb-0 fw-bold">{{ $excused }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card p-3 border-0 shadow-sm">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-list-check text-primary"></i> Student Attendance Records
        </h5>

          <div>
            {{ $studentAttendances->appends(request()->query())->links() }}
        </div>
    </div>

    <form action="{{route('attendance.studentUpdate') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 bg-light p-2 rounded border">
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted me-1">Bulk Action:</span>
                <select id="td_bulk_status" class="form-select form-select-sm w-auto">
                    <option value="">-- Select Status --</option>
                    <option value="present">Present</option>
                    <option value="absent">Absent</option>
                    <option value="excused">Excused</option>
                </select>
                <button type="button" id="apply_td_bulk_status" class="btn btn-sm btn-outline-primary">
                    Apply
                </button>
            </div>
            <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
        </div>

        <div class="table-responsive rounded border">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-center text-uppercase small">
                    <tr>
                        <th style="width: 40px;"><input type="checkbox" class="select-studentAttendance form-check-input"></th>
                        <th class="text-start ps-3">Student Name</th>

                        {{-- LOGIC: Only show Date column if we are NOT filtering by a specific date --}}
                        @if(empty($date))
                        <th>Date</th>
                        @endif

                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody class="text-center">
                    @forelse($studentAttendances as $studentAttendance)
                    <tr>
                        <td>
                            <input type="checkbox" name="studentAttendance_ids[]" class="studentAttendance_checkbox form-check-input" value="{{ $studentAttendance->id }}">
                        </td>

                        <td class="text-start ps-3">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                                    style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                                    {{ substr($studentAttendance->student->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">
                                        {{$studentAttendance->student->name}}
                                    </div>
                                    <div class="text-muted small">
                                        {{$studentAttendance->student->user->email}}
                                    </div>
                                </div>
                            </div>
                        </td>


                        @if(empty($date))
                        <td class="text-muted small">{{ $studentAttendance->date }}</td>
                        @endif

                        <td><span class="badge bg-light text-dark border"><i class="bi bi-clock text-success me-1"></i>{{ $studentAttendance->check_in_time ?? '--:--' }}</span></td>
                        <td><span class="badge bg-light text-dark border"><i class="bi bi-clock text-danger me-1"></i>{{ $studentAttendance->check_out_time ?? '--:--' }}</span></td>

                        <td>
                            @if($studentAttendance->status === 'present')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Present</span>
                            @elseif($studentAttendance->status === 'absent')
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Absent</span>
                            @else
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Excused</span>
                            @endif
                        </td>

                        <td>
                            <select name="status[{{ $studentAttendance->id }}]" class="form-select form-select-sm d-inline-block" style="width: 110px;">
                                <option value="present" {{ $studentAttendance->status === 'present' ? 'selected' : '' }}>Present</option>
                                <option value="absent" {{ $studentAttendance->status === 'absent' ? 'selected' : '' }}>Absent</option>
                                <option value="excused" {{ $studentAttendance->status === 'excused' ? 'selected' : '' }}>Excused</option>
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ empty($date) ? '7' : '6' }}" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <div class="bg-light rounded-circle p-3 mb-3">
                                    <i class="bi bi-clipboard-x fs-1 text-secondary"></i>
                                </div>
                                <span class="fw-semibold fs-5">No records found</span>
                                <small class="text-muted">No attendance data matches the selected date or filter.</small>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </form>
</div>