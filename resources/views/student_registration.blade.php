<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4>Student Registration</h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('student.register') }}">
                @csrf

                {{-- Student Info --}}
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" name="student_name" required>
                    </div>

                    <div class="col">
                        <label class="form-label">Mobile</label>
                        <input type="text" class="form-control" name="contact" required>
                    </div>
                </div>

                {{-- Gender --}}
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <select class="form-select" name="gender" required>
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                {{-- Father --}}
                <div class="mb-3">
                    <label class="form-label">Father Name</label>
                    <input type="text" class="form-control" name="father_name" required>
                </div>

                {{-- College --}}
                <div class="mb-3">
                    <label class="form-label">College</label>
                    <select class="form-select" name="college_id" required>
                        <option value="">Select College</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">
                                {{ $college->FullName }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Course --}}
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select class="form-select" name="course_id" required>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Start Date --}}
                <div class="mb-3">
                    <label class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" required>
                </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-success px-5">
                        Submit
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>