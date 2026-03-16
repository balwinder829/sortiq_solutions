@extends('layouts.public')

@section('content')
<div class="container text-center mt-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-danger">Exam Closed</h3>

            <p class="mt-3">
                This exam ended at
                {{ optional($test->exam_end_at)->format('d M Y h:i A') ?? 'Not Scheduled' }}
            </p>

            <p class="text-muted">
                You can no longer attempt this test.
            </p>

            <!-- <a href="{{ route('student.enter.key') }}"
               class="btn btn-secondary mt-3">
                Close
            </a> -->
        </div>
    </div>
</div>
@endsection
