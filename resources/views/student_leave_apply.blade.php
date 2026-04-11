<!DOCTYPE html>
<html>
<head>
    <title>Student Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4>Student Leave Application</h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('student.leave.store') }}">
                @csrf

                <input type="text" name="website" style="display:none">

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
                        <label>From Date</label>
                        <input type="date" name="from_date" class="form-control" required>
                    </div>

                    <div class="col">
                        <label>To Date</label>
                        <input type="date" name="to_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label>Reason</label>
                    <textarea name="reason" class="form-control"></textarea>
                </div>

                <div class="text-center">
                    <button class="btn btn-success px-5">Apply Leave</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let validStudent = false;

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

$('form').on('submit', function (e) {
    if (!validStudent) {
        e.preventDefault();
        $('#sno_error').text('Please enter valid student');
    }
});
</script>

</body>
</html>