@extends('layouts.app')

@section('title', 'Attendance Records')

@section('content')

<div class="container mt-4">

    <h3>All Employees Attendance</h3>

    {{-- Filters --}}
    <form method="GET" class="row g-2 mb-3">

        <div class="col-md-3">
            <input type="text" name="name" class="form-control"
                   placeholder="Employee Name" value="{{ request('name') }}">
        </div>

        <div class="col-md-3">
            <input type="date" name="start" class="form-control"
                   value="{{ request('start') }}">
        </div>

        <div class="col-md-3">
            <input type="date" name="end" class="form-control"
                   value="{{ request('end') }}">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>

    </form>

    {{-- Attendance Table --}}
    <div class="table-responsive mt-3">
        <table id="adminAttendanceTable" class="table table-bordered table-striped">

            <thead class="table-light">
                <tr>
                    <th>Employee</th>
                    <th>Email</th>
                    <th>Date</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Hours</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($attendance as $att)
                    <tr>
                        <td>{{ $att->employee->name }}</td>
                        <td>{{ $att->employee->email }}</td>

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
    $('#adminAttendanceTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "scrollX": true
    });
});
</script>
@endpush
