@extends('layouts.public')

@section('content')
<div class="container my-5 text-center">

    <div class="card shadow-sm p-4 mx-auto" style="max-width: 500px; border-radius: 12px;">
        
        <div class="mb-3">
            <i class="fa fa-exclamation-triangle text-warning" style="font-size: 60px;"></i>
        </div>

        <h3 class="mb-2">Already Submitted</h3>

        <p class="text-muted">
            You have already submitted this form.<br>
            Multiple submissions are not allowed.
        </p>

    </div>

</div>
@endsection