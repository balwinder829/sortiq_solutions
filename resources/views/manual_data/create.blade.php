
@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Manual Data</h3>

    <form method="POST" action="{{ route('admin.manual_data.store') }}">
        <div class="row">
        @csrf

        {{-- FULL NAME --}}
        <div class="form-group col-md-6">
            <label>Full Name</label>
            <input type="text" name="student_name"
                class="form-control"
                value="{{ old('student_name') }}"
                placeholder="Full Name" required>

            @error('student_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- COLLEGE --}}
        <div class="form-group col-md-6">
            <label>College</label>
            <select name="college_id"
                    class="form-select @error('college_id') is-invalid @enderror select2">

                <option value="">Select College</option>

                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ old('college_id') == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach
            </select>

            @error('college_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
 

        {{-- EMAIL --}}
        <div class="form-group col-md-6">
            <label>Email</label>
            <input type="email" name="student_email"
                class="form-control"
                value="{{ old('student_email') }}"
                placeholder="Email Address" required>

            @error('student_email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- MOBILE --}}
        <div class="form-group col-md-6">
            <label>Mobile No</label>
            <input type="text"
                name="student_mobile"
                class="form-control"
                value="{{ old('student_mobile') }}"
                placeholder="Mobile No"
                required
                minlength="10"
                maxlength="10"
                pattern="[0-9]{10}">

            @error('student_mobile')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- GENDER --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Gender</label>
            <select name="gender" class="form-control" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        {{-- COURSE TYPE --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Course Type</label>
            <select name="course_type" class="form-control" required>
                <option value="">Select Course Type</option>
                <option value="Degree" {{ old('course_type')=='Degree' ? 'selected' : '' }}>Degree</option>
                <option value="Diploma" {{ old('course_type')=='Diploma' ? 'selected' : '' }}>Diploma</option>
            </select>
        </div>

        {{-- CLASS --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Class</label>
            <select name="class" class="form-control" required>
                <option value="">Select Class</option>
                <option value="BCA" {{ old('class')=='BCA' ? 'selected' : '' }}>BCA</option>
                <option value="MCA" {{ old('class')=='MCA' ? 'selected' : '' }}>MCA</option>
                <option value="BTech" {{ old('class')=='BTech' ? 'selected' : '' }}>BTech</option>
                <option value="BSc" {{ old('class')=='BSc' ? 'selected' : '' }}>BSc</option>
                <option value="BSc IT" {{ old('class')=='BSc IT' ? 'selected' : '' }}>BSc IT</option>
                <option value="BSc CS" {{ old('class')=='BSc CS' ? 'selected' : '' }}>BSc CS</option>
            </select>
        </div>

        {{-- SEMESTER --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Semester</label>
            <select name="semester" class="form-control" required>
                <option value="">Select Semester</option>
                @for ($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- BUTTONS --}}
        <div class="form-group col-md-6 mt-3">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('admin.manual_data.index') }}" class="btn btn-secondary ml-2">Back</a>
        </div>

    </div>
    </form>
</div>
@endsection
