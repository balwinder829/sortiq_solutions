@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Assignment Details</h1>

<div class="card">

<div class="card-body">

<p><strong>Project:</strong>
{{ $assignment->project->title }}</p>

<p><strong>Student:</strong>
{{ $assignment->student->student_name }}</p>

<p><strong>Deadline:</strong>
{{ $assignment->deadline }}</p>

<p><strong>Status:</strong>

@if($assignment->status == 'assigned')
<span class="badge bg-secondary">Assigned</span>
@endif

@if($assignment->status == 'in_progress')
<span class="badge bg-warning">In Progress</span>
@endif

@if($assignment->status == 'submitted')
<span class="badge bg-primary">Submitted</span>
@endif

@if($assignment->status == 'reviewed')
<span class="badge bg-success">Reviewed</span>
@endif

</p>

</div>

</div>

</div>

@endsection