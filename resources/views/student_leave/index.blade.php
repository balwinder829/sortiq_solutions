@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Student Leave Requests</h1>
<div class="mb-3">
    <button class="btn btn-primary copy-link" 
            data-link="{{ route('student.leave.apply') }}">
        <i class="fa fa-link"></i> Copy Student Leave Form Link
    </button>
</div>

{{-- Filters --}}
<div class="row mb-3">

    <div class="col-md-3">
        <select name="student_id" class="form-select">
            <option value="">Student</option>
            @foreach($students as $s)
                <option value="{{ $s->id }}">
                    {{ $s->student_name }} ({{ $s->sno }})
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select name="status" class="form-select">
            <option value="">Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="col-md-2">
        <input type="date" name="date" class="form-control">
    </div>

    <div class="col-md-2">
        <select name="range" class="form-select">
            <option value="">Range</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="this_month">This Month</option>
        </select>
    </div>

    <div class="col-md-2">
        <a href="{{ route('admin.student.leave.index') }}" class="btn btn-secondary">Reset</a>
    </div>

</div>

{{-- Table --}}
<table id="student-leave-table" class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Contact</th>
            <th>Dates</th>
            <th>Days</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

</div>

@endsection

@push('scripts')

<script>
$(function () {

    var table = $('#student-leave-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.student.leave.data') }}",
            data: function (d) {
                d.student_id = $('select[name=student_id]').val();
                d.status = $('select[name=status]').val();
                d.date = $('input[name=date]').val();
                d.range = $('select[name=range]').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 }
        ]
    });

    $('select, input').on('change', function () {
        table.ajax.reload();
    });

    // Tooltip
    $('#student-leave-table').on('draw.dt', function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

});
</script>

<script>
$(document).on('click', '.copy-link', function () {

    let link = $(this).data('link');

    navigator.clipboard.writeText(link).then(function () {

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Form link copied to clipboard',
            timer: 1500,
            showConfirmButton: false
        });

    }, function () {

        Swal.fire({
            icon: 'error',
            title: 'Failed!',
            text: 'Could not copy link'
        });

    });

});
</script>

@endpush