@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Verify OTP to Access Data</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
     {{-- Show email mismatch error --}}
        @error('email')
            <div class="alert alert-danger">{{ $message }}</div>
        @enderror
    {{-- Send OTP --}}
    <form action="{{ route('enquiry.otp.send') }}" method="POST" class="mb-3">
        @csrf
        <label>Enter Email to Receive OTP</label>
        <input type="email" name="email" class="form-control mb-2" required>
        <button class="btn btn-primary">Send OTP</button>
    </form>

    {{-- Verify OTP --}}
    <form action="{{ route('enquiry.otp.verify') }}" method="POST">
        @csrf
        <label>Enter OTP</label>
        <input type="text" name="otp" class="form-control mb-2" required>
        <button class="btn btn-success">Verify & Continue</button>
    </form>

</div>
@endsection
