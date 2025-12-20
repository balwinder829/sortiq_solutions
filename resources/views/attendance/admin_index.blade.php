@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('content')

<style>
    table.dataTable td { text-transform: capitalize; }
</style>

<div class="container mt-4">

    <div class="d-flex justify-content-between mb-3">
        <h3>Employee Attendance</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('attendance.employees') }}" class="mb-4">
        <div class="row g-2">

            <div class="col-md-3">
                <input type="text" name="name" class="form-control"
                       placeholder="Employee Name" value="{{ request('name') }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control"
                       value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control"
                       value="{{ request('end_date') }}">
            </div>

            <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Search</button>
            </div>

            <div class="col-md-1 d-grid">
                <a href="{{ route('attendance.employees') }}" class="btn btn-secondary">Reset</a>
            </div>

        </div>
    </form>

    {{-- Attendance Table --}}
    <div class="table-responsive">
        <table id="attendanceTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th width="80px">ID</th>
                    <th>Employee</th>
                    <th>Email</th>
                    <th>Today Login</th>
                    <th>Today Logout</th>
                    <th>Total Hours</th>
                    <th width="150px" class="text-center">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($employees as $emp)

                    @php
                        $record = $emp->attendances->where('login_time', '>=', now()->startOfDay())->first();
                    @endphp

                    <tr>
                        <td>{{ $emp->id }}</td>
                        <td>{{ $emp->name }}</td>
                        <td>{{ $emp->email }}</td>

                        <td>
                                {{ $record ? $record->login_time->format('h:i A') : '—' }}
                            </td>

                            <td>
                                {{ $record && $record->logout_time ? $record->logout_time->format('h:i A') : '—' }}
                            </td>

                       <td>
    @if($record && $record->logout_time)

        @php
            $totalMinutes = $record->login_time->diffInMinutes($record->logout_time); 
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
        @endphp

        {{ $hours }} hrs {{ $minutes }} mins

    @else
        —
    @endif
</td>

                        <td class="text-center">
                            <a href="{{ route('attendance.employeeDetail', $emp->id) }}"
                               class="btn btn-sm btn-primary">
                                View Detail
                            </a>
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
    $('#attendanceTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
        "scrollX": true
    });
});
</script>
@endpush
