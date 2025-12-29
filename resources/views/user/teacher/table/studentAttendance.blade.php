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

<div class="row g-3 mb-4">

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-muted small fw-bold mb-1">Total Students</p>
                        <h3 class="fw-bold mb-0">{{ $studentAttendances->count() }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-success">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-muted small fw-bold mb-1">Present</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $present }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check-lg fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-muted small fw-bold mb-1">Absent</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $absent }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-x-lg fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-muted small fw-bold mb-1">Excused</p>
                        <h3 class="fw-bold mb-0 text-dark">{{ $excused }}</h3>
                    </div>
                    <div class="p-3 rounded-3 bg-warning bg-opacity-10 text-warning">
                        <i class="bi bi-envelope-paper-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="{{route('attendance.studentUpdate') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

        <div class="card-header bg-white py-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

            <div class="d-flex align-items-center gap-3 flex-wrap">
                <h5 class="fw-bold text-dark mb-0 text-nowrap">Attendance List</h5>

                <div>
                    {{ $studentAttendances->links() }}
                </div>
            </div>

            <div class="d-flex align-items-center">
                <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4 text-nowrap">
                    <i class="bi bi-save me-1"></i> Save Changes
                </button>
            </div>

        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase fw-bold">
                        <th class="ps-4 py-3" style="width: 50px;">#</th>
                        <th class="py-3">Student</th>
                        @if(empty($date))
                        <th class="py-3 text-center">Date</th>
                        @endif
                        <th class="py-3 text-center">Check In Time</th>
                        <th class="py-3 text-center">Check Out Time</th>
                        <th class="py-3 text-center">Current Status</th>
                        <th class="py-3 text-center pe-4" style="width: 200px;">Update Action</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($studentAttendances as $studentAttendance)
                    <tr>
                        <td class="ps-4 text-muted">{{ $loop->iteration }}</td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                    {{ substr($studentAttendance->student->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-dark fw-bold">{{ $studentAttendance->student->name }}</div>
                                    <div class="text-muted small">ID: {{ $studentAttendance->student->id }}</div>
                                </div>
                            </div>
                        </td>
                        @if(empty($date))
                        <td class="text-center text-muted small">
                            {{ $studentAttendance->date }}
                        </td>
                        @endif

                        <td class="text-center">
                            <span class="text-success mb-1"><i class="bi bi-clock text-success me-1"></i>{{ $studentAttendance->check_in_time ?? '--:--' }}</span>
                        </td>

                        <td class="text-center">
                            <span class="text-danger"><i class="bi bi-clock text-danger me-1"></i>{{ $studentAttendance->check_out_time ?? '--:--' }}</span>
                        </td>

                        <td class="text-center">
                            @if($studentAttendance->status === 'present')
                            <span class="badge bg-success">Present</span>
                            @elseif($studentAttendance->status === 'absent')
                            <span class="badge bg-danger">Absent</span>
                            @else
                            <span class="badge bg-warning text-dark">Excused</span>
                            @endif
                        </td>

                        <td class="pe-4">
                            <select name="status[{{ $studentAttendance->id }}]" class="form-select form-select-sm shadow-sm border-0 bg-light fw-medium">
                                <option value="present" {{$studentAttendance->status === 'present' ? 'selected' : ''}}>Present</option>
                                <option value="absent" {{$studentAttendance->status === 'absent' ? 'selected' : ''}}>Absent</option>
                                <option value="excused" {{$studentAttendance->status === 'excused' ? 'selected' : ''}}>Excused</option>
                            </select>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ empty($date) ? '7' : '6' }}" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center text-muted">
                                <div class="bg-light rounded-circle p-3 mb-3">
                                    <i class="bi bi-clipboard-x display-6 text-secondary opacity-50"></i>
                                </div>
                                <h6 class="fw-bold text-dark">No Records Found</h6>
                                <p class="small mb-0">Adjust your filters to see student data.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-light py-2 px-4">
            <small class="text-muted fst-italic">* Click "Save Changes" to update the database.</small>
        </div>
    </div>

</form>