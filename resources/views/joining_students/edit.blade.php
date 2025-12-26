@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Joining Student</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('joined_students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Student Name</label>
            <input type="text" name="student_name" value="{{ old('student_name', $student->student_name) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>Father Name</label>
            <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label>College</label>
            <select name="college" class="form-control">
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ $student->college == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Course / Technology</label>
            <select name="technology" class="form-control">
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ $student->technology == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Duration</label>
            <select name="duration" class="form-control">
                @foreach($durations as $duration)
                    <option value="{{ $duration->duration }}"
                        {{ $student->duration == $duration->duration ? 'selected' : '' }}>
                        {{ $duration->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Date of Joining</label>
            <input type="date" name="date_of_joining"
                   value="{{ old('date_of_joining', $student->date_of_joining) }}"
                   class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('joined_students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
