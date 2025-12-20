@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit User</h4>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="{{ old('username', $user->username) }}">
                @error('username')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                @error('name')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="text" name="email" class="form-control" value="{{ old('email', $user->email) }}" readonly disabled>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" readonly disabled>
                @error('phone')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

           
            <div class="form-group col-md-6">
    <label>Role</label>

    <select name="role"
            class="form-control"
            {{ auth()->user()->role == 1 && $user->role == 1 ? 'disabled' : 'required' }}>
        <option value="">Select Role</option>
        @foreach($roles as $role)
            <option value="{{ $role->id }}"
                {{ old('role', $user->role) == $role->id ? 'selected' : '' }}>
                {{ $role->name }}
            </option>
        @endforeach
    </select>

    {{-- Preserve role when select is disabled --}}
    @if(auth()->user()->role == 1 && $user->role == 1)
        <input type="hidden" name="role" value="{{ $user->role }}">
    @endif

    @error('role')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>


            <div class="form-group col-md-6">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="active"   {{ $user->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $user->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

             <div class="form-group col-md-6">
                <label>Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
                @error('password')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>


        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
