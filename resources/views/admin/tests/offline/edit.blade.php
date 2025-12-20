@extends('layouts.app')

@section('content')
<div class="container">

<h3>Edit Test</h3>

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

<a href="{{ route('admin.offline-tests.index') }}" class="btn btn-dark mb-3">
    Back
</a>
<form method="POST"
      action="{{ route('admin.offline-tests.update', ['offline_test' => $test->id]) }}">

    @csrf
    @method('PUT')


<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text"
               name="title"
               class="form-control"
               value="{{ old('title', $test->title) }}"
               required>
    </div>

    {{-- Category --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Category</label>
        <select name="test_category_id" class="form-control" required>
            <option value="">Select Category</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ old('test_category_id', $test->test_category_id) == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- College --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <select name="college_id" class="form-control" required>
            <option value="">Select College</option>
            @foreach($colleges as $col)
                <option value="{{ $col->id }}"
                    {{ old('college_id', $test->college_id) == $col->id ? 'selected' : '' }}>
                    {{ $col->FullName }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Course --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Course</label>
        <select name="student_course_id" class="form-control" required>
            <option value="">Select Course</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}"
                    {{ old('student_course_id', $test->student_course_id) == $course->id ? 'selected' : '' }}>
                    {{ $course->course_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Semester --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Semester</label>
        <select name="semester_id" class="form-control" required>
            <option value="">Select Semester</option>
            @foreach($semesters as $sem)
                <option value="{{ $sem->id }}"
                    {{ old('semester_id', $test->semester_id) == $sem->id ? 'selected' : '' }}>
                    {{ $sem->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status" class="form-control" required>
            <option value="draft" {{ old('status', $test->status) == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status', $test->status) == 'published' ? 'selected' : '' }}>Published</option>
            <option value="unpublished" {{ old('status', $test->status) == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
        </select>
    </div>

    {{-- Active --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Active</label>
        <select name="is_active" class="form-control" required>
            <option value="1" {{ old('is_active', $test->is_active) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $test->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    {{-- Test Date --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Test Date</label>
        <input type="date"
               name="test_date"
               class="form-control"
               value="{{ old('test_date', $test->test_date) }}">
    </div>

    <input type="hidden" name="test_mode" value="offline">

    {{-- Description --}}
    <div class="col-md-12 mb-3">
        <label class="fw-bold">Description</label>
        <textarea name="description"
                  class="form-control"
                  rows="3">{{ old('description', $test->description) }}</textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Update Test</button>

</form>
</div>
@endsection
