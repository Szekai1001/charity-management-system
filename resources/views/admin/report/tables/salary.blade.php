<div class="border-bottom pb-3 mb-4">

    <div class="bg-light border rounded px-4 py-2 d-inline-flex align-items-center shadow-sm">
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
</div>

<div class="row g-3 mb-4">

    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-person-check-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1" style="font-size: 11px;">Paid Teachers</h6>
                    <h5 class="fw-bold text-dark mb-0">{{ $paidTeachers }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-person-x-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1" style="font-size: 11px;">Unpaid Teachers</h6>
                    <h5 class="fw-bold text-dark mb-0">{{ $unpaidTeachers }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1" style="font-size: 11px;">Paid Amount</h6>
                    <h5 class="fw-bold text-dark mb-0">RM {{ $totalPaidSalary }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-6 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1" style="font-size: 11px;">Pending</h6>
                    <h5 class="fw-bold text-dark mb-0">RM {{ $totalUnpaidSalary }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-md">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-calculator-fill fs-4"></i>
                </div>
                <div>
                    <h6 class="text-muted text-uppercase small fw-bold mb-1" style="font-size: 11px;">Est. Total</h6>
                    <h5 class="fw-bold text-dark mb-0">RM {{ $totalPaidSalary + $totalUnpaidSalary }}</h5>
                </div>
            </div>
        </div>
    </div>

</div>


<div class="card border-0 shadow p-3">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex gap-3">
            <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
            <h4 class="fw-semibold mb-0">Salary records</h4>
        </div>
    </div>

    <form action="{{ route('salary.update') }}" method="POST">
        @csrf

        <!-- Top controls: bulk status + Save button -->
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 bg-light p-2 rounded border">
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold text-muted me-1">Bulk Action:</span>
                <select id="bulk_status" class="form-select  form-select-sm w-auto">
                    <option value="">-- Select Status --</option>
                    <option value="Paid">Paid</option>
                    <option value="Unpaid">Unpaid</option>
                </select>
                <button type="button" id="apply_bulk_status" class="btn btn-sm btn-outline-primary">Apply</button>
            </div>
            <div class="me-3">
                <button type="submit" class="btn btn-sm btn-primary px-3 shadow-sm"> <i class="bi bi-save me-1"></i>Save All</button>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light text-center text-uppercase small">
                        <tr>
                            <th scope="col"><input type="checkbox" class="select-all"></th>

                            <th class="text-start ps-3">Teacher Name</th>
                            <th scope="col">Working Hours</th>
                            <th scope="col">Calculated Salary</th>
                            <th scope="col">Payment Date</th>
                            <th scope="col">Payment Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody id="salary-tbody" class="text-center">
                        @forelse($salaryDetails as $salary)
                        <tr>
                            <td><input type="checkbox" name="salary_ids[]" class="salary-checkbox" value="{{ $salary->id }}"></td>

                            <td class="text-start ps-3">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-light text-primary d-flex align-items-center justify-content-center fw-bold me-3 border"
                                        style="width: 40px; height: 40px; font-size: 0.9rem; flex-shrink: 0;">
                                        {{ substr($salary->teacher->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">
                                            {{$salary->teacher->name}}
                                        </div>
                                        <div class="text-muted small">
                                            {{$salary->teacher->user->email}}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                @php
                                $total = $salary->hours_worked;
                                $hours = floor($total);
                                $minutes = round(($total - $hours) * 60);
                                @endphp

                                {{ $hours }}h {{ $minutes }}m
                            </td>

                            <td>
                                <span class="text-secondary small me-1" style="font-size: 0.85em;">RM</span>
                                <span class="fw-bold text-dark">{{ number_format($salary->salary ?? 0, 2) }}</span>
                            </td>

                            <td class="text-muted small">{{ $salary->payment_date ?? '-' }}</td>
                            <td>
                                @if($salary->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                                @elseif($salary->payment_status == 'unpaid')
                                <span class="badge bg-warning text-dark">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <select name="payment_status[{{ $salary->id }}]" class="form-select form-select-sm">
                                    <option value="paid" {{ $salary->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ $salary->payment_status == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No salary records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>