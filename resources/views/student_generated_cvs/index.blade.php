@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">
    <div class="col-md-6">
        <h1 class="page_heading">Student Generated CVs</h1>
    </div>

    <div class="col-md-6 text-end">
        <a href="{{ route('student-generated-cvs.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create CV
        </a>
    </div>
</div>

<div class="table-responsive">
    <table id="student-cvs-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>CV Title</th>
                <th>Professional Title</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Created</th>
                <th width="150">Action</th>
            </tr>
        </thead>
    </table>
</div>

</div>
@endsection

@push('scripts')

<script>
$(function () {

    $('#student-cvs-table').DataTable({

        processing: true,
        serverSide: true,

        ajax: "{{ route('student-generated-cvs.index') }}",

        columns: [
            {
                data: 0,
                name: 'id',
                orderable: false,
                searchable: false
            },
            {
                data: 1,
                name: 'name',
                searchable: true
            },
            {
                data: 2,
                name: 'title',
                searchable: true
            },
            {
                data: 3,
                name: 'professional_title',
                searchable: true
            },
            {
                data: 4,
                name: 'contact',
                orderable: false,
                searchable: false
            },
            {
                data: 5,
                name: 'email',
                orderable: false,
                searchable: false
            },
            {
                data: 6,
                name: 'created_at',
                searchable: false
            },
            {
                data: 7,
                name: 'action',
                orderable: false,
                searchable: false
            }
        ],

        order: [
            [6, 'desc']
        ]

    });

});
</script>

@endpush
