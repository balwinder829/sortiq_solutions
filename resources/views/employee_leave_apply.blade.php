<!DOCTYPE html>
<html>
<head>
    <title>Employee Leave Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
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

                {{-- Honeypot (hidden anti-bot) --}}
                <input type="text" name="website" style="display:none">

                {{-- Employee Info --}}
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

                {{-- Email --}}
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                {{-- Dates --}}
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">From Date</label>
                        <input type="date" class="form-control" name="from_date" required>
                    </div>

                    <div class="col">
                        <label class="form-label">To Date</label>
                        <input type="date" class="form-control" name="to_date" required>
                    </div>
                </div>

                {{-- Reason --}}
                <div class="mb-3">
                    <label class="form-label">Reason</label>
                    <textarea class="form-control" name="reason" rows="3"></textarea>
                </div>

                {{-- Submit --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-success px-5">
                        Apply Leave
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- jQuery (for auto-fill) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$('#emp_code').on('blur', function () {
    let code = $(this).val();

    if (!code) return;

    $('#emp_code_error').text(''); // clear old error

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
</script>

</body>
</html>