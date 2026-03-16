@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Edit CV Template</h1>

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<form method="POST"
action="{{ route('admin.student.cv-templates.update',$template->id) }}"
enctype="multipart/form-data">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Template Name</label>

<input type="text"
name="name"
class="form-control"
value="{{ old('name',$template->name) }}"
required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Template Key</label>

<input type="text"
name="template_key"
class="form-control"
value="{{ old('template_key',$template->template_key) }}"
placeholder="modern">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Sample CV</label>

@if($template->sample_cv)

<div class="mb-2">
<a href="{{ asset('uploads/cv-samples/'.$template->sample_cv) }}"
target="_blank"
class="btn btn-sm btn-info">
View Current File
</a>
</div>
@endif

<input type="file"
name="sample_cv"
class="form-control">

<small class="text-muted">
Leave empty if you don't want to change the file
</small>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Status</label>

<select name="status"
class="form-control">

<option value="1"
{{ $template->status == 1 ? 'selected' : '' }}>
Active
</option>

<option value="0"
{{ $template->status == 0 ? 'selected' : '' }}>
Inactive
</option>

</select>

</div>

</div>

<div class="mt-3">

<button type="submit"
class="btn btn-primary">
Update Template </button>

<a href="{{ route('admin.student.cv-templates.index') }}"
class="btn btn-secondary">
Back </a>

</div>

</form>

</div>

@endsection
