@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add User</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Name" 
                   required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

            <div class="form-group col-md-6">
                <label>User Name</label>
                <input type="text" name="username" class="form-control" required value="{{ old('username') }}" placeholder="UserName">
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

             {{-- Phone --}}
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input type="text" 
                   name="phone" 
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone') }}"
                   placeholder="Phone" 
                   required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email --}}
        <div class="form-group col-md-6">
            <label>Email</label>
            <input type="email" 
                   name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email') }}"
                   placeholder="Email" 
                   required>
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
            <div class="form-group col-md-6">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    <option value="">Select Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
@endsection
