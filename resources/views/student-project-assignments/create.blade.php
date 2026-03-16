@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Assign Project</h1>

<form method="POST"
action="{{ route('student-project-assignments.store') }}">

@csrf

<div class="row">

<div class="col-md-6 mb-3">

<label>Project</label>

<select name="project_id" class="form-control">

@foreach($projects as $project)

<option value="{{ $project->id }}">
{{ $project->title }}
</option>

@endforeach

</select>

</div>

<div class="col-md-6 mb-3">

<label>Deadline</label>

<input type="date"
name="deadline"
class="form-control">

</div>

<div class="col-md-12 mb-3">

<label>Select Students</label>

<select name="students[]"
class="form-control"
multiple>

@foreach($students as $student)

<option value="{{ $student->id }}">
{{ $student->student_name }}
</option>

@endforeach

</select>

</div>

</div>

<button class="btn btn-primary">
Assign Project
</button>

</form>

</div>

@endsection