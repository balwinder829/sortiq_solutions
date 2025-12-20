@extends('layouts.app')
@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-center">Enter Your Details to Access Test</h2>
<form method="POST" action="{{ route('student.test.access') }}">
    @csrf
    <input type="hidden" name="test_id" value="{{ request('test_id') }}">

    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="student_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="student_email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>College Name</label>
        <input type="text" name="college_name" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Start Test</button>
</form>
</div>
@endsection
