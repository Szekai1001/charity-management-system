<h4>Supply Distribution Report</h4>

@if($year || $month)
    <p>
        @if($month) Month: {{ $month }} @endif 
        @if($year) Year: {{ $year }} @endif
    </p>
@endif

<table>
    <thead>
        <tr>
            <th>Supply Request ID</th>
            <th>Beneficiary ID</th>
            <th>Beneficiary Name</th>
            <th>Package</th>
            <th>Date</th>
            <th>Session</th>
            <th>Distribution Method</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse($supplyRequests as $supplyRequest)
            <tr>
                <td>{{ $supplyRequest->id }}</td>
                <td>{{ $supplyRequest->beneficiary->id ?? '-' }}</td>
                <td>{{ $supplyRequest->beneficiary->name ?? '-' }}</td>
                <td>{{ $supplyRequest->package->name ?? '-' }}</td>
                <td>{{ $supplyRequest->delivery_date->date ?? '-' }}</td>
                <td>{{ $supplyRequest->delivery_date->session ?? '-' }}</td>
                <td>{{ $supplyRequest->distribution_method ?? '-' }}</td>
                <td>{{ $supplyRequest->distribution_status ?? '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="8">No records found</td>
            </tr>
        @endforelse
    </tbody>
</table>
