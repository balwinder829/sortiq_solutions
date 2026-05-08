@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('content')

<style>
    table.dataTable td { text-transform: capitalize; }
</style>

<div class="container mt-4">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Employee Attendance</h1>
        </div>
         
    </div>
    
   
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" id="filterForm" class="mb-4">
        <div class="row g-2">

            <div class="col-md-3">
                <input type="text" name="name" class="form-control filterchange"
                       placeholder="Employee Name" value="{{ request('name') }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control filterchange"
                       value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control filterchange"
                       value="{{ request('end_date') }}">
            </div>

            <!-- <div class="col-md-2 d-grid">
                <button class="btn btn-primary">Search</button>
            </div> -->

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

{{-- ================= EMPLOYEES ================= --}}
@foreach($employees as $emp)

    @php
        $record = $emp->attendances
            ->where('login_time', '>=', now()->startOfDay())
            ->first();
    @endphp

    <tr>
        <td> </td>
        <td>{{ $emp->emp_name }} <span class="badge bg-secondary">Employee</span></td>
        <td>{{ $emp->email }}</td>

        <td>{{ $record ? $record->login_time->format('h:i A') : '—' }}</td>
        <td>{{ $record && $record->logout_time ? $record->logout_time->format('h:i A') : '—' }}</td>

        <td>
            @if($record && $record->logout_time)
                @php
                    $mins = $record->login_time->diffInMinutes($record->logout_time);
                @endphp
                {{ floor($mins/60) }} hrs {{ $mins % 60 }} mins
            @else
                —
            @endif
        </td>

        <td class="text-center">
            <a href="{{ route('attendance.employeeDetail', $emp->id) }}"
               class="btn btn-sm" title="View Detail">
                <i class="fa fa-eye"></i>
            </a>
        </td>
    </tr>

@endforeach


{{-- ================= TRAINERS ================= --}}
@foreach($trainers as $trainer)

    @php
        $record = $trainer->attendances
            ->where('login_time', '>=', now()->startOfDay())
            ->first();
    @endphp

    <tr>
        <td> </td>
        <td>{{ $trainer->name }} <span class="badge bg-primary">Trainer</span></td>
        <td>{{ $trainer->email ?? '—' }}</td>

        <td>{{ $record ? $record->login_time->format('h:i A') : '—' }}</td>
        <td>{{ $record && $record->logout_time ? $record->logout_time->format('h:i A') : '—' }}</td>

        <td>
            @if($record && $record->logout_time)
                @php
                    $mins = $record->login_time->diffInMinutes($record->logout_time);
                @endphp
                {{ floor($mins/60) }} hrs {{ $mins % 60 }} mins
            @else
                —
            @endif
        </td>

        <td class="text-center">
           <a href="{{ route('attendance.detail',['type'=>'trainer','id'=>$trainer->id]) }}"
               class="btn btn-sm" title="View Trainer Attendance">
                <i class="fa fa-eye"></i>
            </a>

        </td>
    </tr>

@endforeach

{{-- ================= TRAINERS ================= --}}
@foreach($sales_staff as $staff)

    @php
        $record = $staff->attendances
            ->where('login_time', '>=', now()->startOfDay())
            ->first();
    @endphp

    <tr>
        <td> </td>
        <td>{{ $staff->name }} <span class="badge bg-primary">Sales</span></td>
        <td>{{ $staff->email ?? '—' }}</td>

        <td>{{ $record ? $record->login_time->format('h:i A') : '—' }}</td>
        <td>{{ $record && $record->logout_time ? $record->logout_time->format('h:i A') : '—' }}</td>

        <td>
            @if($record && $record->logout_time)
                @php
                    $mins = $record->login_time->diffInMinutes($record->logout_time);
                @endphp
                {{ floor($mins/60) }} hrs {{ $mins % 60 }} mins
            @else
                —
            @endif
        </td>

        <td class="text-center">
          <a href="{{ route('attendance.detail',['type'=>'sales_staff','id'=>$staff->id]) }}"
               class="btn btn-sm" title="View Sale User Attendance">
            <i class="fa fa-eye"></i>
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
   var table =  $('#attendanceTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50],
         columnDefs: [
            {
                targets: 0, // first column
                searchable: false,
                orderable: false
            }
        ]
        // "scrollX": true
    });
   table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
});
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
        
    });
    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 500); // waits 500ms after typing stops
    });

});
</script>
@endpush
