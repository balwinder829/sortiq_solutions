@extends('layouts.app')

@section('title', 'My Attendance Log')

@section('content')

<div class="container mt-4">

    <h3>My Attendance History</h3>

    <div class="table-responsive mt-3">
        <table id="employeeAttendanceTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Hours</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($attendance as $att)
                    <tr>
                        <td>{{ $att->login_time->format('d M Y') }}</td>
                        <td>{{ $att->login_time->format('H:i:s') }}</td>

                        <td>
                            {{ $att->logout_time ? $att->logout_time->format('H:i:s') : '—' }}
                        </td>

                        <td>
                            @if($att->logout_time)
                                {{ $att->login_time->diffInHours($att->logout_time) }} hrs
                            @else
                                In Progress
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#employeeAttendanceTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "scrollX": true,
    });
});
</script>
@endpush
