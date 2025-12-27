@extends('layouts.app')

@section('content')
<div class="container">

<h3>Create Test</h3>


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


<a href="{{ route('admin.tests.index') }}" class="btn btn-dark mb-3">Back</a>

<form method="POST" action="{{ route('admin.tests.store') }}">
@csrf

<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    {{-- Slug --}}
   <!--  <div class="col-md-6 mb-3">
        <label class="fw-bold">Slug</label>
        <input type="text" name="slug" class="form-control" required>
    </div> -->

    {{-- Access Key --}}
    <!-- <div class="col-md-6 mb-3">
        <label class="fw-bold">Access Key</label>
        <input type="text" name="access_key" class="form-control" required>
    </div> -->

    {{-- Category --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Category</label>
        <select name="test_category_id" class="form-control" required>
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- College --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <select name="college_id" class="form-control" required>
            <option value="">Select College</option>
            @foreach($colleges as $col)
                <option value="{{ $col->id }}">{{ $col->FullName }}</option>
            @endforeach
        </select>
    </div>

    {{-- Course --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Course</label>
        <select name="student_course_id" class="form-control" required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Semester --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Semester</label>
        <select name="semester_id" class="form-control" required>
            <option value="">Select Semester</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem->id }}">{{ $sem->name }}</option>
            @endforeach
        </select>
    </div>

    {{-- Branch --}}
    <!-- <div class="col-md-6 mb-3">
        <label class="fw-bold">Branch</label>
        <select name="branch_id" class="form-control" required>
            <option value="">Select Branch</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
            @endforeach
        </select>
    </div> -->

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status" class="form-control" required>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="unpublished">Unpublished</option>
        </select>
    </div>

     <div class="col-md-6 mb-3">
        <label class="fw-bold">Active</label>
        <select name="is_active" class="form-control" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
            
        </select>
    </div>

    {{-- Test Date --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Test Date</label>
        <input type="date" name="test_date" class="form-control">
    </div>

    {{-- Exam Start Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam Start Time</label>
        <input type="datetime-local"
               name="exam_start_at"
               class="form-control"
               required>
    </div>

    {{-- Exam End Time --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Exam End Time</label>
        <input type="datetime-local"
               name="exam_end_at"
               class="form-control"
               required>
    </div>

    {{-- Timer Type --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Timer Type</label>
        <select name="timer_type" class="form-control" required>
            <option value="fixed" selected>Fixed (Same for all)</option>
            <option value="individual">Individual</option>
        </select>
    </div>


    {{-- Description --}}
    <div class="col-md-12 mb-3">
        <label class="fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3"></textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Save Test</button>

</form>
</div>
@endsection
