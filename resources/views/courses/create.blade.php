@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><h4>Add Technology</h4></div>
        <div class="card-body">
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Technology Name</label>
                    <input type="text" name="course_name" class="form-control" value="{{ old('course_name') }}" required>
                    @error('course_name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                <button class="btn btn-primary mt-2" type="submit">Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
