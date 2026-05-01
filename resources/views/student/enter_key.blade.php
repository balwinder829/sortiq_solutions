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

            {{-- Name --}}
            <div class="mb-3">
                <label class="fw-bold">Full Name</label>
                <input type="text" name="student_name" class="form-control"
                    value="{{ old('student_name') }}" required>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="fw-bold">Email</label>
                <input type="email" name="student_email" class="form-control"
                    value="{{ old('student_email') }}" required>
            </div>

            {{-- Mobile --}}
            <div class="mb-3">
                <label class="fw-bold">Mobile</label>
                <input type="text" name="student_mobile" class="form-control"
                    value="{{ old('student_mobile') }}"
                    required pattern="[0-9]{10}" maxlength="10">
            </div>

            {{-- Gender --}}
            <div class="mb-3">
                <label class="fw-bold">Gender</label>
                <select name="gender" class="form-control" required>
                    <option value="">Select</option>
                    <option value="male" {{ old('gender')=='male'?'selected':'' }}>Male</option>
                    <option value="female" {{ old('gender')=='female'?'selected':'' }}>Female</option>
                </select>
            </div>

            {{-- Course Type --}}
            <div class="mb-3">
                <label class="fw-bold">Course Type</label>
                <select name="course_type" id="courseType" class="form-control">
                    <option value="Degree" {{ old('course_type','Degree')=='Degree'?'selected':'' }}>Degree</option>
                    <option value="Diploma" {{ old('course_type')=='Diploma'?'selected':'' }}>Diploma</option>
                </select>
            </div>

            {{-- Course --}}
            <div class="mb-3">
                <label class="fw-bold">Course</label>
                <select name="class" id="courseField" class="form-control"></select>
            </div>

            {{-- Branch --}}
            <div class="mb-3">
                <label class="fw-bold">Branch</label>
                <select name="student_branch" id="branchField" class="form-control"></select>
            </div>

            {{-- Semester --}}
            <div class="mb-3">
                <label class="fw-bold">Semester</label>
                <select name="semester" id="semesterField" class="form-control"></select>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Start Test
            </button>
        </form>

    </div>
</div>
</div>

<script>
const courseData = {
    Degree: {
        courses: {
            "BCA": {
                branches: ["Software Development", "Web Development", "Data Science"],
                semesters: 6
            },
            "MCA": {
                branches: ["Software Development", "Web Development", "Data Science"],
                semesters: 4
            },
            "BSc IT": {
                branches: ["Software Development", "Web Development", "Data Science"],
                semesters: 6
            },
            "B.Tech": {
                branches: ["Computer Science", "IT", "Mechanical", "Civil", "Electrical"],
                semesters: 8
            }
        }
    },
    Diploma: {
        courses: {
            "Polytechnic": {
                branches: ["Computer Engineering", "IT", "Mechanical", "Civil", "Electrical"],
                semesters: 6
            }
        }
    }
};

const courseType = document.getElementById('courseType');
const courseField = document.getElementById('courseField');
const branchField = document.getElementById('branchField');
const semesterField = document.getElementById('semesterField');

function populateCourses(selectedCourse = null) {
    let type = courseType.value;
    courseField.innerHTML = '<option value="">Select Course</option>';

    Object.keys(courseData[type].courses).forEach(course => {
        let selected = selectedCourse === course ? 'selected' : '';
        courseField.innerHTML += `<option value="${course}" ${selected}>${course}</option>`;
    });

    branchField.innerHTML = '';
    semesterField.innerHTML = '';
}

function populateBranchesAndSemesters(selectedBranch = null, selectedSemester = null) {
    let type = courseType.value;
    let course = courseField.value;

    if (!course) return;

    let data = courseData[type].courses[course];

    // Branch
    branchField.innerHTML = '<option value="">Select Branch</option>';
    data.branches.forEach(branch => {
        let selected = selectedBranch === branch ? 'selected' : '';
        branchField.innerHTML += `<option value="${branch}" ${selected}>${branch}</option>`;
    });

    // Semester
    semesterField.innerHTML = '<option value="">Select Semester</option>';
    for (let i = 1; i <= data.semesters; i++) {
        let selected = selectedSemester == i ? 'selected' : '';
        semesterField.innerHTML += `<option value="${i}" ${selected}>${i}</option>`;
    }
}

courseType.addEventListener('change', () => populateCourses());
courseField.addEventListener('change', () => populateBranchesAndSemesters());

// 🔥 Keep old values after validation error
window.onload = function () {
    let oldCourse = "{{ old('course') }}";
    let oldBranch = "{{ old('student_branch') }}";
    let oldSemester = "{{ old('semester') }}";

    populateCourses(oldCourse);

    if (oldCourse) {
        courseField.value = oldCourse;
        populateBranchesAndSemesters(oldBranch, oldSemester);
    }
};
</script>

@endsection
