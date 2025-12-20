<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Certificates')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <style>
        .otp-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.45);
            backdrop-filter: blur(5px);
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
        }
    </style>
</head>

<body>

@include('layouts.header')
@include('common.logo')
@include('layouts.navbar')

<div class="content-body">
    <div class="container-fluid">
        @yield('content')
    </div>
</div>

<footer class="text-center py-3 text-muted">
    © {{ date('Y') }} Sortiq Solutions. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- ================= OTP POPUP ================= --}}
@if(!session('enquiry_otp_verified'))

@php
    $otpSent = session()->has('enquiry_otp_code');
    $otpExpiresAt = session('enquiry_otp_expires_at');
@endphp

<div class="otp-overlay">
    <div class="otp-modal">
        <h4>Email Verification</h4>

        {{-- EMAIL STEP (ONLY FIRST TIME) --}}
        <div id="otp-email-step" style="{{ $otpSent ? 'display:none' : '' }}">
            <p>Click below to receive OTP on your registered email.</p>
            <button class="btn btn-primary w-100" onclick="sendOtp()">Send OTP</button>
        </div>

        {{-- OTP STEP --}}
        <div id="otp-code-step" style="{{ $otpSent ? '' : 'display:none' }}">
            <p>Enter OTP:</p>

            <input type="text" id="otpCode" class="form-control mb-1">
            <div id="otpCodeError" class="text-danger small mb-2"></div>

            <div class="small text-muted mb-2">
                Time remaining: <span id="otpTimer">--:--</span>
            </div>

            <button id="verifyBtn" class="btn btn-success w-100 mb-2" onclick="verifyOtp()">Verify</button>

            <button id="resendBtn"
                    class="btn btn-link w-100"
                    onclick="sendOtp()"
                    style="display:none">
                Resend OTP
            </button>
        </div>
    </div>
</div>

<script>
let expiresAt = {{ $otpExpiresAt ?? 'null' }};
let timer = null;

function startTimer() {
    if (!expiresAt) return;

    timer = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const remaining = expiresAt - now;

        if (remaining <= 0) {
            clearInterval(timer);
            document.getElementById('otpTimer').innerText = 'Expired';
            document.getElementById('verifyBtn').disabled = true;
            document.getElementById('resendBtn').style.display = 'block';
            return;
        }

        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        document.getElementById('otpTimer').innerText =
            `${m}:${s.toString().padStart(2,'0')}`;
    }, 1000);
}

function sendOtp() {
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
            document.getElementById('otpCodeError').innerText = data.message;
            if (data.status === 'expired') {
                document.getElementById('resendBtn').style.display = 'block';
            }
        }
    });
}

@if($otpSent)
    startTimer();
@endif
</script>

@endif
{{-- ================= END OTP POPUP ================= --}}

</body>
</html>
