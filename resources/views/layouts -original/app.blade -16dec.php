<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Certificates')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<style>
    /* Ensure dropdown width is always enough */
/* Global DataTables Fixes */
.dataTables_length select {
    min-width: 70px !important;
}

.dataTables_wrapper {
    overflow: visible !important;
}

.dataTables_length label,
.dataTables_length select {
    line-height: 1.4em !important;
}

  /* ✅ Prevent DataTable header text from wrapping */
table.dataTable thead th {
    white-space: nowrap !important;
}



</style>

<script>
$(document).ready(function() {
        $('#resultsTable').DataTable();
        $('#student_test').DataTable(); 
});
</script>

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

    <!-- JS -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> <!-- Only ONE jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/perfect-scrollbar@1.5.5/dist/perfect-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/metisMenu/3.0.7/metisMenu.min.js"></script>
    <script src="{{ asset('js/custom.min.js') }}"></script>

    {{-- Page-specific scripts --}}
    <script>
        // Apply to all DataTables globally
$.extend(true, $.fn.dataTable.defaults, {
    pageLength: 50,
    language: {
        lengthMenu: "Show _MENU_ Entries"
    }
});

    </script>
    @stack('scripts')
   

   @if(isset($showOtpPopup) && $showOtpPopup)
<div id="otpOverlay" class="otp-overlay">
    <div class="otp-modal">
        <h4>Email Verification</h4>

        {{-- Email Step --}}
        <div id="otp-email-step">
            <p>Enter email to receive OTP:</p>

            <input type="email" id="otpEmail" class="form-control mb-1" required>

            <!-- ❗ Error will show here -->
            <div id="otpEmailError" class="text-danger small mb-2"></div>

            <button class="btn btn-primary" onclick="sendOtp()">Send OTP</button>
        </div>

        {{-- OTP Step --}}
        <div id="otp-code-step" style="display:none;">
            <p>Enter OTP:</p>

            <input type="text" id="otpCode" class="form-control mb-1" required>

            <!-- ❗ Error will show here -->
            <div id="otpCodeError" class="text-danger small mb-2"></div>

            <button class="btn btn-success" onclick="verifyOtp()">Verify</button>
        </div>

    </div>
</div>


<style>
/* Background Blur */
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

/* Modal Box */
.otp-modal {
    width: 350px;
    background: white;
    padding: 25px;
    border-radius: 8px;
}
.input-error {
    border: 1px solid #dc3545 !important;
    background: #ffe6e6 !important;
}

.error-text {
    color: #dc3545;
    font-size: 13px;
    margin-top: 2px;
}

</style>

<script>
function sendOtp() {
    document.getElementById('otpEmail').classList.remove('input-error');
    document.getElementById('otpEmailError').innerHTML = "";
    fetch("{{ route('enquiry.otp.send') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            email: document.getElementById('otpEmail').value
        })
    })
    .then(async res => {
        const raw = await res.text();
        let data = {};

        try {
            data = JSON.parse(raw);
        } catch (e) {
            document.getElementById('otpEmailError').innerHTML = "Unexpected server response.";
            return;
        }

        // 🔥 CASE 1 — Custom error from your controller
        if (data.status === "error") {
            document.getElementById("otpEmailError").innerHTML = data.message;
            return;
        }

        // 🔥 CASE 2 — Laravel validation errors (422)
        if (res.status === 422 && data.errors?.email) {
            // document.getElementById("otpEmailError").innerHTML = data.errors.email[0];
            document.getElementById("otpEmailError").innerHTML = data.message ?? data.errors.email[0];
            document.getElementById("otpEmail").classList.add("input-error");
            return;
        }

        // SUCCESS
        if (data.status === "sent") {
            document.getElementById('otp-email-step').style.display = "none";
            document.getElementById('otp-code-step').style.display = "block";
        }
    })
    .catch(err => console.error(err));
}

function verifyOtp() {

    document.getElementById('otpCode').classList.remove('input-error');
    document.getElementById('otpCodeError').innerHTML = "";

    fetch("{{ route('enquiry.otp.verify') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            otp: document.getElementById('otpCode').value
        })
    })
    .then(async res => {
        const raw = await res.text();
        let data = {};

        try {
            data = JSON.parse(raw);
        } catch (e) {
            document.getElementById("otpCodeError").innerHTML = "Unexpected server response.";
            return;
        }

        // 🔥 CASE 1 — Laravel validation errors
        if (res.status === 422 && data.errors?.otp) {
            document.getElementById("otpCodeError").innerHTML = data.errors.otp[0];
            document.getElementById("otpCode").classList.add("input-error");
            return;
        }

        // 🔥 CASE 2 — Custom error: status: "error"
        if (data.status === "error") {
            document.getElementById("otpCodeError").innerHTML = data.message;
            document.getElementById("otpCode").classList.add("input-error");
            return;
        }

        // 🔥 CASE 3 — Custom error: status: "invalid"
        if (data.status === "invalid") {
            document.getElementById("otpCodeError").innerHTML = data.message;
            document.getElementById("otpCode").classList.add("input-error");
            return;
        }

        // SUCCESS
        if (data.status === "verified") {
            location.reload();
        }
    })
    .catch(err => console.error(err));
}

</script>

@endif

</body>
 <footer class="text-center py-3 text-muted">
    © {{ date('Y') }} Sortiq Solutions. All rights reserved.
</footer>
</html>
