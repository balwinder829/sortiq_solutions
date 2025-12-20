@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">My Profile</h3>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Error Message --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        {{-- Profile Card --}}
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    @if($user->profile_picture)
                        <img src="{{ asset($user->profile_picture) }}" 
                             class="rounded-circle mb-3" 
                             width="120" height="120" 
                             style="object-fit: cover;">
                    @else
                        <img src="{{ asset('images/default-avatar.png') }}" 
                             class="rounded-circle mb-3" 
                             width="120" height="120">
                    @endif
                    <p class="text-muted"><strong>{{ ucwords(strtolower($user->name)) }}</strong></p>
                </div>
            </div>
        </div>

        {{-- Profile Details + Actions --}}
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">Profile Details</div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ ucwords(strtolower($user->name)) }}</p>
                    <p><strong>Role:</strong> {{ $user->roles->name ?? 'N/A' }}</p>
                </div>
            </div>

            <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>
             @if(auth()->user()->role === '1')
            <a href="{{ route('profile.edit') }}#password-section" class="btn btn-warning">Change Password</a>
            @endif
        </div>
    </div>
</div>
@endsection
