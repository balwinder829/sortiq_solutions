@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-end">

        <div class="col-md-6">
            <h1 class="page_heading">College Mistakes</h1>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('college-mistakes.create') }}"
               class="btn btn-primary">
                Add College Mistake
            </a>
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-3">
            <input type="text"
                   name="college_name"
                   class="form-control"
                   placeholder="College Name">
        </div>

        <div class="col-md-3">
            <input type="text"
                   name="contact_person"
                   class="form-control"
                   placeholder="Contact Person">
        </div>

        <div class="col-md-2">
            <input type="text"
                   name="location"
                   class="form-control"
                   placeholder="Location">
        </div>

        <div class="col-md-2">
            <select name="website_status" class="form-select">
                <option value="">Website</option>
                <option value="with_website">With Website</option>
                <option value="without_website">Without Website</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date"
                   name="date"
                   class="form-control">
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-2">
            <a href="{{ route('college-mistakes.index') }}"
               class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">

        <table class="table table-bordered table-striped"
               id="mistakes-table">

            <thead>
                <tr>
                    <th>ID</th>
                    <th>College Name</th>
                    <th>Contact Person</th>
                    <th>Location</th>
                    <th>Website</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th width="100">Actions</th>
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

    var table = $('#mistakes-table').DataTable({

        processing: true,
        serverSide: true,

        ajax: {
            url: "{{ route('college-mistakes.data') }}",

            data: function (d) {

                d.college_name   = $('input[name=college_name]').val();
                d.contact_person = $('input[name=contact_person]').val();
                d.location       = $('input[name=location]').val();
                d.website_status = $('select[name=website_status]').val();
                d.date           = $('input[name=date]').val();

            }
        },

        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable:false, searchable:false },
        ],

        pageLength: 50,
        lengthMenu: [5,10,25,50,100]

    });

    $('input[name=college_name], input[name=contact_person], input[name=location], input[name=date]').on('keyup change', function () {
        table.ajax.reload();
    });

    $('select[name=website_status]').on('change', function () {
        table.ajax.reload();
    });

});
</script>

@endpush