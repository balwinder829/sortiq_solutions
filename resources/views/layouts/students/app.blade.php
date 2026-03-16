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
@include('layouts.students.header')
<!-- @include('common.logo') -->
@include('layouts.students.navbar')

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

<script>
    $.extend(true, $.fn.dataTable.defaults, {
        pageLength: 50,
        language: { lengthMenu: "Show _MENU_ Entries" }
    });

    // SweetAlert2: global helper for script-based confirm (usage: sweetConfirm('Message?', function() { form.submit(); }))
    window.sweetConfirm = function(msg, onConfirmed) {
        Swal.fire({ title: 'Are you sure?', text: msg, icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33', confirmButtonText: 'Yes' })
            .then(function(r) { if (r.isConfirmed && onConfirmed) onConfirmed(); });
    };

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
 
</script>


@stack('scripts')

{{-- SweetAlert2: flash messages (success, error, warning) --}}
@if(session('success'))
<!-- <script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'success', title: 'Success', text: @json(session('success')) }); }); </script> -->
@endif
@if(session('error'))
<script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'error', title: 'Error', text: @json(session('error')) }); }); </script>
@endif
@if(session('warning'))
<script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'warning', title: 'Warning', text: @json(session('warning')) }); }); </script>
@endif
@if(session('danger'))
<script> document.addEventListener('DOMContentLoaded', function() { Swal.fire({ icon: 'error', title: 'Error', text: @json(session('danger')) }); }); </script>
@endif
 
    
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

 
</body>

<footer class="text-center py-3 text-muted">
    © {{ date('Y') }} Sortiq Solutions. All rights reserved.
</footer>
</html>
