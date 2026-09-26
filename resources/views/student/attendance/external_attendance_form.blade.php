@extends('layouts.public')

@section('content')

<div class="container my-5">
    <h2 class="mb-4 text-center">Please fill your details</h2>

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

            <form method="POST" action="{{ route('form.submit') }}">
                @csrf

                <input type="hidden" name="slug" value="{{ $slug }}">

                {{-- Name --}}
                <div class="mb-3">
                    <label class="fw-bold">Full Name</label>
                    <input
                        type="text"
                        name="student_name"
                        class="form-control"
                        value="{{ old('student_name') }}"
                        required
                    >
                    @error('student_name')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-3">
                    <label class="fw-bold">Email</label>
                    <input
                        type="email"
                        name="student_email"
                        class="form-control"
                        value="{{ old('student_email') }}"
                    >
                </div>

                {{-- Mobile --}}
                <div class="mb-3">
                    <label class="fw-bold">Mobile No</label>
                    <input
                        type="text"
                        name="student_mobile"
                        class="form-control"
                        value="{{ old('student_mobile') }}"
                        required
                        pattern="[0-9]{10}"
                        maxlength="10"
                    >
                    @error('student_mobile')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label class="fw-bold">Gender</label>
                    <select name="gender" class="form-control">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>
                            Male
                        </option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                            Female
                        </option>
                    </select>
                </div>

                {{-- Course Type --}}
                <div class="mb-3">
                    <label class="fw-bold">Course Type</label>
                    <select name="course_type" id="courseType" class="form-control">
                        <option value="">Select Course Type</option>
                        <option value="Degree" {{ old('course_type') == 'Degree' ? 'selected' : '' }}>
                            Degree
                        </option>
                        <option value="Diploma" {{ old('course_type') == 'Diploma' ? 'selected' : '' }}>
                            Diploma
                        </option>
                    </select>
                </div>

                {{-- Branch --}}
                <div class="mb-3">
                    <label class="fw-bold">Branch</label>
                    <select name="student_branch" id="branchField" class="form-control">
                        <option value="">Select Branch</option>
                    </select>
                </div>

                {{-- Semester --}}
                <div class="mb-3">
                    <label class="fw-bold">Semester</label>
                    <select name="semester" id="semesterField" class="form-control">
                        <option value="">Select Semester</option>
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

<style>
@media (max-width: 867px) {
    input[type="text"],
    input[type="email"],
    select,
    textarea {
        font-size: 16px !important;
    }
}
</style>

<script>
const courseData = {
    Degree: {
        branches: [
            "Computer Science",
            "IT",
            "Mechanical",
            "Civil",
            "Electrical"
        ],
        semesters: 8
    },
    Diploma: {
        branches: [
            "Computer Engineering",
            "IT",
            "Mechanical",
            "Civil",
            "Electrical"
        ],
        semesters: 8
    }
};

const courseType = document.getElementById('courseType');
const branchField = document.getElementById('branchField');
const semesterField = document.getElementById('semesterField');

const oldBranch = @json(old('student_branch'));
const oldSemester = @json(old('semester'));

function updateCourseFields() {
    const type = courseType.value;

    branchField.innerHTML = '<option value="">Select Branch</option>';
    semesterField.innerHTML = '<option value="">Select Semester</option>';

    if (!type || !courseData[type]) {
        return;
    }

    const data = courseData[type];

    data.branches.forEach(function (branch) {
        const option = document.createElement('option');
        option.value = branch;
        option.textContent = branch;
        branchField.appendChild(option);
    });

    for (let i = 1; i <= data.semesters; i++) {
        const option = document.createElement('option');
        option.value = i;
        option.textContent = i;
        semesterField.appendChild(option);
    }

    if (oldBranch) {
        branchField.value = oldBranch;
    }

    if (oldSemester) {
        semesterField.value = oldSemester;
    }
}

courseType.addEventListener('change', updateCourseFields);

document.addEventListener('DOMContentLoaded', function () {
    updateCourseFields();
});
</script>

@endsection