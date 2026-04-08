@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-8">
        <h1 class="page_heading">Job Descriptions</h1>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('jd.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add JD
            </a>
        </div>
    </div>

</div>

{{-- FILTERS --}}
<div class="col-md-12 mb-3">
    <form class="row g-2 align-items-end">

        <div class="col-md-3">
            <select id="filter-status" class="form-select">
                <option value="">Status</option>
                <option value="draft">Draft</option>
                <option value="active">Active</option>
                <option value="closed">Closed</option>
            </select>
        </div>

        <div class="col-md-3">
            <select id="filter-type" class="form-select">
                <option value="">Job Type</option>
                <option value="full-time">Full Time</option>
                <option value="internship">Internship</option>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" id="filter-date" class="form-control">
        </div>

        <div class="col-md-3 d-flex gap-2">
            <a href="{{ route('jd.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </form>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
</div>
@endif

<div class="table-responsive">
    <table id="jd-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Job Type</th>
                <th>Last Date</th>
                <th>Status</th>
                <th width="200">Actions</th>
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

    let table = $('#jd-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('jd.data') }}",
            data: function (d) {
                d.status   = $('#filter-status').val();
                d.job_type = $('#filter-type').val();
                d.date     = $('#filter-date').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'title' },
            { data: 2, name: 'job_type' },
            { data: 3, name: 'last_date' },
            { data: 4, name: 'status' },
            { data: 5, name: 'actions', orderable:false, searchable:false }
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
    });

    $('#filter-status, #filter-type, #filter-date').on('change', function () {
        table.ajax.reload();
    });

});
// COPY LINK WITH SWEET ALERT
$(document).on('click', '.copy-link-btn', function(){

    let url = $(this).data('url');

    navigator.clipboard.writeText(url).then(function(){

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'JD link copied successfully',
            timer: 1500,
            showConfirmButton: false
        });

    }).catch(function(){
        alert("Copy failed");
    });

});
</script>
@endpush