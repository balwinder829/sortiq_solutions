<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Certificates')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}"> -->
    <link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <link rel="icon" type="image/jpeg" href="{{ asset('certificate_fav.jpeg') }}">

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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // $('.select2').select2({
        //     theme: 'bootstrap-5',
        //     placeholder: "Search college name",
        //     allowClear: true
        // });

         if ($.fn.select2) {
            $('.select2').select2({
                theme: 'bootstrap-5',
                placeholder: "Search college name",
                allowClear: true
            });
        }
    });

     function normalizeIndianNumber(value) {
    if (!value) return '';

    // Convert to string (Excel sometimes gives weird types)
    value = String(value);

    // Remove EVERYTHING except digits
    let digits = value.replace(/[^\d]/g, '');

    // Remove leading country code (91, 0091, etc.)
    if (digits.length > 10) {
        digits = digits.slice(-10);
    }

    return digits;
}

function handlePaste(e) {
    e.preventDefault();

    let pasted = (e.clipboardData || window.clipboardData).getData('text');

    // Handle multi-line paste from Excel (take first cell only)
    pasted = pasted.split(/\r?\n/)[0];

    e.target.value = normalizeIndianNumber(pasted);
}

function sanitizeContact(el) {
    el.value = normalizeIndianNumber(el.value);
}
    $(document).ready(function () {
        $.extend(true, $.fn.dataTable.defaults, {
            pageLength: 50,

            language: { lengthMenu: "Show _MENU_ Entries" },
            stateSave: true,
            stateDuration: -1, // keep forever
            stateSaveCallback: function (settings, data) {
                localStorage.setItem(
                    'DT_' + window.location.pathname,
                    JSON.stringify(data)
                );
            },
            stateLoadCallback: function (settings) {
                // return JSON.parse(
                //     localStorage.getItem('DT_' + window.location.pathname)
                // );

                  let data = JSON.parse(
                    localStorage.getItem('DT_' + window.location.pathname)
                );

                if (data) {
                    data.search.search = "";

                    data.columns.forEach(function(col) {
                        col.search.search = "";
                    });
                }

                return data;
            }
        });
    });

    // SweetAlert2: global helper for script-based confirm (usage: sweetConfirm('Message?', function() { form.submit(); }))
    window.sweetConfirm = function(msg, onConfirmed) {
        Swal.fire({ title: 'Are you sure?', text: msg, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes' })
            .then(function(r) { if (r.isConfirmed && onConfirmed) onConfirmed(); });
    };

    // SweetAlert2: confirm for buttons (submit) and links that have data-swal-confirm
//     $(document).on('click', '[data-swal-confirm]', function(e) {
//         e.preventDefault();
//         var el = $(this);
//         var msg = el.attr('data-swal-confirm') || 'Are you sure?';
//         var form = el.closest('form');
//         Swal.fire({
//             title: 'Are you sure?',
//             text: msg,
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Yes'
//         }).then(function(result) {
//             if (result.isConfirmed) {
//                 if (form.length) form.off('submit').submit();
//                 else if (el.attr('href')) window.location.href = el.attr('href');
//             }
//         });
//         return false;
//     });

//     // SweetAlert2: confirm for form submit (form has data-swal-confirm)
//     $(document).on('submit', 'form[data-swal-confirm]', function(e) {
//         e.preventDefault();
//         var form = $(this);
//         var msg = form.attr('data-swal-confirm') || 'Are you sure?';
//         Swal.fire({
//             title: 'Are you sure?',
//             text: msg,
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#3085d6',
//             cancelButtonColor: '#d33',
//             confirmButtonText: 'Yes'
//         }).then(function(result) {
//             if (result.isConfirmed) form.off('submit').submit();
//         });
//         return false;
//     });
 </script>

 <script>
/*
|--------------------------------------------------------------------------
| UNIVERSAL SWEETALERT CONFIRM HANDLER
|--------------------------------------------------------------------------
| Supports:
| - <form data-swal-confirm="...">
| - <a data-swal-confirm="..." href="...">
| - <button data-swal-confirm="...">
| Safe for 100+ pages, datatables, ajax redraws
|--------------------------------------------------------------------------
*/
    $(document).on('click', '[data-swal-delete]', function (e) {

    const el   = $(this);
    const form = el.closest('form');
    const msg  = el.data('swal-confirm') || 'Are you sure?';
    const href = el.attr('href');

    // If element is inside form → handle submit here
    if (form.length) {

        e.preventDefault();

        // stop double popup
        if (form.data('swal-processing')) return;

        Swal.fire({
            title: 'Are you sure?',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {

            if (!result.isConfirmed) return;

            form.data('swal-processing', true);

            // 🔥 FORCE NATIVE SUBMIT (works even without type="submit")
            form[0].submit();
        });

        return;
    }

    // 🔥 LINK CASE
    if (href) {
        e.preventDefault();

        Swal.fire({
            title: 'Are you sure?',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = href;
        });
    }

});

// $(document).on('click', '[data-swal-confirm]', function (e) {

//     const el = $(this);
//     const form = el.closest('form');

//     // If button inside form → let submit handler manage it
//     if (form.length && (el.is('button') || el.attr('type') === 'submit')) {
//         return;
//     }

//     e.preventDefault();

//     const msg = el.data('swal-confirm') || 'Are you sure?';
//     const href = el.attr('href');

//     Swal.fire({
//         title: 'Are you sure?',
//         text: msg,
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes'
//     }).then((result) => {

//         if (!result.isConfirmed) return;

//         // 🔥 LINK CASE
//         if (href) {
//             window.location.href = href;
//         }

//     });

// });



// $(document).on('submit', 'form[data-swal-confirm]', function (e) {

//     const form = $(this);

//     // already confirmed → allow normal submit
//     if (form.data('swal-confirmed')) {
//         return true;
//     }

//     e.preventDefault();

//     const msg = form.data('swal-confirm') || 'Are you sure?';

//     Swal.fire({
//         title: 'Are you sure?',
//         text: msg,
//         icon: 'warning',
//         showCancelButton: true,
//         confirmButtonColor: '#3085d6',
//         cancelButtonColor: '#d33',
//         confirmButtonText: 'Yes'
//     }).then((result) => {

//         if (!result.isConfirmed) return;

//         // mark confirmed
//         form.data('swal-confirmed', true);

//         // 🔥 IMPORTANT: use native submit, NOT jQuery submit
//         form[0].submit();

//     });

// });
</script>


@stack('scripts')

{{-- SweetAlert2: flash messages (success, error, warning) --}}
@if(session('success'))
<!-- <script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')) }); }); </script> -->
@endif
@if(session('error'))
<!-- <script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) }); }); </script> -->
@endif
@if(session('warning'))
<script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'warning', title: 'Warning', text: @json(session('warning')) }); }); </script>
@endif
@if(session('danger'))
<script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'error', title: 'Error', text: @json(session('danger')) }); }); </script>
@endif

{{-- ================= OTP POPUP (MIDDLEWARE CONTROLLED) ================= --}}
@if(isset($showOtpPopup) && $showOtpPopup)

@php
     
@endphp

@php
$user = auth()->user();
$email = $user->email ?? '';

$adminEmail = null;

// If role is not 1 get responsible admin email
if ($user->role != 1) {
    $adminEmail = \App\Models\User::where('username','admin')
                    ->where('role',1)
                    ->value('email');
}

// Decide which email to use
$displayEmail = $user->role == 1 ? $email : $adminEmail;


// Email masking function
function maskEmail($email) {

    if ($email && str_contains($email, '@')) {

        [$local, $domain] = explode('@', $email);
        $localLength = strlen($local);

        if ($localLength > 6) {
            $maskedLocal =
                substr($local, 0, 4)
                . str_repeat('*', $localLength - 6)
                . substr($local, -2);

        } elseif ($localLength > 2) {

            $maskedLocal =
                substr($local, 0, 1)
                . str_repeat('*', $localLength - 2)
                . substr($local, -1);

        } else {

            $maskedLocal = str_repeat('*', $localLength);
        }

        return $maskedLocal . '@' . $domain;
    }

    return '********';
}

// Mask email for display
$maskedEmail = maskEmail($displayEmail);


// OTP session
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
<script>

// $(document).ready(function(){

//     $('#sidebarToggle').click(function(){

//         $('body').toggleClass('menu-toggle');

//     });

// });



</script>
<script>

$(document).ready(function(){
    if ($(window).width() > 991) {
        // LOAD SAVED STATE
        if(localStorage.getItem('sidebar-collapsed') === 'true'){

            $('body').addClass('menu-toggle');

        }

        // TOGGLE
        $('#sidebarToggle').click(function(){

            $('body').toggleClass('menu-toggle');

            // SAVE STATE
            if($('body').hasClass('menu-toggle')){

                localStorage.setItem('sidebar-collapsed', 'true');

            }else{

                localStorage.setItem('sidebar-collapsed', 'false');

            }

        
    });
        }
});

</script>
</body>

<footer class="text-center py-3 text-muted">
    © {{ date('Y') }} Sortiq Solutions. All rights reserved.
</footer>
</html>
