@extends('layouts.public')

@section('content')
<div class="container my-5">

    <!-- <h2 class="mb-2 text-center">{{ $test->title }}</h2> -->
    <h2 class="text-center text-muted mb-4">Please fill your details</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">

            {{-- Errors --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('form.submit') }}">
                @csrf

                <input type="hidden" name="slug" value="{{ $slug }}">

                {{-- Name --}}
                <div class="mb-3">
                    <label class="fw-bold">Full Name</label>
                    <input type="text"
                           name="student_name"
                           class="form-control"
                           value="{{ old('student_name') }}"
                           required>
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="fw-bold">Email</label>
                    <input type="email"
                           name="student_email"
                           class="form-control"
                           value="{{ old('student_email') }}"
                           >
                </div>

                {{-- Mobile --}}
                <div class="mb-3">
                    <label class="fw-bold">Mobile No</label>
                    <input type="text"
                           name="student_mobile"
                           class="form-control"
                           value="{{ old('student_mobile') }}"
                           required
                           maxlength="10"
                           pattern="[0-9]{10}">
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label class="fw-bold">Gender</label>
                    <select name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                {{-- Course --}}
                <div class="mb-3">
                    <label class="fw-bold">Course</label>
                    <select name="course_id" class="form-control" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Class --}}
                <div class="mb-3">
                    <label class="fw-bold">Class</label>
                    <select name="class" class="form-control" required>
                        <option value="">Select Class</option>
                        <option value="BCA" {{ old('class')=='BCA' ? 'selected' : '' }}>BCA</option>
                        <option value="B.Tech" {{ old('class')=='B.Tech' ? 'selected' : '' }}>B.Tech</option>
                        <option value="MCA" {{ old('class')=='MCA' ? 'selected' : '' }}>MCA</option>
                        <!-- <option value="MBA" {{ old('class')=='MBA' ? 'selected' : '' }}>MBA</option> -->
                    </select>
                </div>

                {{-- Semester --}}
                <div class="mb-3">
                    <label class="fw-bold">Semester</label>
                    <select name="semester" class="form-control" required>
                        <option value="">Select Semester</option>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}"
                                {{ old('semester') == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary w-100">
                    Submit
                </button>

            </form>

        </div>
    </div>
</div>
@endsection