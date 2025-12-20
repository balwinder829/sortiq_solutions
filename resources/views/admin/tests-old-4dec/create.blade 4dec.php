@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Test</h2>

    <form method="POST" action="{{ route('admin.tests.store') }}">
        @csrf
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Select Course</label>
            <select name="student_course_id" class="form-control" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button class="btn btn-success">Create Test</button>
    </form>
</div>
@endsection
