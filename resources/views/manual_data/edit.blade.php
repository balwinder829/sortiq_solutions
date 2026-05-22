@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Manual Data</h3>

    <form method="POST" action="{{ route('admin.manual_data.update', $manualData->id) }}">
        <div class="row">
        @csrf
        @method('PUT')

        {{-- FULL NAME --}}
        <div class="form-group col-md-6">
            <label>Full Name</label>
            <input type="text" name="student_name"
                class="form-control"
                value="{{ old('student_name', $manualData->student_name) }}"
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
                        {{ old('college_id', $manualData->college_id) == $college->id ? 'selected' : '' }}>
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
                value="{{ old('student_email', $manualData->student_email) }}"
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
                value="{{ old('student_mobile', $manualData->student_mobile) }}"
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
                <option value="male" {{ old('gender', $manualData->gender)=='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender', $manualData->gender)=='female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

       {{-- COURSE TYPE --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Course Type</label>

            <select name="course_type" id="course_type" class="form-control" required>
                <option value="">Select Course Type</option>

                <option value="Degree"
                    {{ old('course_type', $manualData->course_type)=='Degree' ? 'selected' : '' }}>
                    Degree
                </option>

                <option value="Diploma"
                    {{ old('course_type', $manualData->course_type)=='Diploma' ? 'selected' : '' }}>
                    Diploma
                </option>
            </select>
        </div>

        {{-- CLASS --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Class</label>

            <select name="class" id="class" class="form-control" required>
                <option value="">Select Class</option>
            </select>
        </div>
        {{-- SEMESTER --}}
        <div class="form-group col-md-6">
            <label class="fw-bold">Semester</label>
            <select name="semester" class="form-control" required>
                <option value="">Select Semester</option>
                @for ($i = 1; $i <= 8; $i++)
                    <option value="{{ $i }}" {{ old('semester', $manualData->semester) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        {{-- BUTTONS --}}
        <div class="form-group col-md-6 mt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('admin.manual_data.index') }}" class="btn btn-secondary ml-2">Back</a>
        </div>

    </div>
    </form>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {

    const courseType = document.getElementById("course_type");
    const classSelect = document.getElementById("class");

    const selectedClass = "{{ old('class', $manualData->class) }}";

    const degreeCourses = [
        "BCA",
        "MCA",
        "BTech",
        "BSc",
        "BSc IT",
        "BSc CS"
    ];

    const diplomaCourses = [
        "CSE",
        "IT",
        "Civil",
        "Polytechnic",
    ];

    function loadClasses(type) {

        classSelect.innerHTML =
            '<option value="">Select Class</option>';

        let courses = [];

        if (type === "Degree") {
            courses = degreeCourses;
        } else if (type === "Diploma") {
            courses = diplomaCourses;
        }

        courses.forEach(function(course) {

            let option = document.createElement("option");

            option.value = course;
            option.text = course;

            // selected value in edit
            if (course === selectedClass) {
                option.selected = true;
            }

            classSelect.appendChild(option);
        });
    }

    // onchange
    courseType.addEventListener("change", function () {
        loadClasses(this.value);
    });

    // page load
    loadClasses(courseType.value);

});
</script>
@endsection
