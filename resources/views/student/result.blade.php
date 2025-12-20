@extends('layouts.public')
@section('content')
<div class="container my-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Test Result</h3>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> {{ $studentTest->student_name }}</p>
            <p><strong>Email:</strong> {{ $studentTest->student_email }}</p>
            <p>
                <strong>Score:</strong> 
                <span class="badge bg-success">{{ $studentTest->score }} / {{ $studentTest->test->questions->count() }}</span>
            </p>
        </div>
        <div class="card-footer text-center">
            <a href="{{ route('student.enter.key') }}" class="btn btn-primary">Back to Tests</a>
        </div>
    </div>
</div>
@endsection
