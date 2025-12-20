@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Test</h2>

    <form method="POST" action="{{ route('admin.tests.update', $test->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" value="{{ $test->title }}" required>
        </div>
        <div class="mb-3">
            <label>Select Course</label>
            <select name="student_course_id" class="form-control" required>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ $test->student_course_id==$course->id?'selected':'' }}>{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ $test->description }}</textarea>
        </div>
        <button class="btn btn-success">Update Test</button>
    </form>
</div>
@endsection
