{{-- resources/views/common/logo.blade.php --}}
<div class="nav-header d-flex align-items-center justify-content-between">
    
    {{-- MOBILE HAMBURGER (only visible on mobile) --}}
    <div class="nav-control d-lg-none">
    <div class="hamburger">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
    </div>
</div>
@php
$link = url('/');

if(auth()->guard('student')->check()){
    $link = route('students.dashboard');
}elseif(auth()->guard('trainer')->check()){
    $link = route('batches.mybatches');
}elseif(auth()->guard('sales_staff')->check()){
    $link = route('sales.dashboard');
}elseif(auth()->guard('employee')->check()){
    $link = route('attendance.employee');
}
@endphp

    {{-- LOGO --}}
    <a href="{{ $link }}" class="brand-logo ms-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:40px;">
    </a>

</div>
