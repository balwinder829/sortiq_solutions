@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading">Add Job Description</h1>

<form method="POST" action="{{ route('jd.store') }}">
@csrf

<div class="row">

    <div class="col-md-6 mb-2">
        <label>Title</label>
        <input type="text" name="title" class="form-control">
    </div>

    <div class="col-md-3 mb-2">
        <label>Job Type</label>
        <select name="job_type" class="form-select">
            <option value="full-time">Full Time</option>
            <option value="internship">Internship</option>
        </select>
    </div>

    <div class="col-md-3 mb-2">
        <label>Status</label>
        <select name="status" class="form-select">
            <option value="draft">Draft</option>
            <option value="active">Active</option>
            <option value="closed">Closed</option>
        </select>
    </div>

    <div class="col-md-4 mb-2">
        <label>Last Date</label>
        <input type="date" name="last_date" class="form-control">
    </div>

    <div class="col-md-12 mb-2">
        <label>Description</label>
        <textarea name="description" id="editor" class="form-control"></textarea>
    </div>

</div>

<button class="btn btn-success mt-3">Save</button>

</form>

</div>

@endsection

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('editor');
</script> 

@endpush