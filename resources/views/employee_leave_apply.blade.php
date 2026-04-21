<!DOCTYPE html>
<html>
<head>
    <title>Employee Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

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
    </style>
</head>

<body>

<section class="hero-section">
<div class="container">

<div class="card shadow">

    <!-- HEADER UPDATED -->
    <div class="card-header d-flex align-items-center position-relative">

        <!-- LEFT LOGO -->
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('images/front_ss-logo.png') }}" style="height:35px;">
        </div>

        <!-- CENTER TITLE -->
        <div class="position-absolute w-100 text-center">
            <strong>Employee Leave Application</strong>
        </div>

        <!-- RIGHT -->
        <div class="ms-auto" style="font-size:13px; text-align:right;">
            <div><strong>To:</strong> hr.sortiqsolutions@gmail.com</div>
            <div><strong>CC:</strong> sortiqsolutions@gmail.com</div>
        </div>

    </div>

    <div class="card-body">

        {{-- Success --}}
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Error --}}
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

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

        <form method="POST" action="{{ route('employee.leave.store') }}">
            @csrf

            <input type="text" name="website" style="display:none">

            <div class="row mb-3">
                <div class="col">
                    <input type="text" class="form-control" name="emp_code" id="emp_code" placeholder="Employee Code" required>
                    <small class="text-danger" id="emp_code_error"></small>
                </div>

                <div class="col">
                    <input type="text" class="form-control" name="emp_name" id="emp_name" placeholder="Employee Name" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <input type="text" class="form-control" name="contact" placeholder="Contact Number" required>
                </div>

                <div class="col">
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>
            </div>

            <div class="mb-3">
                <input type="number" id="days" class="form-control" placeholder="Number of Days" min="1">
            </div>

            <div class="row mb-3">
                <div class="col">
                    <input type="date" class="form-control" name="from_date" required>
                </div>

                <div class="col" id="toDateDiv" style="display:none;">
                    <input type="date" class="form-control" name="to_date" readonly>
                </div>
            </div>

            <div class="mb-3">
                <textarea class="form-control" name="reason" placeholder="Reason" rows="3"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn primary-btn px-5">
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
$('#emp_code').on('blur', function () {
    let code = $(this).val();

    if (!code) return;

    $('#emp_code_error').text('');

    $.get("{{ route('employee.find') }}", { emp_code: code }, function (data) {

        if (data) {
            $('#emp_name').val(data.emp_name);
        } else {
            $('#emp_name').val('');
            $('#emp_code_error').text('Employee not found');
        }

    }).fail(function () {
        $('#emp_code_error').text('Something went wrong');
    });
});

// EXISTING LOGIC KEPT SAME
// $('#days, input[name="from_date"]').on('input change', function () {

//     let days = parseInt($('#days').val());
//     let fromDate = $('input[name="from_date"]').val();

//     if (!days || !fromDate) return;

//     let from = new Date(fromDate);

//     if (days > 1) {
//         $('#toDateDiv').show();

//         let to = new Date(from);
//         to.setDate(to.getDate() + days - 1);

//         $('input[name="to_date"]').val(to.toISOString().split('T')[0]);

//     } else {
//         $('#toDateDiv').hide();
//         $('input[name="to_date"]').val(fromDate);
//     }
// });
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