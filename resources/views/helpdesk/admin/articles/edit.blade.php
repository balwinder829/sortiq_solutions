@extends('layouts.app')
@php use Illuminate\Support\Str; @endphp


@section('content')

<div class="container">

<div class="row mb-4">
    <div class="col-md-12">
        <h1 class="page_heading">Edit Helpdesk Post</h1>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
<ul class="mb-0">
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form action="{{ route('admin.helpdesk.articles.update',$article->id) }}"
method="POST"
enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="row">

{{-- TECHNOLOGY --}}
<div class="form-group col-md-6 mb-3">
<label>Categories</label>
<select name="technology_id" class="form-control">
@foreach($technologies as $id=>$name)
<option value="{{ $id }}"
{{ old('technology_id',$article->technology_id)==$id?'selected':'' }}>
{{ $name }}
</option>
@endforeach
</select>
</div>

{{-- TITLE --}}
<div class="form-group col-md-6 mb-3">
<label>Title</label>
<input type="text"
name="title"
value="{{ old('title',$article->title) }}"
class="form-control">
</div>

{{-- STATUS --}}
<div class="form-group col-md-4 mb-3">
<label>Status</label>
<select name="status" class="form-control">
<option value="draft"
{{ old('status',$article->status)=='draft'?'selected':'' }}>
Draft
</option>
<option value="published"
{{ old('status',$article->status)=='published'?'selected':'' }}>
Published
</option>
</select>
</div>

{{-- DESCRIPTION --}}
<div class="form-group col-md-12 mb-3">
<label>Content</label>
<textarea name="description"
rows="6"
class="form-control">{{ old('description',$article->description) }}</textarea>
</div>

{{-- NEW FILES --}}
<!-- <div class="form-group col-md-8 mb-3">
<label>Add More Attachments</label>
<input type="file" name="files[]" multiple class="form-control">
</div> -->

{{-- EXPIRE --}}
<div class="form-group col-md-4 mb-3">
<label>Expire New Files At</label>
<input type="datetime-local"
name="expires_at"
value="{{ old('expires_at', $article->expires_at ? \Carbon\Carbon::parse($article->expires_at)->format('Y-m-d\TH:i') : '') }}"
class="form-control">
</div>

{{-- EXISTING FILES --}}
@if($article->attachments->count())
<!-- <div class="col-md-12 mt-4">
<label>Existing Attachments</label>

<table class="table table-bordered">
<thead>
<tr>
<th>File</th>
<th>Type</th>
<th width="120">Action</th>
</tr>
</thead>

<tbody>
@foreach($article->attachments as $file)
<tr>
<td class="position-relative">

@php
    $fileUrl = asset('storage/'.$file->file_path);
@endphp

<span class="admin-preview-trigger text-primary"
      style="cursor:pointer;">
    {{ $file->file_name }}
</span>

{{-- HOVER PREVIEW BOX --}}
<div class="admin-preview-box shadow"
     style="
        display:none;
        position:absolute;
        top:28px;
        left:0;
        background:#fff;
        border:1px solid #ddd;
        padding:8px;
        z-index:9999;
        max-width:420px;">

    {{-- IMAGE PREVIEW --}}
    @if(Str::contains($file->file_type,'image'))
        <img src="{{ $fileUrl }}"
             style="max-width:380px;height:auto;">
    
    {{-- PDF / OFFICE PREVIEW --}}
    @elseif(Str::contains($file->file_type,'pdf')
        || Str::contains($file->file_type,'officedocument')
        || Str::contains($file->file_type,'msword'))
        <iframe src="{{ $fileUrl }}"
                width="380"
                height="300"
                style="border:none;"></iframe>
    @else
        <span>No preview available</span>
    @endif

</div>

</td>

<td>{{ $file->file_type }}</td>
<td>
<button type="button"
class="btn btn-danger btn-sm"
onclick="document.getElementById('delete-file-{{ $file->id }}').submit();">
Delete
</button>
</td>

</tr>
@endforeach
</tbody>
</table>

</div> -->
@endif

</div>

<button type="submit" class="btn btn-primary">
Update Post
</button>
 <a href="{{ route('admin.helpdesk.articles.index') }}" class="btn btn-success">Back</a>

</form>
{{-- OUTSIDE MAIN ARTICLE FORM --}}
@if($article->attachments->count())
@foreach($article->attachments as $file)

<form id="delete-file-{{ $file->id }}"
action="{{ route('admin.helpdesk.attachments.destroy',$file->id) }}"
method="POST"
style="display:none;">
@csrf
@method('DELETE')
</form>

@endforeach
@endif


</div>
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('description');
</script>
<script>
document.querySelectorAll('.admin-preview-trigger').forEach(el => {

    const box = el.nextElementSibling;

    el.addEventListener('mouseenter', function(){
        box.style.display = 'block';
    });

    el.addEventListener('mouseleave', function(){
        box.style.display = 'none';
    });

});
</script>


@endsection
