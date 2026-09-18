@extends('layouts.public')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm text-center">
        
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0" style="text-align: center; width: 100%;">Thank You!</h3>
        </div>

        <div class="card-body">
            <h4 class="mb-3">Test Submitted Successfully</h4>

            <p>
                Thank you for completing the test.
            </p>

            <p>Your responses have been recorded successfully.</p>
        </div>

        <!-- <div class="card-footer">
            <a href="{{ route('student.enter.key') }}" class="btn btn-primary">
                Back to Tests
            </a>
        </div>
 -->
    </div>
</div>
<script>
    // Add multiple history entries so Back button stays on this page
    history.pushState(null, '', location.href);
    history.pushState(null, '', location.href);
    history.pushState(null, '', location.href);
    history.pushState(null, '', location.href);
    history.pushState(null, '', location.href);

    window.addEventListener('popstate', function () {
        // Immediately restore the history state
        history.pushState(null, '', location.href);
    });
</script>
@endsection