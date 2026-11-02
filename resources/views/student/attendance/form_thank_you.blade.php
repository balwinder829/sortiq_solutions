@extends('layouts.public')

@section('content')
<div class="container my-5 text-center">
    <div class="card shadow-sm p-4 mx-auto"
         style="max-width: 550px; border-radius: 12px;">

        <div class="mb-3">
            <i class="fa fa-check-circle text-success"
               style="font-size: 60px;"></i>
        </div>

        <h3 class="mb-2">Thank You!</h3>

        <p class="text-muted mb-4">
            Your attendance has been submitted successfully.
            Click below to continue to your test.
        </p>

        <a href="{{ route('form.choose') }}"
           class="btn btn-primary btn-lg w-100">
            Click to Test
        </a>
    </div>
</div>
@endsection