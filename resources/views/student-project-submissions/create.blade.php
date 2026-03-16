@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Submit Project</h1>

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('student-project-submissions.store') }}">

@csrf

<div class="row">

<div class="col-md-6 mb-3">

<label>Assignment</label>

<select name="assignment_id" class="form-control">

@foreach($assignments as $assignment)

<option value="{{ $assignment->id }}">
{{ $assignment->project->title }} - {{ $assignment->student->student_name }}
</option>

@endforeach

</select>

</div>


<div class="col-md-6 mb-3">

<label>GitHub Link</label>

<input type="url"
name="github_link"
class="form-control"
placeholder="https://github.com/username/project">

</div>


<div class="col-md-6 mb-3">

<label>Live Link</label>

<input type="url"
name="live_link"
class="form-control"
placeholder="https://projectdemo.com">

</div>


<div class="col-md-12 mb-3">

<label>Notes</label>

<textarea name="notes"
class="form-control"
rows="4"></textarea>

</div>

</div>

<button class="btn btn-primary">
Submit Project
</button>

</form>

</div>

@endsection