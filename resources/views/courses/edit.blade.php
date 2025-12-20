@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Course</h4>
        </div>
        <div class="card-body">
            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('courses.update', $course->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Important for PUT request -->

                <div class="form-group">
                    <label for="course_name">Course Name</label>
                    <input 
                        type="text" 
                        name="course_name" 
                        id="course_name" 
                        class="form-control" 
                        value="{{ old('course_name', $course->course_name) }}" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary mt-2">Update Course</button>
                <a href="{{ route('courses.index') }}" class="btn btn-secondary mt-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
