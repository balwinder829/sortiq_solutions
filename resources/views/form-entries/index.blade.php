@extends('layouts.app')
 
@section('title', 'Form Entries')
 
@section('content')
 
<style>
table.dataTable td {
    text-transform: capitalize;
}
</style>
 
<div class="container">
 
 <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Gmail Form Entries</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                  <a href="{{ route('admin.form-entries.export') }}" class="btn btn-success mb-3">
                    Download Excel
                </a>
            </div>
        </div>
    </div>
 
    @if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
    @endif
 
    @if(session('error'))
<div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
    @endif
 
    <div class="table-responsive">
<table id="entries-table" class="table table-bordered table-striped">
<thead>
<tr>
<th>#</th>
<th>Date</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Message</th>
<th>Action</th>
</tr>
</thead>
</table>
</div>
 
</div>
 
<div class="modal fade" id="messageModal" tabindex="-1">
<div class="modal-dialog modal-lg">
<div class="modal-content">
 
            <div class="modal-header">
<h5 class="modal-title">Full Message</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>
 
            <div class="modal-body">
<p id="fullMessage" style="white-space: pre-line;"></p>
</div>
 
        </div>
</div>
</div>
 
@endsection
 
@push('scripts')
<script>
$(document).ready(function () {
    $('#entries-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.form-entries.data') }}",
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
        lengthMenu: [10, 25, 50, 100]
    });
});
</script>
 
<script>
$(document).on('click', '.view-message', function () {
    let message = $(this).data('message');
    $('#fullMessage').text(message);
    $('#messageModal').modal('show');
});
</script>
@endpush