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
        <div class="row">
        @csrf
        @method('PUT')

         {{-- Trainer Name --}}
        <div class="form-group col-md-6">
            <label>UserName</label>
            <input type="text" 
                   name="username" 
                   class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $trainer->username ?? '') }}"
                   >
            @error('username')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        {{-- Trainer Name --}}
        <div class="form-group col-md-6">
            <label>Full Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $trainer->name ?? '') }}"
                   required>
            @error('name')
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
                   placeholder="Leave blank to keep current password">
            @error('password')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Password --}}
            <div class="form-group col-md-6">
                <label>Existing Password</label>

                <div class="input-group">
                    <input type="password"
                           id="trainer_pswd"
                           name="trainer_pswd"
                           class="form-control @error('trainer_pswd') is-invalid @enderror"
                           value="{{ old('trainer_pswd', $trainer->trainer_pswd) }}"
                           readonly>

                    <span class="input-group-text" style="cursor:pointer"
                          onclick="toggleProbation()">
                        👁
                    </span>
                </div>
            </div>
        {{-- Gender --}}
        <div class="form-group col-md-6">
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
        <div class="form-group col-md-6">
            <label>Phone</label>
            <input type="text" 
                   name="phone" 
                   class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $trainer->phone ?? '') }}"
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
        <div class="form-group col-md-6">
            <label>Email</label>
            <input type="email" 
                    name="email"
                   class="form-control"
                   value="{{ old('email', $trainer->email ?? '') }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        
        {{-- Status --}}
        <div class="form-group col-md-6">
            <label>Status</label>
            <select name="status" class="form-control" required>
                <option value="">Select Status</option>
                <option value="active"   {{ $trainer->status == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $trainer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
<!-- 
        {{-- Technology (MULTIPLE) --}}
        <div class="form-group col-md-6">
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
        </div> -->

        {{-- Technology --}}
<div class="form-group col-md-6">
    <label>Technology</label>

    @php
        $selectedTech = old(
            'technology',
            $trainer->technology
                ? explode(',', $trainer->technology)
                : []
        );
    @endphp

    <select name="technology[]"
            class="form-control technology select2 @error('technology') is-invalid @enderror"
            id="txttechnology"
            multiple
            data-placeholder="Select technology"
            required>

        @foreach($courses as $course)
            <option value="{{ $course->id }}"
                {{ in_array($course->id, $selectedTech) ? 'selected' : '' }}>
                {{ $course->course_name }}
            </option>
        @endforeach

    </select>

    <small class="text-muted">
        Hold Ctrl (Windows) or Cmd (Mac) to select multiple Technologies.
    </small>

    @error('technology')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>


        <div class="form-group col-md-12">
            <button type="submit" class="btn btn-primary mt-3">Update</button>
             <a href="{{ route('trainers.index') }}" class="btn btn-secondary mt-3 ml-2">Back</a>
        </div>
        </div>
    </form>
</div>
<script>
    function toggleProbation() {
        const input = document.getElementById('trainer_pswd');
        input.type = input.type === 'password' ? 'text' : 'password';
    } 
</script>
@endsection
