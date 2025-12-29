@extends('layout.admin')
@section('content')

<form action="{{ route('supplyRequestReporting.filter') }}" method="POST">
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
                <option value="">-- All Months --</option>
                @foreach (range(1, 12) as $m)
                <option value="{{ $m }}"
                    {{ (int) request('month') === (int)$m ? 'selected' : '' }}>
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
<div class="container-fluid px-0 py-2">

    <div class="row g-4 mb-5">

        <div class="col-xl-6 col-12">
            <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-2 me-3">
                        <i class="bi bi-box-seam fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Package Insights</h6>
                        <small class="text-muted">Demand Analysis</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-6 border-end-md p-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3 tracking-wide">
                                <i class="bi bi-arrow-up-circle text-success me-1"></i> Highest Demand
                            </h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($mostDemandedPackages as $package)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-dark">{{ $package->name }}</span>
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">{{ $package->total_packages ?? 0 }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6 p-4 bg-light bg-opacity-50">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3 tracking-wide">
                                <i class="bi bi-arrow-down-circle text-warning me-1"></i> Lowest Demand
                            </h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($mostLeastDemandedPackages as $package)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-secondary">{{ $package->name }}</span>
                                    <span class="fw-bold text-dark small">{{ $package->total_least_packages ?? 0 }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12">
            <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3">
                        <i class="bi bi-tools fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">Item Inventory</h6>
                        <small class="text-muted">Movement Stats</small>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="row g-0 h-100">
                        <div class="col-md-6 border-end-md p-4">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3 tracking-wide">
                                <i class="bi bi-star text-primary me-1"></i>  Highest Demand
                            </h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($mostDemandedItems as $item)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-dark text-truncate pe-2">{{ $item->name }}</span>
                                    <span class="fw-bold text-primary small">{{ $item->total_items ?? 0 }} <span class="text-muted fw-light" style="font-size: 0.75rem">units</span></span>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-6 p-4 bg-light bg-opacity-50">
                            <h6 class="text-uppercase text-muted fw-bold small mb-3 tracking-wide">
                                <i class="bi bi-hourglass text-warning me-1"></i> Lowest Demand
                            </h6>
                            <div class="d-flex flex-column gap-3">
                                @foreach($mostLeastDemandedItems as $item)
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-medium text-secondary text-truncate pe-2">{{ $item->name }}</span>
                                    <span class="fw-bold text-dark small">{{ $item->total_least_items ?? 0 }} <span class="text-muted fw-light" style="font-size: 0.75rem">units</span></span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row g-4 mb-5">

        <div class="col-lg-6 col-12">
            <div class="card shadow-sm border-0 rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-bold text-dark m-0">Total Applications</h6>
                        <small class="text-muted">Volume over time</small>
                    </div>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Year {{ $filterYear }}</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="supplyApplicationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card shadow-sm border-0 rounded-4 h-100 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-bold text-dark m-0">Cost Analysis</h6>
                        <small class="text-muted">Spending trends (RM)</small>
                    </div>
                    <span class="badge bg-danger bg-opacity-10 text-danger">Financials</span>
                </div>
                <div style="position: relative; height: 260px; width: 100%;">
                    <canvas id="costSpendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h6 class="fw-bold text-dark m-0">Package Popularity</h6>
                        <small class="text-muted">Breakdown by package type</small>
                    </div>
                </div>
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="packageApplicationChart"></canvas>
                </div>
            </div>
        </div>

    </div>


    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-1 fw-bold text-dark">Monthly Breakdown</h5>
                <p class="text-muted small mb-0">Detailed view of applications, distribution, and costs.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                <thead class="bg-light text-secondary small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4 py-3 border-0">Month</th>
                        <th class="py-3 border-0 text-center">Total Applications</th>
                        <th class="py-3 border-0 text-center">Items Distributed</th>
                        <th class="py-3 border-0 text-end">Total Cost</th>
                        <th class="py-3 border-0 text-end">Avg. Cost per App.</th>
                        <th class="py-3 border-0 ps-4">Most Requested Item</th>
                        <th class="py-3 border-0">Top Package</th>
                        <th class="pe-4 py-3 border-0 text-end">Total Spent (YTD)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tableData as $row)
                    <tr>
                        <td class="ps-4 py-3 fw-bold text-dark">{{ $row['month_name'] }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-2">{{ number_format($row['applications']) }}</span>
                        </td>
                        <td class="text-center text-muted">{{ number_format($row['items_distributed']) }}</td>
                        <td class="text-end fw-bold text-dark">{{ number_format($row['cost'], 2) }}</td>
                        <td class="text-end text-muted small">{{ number_format($row['average_cost'], 2) }}</td>
                        <td class="ps-4 text-muted small text-truncate" style="max-width: 150px;">{{ $row['highest_cost_item'] }}</td>
                        <td class="text-muted small text-truncate" style="max-width: 150px;">{{ $row['top_package'] }}</td>
                        <td class="pe-4 text-end fw-bold text-success">{{ number_format($row['ytd'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <div class="mb-2"><i class="bi bi-inbox fs-1 opacity-25"></i></div>
                            No records found for {{ $filterYear }}.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($tableData) > 0)
                <tfoot class="bg-light">
                    <tr class="fw-bold">
                        <td class="ps-4 py-3 text-uppercase text-secondary small">Total YTD</td>
                        <td class="text-center">{{ number_format($grandTotalApps) }}</td>
                        <td class="text-center">{{ number_format($grandTotalItems) }}</td>
                        <td class="text-end text-danger">{{ number_format($grandTotalCost, 2) }}</td>
                        <td class="text-end">-</td>
                        <td class="ps-4">-</td>
                        <td>-</td>
                        <td class="pe-4 text-end text-success">RM {{ number_format($runningYtd, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>

<style>
    @media (min-width: 768px) {
        .border-end-md {
            border-right: 1px solid #dee2e6;
        }
    }

    .text-justify-between {
        justify-content: space-between;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ==== Global Chart.js Default Styling (Cleaner, Professional Look) ====
    Chart.defaults.font.family = "'Inter', 'Segoe UI', sans-serif";
    Chart.defaults.font.size = 13;
    Chart.defaults.color = "#333";

    Chart.defaults.plugins.tooltip.backgroundColor = "rgba(0,0,0,0.7)";
    Chart.defaults.plugins.tooltip.padding = 12;
    Chart.defaults.plugins.tooltip.cornerRadius = 8;

    Chart.defaults.plugins.legend.labels.boxWidth = 12;
    Chart.defaults.plugins.legend.labels.boxHeight = 12;

    // Soft modern colors for categories
    const palette = [
        "#4A90E2", // blue
        "#50E3C2", // mint
        "#B8E986", // green
        "#F5A623", // orange
        "#D0021B", // red
        "#9013FE" // purple
    ];

    // ===== 1. Supply Application Chart (Bar Chart) =====
    const supplyApplicationCtx = document.getElementById('supplyApplicationChart').getContext('2d');

    const supplyChart = new Chart(supplyApplicationCtx, {
        type: 'bar',
        data: {
            labels: @json($supplyApplicationLabel),
            datasets: [{
                label: 'Applications',
                data: @json($supplyApplicationData),
                backgroundColor: "rgba(74, 144, 226, 0.5)",
                borderColor: "#4A90E2",
                borderWidth: 2,
                borderRadius: 8,
                hoverBackgroundColor: "rgba(74, 144, 226, 0.7)"
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

    // ===== 2. Package Application Chart (Line Chart with Multiple Lines) =====
    const datasets = @json($packageApplicationData).map((pkg, index) => ({
        label: pkg.label,
        data: pkg.data,
        fill: false,
        borderColor: palette[index % palette.length],
        borderWidth: 2,
        tension: 0.35,
        pointRadius: 3,
        pointBackgroundColor: palette[index % palette.length]
    }));

    const packageApplicationCtx = document.getElementById('packageApplicationChart').getContext('2d');

    const packageChart = new Chart(packageApplicationCtx, {
        type: 'line',
        data: {
            labels: @json($packageApplicationLabel),
            datasets: datasets
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

    // ===== 3. Monthly Cost Spend Chart (Smooth Line + Soft Fill) =====
    const costCtx = document.getElementById('costSpendChart').getContext('2d');

    const costChart = new Chart(costCtx, {
        type: 'line',
        data: {
            labels: @json($totalCostLabel),
            datasets: [{
                label: 'Total Cost per Month',
                data: @json($totalCostData),
                borderColor: "#F5A623",
                backgroundColor: "rgba(245, 166, 35, 0.2)",
                borderWidth: 2.5,
                fill: true,
                tension: 0.35,
                pointRadius: 4,
                pointBackgroundColor: "#F5A623"
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
</script>

@endsection