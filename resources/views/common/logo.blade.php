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


    {{-- LOGO --}}
    <a href="{{ url('/') }}" class="brand-logo ms-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:40px;">
    </a>

</div>
