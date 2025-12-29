<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Student Name</th>
            <th>Date</th>
            <th>Check In Time</th>
            <th>Check Out Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($studentAttendance as $attendance)
        <tr>
            <td>{{ $attendance->student_id }}</td>
            <td>{{ $attendance->student->name }}</td>
            <td>{{ $attendance->date }}</td>
            <td>{{ $attendance->check_in_time }}</td>
            <td>{{ $attendance->check_out_time }}</td>
            <td>{{ $attendance->status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>