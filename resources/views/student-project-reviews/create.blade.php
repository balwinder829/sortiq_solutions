@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Project Review</h1>

<form method="POST"
action="{{ route('student-project-reviews.store') }}">

@csrf

<div class="row">

<div class="col-md-6 mb-3">

<label>Submission</label>

<select name="submission_id"
class="form-control">

@foreach($submissions as $submission)

<option value="{{ $submission->id }}">
{{ $submission->assignment->project->title }}
- {{ $submission->assignment->student->student_name }}
</option>

@endforeach

</select>

</div>


<div class="col-md-6 mb-3">

<label>Rating</label>

<select name="rating"
class="form-control">

<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>

</select>

</div>


<div class="col-md-12 mb-3">

<label>Feedback</label>

<textarea name="feedback"
class="form-control"
rows="4"></textarea>

</div>

</div>

<button class="btn btn-success">
Submit Review
</button>

</form>

</div>

@endsection