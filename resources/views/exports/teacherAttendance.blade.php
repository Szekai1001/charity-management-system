<table>
    <thead>
        <tr>
            <td colspan="6" style="font-size: 18px; font-weight: bold; text-align: center; height: 30px; vertical-align: middle;">
                Teacher Attendance Report
            </td>
        </tr>

        @if($date || $year || $month)
        <tr>
            <td colspan="6" style="text-align: center; font-style: italic; color: #555555;">

                {{-- SCENARIO 1: Specific Date is filtered --}}
                @if($date)
                Date: <strong>{{ \Carbon\Carbon::parse($date)->format('d F Y') }}</strong>

                {{-- SCENARIO 2: Month and/or Year --}}
                @else
                @if($month) Month: <strong>{{ $month }}</strong> @endif

                {{-- Only show the separator | if both Month AND Year exist --}}
                @if($month && $year) &nbsp;|&nbsp; @endif

                @if($year) Year: <strong>{{ $year }}</strong> @endif
                @endif

            </td>
        </tr>
        @endif

        <tr>
            <td colspan="6"></td>
        </tr>

        <tr>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Teacher ID</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: left; width: 30px;">Teacher Name</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Date</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Check In</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Check Out</th>
            <th style="font-weight: bold; border: 1px solid #000000; background-color: #dbe5f1; text-align: center; width: 15px;">Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($teacherAttendances as $teacherAttendance)
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">
                {{ $teacherAttendance->teacher->id }}
            </td>

            <td style="border: 1px solid #000000; text-align: left;">
                {{ $teacherAttendance->teacher->name }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{-- Format Date for Excel --}}
                {{ \Carbon\Carbon::parse($teacherAttendance->date)->format('d-m-Y') }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $teacherAttendance->check_in_time }}
            </td>

            <td style="border: 1px solid #000000; text-align: center;">
                {{ $teacherAttendance->check_out_time }}
            </td>

            {{-- Conditional Coloring for Status --}}
            @php
            $statusColor = '#ffffff'; // Default white
            if(strtolower($teacherAttendance->status) == 'present') $statusColor = '#c6efce'; // Light Green
            if(strtolower($teacherAttendance->status) == 'absent') $statusColor = '#ffc7ce'; // Light Red
            if(strtolower($teacherAttendance->status) == 'late') $statusColor = '#ffeb9c'; // Light Yellow
            @endphp

            <td style="border: 1px solid #000000; text-align: center; background-color: {{ $statusColor }};">
                {{ $teacherAttendance->status }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="border: 1px solid #000000; text-align: center; color: #555555; height: 30px; vertical-align: middle;">
                No records found
            </td>
        </tr>
        @endforelse
    </tbody>
</table>