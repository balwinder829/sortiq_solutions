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

<a href="{{ route('admin.tests.index') }}" class="btn btn-dark mb-3">Back</a>

<form method="POST" action="{{ route('admin.tests.update', $test->id) }}">
@csrf @method('PUT')

<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text" name="title" class="form-control" 
               value="{{ $test->title }}" required>
    </div>

    {{-- Slug --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Slug</label>
        <input type="text" name="slug" class="form-control" 
               value="{{ $test->slug }}" required>
    </div>

    {{-- Access Key --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Access Key</label>
        <input type="text" name="access_key" class="form-control" 
               value="{{ $test->access_key }}" required>
    </div>

    {{-- Category --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Category</label>
        <select name="test_category_id" class="form-control">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" 
                        {{ $test->test_category_id == $cat->id ? 'selected':'' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- College --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <select name="college_id" class="form-control">
            @foreach($colleges as $col)
                <option value="{{ $col->id }}" 
                        {{ $test->college_id == $col->id ? 'selected':'' }}>
                    {{ $col->college_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Course --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Course</label>
        <select name="student_course_id" class="form-control">
            @foreach($courses as $course)
                <option value="{{ $course->id }}"
                        {{ $test->student_course_id == $course->id ? 'selected':'' }}>
                    {{ $course->course_name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- Semester --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Semester</label>
        <select name="semester_id" class="form-control">
            @foreach($semesters as $sem)
                <option value="{{ $sem->id }}"
                        {{ $test->semester_id == $sem->id ? 'selected':'' }}>
                    {{ $sem->name }}
                </option>
            @endforeach
        </select>
    </div>

    
    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status" class="form-control">
            <option value="draft"       {{ $test->status=='draft'?'selected':'' }}>Draft</option>
            <option value="published"   {{ $test->status=='published'?'selected':'' }}>Published</option>
            <option value="unpublished" {{ $test->status=='unpublished'?'selected':'' }}>Unpublished</option>
        </select>
    </div>

    {{-- Test Date --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Test Date</label>
        <input type="date" name="test_date" class="form-control"
               value="{{ $test->test_date }}">
    </div>

    {{-- Description --}}
    <div class="col-md-12 mb-3">
        <label class="fw-bold">Description</label>
        <textarea name="description" class="form-control" rows="3">
            {{ $test->description }}
        </textarea>
    </div>

</div>

<button class="btn btn-primary mt-3">Update Test</button>

</form>
</div>
@endsection
