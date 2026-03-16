@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Edit Project</h1>

<form method="POST" action="{{ route('student-projects.update',$project->id) }}">

@csrf
@method('PUT')

<div class="row">

<div class="col-md-6 mb-3">
<label>Project Title</label>
<input type="text"
name="title"
class="form-control"
value="{{ $project->title }}">
</div>

<div class="col-md-6 mb-3">
<label>Project Type</label>

<select name="project_type" class="form-control">

<option value="mini"
{{ $project->project_type=='mini'?'selected':'' }}>
Mini
</option>

<option value="major"
{{ $project->project_type=='major'?'selected':'' }}>
Major
</option>

</select>

</div>

<div class="col-md-6 mb-3">
<label>Technology</label>
<input type="text"
name="technology"
class="form-control"
value="{{ $project->technology }}">
</div>

<div class="col-md-6 mb-3">
<label>Difficulty</label>

<select name="difficulty" class="form-control">

<option value="easy"
{{ $project->difficulty=='easy'?'selected':'' }}>
Easy
</option>

<option value="medium"
{{ $project->difficulty=='medium'?'selected':'' }}>
Medium
</option>

<option value="hard"
{{ $project->difficulty=='hard'?'selected':'' }}>
Hard
</option>

</select>

</div>

<div class="col-md-6 mb-3">
<label>Estimated Days</label>

<input type="number"
name="estimated_days"
class="form-control"
value="{{ $project->estimated_days }}">

</div>

<div class="col-md-12 mb-3">

<label>Description</label>

<textarea name="description"
class="form-control">

{{ $project->description }}

</textarea>

</div>

</div>

<button class="btn btn-primary">Update</button>

</form>

</div>

@endsection