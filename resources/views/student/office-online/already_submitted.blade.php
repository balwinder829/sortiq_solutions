@extends('layouts.public')

@section('content')
<div class="container text-center mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-success">Exam Already Submitted</h3>

            <p class="mt-3">
                You have already submitted this exam successfully.
            </p>

            <p class="text-muted">
                You cannot attempt the exam again.
            </p>

            <a href="{{ route('student.enter.key') }}"
               class="btn btn-secondary mt-3">
                Close
            </a>
        </div>
    </div>
</div>
@endsection
