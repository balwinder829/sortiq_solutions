@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    {{-- LEFT: PAGE TITLE --}}
    <div class="col-md-8">
        <h1 class="page_heading">Employee Leave Requests</h1>
    </div>


</div>
<div class="mb-3">
    <button class="btn btn-primary copy-link" 
            data-link="{{ route('employee.leave.apply') }}">
        <i class="fa fa-link"></i> Copy Employee Leave Form Link
    </button>
</div>
{{-- FILTERS --}}
<div class="col-md-12 mb-3">
    <form class="row g-2 align-items-end">

        {{-- Employee --}}
        <div class="col-md-3">
            <select name="employee_id" class="form-select">
                <option value="">Employee</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">
                        {{ $emp->emp_name }} ({{ $emp->emp_code }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Status --}}
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>

        {{-- Date --}}
        <div class="col-md-2">
            <input type="date" name="date" class="form-control">
        </div>

        {{-- Range --}}
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
            <a href="{{ route('admin.employee.leave.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </form>
</div>

{{-- Alerts --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- TABLE --}}
<div class="table-responsive">
    <table id="leave-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Email</th>
                <th>Leave Dates</th>
                <th>Days</th>
                <th>Status</th>
                <th width="150">Actions</th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>
</div>

</div>

@endsection

@push('scripts')

<script>
$(document).ready(function () {

    var table = $('#leave-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.employee.leave.data') }}",
            data: function (d) {
                d.employee_id = $('select[name=employee_id]').val();
                d.status      = $('select[name=status]').val();
                d.date        = $('input[name=date]').val();
                d.range       = $('select[name=range]').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'emp_name' },
            { data: 2, name: 'email' },
            { data: 3, name: 'from_date' },
            { data: 4, name: 'total_days' },
            { data: 5, name: 'status' },
            { data: 6, name: 'actions', orderable:false, searchable:false }
        ],
        pageLength: 50
    });

    // 🔄 Reload on filter change
    $('select[name=employee_id], select[name=status], select[name=range]').on('change', function () {
        table.ajax.reload();
    });

    $('input[name=date]').on('change', function () {
        table.ajax.reload();
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