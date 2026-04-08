@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Employees</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end gap-2">
                   <a href="{{ route('admin.payroll.index') }}" class="btn mb-3" style="background-color:#6b51df;color:#fff;">View Salary List </a> 
                   <a href="{{ route('employees.create') }}" class="btn mb-3" style="background-color:#6b51df;color:#fff;">Add Employee </a>
            </div>
        </div>
    </div>

<div class="row mb-3 align-items-end">
    <div class="col-md-3">
        <label>Status</label>
        <select id="filter_status" class="form-control">
            <option value="">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Employee Status</label>
        <select id="filter_lifecycle" class="form-control">
            <option value="">All</option>
            <option value="current">Current</option>
            <option value="former">Former</option>
            <option value="pending">Pending</option>
        </select>
    </div>

    <div class="col-md-2">
        <button id="reset_filters" class="btn btn-secondary w-100">
            Reset
        </button>
    </div>
</div>
    <div class="col-md-8 mb-4">
    <p class="mb-1 fw-bold">Employee Login URL</p>

    <div class="input-group">
        <a href="{{ route('sale_staff.login') }}"
           target="_blank"
           id="loginUrl"
           class="form-control text-primary text-decoration-none">
            {{ route('employee.login') }}
        </a>

        <button class="btn btn-outline-secondary"
                type="button"
                 data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Copy Employee Login URL"
                onclick="copyLoginUrl()">
            <i class="fa fa-copy"></i>
        </button>
    </div>

    <small id="copyMessage" class="text-success d-none">
        Copied to clipboard!
    </small>
</div>
   

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="employeesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Position</th>
                <th>Joining Date</th>
                <th>Username</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>
</div>
@endsection

 

@push('scripts')
<script>
$(document).ready(function() {
    $('#employeesTable').DataTable({
        processing: true,
        serverSide: true,
        // ajax: "{{ route('employees.data') }}",
        ajax: {
            url: "{{ route('employees.data') }}",
            data: function (d) {
                d.status = $('#filter_status').val();
                d.lifecycle = $('#filter_lifecycle').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6, orderable: false, searchable: false }
        ],
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });

    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });

    $('#filter_status, #filter_lifecycle').change(function () {
        $('#employeesTable').DataTable().ajax.reload();
    });
    
    $('#reset_filters').click(function () {
        // Reset dropdowns
        $('#filter_status').val('');
        $('#filter_lifecycle').val('');

        // Reload table
        $('#employeesTable').DataTable().ajax.reload();
    });
});
</script>
 <script>
function copyLoginUrl() {
    const url = document.getElementById('loginUrl').textContent.trim(); // ✅ removes extra spaces;

    navigator.clipboard.writeText(url).then(function() {
        const msg = document.getElementById('copyMessage');
        msg.classList.remove('d-none');

        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);
    });
}
</script>
@endpush
