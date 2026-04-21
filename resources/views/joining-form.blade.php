<!DOCTYPE html>
<html>
<head>
    <title>Student Joining</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .hero-section {
            padding:40px 0;
            background:#f5f7fb;
        }

        .card{
            border-radius:15px;
            overflow:hidden;
            background: rgba(255,255,255,0.95);
        }

        .card-header{
            background-color:#343957;
            color:#fff;
            border-radius:15px 15px 0 0;
        }

        .primary-btn{
            background-color:#343957;
            color:#fff;
            border:none;
            padding:10px 25px;
        }

        .primary-btn:hover{
            background-color:#2a2f4a;
        }
        .select2-container .select2-selection--single {
            height: 38px;
            border-radius: 6px;
            border: 1px solid #ced4da;
        }

        .select2-selection__rendered {
            line-height: 38px !important;
        }

        .select2-selection__arrow {
            height: 38px !important;
        }
    </style>
</head>

<body>

<section class="hero-section">
<div class="container">

<div class="card shadow">

    <!-- HEADER -->
    <div class="card-header d-flex align-items-center position-relative">

        <!-- LEFT LOGO -->
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/front_ss-logo.png') }}" style="height:35px;">
        </div>

        <!-- CENTER TITLE -->
        <div class="position-absolute w-100 text-center">
            <strong>Student Joining Form</strong>
        </div>

        <!-- RIGHT EMPTY (kept for alignment consistency) -->
        <div class="ms-auto" style="font-size:13px;"></div>

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

            <!-- Student & Father -->
            <div class="row mb-3">
                <div class="col">
                    <input type="text"
                           class="form-control @error('student_name') is-invalid @enderror"
                           name="student_name"
                           value="{{ old('student_name') }}"
                           placeholder="Student Name"
                           required>
                    @error('student_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col">
                    <input type="text"
                           class="form-control @error('father_name') is-invalid @enderror"
                           name="father_name"
                           value="{{ old('father_name') }}"
                           placeholder="Father Name"
                           required>
                    @error('father_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>


            <div class="row mb-3">
                <div class="col">
                    <input type="text"
                           class="form-control @error('contact') is-invalid @enderror"
                           name="contact"
                           value="{{ old('contact') }}"
                           placeholder="Mobile Number"
                            max="10" required minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number">
                    @error('contact')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col">
                    <input type="text"
                           class="form-control @error('email') is-invalid @enderror"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="Email"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <select class="form-select" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="col">
                    <select class="form-select @error('college') is-invalid @enderror select2"
                        name="college" required>
                        <option value="" disabled selected>Select College</option>
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
            </div>

            <!-- College -->
            <!-- <div class="mb-3">
                <select class="form-select @error('college') is-invalid @enderror select2"
                        name="college" required>
                    <option value="" disabled selected>Select College</option>
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
            </div> -->

            <!-- Course -->
            <!-- <div class="mb-3">
                <select class="form-select @error('technology') is-invalid @enderror"
                        name="technology" required>
                    <option value="" disabled selected>Select Course</option>
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
            </div> -->

            <!-- Duration -->
            <!-- <div class="mb-3">
                <select class="form-select @error('duration') is-invalid @enderror"
                        name="duration" required>
                    <option value="" disabled selected>Select Duration</option>
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
            </div> -->

            <!-- Date -->
            <div class="row mb-3">
                <div class="col">
                    <small class="text-muted">Date of Joining</small>
                    <input type="date"
                           class="form-control @error('date_of_joining') is-invalid @enderror"
                           name="date_of_joining"
                           value="{{ old('date_of_joining') }}"
                           required>
                    @error('date_of_joining')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            <div class="col"></div>
            </div>

            <!-- Submit -->
            <div class="text-center">
                <button type="submit" class="btn primary-btn px-5">
                    Join Now
                </button>
            </div>

        </form>
    </div>
</div>

</div>
</section>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
    $('.select2').select2({
        placeholder: "Search College",
        allowClear: true,
        width: '100%'
    });
});
</script>
</body>
</html>