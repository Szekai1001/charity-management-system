<table>
    <thead>
        <tr>
            <td colspan="6" style="font-size: 18px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                Salary Report
            </td>
        </tr>

        @if($year || $month)
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; color: #555555;">

                @if($month) Month: <strong>{{ $month }}</strong> @endif

                {{-- Only show the separator | if both Month AND Year exist --}}
                @if($month && $year) &nbsp;|&nbsp; @endif

                @if($year) Year: <strong>{{ $year }}</strong> @endif
        

            </td>
        </tr>
        @endif

        <tr>
            <td colspan="6"></td>
        </tr>

        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Teacher ID</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left; width: 30px;">Teacher Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Working Hours</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Calculated Salary</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Payment Date</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Payment Status</th>
        </tr>
    </thead>

    <tbody>
        @foreach($salaryDetails as $salary)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">
              {{ $salary->teacher->id }}
            </td>

            <td style="border: 1px solid #000000; text-align: left;">
                {{ $salary->teacher->name }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $salary->hours_worked }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                RM {{ $salary->salary }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $salary->payment_date ?? '-' }}
            </td>

            {{-- Conditional Coloring for Status --}}
            @php
            $statusColor = '#ffffff'; // Default white
            if(strtolower($salary->payment_status) == 'unpaid') $statusColor = '#ffc7ce'; // Light Red
            if(strtolower($salary->payment_status) == 'paid') $statusColor = '#ffeb9c'; // Light Yellow
            @endphp

            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $statusColor }};">
                {{ $salary->payment_status }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>