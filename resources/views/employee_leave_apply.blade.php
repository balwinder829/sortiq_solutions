<!DOCTYPE html>
<html>
<head>
    <title>Employee Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* LOGO HEADER */
        .site-header{
            position:absolute;
            top:20px;
            left:30px;
            z-index:2000;
        }
        .site-header img{
            height:55px;
        }

        /* HERO BACKGROUND */
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

        /* CARD */
        .card{
            border-radius:15px;
            overflow:hidden;
            background: rgba(255,255,255,0.95);
        }

        /* BRAND COLORS */
        .primary-bg{
            background-color:#343957 !important;
            color:#fff !important;
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

<!-- HEADER -->
<div class="site-header">
  <a href="/">
    <img src="{{ asset('images/front_ss-logo.png') }}" alt="Sortiq">
  </a>
</div>

<!-- HERO -->
<section class="hero-section">
<div class="container mt-5">

    <div class="card shadow">
        <div class="card-header primary-bg text-center">
            <h4>Employee Leave Application</h4>
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
                        <label class="form-label">Employee Code</label>
                        <input type="text" class="form-control" name="emp_code" id="emp_code" required>
                        <small class="text-danger" id="emp_code_error"></small>
                    </div>

                    <div class="col">
                        <label class="form-label">Employee Name</label>
                        <input type="text" class="form-control" name="emp_name" id="emp_name" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                    <label class="form-label">Contact</label>
                    <input type="text" class="form-control" name="contact" required>
                </div>
                <div class="col">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Number of Days</label>
                    <input type="number" id="days" class="form-control" min="1">
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="from_date" required>
                    </div>

                    <div class="col" id="toDateDiv" style="display:none;">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="to_date" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" rows="3"></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">To</label>
                    <input type="email" class="form-control" value="hr.sortiqsolutions@gmail.com" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label">CC</label>
                    <input type="text" class="form-control" value="sortiqsolutions@gmail.com" readonly>
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
// Days logic
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