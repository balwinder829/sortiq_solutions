@extends('layouts.app')

@section('content')
<div class="container">

    {{-- Page Heading --}}
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Fee Statistics</h1>
        </div>
    </div>

    {{-- STATISTICS CARDS --}}
   <div class="row mb-4 align-items-stretch">

    <div class="col-md-3">
        <div class="card text-center shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6>Total Fees</h6>
                <h4>Rs. {{ number_format($totalFee) }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center text-success">
                <h6>Paid Fees</h6>
                <h4>Rs. {{ number_format($paidFee) }}</h4>
                <small>{{ $paidPercent }}%</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center text-danger">
                <h6>Pending Fees</h6>
                <h4>Rs. {{ number_format($pendingFee) }}</h4>
                <small>{{ $pendingPercent }}%</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center shadow-sm h-100">
            <div class="card-body d-flex flex-column justify-content-center">
                <h6>Total Students</h6>
                <h4>{{ $students->count() }}</h4>
            </div>
        </div>
    </div>

</div>


    {{-- FILTERS --}}
    <div class="row mb-3">
        <div class="col-md-10">
            <form method="GET" action="{{ route('fee.status') }}" class="row g-2">

                <div class="col-md-4">
                    <select name="college_id" class="form-control">
                        <option value="">All Colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}"
                                {{ request('college_id') == $college->id ? 'selected' : '' }}>
                                {{ $college->college_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <select name="course_id" class="form-control">
                        <option value="">All Technologies</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('fee.status') }}" class="btn btn-secondary">Reset</a>
                    <a href="{{ route('fee.status.export', request()->query()) }}"
                        class="btn btn-success"
                    >
                        Export
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- TABLE --}}
    <table id="feeStatusTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Student</th>
                <th>College</th>
                <th>Technology</th>
                <th>Total Fee</th>
                <th>Paid Fee</th>
                <th>Pending Fee</th>
                <th>Paid %</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @foreach($students as $student)
                <tr>
                    <td>{{ $student->student_name }}</td>
                    <td>{{ $student->collegeData->college_name ?? '-' }}</td>
                    <td>{{ $student->courseData->course_name ?? '-' }}</td>
                    <td>{{ number_format($student->total_fees) }}</td>
                    <td>{{ number_format($student->reg_fees) }}</td>
                    <td>{{ number_format($student->pending_fees) }}</td>
                    <td>
                        <span class="badge bg-success">
                            {{ $student->paid_percentage }}%
                        </span>
                    </td>
                    <td>
                        @if($student->fee_status === 'Fully Paid')
                            <span class="badge bg-success">Fully Paid</span>
                        @elseif($student->fee_status === 'Partially Paid')
                            <span class="badge bg-warning">Partially Paid</span>
                        @else
                            <span class="badge bg-danger">Not Paid</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection
@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {
    $('#feeStatusTable').DataTable({
        pageLength: 10,
        lengthMenu: [10, 15, 20, 25, 50, 100]
    });
});
</script>
@endpush
