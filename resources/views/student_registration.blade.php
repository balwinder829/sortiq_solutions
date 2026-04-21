<!DOCTYPE html>
<html>
<head>
    <title>Student Registration</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SELECT2 CSS -->
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

        /* SELECT2 MATCH STYLE */
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
            <strong>Student Registration</strong>
        </div>

        <!-- RIGHT EMPTY -->
        <div class="ms-auto"></div>

    </div>

    <div class="card-body">

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.register') }}">
            @csrf

            <!-- Student Info -->
            <div class="row mb-3">
                <div class="col">
                    <input type="text" class="form-control" name="student_name" placeholder="Student Name" required>
                </div>

                <div class="col">
                    <input type="text" class="form-control" name="father_name" placeholder="Father Name" required>
                </div>

                
            </div>
            <div class=" row mb-3">
                <div class="col">
                    <input type="text" class="form-control" name="contact" placeholder="Mobile Number" max="10" required minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number">
                </div>
                <div class="col">
                    <input type="email" class="form-control" name="email" placeholder="Email" required>
                </div>
            </div>

            <!-- Gender -->
            <div class=" row mb-3">
                 <div class="col">
                    <select class="form-select" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <!-- <div class="col">
                    <input type="text" class="form-control" name="college_name_input" placeholder="College Full Name" required>
                </div> -->

                  <div class="col">
                    <select class="form-select select2" name="college_id" required>
                        <option value="">Search College</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->id }}">
                                {{ $college->FullName }}
                            </option>
                        @endforeach
                    </select>
                </div> 
            </div>

            <!-- Father -->
            

            <!-- College (SELECT2 ENABLED) -->
            <!-- <div class="row mb-3">
                <select class="form-select select2" name="college_id" required>
                    <option value="">Search College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}">
                            {{ $college->FullName }}
                        </option>
                    @endforeach
                </select>
            </div> -->

            <!-- Course -->
            <!-- <div class="mb-3">
                <select class="form-select" name="course_id" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div> -->

            <!-- Start Date -->
            <div class="row mb-3">
                <div class="col">
                    <small class="text-muted">Start Date</small>
                    <input type="date" class="form-control" name="start_date" required>
                </div>
                <div class="col">
                </div>
            </div>

            <!-- Submit -->
            <div class="text-center">
                <button type="submit" class="btn primary-btn px-5">
                    Submit
                </button>
            </div>

        </form>
    </div>
</div>

</div>
</section>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- INIT -->
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