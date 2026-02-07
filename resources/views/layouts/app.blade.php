<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Certificates')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        .dataTables_length select { min-width: 70px !important; }
        .dataTables_wrapper { overflow: visible !important; }
        table.dataTable thead th { white-space: nowrap !important; }
    </style>

    <script>
        $(document).ready(function () {
            $('#resultsTable').DataTable();
            $('#student_test').DataTable();
        });
    </script>
</head>

<body>

<!-- <div id="main-wrapper"> -->
@include('layouts.header')
<!-- @include('common.logo') -->
@include('layouts.navbar')

<div class="content-body">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>
<!-- </div> -->

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.5/dist/perfect-scrollbar.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>

<script>
    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 50,
        language: { lengthMenu: "Show _MENU_ Entries" }
    });
</script>

@stack('scripts')

{{-- ================= OTP POPUP (MIDDLEWARE CONTROLLED) ================= --}}
@if(isset($showOtpPopup) && $showOtpPopup)

@php
     
@endphp

@php
    $user = auth()->user();
    $email = $user->email ?? '';

    if ($email && str_contains($email, '@')) {

        [$local, $domain] = explode('@', $email);
        $localLength = strlen($local);

        if ($localLength > 6) {
            // Normal email
            $maskedLocal =
                substr($local, 0, 4)
                . str_repeat('*', $localLength - 6)
                . substr($local, -2);
        } elseif ($localLength > 2) {
            // Short email
            $maskedLocal =
                substr($local, 0, 1)
                . str_repeat('*', $localLength - 2)
                . substr($local, -1);
        } else {
            // Very short (1–2 chars)
            $maskedLocal = str_repeat('*', $localLength);
        }

        $maskedEmail = $maskedLocal . '@' . $domain;

    } else {
        // Fallback if email missing or invalid
        $maskedEmail = '********';
    }

    $otpExpiresAt = session('enquiry_otp_expires_at');
@endphp


@php
    $otpExpiresAt = session('enquiry_otp_expires_at');
    $otpExists = session()->has('enquiry_otp_code');
    $otpValid = $otpExists && $otpExpiresAt && now()->timestamp < $otpExpiresAt;
@endphp

<div id="otpOverlay" class="otp-overlay">
    <!-- 
    <div class="otp-modal">
        <button type="button"
                class="btn-close position-absolute"
                style="top:15px; right:15px;"
                onclick="closeOtpPopup()">
        </button>
        <h4>Email Verification</h4>
         
        <p class="text-muted mb-2">
            OTP has been sent to<br>
            <strong>{{ $maskedEmail }}</strong>
        </p>

        <input type="text"
               id="otpCode"
               class="form-control mb-2"
               placeholder="Enter OTP">

        <div id="otpError" class="text-danger small mb-2"></div>

        <div class="small text-muted mb-2">
            Time remaining:
            <span id="otpTimer">--:--</span>
        </div>

        <button id="verifyBtn"
                class="btn btn-success w-100"
                onclick="verifyOtp()">
            Verify OTP
        </button>

        <button id="resendBtn"
                class="btn btn-link w-100 mt-2"
                onclick="sendOtp()"
                style="display:none">
            Resend OTP
        </button>
    </div> -->

    <div class="otp-modal">
    <button type="button"
            class="btn-close position-absolute"
            style="top:15px; right:15px;"
            onclick="closeOtpPopup()">
    </button>

    <h4>Email Verification</h4>

    <p class="text-muted mb-3">
        Verify your email to continue
    </p>

    <!-- SEND OTP SECTION -->
    <div id="sendOtpSection" style="{{ $otpValid ? 'display:none' : '' }}">
        <p class="text-muted mb-2">
            OTP will be sent to<br>
            <strong>{{ $maskedEmail }}</strong>
        </p>

        <button class="btn btn-primary w-100" id="sendOtpBtn"  onclick="sendOtp()">
            Send OTP
        </button>
    </div>

    <!-- VERIFY OTP SECTION -->
    <div id="verifyOtpSection"  style="{{ $otpValid ? '' : 'display:none' }}">
        <input type="text"
               id="otpCode"
               class="form-control mb-2"
               placeholder="Enter OTP"

                oninput="toggleVerifyBtn()">

        <div id="otpError" class="text-danger small mb-2"></div>

        <div class="small text-muted mb-2">
            Time remaining:
            <span id="otpTimer">--:--</span>
        </div>

        <button id="verifyBtn"
                class="btn btn-success w-100"
                onclick="verifyOtp()">
            Verify OTP
        </button>

        <button id="resendBtn"
                class="btn btn-link w-100 mt-2"
                onclick="sendOtp()"
                style="display:none">
            Resend OTP
        </button>
    </div>
</div>


</div>

<style>
    .otp-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(5px);
        background: rgba(0,0,0,0.4);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 999999;
    }

    .otp-modal {
        width: 350px;
    background: #fff;
    padding: 25px;
    border-radius: 8px;
    position: relative; /* 🔥 REQUIRED */
    }

  
</style>

<script>
let expiresAt = {{ $otpExpiresAt ?? 'null' }};
let timerInterval = null;

function startOtpTimer() {
    if (!expiresAt) return;

    timerInterval = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const remaining = expiresAt - now;

        if (remaining <= 0) {
            clearInterval(timerInterval);
            document.getElementById('otpTimer').innerText = 'Expired';
            document.getElementById('verifyBtn').disabled = true;
            document.getElementById('resendBtn').style.display = 'block';
            return;
        }

        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        document.getElementById('otpTimer').innerText =
            `${m}:${s.toString().padStart(2, '0')}`;
    }, 1000);
}

function sendOtp() {

    const btn = document.getElementById('sendOtpBtn');
    btn.disabled = true;
    btn.innerText = 'Sending...';

    fetch("{{ route('enquiry.otp.send') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === 'sent') {

            document.getElementById('sendOtpSection').style.display = 'none';
            document.getElementById('verifyOtpSection').style.display = 'block';

            expiresAt = data.expires_at;
            startOtpTimer();

             // 🔥 RESET VERIFY STATE
            const verifyBtn = document.getElementById('verifyBtn');
            const otpInput = document.getElementById('otpCode');
            const resendBtn = document.getElementById('resendBtn');

            verifyBtn.disabled = true;
            verifyBtn.innerText = 'Verify OTP';

            otpInput.value = '';
            otpInput.focus();

            resendBtn.style.display = 'none';

        } else {
            btn.disabled = false;
            btn.innerText = 'Send OTP';
            alert(data.message || 'Failed to send OTP');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Send OTP';
        alert('Network error. Try again.');
    });
}

function sendOtpworkformanualotp() {

    fetch("{{ route('enquiry.otp.send') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'sent') {

            // Switch UI
            document.getElementById('sendOtpSection').style.display = 'none';
            document.getElementById('verifyOtpSection').style.display = 'block';

            // Set expiry & start timer
            expiresAt = data.expires_at;
            startOtpTimer();

        } else {
            alert(data.message || 'Failed to send OTP');
        }
    });
}


function sendOtpold() {
    fetch("{{ route('enquiry.otp.send') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'sent') {
            location.reload();
        } else {
            alert(data.message || 'Failed to send OTP');
        }
    });
}

function verifyOtp() {

    const btn = document.getElementById('verifyBtn');
    const errorBox = document.getElementById('otpError');
    const resendBtn = document.getElementById('resendBtn');

    btn.disabled = true;
    btn.innerText = 'Verifying...';
    errorBox.innerText = '';

    fetch("{{ route('enquiry.otp.verify') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            otp: document.getElementById('otpCode').value
        })
    })
    .then(res => res.json())
    .then(data => {

        if (data.status === 'verified') {
            location.reload();
            return;
        }

        // 🔥 RE-ENABLE VERIFY BUTTON ON FAILURE
        btn.disabled = false;
        btn.innerText = 'Verify OTP';

        errorBox.innerText = data.message || 'Invalid OTP';

        if (data.status === 'expired') {
            btn.disabled = true; // expired → cannot verify
            resendBtn.style.display = 'block';
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Verify OTP';
        errorBox.innerText = 'Network error. Try again.';
    });
}

function verifyOtpas() {
    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.innerText = 'Verifying...';

    document.getElementById('otpError').innerText = '';

    fetch("{{ route('enquiry.otp.verify') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            otp: document.getElementById('otpCode').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'verified') {
            location.reload();
        } else {
            document.getElementById('otpError').innerText = data.message;
            if (data.status === 'expired') {
                document.getElementById('resendBtn').style.display = 'block';
            }
        }
    })
     .catch(() => {
        btn.disabled = false;
        btn.innerText = 'Verify OTP';
        document.getElementById('otpError').innerText = 'Network error. Try again.';
    });
}

function toggleVerifyBtn() {
    const otp = document.getElementById('otpCode').value;
    const btn = document.getElementById('verifyBtn');

    btn.disabled = otp.length < 6;
}


@if($otpExpiresAt)
    startOtpTimer();
@endif

function closeOtpPopup() {
    // Redirect to dashboard or previous page
    window.location.href = "{{ url('/dashboard') }}";
    // OR
    // window.history.back();
}
</script>
 <script>
document.addEventListener('DOMContentLoaded', function () {

    const hamburger = document.querySelector('.hamburger');
    if (!hamburger) return;

    hamburger.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Toggle sidebar
        document.body.classList.toggle('mobile-menu-open');

        // Toggle icon
        hamburger.classList.toggle('is-active');
    });

});
</script>



@endif
{{-- ================= END OTP POPUP ================= --}}

</body>

<footer class="text-center py-3 text-muted">
    © {{ date('Y') }} Sortiq Solutions. All rights reserved.
</footer>
</html>
