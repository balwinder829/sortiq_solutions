@extends('layouts.app')
@section('content')
<div class="container">

    <h3>Sign in with Baileys WhatsApp Account</h3>
    
    <div>
    <form action="{{ route('admin.whatsapp.login.submit') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label for="email">Email</label>
        <input
            type="email"
            name="email"
            id="email"
            value="{{ old('email', session('whatsapp_username')) }}"
            required
            class="form-control"
        >

        @error('email')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3">
        <label for="password">Password</label>
        <input
            type="password"
            name="password"
            id="password"
            value="{{ old('password', session('whatsapp_password')) }}"
            required
            class="form-control"
        >

        @error('password')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary">
        Login
    </button>

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</form>

</div>
</div>

@endsection