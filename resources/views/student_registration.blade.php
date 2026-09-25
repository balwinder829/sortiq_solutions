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

        <!-- SHOW ERRORS -->
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form method="POST" action="{{ route('student.register') }}">
    @csrf

    <!-- Student Info -->
    <div class="row mb-3">
        <div class="col">
            <input type="text" class="form-control" name="student_name"
                   value="{{ old('student_name') }}"
                   placeholder="Full Name" required>
        </div>

        <div class="col">
            <input type="text" class="form-control" name="father_name"
                   value="{{ old('father_name') }}"
                   placeholder="Father Name" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <input type="text" class="form-control" name="contact"
                   value="{{ old('contact') }}"
                   placeholder="Mobile Number"
                   minlength="10"
                   maxlength="10"
                   pattern="[0-9]{10}"
                   title="Enter a valid 10-digit mobile number"
                   required>
        </div>

        <div class="col">
            <input type="email" class="form-control" name="email"
                   value="{{ old('email') }}"
                   placeholder="Email" required>
        </div>
    </div>

    <!-- Gender -->
    <div class="row mb-3">
        <div class="col">
            <select class="form-select" name="gender" required>
                <option value="">Select Gender</option>
                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="col">
            <input type="text" class="form-control" name="college_name_input"
                   value="{{ old('college_name_input') }}"
                   placeholder="College Name" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <input type="text" class="form-control" name="semester"
                   value="{{ old('semester') }}"
                   placeholder="Stream & Semester" required>
        </div>

        <div class="col">
            <input type="text" class="form-control" name="course_name_input"
                   value="{{ old('course_name_input') }}"
                   placeholder="Technology / Domain" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <select class="form-select" name="study_mode" required>
                <option value="">Select Training Mode </option>
                <option value="online" {{ old('study_mode') == 'online' ? 'selected' : '' }}>Online</option>
                <option value="offline" {{ old('study_mode') == 'offline' ? 'selected' : '' }}>Offline</option>
            </select>
        </div>

        <div class="col mb-2">
            <small class="text-muted">Start Date</small>
            <input type="date" class="form-control" name="start_date"
                   value="{{ old('start_date') }}" required>
        </div>

        <!-- PAYMENT SECTION -->
<div class="mt-4">

    <div class="card border">

        <div class="card-header">
            <strong>Payment</strong>
        </div>

        <div class="card-body">

            @if($paymentUpi)

                <div class="row">

                    {{-- QR CODE --}}
                    <div class="col-md-5 text-center mb-3">

                        <h6 class="mb-3">
                            Scan & Pay
                        </h6>

                        <div>
                            <img
                                src="{{ asset($paymentUpi->qr_image) }}"
                                alt="Payment QR Code"
                                style="
                                    width:220px;
                                    height:220px;
                                    object-fit:contain;
                                    border:1px solid #ddd;
                                    padding:8px;
                                    border-radius:8px;
                                    background:#fff;
                                "
                            >
                        </div>

                        <div class="mt-2">

                            @if($paymentUpi->provider)

                                <strong>
                                    {{ $paymentUpi->provider }}
                                </strong>

                                <br>

                            @endif

                            <small class="text-muted">
                                {{ $paymentUpi->name }}
                            </small>

                        </div>

                    </div>


                    {{-- PAYMENT DETAILS --}}
                    <div class="col-md-7">

                        @if($paymentUpi->upi_id)

                            <label>
                                <strong>UPI ID</strong>
                            </label>

                            <div class="input-group mb-3">

                                <input
                                    type="text"
                                    class="form-control"
                                    id="paymentUpiId"
                                    value="{{ $paymentUpi->upi_id }}"
                                    readonly
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="copyPaymentUpi()"
                                >
                                    Copy
                                </button>

                            </div>

                        @endif


                        <div class="mb-3">

                            <label>
                                <strong>Payment Amount</strong>
                            </label>

                            <input
                                type="number"
                                name="payment_amount"
                                class="form-control"
                                value="{{ old('payment_amount') }}"
                                min="1"
                                step="0.01"
                                placeholder="Enter amount paid"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label>
                                <strong>Transaction / UTR ID</strong>
                            </label>

                            <input
                                type="text"
                                name="payment_transaction_id"
                                class="form-control"
                                value="{{ old('payment_transaction_id') }}"
                                maxlength="150"
                                placeholder="Enter UTR / Transaction ID"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label>
                                <strong>Payment Date & Time</strong>
                            </label>

                            <input
                                type="datetime-local"
                                name="payment_date"
                                class="form-control"
                                value="{{ old('payment_date') }}"
                                required
                            >

                        </div>


                        <div class="mb-3">

                            <label>
                                <strong>Payment Screenshot</strong>
                            </label>

                            <input
                                type="file"
                                name="payment_proof"
                                class="form-control"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                            >

                            <small class="text-muted">
                                JPG, PNG or WEBP. Maximum 5 MB.
                            </small>

                        </div>

                    </div>

                </div>


                <div class="alert alert-info mt-3 mb-0">

                    <strong>Payment Instructions:</strong>

                    Scan the QR code using your UPI app, complete the payment,
                    then enter the transaction/UTR ID and upload the payment screenshot.

                </div>


            @else

                <div class="alert alert-danger mb-0">

                    Payment QR is currently unavailable.
                    Please contact the administrator.

                </div>

            @endif

        </div>

    </div>

</div>
    </div>

    <!-- Submit -->
    <div class="text-center">
        <button type="submit" class="btn primary-btn px-5 mt-10">
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
function copyPaymentUpi() {

    const input =
        document.getElementById('paymentUpiId');

    if (!input) {
        return;
    }

    navigator.clipboard.writeText(input.value)
        .then(function () {

            alert('UPI ID copied.');

        })
        .catch(function () {

            input.select();

            document.execCommand('copy');

            alert('UPI ID copied.');

        });
}
</script>

</body>
</html>

