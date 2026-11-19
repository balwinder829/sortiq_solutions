<!DOCTYPE html>
<html>
<head>
    <title>Seminar Registration</title>

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

        /* PAYMENT QR */
.payment-qr-image {
    width: 220px;
    height: 220px;
    object-fit: contain;
    border: 1px solid #ddd;
    padding: 8px;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.payment-qr-image:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

/* FULLSCREEN QR */
.payment-qr-fullscreen {
    max-width: min(90vw, 700px);
    max-height: 80vh;
    width: auto;
    height: auto;
    object-fit: contain;
    background: #fff;
    padding: 15px;
    border-radius: 12px;
}

/* =========================
   MOBILE RESPONSIVE FORM
   ========================= */

.form-control,
.form-select {
    min-height: 44px;
}

/* QR image */
.payment-qr-image {
    width: 220px;
    height: 220px;
    max-width: 80vw;
    object-fit: contain;
    border: 1px solid #ddd;
    padding: 8px;
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.payment-qr-image:hover {
    transform: scale(1.03);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
}

/* QR fullscreen */
.payment-qr-fullscreen {
    display: block;
    width: auto;
    height: auto;
    max-width: 90vw;
    max-height: 75vh;
    object-fit: contain;
    background: #fff;
    padding: 10px;
    border-radius: 12px;
}

/* Mobile */
@media (max-width: 767.98px) {

    .hero-section {
        padding: 15px 0;
    }

    .hero-section .container {
        padding-left: 10px;
        padding-right: 10px;
    }

    .card {
        border-radius: 10px;
    }

    .card-body {
        padding: 15px;
    }

    .card-header {
        padding: 12px 15px;
    }

    /* QR */
    .payment-qr-image {
        width: 200px;
        height: 200px;
    }

    /* Payment card */
    .payment-qr-fullscreen {
        max-width: 88vw;
        max-height: 70vh;
        padding: 8px;
    }

    /* Payment details */
    .payment .form-control,
    .payment .form-select {
        width: 100%;
    }

    /* Submit button */
    .primary-btn {
        width: 100%;
        padding: 12px 20px;
    }

    /* Fullscreen modal */
    #paymentQrModal .modal-header {
        padding: 12px 15px;
    }

    #paymentQrModal .modal-title {
        font-size: 18px;
    }

    #paymentQrModal .modal-body {
        padding: 10px;
    }
}

/* Very small phones */
@media (max-width: 375px) {

    .hero-section .container {
        padding-left: 7px;
        padding-right: 7px;
    }

    .card-body {
        padding: 12px;
    }

    .payment-qr-image {
        width: 180px;
        height: 180px;
    }

    .payment-qr-fullscreen {
        max-width: 92vw;
        max-height: 68vh;
    }
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
            <strong>Seminar Registration</strong>
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


<form method="POST" action="{{ route('seminar.register') }}"  enctype="multipart/form-data">
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
                   placeholder="Email">
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
                   placeholder="College Name" >
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <input type="text" class="form-control" name="semester"
                   value="{{ old('semester') }}"
                   placeholder="Stream & Semester" >
        </div>

        <div class="col">
            <input type="text" class="form-control" name="course_name_input"
                   value="{{ old('course_name_input') }}"
                   placeholder="Technology / Domain">
        </div>
    </div>

    <div class="row mb-3">
        

 

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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- INIT -->
<script>

    function openPaymentQr() {
    const modalElement = document.getElementById('paymentQrModal');

    if (!modalElement) {
        return;
    }

    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Search College",
        allowClear: true,
        width: '100%'
    });
});
function copyPaymentUpi() {
    const input = document.getElementById('paymentUpiId');

    if (!input) {
        return;
    }

    function showCopiedMessage() {
        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'UPI ID copied',
            timer: 1500,
            showConfirmButton: false
        });
    }

    function copyFallback() {
        input.select();
        input.setSelectionRange(0, input.value.length);

        const copied = document.execCommand('copy');

        if (copied) {
            showCopiedMessage();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Copy failed',
                text: 'Please copy the UPI ID manually.'
            });
        }
    }

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value)
            .then(showCopiedMessage)
            .catch(copyFallback);
    } else {
        copyFallback();
    }
}
</script>

</body>
</html>

