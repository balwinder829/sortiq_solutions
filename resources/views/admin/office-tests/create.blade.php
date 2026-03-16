@extends('layouts.app')

@section('content')
<div class="container">

<div class="row mb-2">
    <div class="col-md-6">
        <h1 class="page_heading">Create Office Test</h1>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif


<form method="POST" action="{{ route('admin.office-tests.store') }}">
@csrf

<div class="row">

{{-- Title --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Title</label>
<input type="text" name="title" class="form-control" required>
</div>

{{-- Session --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Session</label>
<select name="session_id" class="form-control" required>
<option value="">Select Session</option>
@foreach($sessions as $session)
<option value="{{ $session->id }}">{{ $session->session_name }}</option>
@endforeach
</select>
</div>

{{-- Category --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Category</label>
<select name="test_category_id" class="form-control">
<option value="">Select Category</option>
@foreach($categories as $cat)
<option value="{{ $cat->id }}">{{ $cat->name }}</option>
@endforeach
</select>
</div>

{{-- Course --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Course</label>
<select name="student_course_id" class="form-control">
<option value="">Select Course</option>
@foreach($courses as $course)
<option value="{{ $course->id }}">{{ $course->course_name }}</option>
@endforeach
</select>
</div>

{{-- Batch --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Batch</label>
<select name="batch_id" class="form-control">
<option value="">Select Batch</option>
@foreach($batches as $batch)
<option value="{{ $batch->id }}">{{ $batch->batch_name }}</option>
@endforeach
</select>
</div>

{{-- Trainer --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Trainer</label>
<select name="trainer_id" class="form-control">
<option value="">Select Trainer</option>
@foreach($trainers as $trainer)
<option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
@endforeach
</select>
</div>

{{-- Exam Mode --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Exam Mode</label>
<select name="exam_mode" class="form-control" required>
<option value="offline">Offline</option>
<option value="online">Online</option>
</select>
</div>

{{-- Status --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Status</label>
<select name="status" class="form-control" required>
<option value="draft">Draft</option>
<option value="published">Published</option>
<option value="unpublished">Unpublished</option>
</select>
</div>

{{-- Active --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Active</label>
<select name="is_active" class="form-control">
<option value="1">Active</option>
<option value="0">Inactive</option>
</select>
</div>

{{-- Test Date --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Test Date</label>
<input type="date" name="test_date" class="form-control">
</div>

{{-- Start Time --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Exam Start Time</label>
<input type="datetime-local" name="exam_start_at" class="form-control">
</div>

{{-- End Time --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Exam End Time</label>
<input type="datetime-local" name="exam_end_at" class="form-control">
</div>

{{-- Timer --}}
<div class="col-md-6 mb-3">
<label class="fw-bold">Timer Type</label>
<select name="timer_type" class="form-control">
<option value="fixed">Fixed (Same for all)</option>
<option value="individual">Individual</option>
</select>
</div>

{{-- Description --}}
<div class="col-md-12 mb-3">
<label class="fw-bold">Description</label>
<textarea name="description" class="form-control" rows="3"></textarea>
</div>

</div>

<div class="form-group col-md-6">
<button class="btn btn-primary">Save Test</button>

<a href="{{ route('admin.office-tests.index') }}"
class="btn btn-secondary ml-2">Back</a>
</div>

</form>
</div>
@endsection