@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-4">Upload Attachment</h1>

<form action="{{ route('admin.helpdesk.attachments.store') }}"
method="POST" enctype="multipart/form-data">
@csrf

<div class="row">

<div class="form-group col-md-6 mb-3">
<label>Article</label>
<select name="article_id" class="form-control">
@foreach($articles as $id=>$title)
<option value="{{ $id }}">{{ $title }}</option>
@endforeach
</select>
</div>

<div class="form-group col-md-6 mb-3">
<label>File</label>
<input type="file" name="file" class="form-control">
</div>

<div class="form-group col-md-6 mb-3">
<label>Expire At</label>
<input type="datetime-local" name="expires_at" class="form-control">
</div>

</div>

<button class="btn btn-success">Upload</button>

</form>

</div>

@endsection
