<table>
    <thead>
        {{-- 1. TITLE --}}
        <tr>
            <th colspan="6" style="font-size: 16pt; font-weight: bold; text-align: center;">
                Salary Report
            </th>
        </tr>

        {{-- 2. FILTERS --}}
        @if($year || $month)
        <tr>
            <td colspan="6" style="text-align: center; color: #555555;">
                @if($month) Month: <strong>{{ \Carbon\Carbon::create(null, $month)->format('F') }}</strong> @endif
                @if($month && $year) | @endif
                @if($year) Year: <strong>{{ $year }}</strong> @endif
            </td>
        </tr>
        @endif

        <tr><td colspan="6"></td></tr> {{-- Spacer row --}}

        {{-- 3. TABLE HEADERS --}}
        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center;">Teacher ID</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left;">Teacher Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center;">Working Hours</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center;">Calculated Salary</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center;">Payment Date</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center;">Payment Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach($salaryDetails as $salary)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $salary->teacher->id }}</td>
            <td style="border: 1px solid #000000; text-align: left;">{{ $salary->teacher->name }}</td>
            <td style="border: 1px solid #000000; text-align: center;">
                @php
                    $total = $salary->hours_worked;
                    $hours = floor($total);
                    $minutes = round(($total - $hours) * 60);
                @endphp
                {{ $hours }}h {{ $minutes }}m
            </td>
            <td style="border: 1px solid #000000; text-align: right;">
                RM {{ number_format($salary->salary, 2) }}
            </td>
            <td style="border: 1px solid #000000; text-align: center;">{{ $salary->payment_date ?? '-' }}</td>

            @php
                $statusColor = '#ffffff';
                $statusLower = strtolower($salary->payment_status);
                if($statusLower == 'unpaid') $statusColor = '#ffc7ce'; // Light Red
                if($statusLower == 'paid') $statusColor = '#c6efce';   // Light Green (Better for 'Paid')
            @endphp
            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $statusColor }};">
                {{ $salary->payment_status }}
            </td>
        </tr>
        @endforeach
    </tbody>

    {{-- 4. TOTALS AT THE BOTTOM --}}
    <tfoot>
        <tr style="font-weight: bold; background-color: #f2f2f2;">
            <td colspan="3" style="border: 1px solid #000000; text-align: right;">TOTAL PAYOUT:</td>
            <td style="border: 1px solid #000000; text-align: right;">
                RM {{ number_format($salaryDetails->sum('salary'), 2) }}
            </td>
            <td colspan="2" style="border: 1px solid #000000;"></td>
        </tr>
    </tfoot>
</table>