@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Hard Data</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.hard_data.store') }}">
        @csrf

        <div class="row">
            <div class="form-group col-md-6 mb-3">
                <label>Full Name <span class="text-danger">*</span></label>
                <input type="text" name="student_name" class="form-control"
                       value="{{ old('student_name') }}" placeholder="Full Name" required>
                @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Mobile No <span class="text-danger">*</span></label>
                <input type="text" name="student_mobile" class="form-control"
                       value="{{ old('student_mobile') }}" placeholder="10-digit mobile number"
                       required minlength="10" maxlength="10" pattern="[0-9]{10}" inputmode="numeric">
                @error('student_mobile') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>College Name</label>
                <input type="text" name="college_name" class="form-control"
                       value="{{ old('college_name') }}" placeholder="Enter college name" required>
                @error('college_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="student_email" class="form-control"
                       value="{{ old('student_email') }}" placeholder="Email Address">
                @error('student_email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Gender</label>
                <select name="gender" class="form-control">
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Course Type</label>
                <select name="course_type" id="course_type" class="form-control">
                    <option value="">Select Course Type</option>
                    <option value="Degree" {{ old('course_type') == 'Degree' ? 'selected' : '' }}>Degree</option>
                    <option value="Diploma" {{ old('course_type') == 'Diploma' ? 'selected' : '' }}>Diploma</option>
                </select>
                @error('course_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Class</label>
                <select name="class" id="class" class="form-control">
                    <option value="">Select Class</option>
                </select>
                @error('class') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 mb-3">
                <label>Semester</label>
                <select name="semester" class="form-control">
                    <option value="">Select Semester</option>
                    @for ($i = 1; $i <= 8; $i++)
                        <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                @error('semester') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="col-12 mt-3">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="{{ route('admin.hard_data.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const courseType = document.getElementById("course_type");
    const classSelect = document.getElementById("class");
    const selectedClass = @json(old('class', ''));

    const degreeCourses = ["BCA", "MCA", "BTech", "BSc", "BSc IT", "BSc CS"];
    const diplomaCourses = ["CSE", "IT", "Civil", "Polytechnic"];

    function loadClasses(type) {
        classSelect.innerHTML = '<option value="">Select Class</option>';
        let courses = [];

        if (type === "Degree") courses = degreeCourses;
        if (type === "Diploma") courses = diplomaCourses;

        courses.forEach(function (course) {
            const option = document.createElement("option");
            option.value = course;
            option.textContent = course;
            if (course === selectedClass) option.selected = true;
            classSelect.appendChild(option);
        });
    }

    courseType.addEventListener("change", function () {
        loadClasses(this.value);
    });

    loadClasses(courseType.value);
});
</script>
@endsection
