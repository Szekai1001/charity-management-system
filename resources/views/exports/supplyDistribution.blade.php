<table>
    <thead>
        <tr>
            <td colspan="8"
                style="font-size: 18px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                Supply Distribution Report
            </td>
        </tr>

        @if($year || $month)
        <tr>
            <td colspan="8" style="text-align: center; font-style: italic; color: #555555;">
                @if($month) Month: <strong>{{ $month }}</strong> @endif

                {{-- Show separator only if both exist --}}
                @if($month && $year) &nbsp;|&nbsp; @endif

                @if($year) Year: <strong>{{ $year }}</strong> @endif
            </td>
        </tr>
        @endif

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">
                Supply ID
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">
                Beneficiary ID
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left; width: 30px;">
                Beneficiary Name
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left; width: 25px;">
                Package
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 20px;">
                Date
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">
                Session
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 25px;">
                Distribution Method
            </th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">
                Status
            </th>
        </tr>
    </thead>

    <tbody>
        @forelse($supplyRequests as $supplyRequest)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">
                {{ $supplyRequest->id }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $supplyRequest->beneficiary->id ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: left;">
                {{ $supplyRequest->beneficiary->name ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: left;">
                {{ $supplyRequest->package->name ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $supplyRequest->delivery_date->date ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $supplyRequest->delivery_date->session ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $supplyRequest->distribution_method ?? '-' }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ ucfirst($supplyRequest->distribution_status) ?? '-' }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" style="border: 1px solid #000000; text-align: center;">
                No records found
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
