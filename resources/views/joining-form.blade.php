<!DOCTYPE html>
<html>
<head>
    <title>Student Joining</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4>Student Joining Form</h4>
        </div>

        <div class="card-body">

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('joining_student.store') }}">
                @csrf

                {{-- Student & Father --}}
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Student Name</label>
                        <input type="text"
                               class="form-control @error('student_name') is-invalid @enderror"
                               name="student_name"
                               value="{{ old('student_name') }}"
                               required>
                        @error('student_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col">
                        <label class="form-label">Father Name</label>
                        <input type="text"
                               class="form-control @error('father_name') is-invalid @enderror"
                               name="father_name"
                               value="{{ old('father_name') }}"
                               required>
                        @error('father_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- College --}}
                <div class="mb-3">
                    <label class="form-label">College</label>
                    <select class="form-select @error('college') is-invalid @enderror"
                            name="college" required>
                        <option value="">Select College</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}"
                                {{ old('college') == $college->id ? 'selected' : '' }}>
                                {{ $college->FullName }}
                            </option>
                        @endforeach
                    </select>
                    @error('college')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Course --}}
                <div class="mb-3">
                    <label class="form-label">Technology / Course</label>
                    <select class="form-select @error('technology') is-invalid @enderror"
                            name="technology" required>
                        <option value="">Select Course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ old('technology') == $course->id ? 'selected' : '' }}>
                                {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('technology')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Duration --}}
                <div class="mb-3">
                    <label class="form-label">Duration</label>
                    <select class="form-select @error('duration') is-invalid @enderror"
                            name="duration" required>
                        <option value="">Select Duration</option>
                        @foreach($durations as $duration)
                            <option value="{{ $duration->duration }}"
                                {{ old('duration') == $duration->duration ? 'selected' : '' }}>
                                {{ $duration->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('duration')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Date --}}
                <div class="mb-3">
                    <label class="form-label">Date of Joining</label>
                    <input type="date"
                           class="form-control @error('date_of_joining') is-invalid @enderror"
                           name="date_of_joining"
                           value="{{ old('date_of_joining') }}"
                           required>
                    @error('date_of_joining')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-success px-5">
                        Join Now
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

</body>
</html>
