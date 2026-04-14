<!DOCTYPE html>

<html>
<head>
    <title>Student Leave Application</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    .site-header{
        position:absolute;
        top:20px;
        left:30px;
        z-index:2000;
    }

    .site-header img{
        height:55px;
    }

    .hero-section {
        background:url('{{ asset("images/internship.avif") }}') center/cover no-repeat;
        padding:120px 0 100px;
        position:relative;
        color:#fff;
        z-index:1;
    }

    .hero-section::after {
        content:'';
        position:absolute;
        top:0;
        left:0;
        width:100%;
        height:100%;
        background:rgba(0,0,0,0.55);
        z-index:-1;
    }

    .card{
        border-radius:15px;
        overflow:hidden;
        background: rgba(255,255,255,0.95);
    }
</style>


</head>

<body>

<!-- LOGO -->

<div class="site-header">
    <a href="/">
        <img src="{{ asset('images/front_ss-logo.png') }}" alt="Logo">
    </a>
</div>

<section class="hero-section">
<div class="container mt-5">


<div class="card shadow">
    <div class="card-header text-white text-center" style="background-color:#343957;">
        <h4>Student Leave Application</h4>
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
                    <label>Student No (SNO)</label>
                    <input type="text" name="sno" id="sno" class="form-control" required>
                    <small class="text-danger" id="sno_error"></small>
                </div>

                <div class="col">
                    <label>Student Name</label>
                    <input type="text" name="student_name" id="student_name" class="form-control" readonly required>
                </div>
            </div>

            <div class="row mb-3">
            <div class="col">
                    <label>Contact</label>
                    <input type="text" name="contact" class="form-control" required  placeholder="Enter Contact No.">
                </div>
             <div class="col">
                <label>Mentor</label>
                <input type="text" name="mentor" class="form-control" placeholder="Enter mentor name">
            </div>
        </div>
            <!-- COURSE -->
            <div class="mb-3">
                <label>Course</label>
                <select name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- MENTOR -->
           

            <!-- DAYS -->
            <div class="mb-3">
                <label>Number of Days</label>
                <input type="number" id="days" class="form-control" min="1">
            </div>

            <!-- DATES -->
            <div class="row mb-3">
                <div class="col">
                    <label>From Date</label>
                    <input type="date" name="from_date" class="form-control" required>
                </div>

                <div class="col" id="toDateDiv" style="display:none;">
                    <label>To Date</label>
                    <input type="date" name="to_date" class="form-control" readonly>
                </div>
            </div>

            <!-- REASON -->
            <div class="mb-3">
                <label>Reason</label>
                <textarea name="reason" class="form-control"></textarea>
            </div>

            <!-- EMAIL INFO -->
            <div class="mb-3">
                <label>To</label>
                <input type="email" class="form-control" value="hr.sortiqsolutions@gmail.com" readonly>
            </div>

            <div class="mb-3">
                <label>CC</label>
                <input type="text" class="form-control" value="sortiqsolutions@gmail.com" readonly>
            </div>

            <!-- SUBMIT -->
            <div class="text-center">
                <button class="btn text-white px-5" style="background-color:#343957;">
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
$('#days, input[name="from_date"]').on('input change', function () {

    let days = parseInt($('#days').val());
    let fromDate = $('input[name="from_date"]').val();

    if (!days || !fromDate) return;

    let from = new Date(fromDate);

    if (days > 1) {
        $('#toDateDiv').show();

        let to = new Date(from);
        to.setDate(to.getDate() + days - 1);

        $('input[name="to_date"]').val(to.toISOString().split('T')[0]);

    } else {
        $('#toDateDiv').hide();
        $('input[name="to_date"]').val(fromDate);
    }
});
</script>

</body>
</html>
