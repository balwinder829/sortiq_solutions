@extends('layouts.app')

@section('content')
<style>

.permission-group{
    cursor:pointer;
    transition:0.2s ease;
    margin-right:3px;
}

.permission-group:hover{
    background:#6b51df !important;
    color:#fff !important;
}

/* 🔥 Modern Popover Style */
.popover{
    border-radius:12px;
    border:none;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
    max-width:260px;
}

.popover-body{
    font-size:13px;
    line-height:1.6;
    padding:10px 14px;
}

.perm-popover{
    padding:2px 0;
}

</style>
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Users</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                    <a href="{{ route('users.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add User</a>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <table id="usersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th> <!-- Active / Inactive / Deleted -->
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody></tbody>
    </table>


</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS (for tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('users.data') }}",
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
        lengthMenu: [5, 10, 25, 50, 100],
        columnDefs: [
            {
                targets: 6,          // ✅ Actions column index
                width: "150px",
                orderable: false
            }
        ]
    });

    $('#usersTable').on('draw.dt', function () {

        var popoverTriggerList = [].slice.call(
            document.querySelectorAll('[data-bs-toggle="popover"]')
        );

        popoverTriggerList.map(function (el) {
            return new bootstrap.Popover(el);
        });

    });
});
</script>
@endpush
