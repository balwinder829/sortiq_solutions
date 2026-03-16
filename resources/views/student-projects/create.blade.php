@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Create Project</h1>

<form method="POST" action="{{ route('student-projects.store') }}">

@csrf

<div class="row">

<div class="col-md-6 mb-3">
<label>Project Title</label>
<input type="text" name="title" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label>Project Type</label>
<select name="project_type" class="form-control">

<option value="mini">Mini Project</option>
<option value="major">Major Project</option>

</select>
</div>

<div class="col-md-6 mb-3">
<label>Technology</label>
<input type="text" name="technology" class="form-control">
</div>

<div class="col-md-6 mb-3">
<label>Difficulty</label>
<select name="difficulty" class="form-control">

<option value="easy">Easy</option>
<option value="medium">Medium</option>
<option value="hard">Hard</option>

</select>
</div>

<div class="col-md-6 mb-3">
<label>Estimated Days</label>
<input type="number" name="estimated_days" class="form-control">
</div>

<div class="col-md-12 mb-3">
<label>Description</label>
<textarea name="description" class="form-control"></textarea>
</div>

</div>

<button class="btn btn-primary">Save</button>

</form>

</div>

@endsection