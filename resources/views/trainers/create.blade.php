@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Mentor</h3>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form method="POST" action="{{ route('trainers.store') }}">
        <div class="row">
        @csrf

        {{-- Trainer Name --}}
        <div class="form-group col-md-6">
            <label>Full Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Full Name" 
                   required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- User Name --}}
        <div class="form-group col-md-6">
            <label>User Name</label>
            <input type="text" 
                   name="username" 
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username') }}"
                   placeholder="Name" 
                   required>
            @error('username')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Password --}}
        <div class="form-group col-md-6">
            <label>Password</label>
            <input type="text" 
                   name="password" 
                   class="form-control @error('password') is-invalid @enderror"
                   value="{{ old('password') }}"
                   placeholder="Password" 
                   required>
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        {{-- Gender --}}
        <div class="form-group col-md-6">
            <label>Gender</label>
            <select name="gender" 
                    class="form-control @error('gender') is-invalid @enderror" 
                    required>

                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>--Select--</option>
                <option value="male"   {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
                <small class="text-danger">{{ $message }}</small>
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

        {{-- Technology --}}
        <div class="form-group col-md-6">
            <label>Technology</label>
            <select name="technology[]" 
                class="form-control technology" 
                id="txttechnology"
                multiple
                required>
                <option value="" disabled {{ old('technology') ? '' : 'selected' }}>Choose one</option>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ old('technology') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
            @error('technology')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="">Select Status</option>
                <option value="active"   {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

         <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary mt-2">Save</button>
         <a href="{{ route('trainers.index') }}" class="btn btn-secondary mt-1 ml-2">Back</a>
    </div>
</div>
    </form>
</div>
@endsection
