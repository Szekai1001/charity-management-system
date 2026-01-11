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
        const container = document.getElementById('teacherAttendanceDetails');

        /**
         * 1. CORE FETCH FUNCTION
         * Handles the AJAX request and updates the HTML container
         */
        const fetchData = (url) => {
            if (!container) return;

            // Visual feedback: dim the table while loading
            container.style.opacity = '0.5';

            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Crucial for Laravel $request->ajax()
                    }
                })
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    // Update the container with the 'adminHtml' from your JSON response
                    container.innerHTML = data.adminHtml;
                    container.style.opacity = '1';

                    // Optional: Scroll back to the top of the table after update
                    container.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    container.style.opacity = '1';
                });
        };

        /**
         * 2. FILTER INPUT LOGIC
         * Gathers current filter values and triggers fetchData
         */
        const sendRequest = () => {
            const params = {
                'taYear': document.getElementById('td-log-year')?.value || '',
                'taMonth': document.getElementById('td-log-month')?.value || '',
                'taStatus': document.getElementById('td-log-status')?.value || '',
            };

            const baseUrl = "{{ route('attendance.teacherFilter') }}";
            const url = `${baseUrl}?${new URLSearchParams(params).toString()}`;

            fetchData(url);
        };

        /**
         * 3. PAGINATION CLICK INTERCEPTOR (Event Delegation)
         * Stops the browser from following the link and uses AJAX instead
         */
        if (container) {
            container.addEventListener('click', function(e) {
                // Check if the clicked element (or its parent) is a pagination link
                const link = e.target.closest('.pagination a');

                if (link) {
                    e.preventDefault(); // STOP the raw JSON from appearing
                    const url = link.getAttribute('href');

                    if (url && url !== '#') {
                        fetchData(url);
                    }
                }
            });
        }

        /**
         * 4. EVENT LISTENERS FOR FILTERS
         */
        document.querySelectorAll('input.filter, select.filter').forEach(el => {
            // Handle dropdown changes
            if (el.tagName === 'SELECT') {
                el.addEventListener('change', sendRequest);
            }

            // Handle text input with debounce (waiting for user to stop typing)
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