@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Add CV Template</h1>

<form method="POST"
action="{{ route('admin.student.cv-templates.store') }}"
enctype="multipart/form-data">

@csrf

<div class="row">

<div class="col-md-6 mb-3">

<label>Template Name</label>

<input type="text"
name="name"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Template Key</label>

<input type="text"
name="template_key"
class="form-control"
placeholder="modern">

</div>

<div class="col-md-6 mb-3">

<label>Sample CV</label>

<input type="file"
name="sample_cv"
class="form-control">

</div>

<div class="col-md-6 mb-3">

<label>Status</label>

<select name="status"
class="form-control">

<option value="1">Active</option>
<option value="0">Inactive</option>

</select>

</div>

</div>

<button class="btn btn-primary">
Save Template
</button>

</form>

</div>

@endsection