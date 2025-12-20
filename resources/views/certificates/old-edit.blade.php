@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="container mt-4">
    <h3>Edit Student</h3>

    <form action="{{ route('students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Student Name</label>
                <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $student->student_name) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Father's Name</label>
                <input type="text" name="f_name" class="form-control" value="{{ old('f_name', $student->f_name) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email_id" class="form-control" value="{{ old('email_id', $student->email_id) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label>Contact</label>
                <input type="text" name="contact" class="form-control" value="{{ old('contact', $student->contact) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>College</label>
                <input type="text" name="college_name" class="form-control" value="{{ old('college_name', $student->college_name) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="Active" {{ $student->status == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ $student->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
