@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Mentor</h3>
    {{-- 🔴 SHOW ALL VALIDATION ERRORS ON TOP --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form method="POST" action="{{ route('trainers.update', $trainer->id) }}">
        @csrf
        @method('PUT')

        {{-- Trainer Name --}}
        <div class="form-group">
            <label>Full Name</label>
            <input type="text" 
                   name="trainer_name" 
                   class="form-control @error('trainer_name') is-invalid @enderror"
                   value="{{ old('trainer_name', $trainer->trainer_name ?? $trainer->user->name ?? '') }}"
                   required>
            @error('trainer_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Gender --}}
        <div class="form-group">
            <label>Gender</label>
            <select name="gender" 
                    class="form-control @error('gender') is-invalid @enderror" 
                    required>
                <option value="">--Select--</option>
                <option value="male"   {{ old('gender', $trainer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $trainer->gender) == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="form-group">
            <label>Phone</label>
            <input type="text" 
                   name="phone" 
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $trainer->phone ?? $trainer->user->phone ?? '') }}"
                   required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email (READ ONLY) --}}
        <div class="form-group">
            <label>Email</label>
            <input type="email" 
                    name="email"
                   class="form-control"
                   value="{{ $trainer->user->email ?? $trainer->email }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Technology (MULTIPLE) --}}
        <div class="form-group">
            <label>Technology</label>

            @php
                // Convert "1,3,5" → [1,3,5]
                $selectedTech = $trainer->technology ? explode(',', $trainer->technology) : [];
            @endphp

            <select name="technology[]" 
                    class="form-control @error('technology') is-invalid @enderror" 
                    multiple
                    required>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ in_array($course->id, $selectedTech) ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach

            </select>

            @error('technology')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

 

        <button type="submit" class="btn btn-primary mt-3">Update</button>

    </form>
</div>
@endsection
