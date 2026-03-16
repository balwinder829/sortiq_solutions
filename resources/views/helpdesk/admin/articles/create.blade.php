@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page_heading">Create Helpdesk Content</h1>
    </div>
</div>

{{-- GLOBAL ERROR BLOCK --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.helpdesk.articles.store') }}"
method="POST"
enctype="multipart/form-data">
@csrf

<div class="row">

{{-- TECHNOLOGY --}}
<div class="form-group col-md-6 mb-3">
<label>Category</label>
<select name="technology_id"
class="form-control @error('technology_id') is-invalid @enderror" required>
<option value="">--Choose Category--</option>
@foreach($technologies as $id=>$name)
<option value="{{ $id }}"
{{ old('technology_id')==$id ? 'selected':'' }}>
{{ $name }}
</option>
@endforeach

</select>

@error('technology_id')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- TITLE --}}
<div class="form-group col-md-6 mb-3">
<label>Title</label>
<input type="text"
name="title"
value="{{ old('title') }}"
class="form-control @error('title') is-invalid @enderror">

@error('title')
<div class="invalid-feedback">{{ $message }}</div>
@enderror
</div>

{{-- STATUS --}}
<div class="form-group col-md-4 mb-3">
<label>Status</label>
<select name="status"
class="form-control @error('status') is-invalid @enderror">
<option value="draft" {{ old('status')=='draft'?'selected':'' }}>Draft</option>
<option value="published" selected {{ old('status')=='published'?'selected':'' }}>Published</option>
</select>
</div>

{{-- DESCRIPTION --}}
<div class="form-group col-md-12 mb-3">
<label>Content</label>
<textarea name="description"
rows="6"
class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
</div>

{{-- FILE UPLOAD --}}
<!-- <div class="form-group col-md-8 mb-3">
<label>Upload Attachments</label>
<input type="file"
name="files[]"
multiple
required 
class="form-control @error('files.*') is-invalid @enderror">
</div> -->

{{-- EXPIRE --}}
<div class="form-group col-md-4 mb-3">
<label>Expire Files At</label>
<input type="datetime-local"
name="expires_at"
value="{{ old('expires_at') }}"
class="form-control">
</div>

</div>

<button class="btn btn-primary">Save</button>
<a href="{{ route('admin.helpdesk.articles.index') }}" class="btn btn-success">Back</a>

</form>

</div>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('description');
</script>
@endsection

