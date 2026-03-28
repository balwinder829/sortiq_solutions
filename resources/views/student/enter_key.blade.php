@extends('layouts.public')

@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-center">Enter Your Details to Access Test</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.test.access') }}">
                @csrf
                <input type="hidden" name="slug" value="{{ request('slug') }}">

                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="student_name"
                        class="form-control"
                        value="{{ old('student_name') }}"
                        placeholder="Full Name" 
                        required>
                </div>

                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="student_email"
                        class="form-control"
                        value="{{ old('student_email') }}"
                        placeholder="Email Address" 
                        required>
                </div>

                <div class="mb-3">
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
                </div>

                <div class="mb-3">
                    <label class="fw-bold">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Course Type --}}
                <div class="mb-3">
                    <label class="fw-bold">Course Type</label>
                    <select name="course_type" class="form-control" required>
                        <option value="">Select Course Type</option>
                        <option value="Degree" {{ old('course_type')=='Degree' ? 'selected' : '' }}>Degree</option>
                        <option value="Diploma" {{ old('course_type')=='Diploma' ? 'selected' : '' }}>Diploma</option>
                    </select>
                </div>

                {{-- Class --}}
                <div class="mb-3">
                    <label class="fw-bold">Class</label>
                    <select name="class" class="form-control" required>
                        <option value="">Select Class</option>
                        <option value="BCA" {{ old('class')=='BCA' ? 'selected' : '' }}>BCA</option>
                        <option value="MCA" {{ old('class')=='MCA' ? 'selected' : '' }}>MCA</option>
                        <option value="BTech" {{ old('class')=='BTech' ? 'selected' : '' }}>BTech</option>
                        <option value="BSc" {{ old('class')=='BSc' ? 'selected' : '' }}>BSc</option>
                    </select>
                </div>

                {{-- Semester --}}
                <div class="mb-3">
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

                <button type="submit" class="btn btn-primary w-100">
                    Start Test
                </button>
            </form>

        </div>
    </div>
</div>
@endsection