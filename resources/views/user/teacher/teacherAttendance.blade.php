@extends('layout.teacher')
@include('components.alerts')

@section('content')

<div class="card shadow-sm border-0 rounded-4 p-3 col-md-12 mt-3 mb-5">
    <div class="row gy-3 align-items-end">

        {{-- 1. Year Input --}}
        <div class="col-md-3">
            <label for="td-log-year" class="form-label fw-semibold">
                <i class="bi bi-calendar me-1 text-primary"></i> Year
            </label>
            <input type="number" id="td-log-year" name="td-log-year" class="form-control filter"
                value="{{ $year ?? '' }}" min="2000" max="2100"
                placeholder="e.g. 2025">
        </div>

        {{-- 2. Month Select --}}
        <div class="col-md-5">
            <label for="td-log-month" class="form-label fw-semibold">
                <i class="bi bi-calendar-month me-1 text-primary"></i> Month
            </label>
            <select name="td-log-month" id="td-log-month" class="form-select filter">
                <option value="">-- All Months --</option>
                @foreach (range(1, 12) as $m)
                <option value="{{ $m }}" {{ (int)$m === (int)($month ?? 0) ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- 3. Status Select --}}
        <div class="col-md-4">
            <label for="td-log-status" class="form-label fw-semibold">
                <i class="bi bi-clipboard-check me-1 text-success"></i> Status
            </label>
            <select name="td-log-status" id="td-log-status" class="form-select filter">
                <option value="">-- All Statuses --</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="excused">Excused</option>
            </select>
        </div>

    </div>
</div>


<div class="container-fluid px-0" id="teacherAttendanceDetails">
    @include('user.teacher.table.teacherAttendanceTable')
</div>




<script>
    document.addEventListener('DOMContentLoaded', function() {
        let debounceTimer;

        const sendRequest = () => {
            // 1. Get values using the correct IDs
            const params = {
                'taYear': document.getElementById('td-log-year')?.value || '',
                'taMonth': document.getElementById('td-log-month')?.value || '',
                'taStatus': document.getElementById('td-log-status')?.value || '',
            }

            const container = document.getElementById('teacherAttendanceDetails');
            if(container) container.style.opacity = '0.5';

            fetch("{{ route('attendance.teacherFilter') }}?" + new URLSearchParams(params))
                .then(response => response.json()) // <--- CHANGED: expecting JSON now
                .then(data => {
                    if(container) {
                        // <--- CHANGED: Access the 'adminHtml' key from your JSON
                        container.innerHTML = data.adminHtml; 
                        container.style.opacity = '1';
                    }
                })
                .catch(error => {
                    console.error('Error: ', error);
                    // Fallback: If the controller actually returns text/html on error
                    // remove this if you only ever return JSON
                    if(container) container.style.opacity = '1';
                });
        }

        document.querySelectorAll('input.filter, select.filter').forEach(el => {
            if (el.tagName === 'SELECT') {
                el.addEventListener('change', sendRequest);
            }
            if (el.tagName === 'INPUT') {
                el.addEventListener('keyup', function() {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(sendRequest, 500);
                });
                el.addEventListener('change', sendRequest); 
            }
        });
    });
</script>

@endsection