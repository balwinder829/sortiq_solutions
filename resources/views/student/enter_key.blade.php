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
<!-- <style>
    @media screen and (max-width: 767px) {
        #testAccessForm input,
        #testAccessForm select,
        #testAccessForm textarea {
            font-size: 16px !important;
        }
    }
</style> -->
<script>
const courseData = {
    Degree: {
        courses: {
            "BCA": {
                branches: ["Computer Science", "IT", "Mechanical", "Civil", "Electrical"],
                semesters: 8
            },
            "MCA": {
                branches: ["Computer Science", "IT", "Mechanical", "Civil", "Electrical"],
                semesters: 8
            },
            "BSc IT": {
                branches: ["Computer Science", "IT", "Mechanical", "Civil", "Electrical"],
                semesters: 8
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
                semesters: 8
            }
        }
    }
};


const courseType = document.getElementById('courseType');
const branchField = document.getElementById('branchField');
const semesterField = document.getElementById('semesterField');


// Course Type Change
courseType.addEventListener('change', function () {

    let type = courseType.value;

    branchField.innerHTML = '<option value="">Select Branch</option>';
    semesterField.innerHTML = '<option value="">Select Semester</option>';

    if (!type) {
        return;
    }

    // Get first course of selected Course Type
    let courses = courseData[type].courses;
    let firstCourse = Object.keys(courses)[0];

    let data = courses[firstCourse];


    // Branch
    data.branches.forEach(function (branch) {

        branchField.innerHTML +=
            '<option value="' + branch + '">' +
            branch +
            '</option>';

    });


    // Semester
    for (let i = 1; i <= data.semesters; i++) {

        semesterField.innerHTML +=
            '<option value="' + i + '">' +
            i +
            '</option>';

    }

});


// Keep old values after validation error
window.onload = function () {

    let oldType = "{{ old('course_type', 'Degree') }}";
    let oldBranch = "{{ old('student_branch') }}";
    let oldSemester = "{{ old('semester') }}";

    courseType.value = oldType;

    courseType.dispatchEvent(new Event('change'));

    branchField.value = oldBranch;
    semesterField.value = oldSemester;

};
 
</script>
<script>
document.querySelector('form').addEventListener('submit', function () {

    Object.keys(localStorage).forEach(function (key) {

        if (
            key.startsWith('exam_submitted_test_') ||
            key.startsWith('exam_answers_') ||
            key.startsWith('exam_pending_')
        ) {
            localStorage.removeItem(key);
        }

    });

});
</script>
@endsection
