<!DOCTYPE html>
<html>
<head>
    <title>Student Leave Application</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* HERO BACKGROUND */
        .hero-section {
            padding:40px 0;
            background:#f5f7fb; /* clean light background */
        }

        /* CARD */
        .card{
            border-radius:15px;
            overflow:hidden;
            background: rgba(255,255,255,0.95);
        }

        /* HEADER */
        .card-header{
            background-color:#343957;
            color:#fff;
            border-radius:15px 15px 0 0;
        }

        /* BUTTON */
        .primary-btn{
            background-color:#343957;
            color:#fff;
            border:none;
            padding:10px 25px;
        }

        .primary-btn:hover{
            background-color:#2a2f4a;
        }
    </style>

</head>

<body>

<section class="hero-section">
<div class="container">

<div class="card shadow">

    <!-- HEADER WITH LOGO + TO/CC -->
   <div class="card-header d-flex align-items-center position-relative">

    <!-- LEFT (LOGO) -->
    <div class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/front_ss-logo.png') }}" style="height:35px;">
    </div>

    <!-- CENTER TITLE -->
    <div class="position-absolute w-100 text-center">
        <strong>Student Leave Application</strong>
    </div>

    <!-- RIGHT -->
    <div class="ms-auto" style="font-size:13px; text-align:right;">
        <div><strong>To:</strong> hr.sortiqsolutions@gmail.com</div>
        <div><strong>CC:</strong> sortiqsolutions@gmail.com</div>
    </div>

</div>

    <div class="card-body">

        <!-- SUCCESS -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- ERROR -->
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- VALIDATION -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('student.leave.store') }}">
            @csrf

            <input type="text" name="website" style="display:none">

            <!-- SNO + NAME -->
            <div class="row mb-3">
                <div class="col">
                    <input type="text" name="sno" id="sno" class="form-control" placeholder="Student No (SNO)" required>
                    <small class="text-danger" id="sno_error"></small>
                </div>

                <div class="col">
                    <input type="text" name="student_name" id="student_name" class="form-control" placeholder="Student Name" readonly required>
                </div>
            </div>

            <!-- CONTACT + MENTOR -->
            <div class="row mb-3">
                <div class="col">
                    <input type="text" name="contact" class="form-control" placeholder="Contact Number" required>
                </div>
                <div class="col">
                    <input type="text" name="mentor" class="form-control" placeholder="Mentor Name">
                </div>
            </div>

            <!-- COURSE -->
            <div class="mb-3">
                <select name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- DAYS -->
            <div class="mb-3">
                <input type="number" id="days" class="form-control" placeholder="Number of Days" min="1">
            </div>

            <!-- DATES -->
            <div class="row mb-3">
                <div class="col">
                    <input type="date" name="from_date" class="form-control" required>
                </div>

                <div class="col" id="toDateDiv" style="display:none;">
                    <input type="date" name="to_date" class="form-control" readonly>
                </div>
            </div>

            <!-- REASON -->
            <div class="mb-3">
                <textarea name="reason" class="form-control" placeholder="Reason"></textarea>
            </div>

            <!-- SUBMIT -->
            <div class="text-center">
                <button class="btn primary-btn px-5">
                    Apply Leave
                </button>
            </div>

        </form>

    </div>
</div>

</div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let validStudent = false;

// FETCH STUDENT
$('#sno').on('blur', function () {

    let sno = $(this).val();

    if (!sno) return;

    $('#sno_error').text('');

    $.get("{{ route('student.find') }}", { sno: sno }, function (data) {

        if (data) {
            $('#student_name').val(data.student_name);
            validStudent = true;
        } else {
            $('#student_name').val('');
            $('#sno_error').text('Student not found');
            validStudent = false;
        }

    });
});

// PREVENT SUBMIT IF INVALID
$('form').on('submit', function (e) {
    if (!validStudent) {
        e.preventDefault();
        $('#sno_error').text('Please enter valid student');
    }
});

// DAYS LOGIC
$('#days').on('input', function () {

    let days = parseInt($('#days').val());
    let fromDate = $('input[name="from_date"]').val();

    if (!days) return;

    if (days > 1) {
        $('#toDateDiv').show();

        if (fromDate) {
            let from = new Date(fromDate);
            let to = new Date(from);

            to.setDate(to.getDate() + days - 1);

            $('input[name="to_date"]').val(to.toISOString().split('T')[0]);
        }

    } else {
        $('#toDateDiv').hide();
        $('input[name="to_date"]').val('');
    }

});
$('input[name="from_date"]').on('change', function () {

    let days = parseInt($('#days').val());

    if (days > 1) {

        let fromDate = $(this).val();
        if (!fromDate) return;

        let from = new Date(fromDate);
        let to = new Date(from);

        to.setDate(to.getDate() + days - 1);

        $('input[name="to_date"]').val(to.toISOString().split('T')[0]);
    }

});
</script>

</body>
</html>