@extends('layout.admin')
@include('components.alerts')
@section('content')


<!-- Parent file of three applications -->
<ul class="nav nav-tabs mb-3" id="applicationTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link d-flex align-items-center gap-2 {{ $activeTab == 'student' ? 'active' : '' }}" href="?tab=student">
            Students
            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary small">
                {{ $studentApplications->count() }}
            </span>
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link d-flex align-items-center gap-2 {{ $activeTab == 'beneficiary' ? 'active' : '' }}" href="?tab=beneficiary">
            Beneficiaries
            <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary small">
                {{ $beneficiaryApplications->count() }}
            </span>
        </a>
    </li>
</ul>

<div class="card p-3 bg-info-subtle border-0 shadow-sm mb-3">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-exclamation-circle-fill fs-5"></i>
        <p class="mb-0"><strong>Note:</strong> Higher scores indicate greater eligibility.</p>
    </div>
</div>

<div class="tab-content mt-3" id="applicationTabContent">

    {{-- Students Tab --}}
    <div class="tab-pane fade {{ $activeTab == 'student' ? 'show active' : '' }}" id="studentApplications" role="tabpanel">

        <div class="row g-4 mb-4 mt-2">

            {{-- Card 1: Processing --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        {{-- 1. Large Icon Box on Left --}}
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center me-4"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-hourglass-split fs-2"></i>
                        </div>

                        {{-- 2. Text Content on Right --}}
                        <div>
                            <span class="text-muted text-uppercase fw-bold small d-block mb-1">Processing</span>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalProcessingStudents }}</h2>
                        </div>
                    </div>
                    {{-- Optional: Colored bottom bar for flair --}}
                    <div class="bg-warning" style="height: 4px;"></div>
                </div>
            </div>

            {{-- Card 2: Total Students --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        {{-- 1. Large Icon Box on Left --}}
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-4"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-check-circle-fill fs-2"></i>
                        </div>

                        {{-- 2. Text Content on Right --}}
                        <div>
                            <span class="text-muted text-uppercase fw-bold small d-block mb-1">Total Students</span>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalStudents }}</h2>
                        </div>
                    </div>
                    {{-- Optional: Colored bottom bar for flair --}}
                    <div class="bg-success" style="height: 4px;"></div>
                </div>
            </div>

        </div>
        <div class="card border-0 p-3 shadow-sm">
            <div class="d-flex gap-3 mb-5">
                <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                <h4 class="fw-semibold mb-0">Student Applications</h4>
            </div>
            <form action="{{ route('application.updateMultiple', ['id' => 'bulk']) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="student">

                <div class="d-flex justify-content-between align-items-center mb-1">
                    {{ $studentApplications->appends(['tab' => 'student'])->links()}}
                    <button type="submit" class="btn btn-primary">Save Students Change</button>
                </div>

                <div class="card shadow-sm">
                    <div class="table-responsive shadow-sm rounded">
                        <table class="table table-hover">
                            <thead class="table-light text-uppercase small">
                                <tr class="text-center align-middle">
                                    {{-- 1. Index --}}
                                    <th style="width: 50px;">#</th>

                                    {{-- 2. Name (Aligned Left to match the avatar list) --}}
                                    <th class="text-start ps-4">Name</th>

                                    {{-- 3. IC --}}
                                    <th>IC</th>

                                    {{-- 4. Score --}}
                                    <th>
                                        <button type="button"
                                            class="btn text-dark p-0 d-inline-flex align-items-center btn-sort fw-bold"
                                            style="text-decoration: none;"
                                            data-type="student"
                                            data-sort="asc"
                                            value="sort=asc&type=student">
                                            <span class="me-1">Score</span>
                                            <i class="bi bi-caret-up-fill"></i>
                                        </button>
                                    </th>

                                    {{-- 5. Phone --}}
                                    <th>Phone Number</th>

                                    {{-- 6. Action (Status) --}}
                                    <th>Action</th>

                                    {{-- 7. More --}}
                                    <th>More</th>
                                </tr>
                            </thead>
                            <tbody id="studentApplicationTable">
                                @include('admin.partial.studentApplication', ['applications' => $studentApplications])
                            </tbody>
                        </table>
                    </div>
                </div>

            </form>
        </div>

    </div>

    {{-- Beneficiaries Tab --}}
    <div class="tab-pane fade {{ $activeTab == 'beneficiary' ? 'show active' : '' }}" id="beneficiaryApplications" role="tabpanel">
        <div class="row g-4 mb-4 mt-2">

            {{-- Card 1: Processing --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        {{-- 1. Large Icon Box on Left --}}
                        <div class="bg-warning bg-opacity-10 text-warning rounded-3 d-flex align-items-center justify-content-center me-4"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-hourglass-split fs-2"></i>
                        </div>

                        {{-- 2. Text Content on Right --}}
                        <div>
                            <span class="text-muted text-uppercase fw-bold small d-block mb-1">Processing</span>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalProcessingBeneficiaries }}</h2>
                        </div>
                    </div>
                    {{-- Optional: Colored bottom bar for flair --}}
                    <div class="bg-warning" style="height: 4px;"></div>
                </div>
            </div>

            {{-- Card 2: Total Students --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body p-4 d-flex align-items-center">
                        {{-- 1. Large Icon Box on Left --}}
                        <div class="bg-success bg-opacity-10 text-success rounded-3 d-flex align-items-center justify-content-center me-4"
                            style="width: 64px; height: 64px;">
                            <i class="bi bi-check-circle-fill fs-2"></i>
                        </div>

                        {{-- 2. Text Content on Right --}}
                        <div>
                            <span class="text-muted text-uppercase fw-bold small d-block mb-1">Total Beneficiaries</span>
                            <h2 class="fw-bold text-dark mb-0">{{ $totalBeneficiaries }}</h2>
                        </div>
                    </div>
                    {{-- Optional: Colored bottom bar for flair --}}
                    <div class="bg-success" style="height: 4px;"></div>
                </div>
            </div>

        </div>

        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex gap-3 mb-5">
                <i class="bi bi-calendar-check-fill text-primary fs-4"></i>
                <h4 class="fw-semibold mb-0">Beneficiary Applications</h4>
            </div>
            <form action="{{ route('application.updateMultiple', ['id' => 'bulk']) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="tab" value="beneficiary">

                <div class="d-flex justify-content-between align-items-center gap-3 mb-1">
                    {{ $beneficiaryApplications->appends(['tab' => 'beneficiary'])->links() }}
                    <button type="submit" class="btn btn-primary">Save Beneficiaries Change</button>

                </div>

                <div class="card shadow-sm">
                    <div class="table-responsive rounded-4">
                        <table class="table table-hover align-middle">
                            <thead class="table-light text-uppercase small">
                                <tr class="text-center align-middle">
                                    <th style="width:50px;">#</th>
                                    <th class="text-start ps-4">Name</th>
                                    <th>IC</th>
                                    <th>
                                        <button
                                            type="button"
                                            class="btn text-dark p-0 d-inline-flex align-items-center btn-sort fw-bold"
                                            style="text-decoration: none;"
                                            data-type="beneficiary"
                                            data-sort="asc"
                                            value="sort=asc&type=beneficiary">
                                            <span class="me-1">Score</span>
                                            <i class="bi bi-caret-up-fill"></i>
                                        </button>
                                    </th>
                                    <th>Phone Number</th>
                                    <th>Action</th>
                                    <th>More</th>
                                </tr>
                            </thead>
                            <tbody id="beneficiaryApplicationTable">
                                @include('admin.partial.beneficiaryApplication', ['applications' => $beneficiaryApplications])
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('.btn-sort').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                let currentSort = this.dataset.sort || 'asc';
                let newSort = currentSort === 'asc' ? 'desc' : 'asc';
                let type = this.dataset.type;

                // Update button value and data-sort
                this.dataset.sort = newSort;
                this.value = `sort=${newSort}&type=${type}`;

                // Toggle icon direction
                let icon = this.querySelector('i');
                if (icon) {
                    icon.classList.remove('bi-caret-up-fill', 'bi-caret-down-fill');
                    icon.classList.add(newSort === 'asc' ? 'bi-caret-up-fill' : 'bi-caret-down-fill');
                }

                // Fetch new sorted data
                let targetTable = type === 'student' ?
                    '#studentApplicationTable' :
                    '#beneficiaryApplicationTable';

                fetch(`{{ route('application.index') }}?sort=${newSort}&type=${type}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        document.querySelector(targetTable).innerHTML = html;
                    })
                    .catch(error => console.error('Error:', error));
            });
        });



    });
</script>

<!-- </div> -->
@endsection