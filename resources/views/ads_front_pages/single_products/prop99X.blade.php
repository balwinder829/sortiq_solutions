<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@if(!empty($page->location))
    <meta name="geo.placename" content="{{ $page->location }}">
    <meta name="geo.region" content="IN">
@endif
<title>Prop99X | Sortiq Solutions Pvt Ltd</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="icon" type="image/jpeg" href="{{ asset('certificate_fav.jpeg') }}">

<style>
    /* Sticky CTA */
.sticky-cta {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #000;
  padding: 10px;
  z-index: 9998;

  display: none;
  gap: 10px;
}

/* Buttons */
.sticky-cta a {
  flex: 1;
  text-align: center;
  padding: 12px;
  border-radius: 12px;
  text-decoration: none;
  color: #fff;
  font-weight: 600;
}

/* Colors */
.apply-btn {
  background: #ff6b00;
}

.call-btn {
  background: #25D366;
}


/* Mobile only */
@media (max-width: 768px) {
  .sticky-cta {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  background: #000;
  padding: 10px;
  z-index: 9998;

  display: flex;   /* ALWAYS visible */
  gap: 10px;
}
body {
  /*padding-bottom: 65px;*/
  overflow-x: hidden;
}
}
    .card {
  transition: transform .3s ease, box-shadow .3s ease;
}

.card:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.15);
}
    /* Section background */
.urgency-section {
  /*background: linear-gradient(135deg, #fff7f2, #ffffff);*/
   background: linear-gradient(135deg, #ffece0, #f5f7fa);
}

/* Card base */
.urgency-card {
  border: none;
  border-radius: 16px;
  padding: 30px 20px;
  transition: all 0.35s ease;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
  cursor: pointer;
}

/* Icon circle */
.icon-box {
  width: 65px;
  height: 65px;
  margin: 0 auto 15px;
  border-radius: 50%;
  background: #fff3ec;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 26px;
  color: #ff5800;
  transition: 0.3s;
}

/* Hover effect */
.urgency-card:hover {
  transform: translateY(-10px) scale(1.02);
  box-shadow: 0 18px 40px rgba(255,88,0,0.2);
}

/* Icon animation on hover */
.urgency-card:hover .icon-box {
  background: #ff5800;
  color: #fff;
  transform: scale(1.1);
}

/* Text */
.urgency-card h5 {
  font-weight: 600;
  color: #00163e;
}
    .whatsapp-float {
    position: fixed;
    bottom: 80;
    right: 20px;
    background: #25D366;
    color: #fff;
    padding: 12px 18px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    z-index: 9999;

    opacity: 0;
    /*transform: translateY(20px);*/
    transition: all .4s ease;
}

.sticky-cta {
  box-sizing: border-box;
}

.sticky-cta a {
  min-width: 0; /* important for flex overflow */
}

.whatsapp-float.show {
    opacity: 1;
    transform: translateY(0);
}

.whatsapp-float:hover {
    background: #1ebe5d;
}

/* ========== LOGO HEADER ========= */
.site-header{
    position:absolute;
    top:20px; left:30px;
    z-index:2000;
}
.site-header img{ height:55px; }

/* ========== HERO SECTION ========= */
.hero-section {
  background: url("{{ asset('images/internship.avif') }}") center/cover no-repeat;
  padding:120px 0 100px;
  position:relative;
  color:#fff;
  z-index:1;
}
.hero-section::after {
  content:'';
  position:absolute;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.55);
  z-index:-1;   /* ↓ keeps blur UNDER content */
}

.hero-content{ position:relative; z-index:2; padding-right:40px; }
.hero-content h2{ font-size:42px;font-weight:800;margin-bottom:15px;}
.hero-content p{ font-size:18px;margin-bottom:30px; }


.hero-btn{
  background:#ff5800;
  width: auto!important;
  border:none;
  border-radius:12px;
  color:#fff;
  padding:10px 20px;
  font-size:18px;font-weight:600;
  text-decoration:none;
}

/* ========== FORM DESIGN ========== */
.form-box{
  background:#fff;padding:25px;border-radius:14px;z-index:2;
  box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

.form-box h3{
  font-size:22px;
  margin-bottom:25px;
  color:#00163e;
  font-weight:700;
}

/* New Improved Input Look */
form input, form textarea{
    width:100%;
    padding:14px;
    border-radius:10px;
    border:1px solid #d8d8d8;
    font-size:15px;
    background:#fafafa;
}

form textarea{
    height:100px !important;
    resize:none;
}

/* Submit Button */
.form-submit{
    background:#ff5800;
    color:#fff;
    border:none;
    width:100%;
    padding:10px;
    font-size:17px;
    border-radius:10px;
    font-weight:600;
    cursor:pointer;
    margin-top:5px;
}
.form-submit:hover{
    opacity:.9;
}

/* Sections */
.section{padding:70px 0;}
.section-title{
  font-size:32px;font-weight:700;color:#00163e;text-align:center;margin-bottom:45px;
}

.info-box{
  background:#fff;padding:28px;border-radius:14px;text-align:center;
  box-shadow:0 6px 20px rgba(0,0,0,0.08);transition:.3s;
}
.info-box img{width:90px;height:90px;border-radius:50%;margin-bottom:10px;}
.info-box:hover{transform:translateY(-5px);}
.info-box i{font-size:40px;color:#003972;margin-bottom:10px;}

.services-area{background:#00163e;}

footer{
  background:#00163e;text-align:center;color:#fff;padding:10px 0;font-size:14px;margin-top:0;
  border-top:1px solid rgba(255,255,255,0.3);
}

/* Responsive */
@media(max-width:991px){
  .hero-content{text-align:center;padding-right:0;}
  .hero-btn{margin:auto;display:block;width:200px;}
  .form-box{margin-top:35px;}
  .hero-content h2{font-size:30px;}
  .hero-content h3{font-size:20px; }
  .hero-section{padding:80px 0;}
  .site-header img{height:45px;}
}
@media (max-width: 768px) {
 
}

/* ================= CERTIFICATE CAROUSEL ================= */
/* ================= FIXED RESPONSIVE CERTIFICATE CAROUSEL ================= */
.certi-slider {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding: 10px 0 45px;
}

.certi-track {
  display: flex;
  gap: 20px;
  transition: transform .5s ease-in-out;
}

.certi-item {
  flex: 0 0 calc(33.33% - 20px);
  background: #fff;
  padding: 15px;
  border-radius: 12px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.certi-item img {
  width: 100%;
  border-radius: 10px;
}

/* FIXED NAV BUTTONS */
.certi-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background: #ff9a34;
  border: none;
  color: #fff;
  font-size: 26px;
  width: 45px;
  height: 45px;
  border-radius: 50%;
  cursor: pointer;
  z-index: 20;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Desktop Button Positions */
.certi-btn.prev { left: 10px; }
.certi-btn.next { right: 10px; }

/* Mobile Fix */
@media (max-width: 768px) {

  .certi-item {
    flex: 0 0 85% !important;
  }

  .certi-btn {
    width: 40px;
    height: 40px;
    font-size: 22px;
  }

  /* Correct Arrow Positions – NOT overlapping image */
  .certi-btn.prev { left: 5px !important; }
  .certi-btn.next { right: 5px !important; }
}

@media (max-width: 480px) {

  .certi-item {
    flex: 0 0 90% !important;
  }

  .certi-btn.prev { left: 5px !important; }
  .certi-btn.next { right: 5px !important; }
}

.hero-section::after {
    pointer-events: none !important;
}

/* Footer Container */
.site-footer {
    background: #00163e;
    text-align: center;
    /*padding: 25px 15px;*/
    color: #fff;
    font-size: 15px;
    position: relative;
    padding: 10px 0px;
}
.site-footer {
    
    /*font-size: 13px;*/
}

.site-footer p {
  margin-bottom: 0;
}
 
form input,
form textarea,
form select {
    width: 100%;
    height: 42px;                /* 🔑 same height */
    padding: 0 16px;             /* 🔑 vertical handled by line-height */
    border-radius: 12px;
    border: 1px solid #dcdcdc;
    font-size: 15px;
    background: #fff;
    color: #333;
    outline: none;
    box-sizing: border-box;      /* 🔑 critical */
    /*line-height: 52px;           /* 🔑 centers text vertically */*/
}
form textarea {
    height: 100px !important;
    padding: 14px 16px;
    line-height: normal;
    resize: none;
}
form select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='%23999' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 16px center;
    background-size: 18px;
    padding-right: 42px;
}
form input:focus,
form textarea:focus,
form select:focus {
    border-color: #ff6a00;
    box-shadow: 0 0 0 3px rgba(255,106,0,0.15);
}
/*********NEW CSS****************/

.container {
  max-width: 1200px;
  width: 100%;
}
.whatsapp-float {
  max-width: calc(100% - 40px);
  white-space: nowrap;
}
.whatsapp-float {
  white-space: normal;
}

.footer-cta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;   /* 🔥 KEY FIX */
  gap: 15px;
}

.footer-left {
  flex: 1 1 300px;
}

.footer-right {
  flex: 1 1 300px;
  text-align: right;
}

@media (max-width: 768px) {
  .footer-cta {
    flex-direction: column;
    align-items: flex-start;
  }

  .footer-right {
    text-align: left;
  }
}

.responsive-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;  /* normal case (1 line) */
  flex-wrap: wrap;
  gap: 10px;
}

.hero-content ul {
  list-style: none;
  padding: 0;
  margin: 0 auto 20px;
  display: inline-block;   /* 🔥 KEY FIX */
  text-align: left;        /* keeps text aligned with bullet */
}

.hero-content li {
  position: relative;
  padding-left: 20px;
  margin-bottom: 6px;
}

/* custom bullet */
.hero-content li::before {
  content: "•";
  position: absolute;
  left: 0;
  top: 0;
}
@media (max-width: 1024px) {
  .responsive-flex {
    flex-direction: column;
    align-items: center;   /* center all */
    text-align: center;
  }

  .responsive-flex a {
    margin-left: 0 !important; /* 🔥 overrides ms-auto */
    margin-top: 10px;
  }
}

/* Dark Section Background */
.client-dark {
  background: linear-gradient(135deg, #00163e, #002b6b);
  color: #fff;
}

/* Cards inside dark section */
.client-card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  backdrop-filter: blur(10px);
  border-radius: 14px;
  transition: all 0.3s ease;
}

/* Text color */
.client-card h5 {
  color: #fff;
}

/* Icons */
.client-card i {
  color: #ff8a3d;
  transition: 0.3s;
}

/* Hover effect */
.client-card:hover {
  transform: translateY(-8px);
  background: rgba(255,255,255,0.1);
}

.client-card:hover i {
  transform: scale(1.2);
  color: #ff6b00;
}

.cta-section {
  background: linear-gradient(135deg, #0d6efd, #6610f2);
  padding: 80px 0;
  border-radius: 10px;
}

.cta-section h2 {
  font-weight: 700;
}

.cta-section .btn {
  border-radius: 30px;
  transition: 0.3s;
}

.cta-section .btn:hover {
  transform: scale(1.05);
}

.benefit-card {
  transition: all 0.3s ease;
  border-width: 2px;
  border-radius: 12px;
}

/* Hover lift */
.benefit-card:hover {
  transform: translateY(-8px);
}

/* Icon animation */
.benefit-card i {
  transition: 0.3s;
}

.benefit-card:hover i {
  transform: scale(1.2);
}

/* Colored glow effects */
.benefit-card.border-primary:hover {
  box-shadow: 0 15px 35px rgba(13,110,253,0.2);
}

.benefit-card.border-success:hover {
  box-shadow: 0 15px 35px rgba(25,135,84,0.2);
}

.benefit-card.border-warning:hover {
  box-shadow: 0 15px 35px rgba(255,193,7,0.25);
}

.benefit-card.border-danger:hover {
  box-shadow: 0 15px 35px rgba(220,53,69,0.2);
}

.benefit-card.border-info:hover {
  box-shadow: 0 15px 35px rgba(13,202,240,0.2);
}

.how-card {
  position: relative;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.how-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Step badge (number circle) */
.step-badge {
  position: absolute;
  top: -12px;
  left: 50%;
  transform: translateX(-50%);
  background: #0d6efd;
  color: #fff;
  width: 35px;
  height: 35px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
}

/* Icon animation */
.how-card i {
  transition: 0.3s;
}

.how-card:hover i {
  transform: scale(1.2);
}

.target-card {
  border: none;
  border-radius: 12px;
  transition: all 0.3s ease;
}

.target-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.1);
}

/* Icon animation */
.target-card i {
  transition: 0.3s;
}

.target-card:hover i {
  transform: scale(1.2);
}
</style>
</head>

<body> 
<!-- LOGO -->
<div class="site-header">
  <a href="/"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png" alt="Sortiq"></a>
</div>

<!-- HERO -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-8 hero-content">
        <h3>{{ $page->heading !== null && $page->heading !== '' 
    ? ucwords($page->heading) 
    : 'Kickstart Your IT Career with Real Project Internship' }}</h3>


    {!! $page->content !== null && $page->content !== '' 
    ? ucwords($page->content) 
    : 'Work with experts, gain real project exposure & develop skills to become industry-ready.' !!}
      
      </div>

      <!-- FORM -->
      <div class="col-lg-4" id="form">
        <div class="form-box">
          <h3 class="text-center">Service Request</h3>

      

 <form id="internshipForm"
      method="POST"
      action="{{ route('single-product-registrations.store') }}">

    @csrf
    <input type="hidden" name="slug" value="{{ $page->slug }}">
    <div class="row g-3">

        {{-- Full Name --}}
        <div class="col-md-6">
            <input type="text"
                   name="full_name"
                   placeholder="Full Name"
                   value="{{ old('full_name') }}"
                   required>

            @error('full_name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Email --}}
        <div class="col-md-6">
            <input type="email"
                   name="email"
                   placeholder="Email"
                   value="{{ old('email') }}"
                   required>

            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Phone --}}
        <div class="col-md-6">
           <input type="tel"
       name="phone"
       placeholder="Phone Number"
       value="{{ old('phone') }}"
       maxlength="20"
       inputmode="numeric"
       pattern="[0-9]{1,20}"
       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
       required>

            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

      {{-- College --}}
         <div class="col-md-6">
            <input type="tel"
                   name="location"
                   placeholder="Location"
                   value="{{ old('location') }}"
                   required>

            @error('location')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        {{-- Technology / Course --}}
        <div class="col-md-12">
            <!-- <input type="text"
                   name="technology"
                   placeholder="Course Name"
                   value="{{ old('technology') }}"
                   required> -->
                   <select name="technology" required>
                        <option value="" disabled {{ old('technology') ? '' : 'selected' }}>
                            Select Service
                        </option>

                        <option value="Campusedgepro" {{ old('technology') == 'Website Development' ? 'selected' : '' }}>
                            Campusedgepro
                        </option>

                        <option value="BlogerzWorld" {{ old('technology') == 'E-commerce Development' ? 'selected' : '' }}>
                            BlogerzWorld
                        </option>

                        <option value="InventoryManageSuite" {{ old('technology') == 'SEO Services' ? 'selected' : '' }}>
                            InventoryManageSuite
                        </option>

                        <option value="AllmartX" {{ old('technology') == 'Google Ads' ? 'selected' : '' }}>
                            AllmartX
                        </option>

                        <option value="Prop99X" {{ old('technology') == 'Social Media Marketing' ? 'selected' : '' }}>
                            Prop99X
                        </option>
                        <option value="Siterankify" {{ old('technology') == 'Social Media Marketing' ? 'selected' : '' }}>
                            Siterankify
                        </option>
                    </select>

            @error('technology')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
        {{-- Message --}}
        <div class="col-md-12">
            <textarea name="message"
                      placeholder="Message (optional)">{{ old('message') }}</textarea>

            @error('message')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Submit --}}
        <div class="col-12 text-center">
            <button type="submit" class="form-submit">
                Request Demo
            </button>
        </div>

        {{-- Success Message --}}
        @if(session('success'))
            <div class="col-12 text-center">
                <div id="formMessage" class="text-success" style="margin-top: 15px; font-weight:600;">
                    {{ session('success') }}
                </div>
            </div>
        @endif

    </div>

</form>




        </div>
      </div>

    </div>
  </div>
</section>

<section class="section about-section bg-light">
  <div class="container">
    <div class="row align-items-center g-5">

      <!-- Text Content -->
      <div class="col-md-6">
        <h2 class="section-title mb-3">
          Complete Real Estate ERP for Builders & Agents
        </h2>

        <p>
          <strong>Prop99X</strong> by <strong>SortIQ Solutions</strong> is a modern property management ERP designed for real estate companies, builders, and brokers.
        </p>

        <p>
          It simplifies property listings, lead management, sales tracking, and client communication—all from one centralized dashboard.
        </p>

        <a href="#" class="btn btn-primary mt-3">Request Demo</a>
      </div>

      <!-- Image / Illustration -->
      <div class="col-md-6 text-center">
        <img src="{{ asset('images/ads/campusedgepro.jpg') }}" alt="Campus ERP System" class="img-fluid rounded shadow">
      </div>

    </div>
    <div class="text-center mt-5">
      <p class="fw-semibold">
        👉  Real estate ERP systems centralize sales, property management, and financial operations in one platform, improving efficiency and decision-making.
      </p>
    </div>
  </div>
</section>
<section class="section features-section">
  <div class="container">
    
    <h2 class="section-title text-center mb-5">
      Everything You Need to Manage Properties
    </h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-building fs-1 mb-3"></i>
          <h5>Property Listing</h5>
          <p>Property Listing & Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-people fs-1 mb-3"></i>
          <h5>Lead & CRM</h5>
          <p>Lead & CRM Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-person-check fs-1 mb-3"></i>
          <h5>Buyer & Seller Tracking</h5>
          <p>Buyer & Seller Tracking</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-diagram-3 fs-1 mb-3"></i>
          <h5>Sales Pipeline</h5>
          <p>Deal & Sales Pipeline Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-search fs-1 mb-3"></i>
          <h5>Property Search</h5>
          <p>Property Search with Filters</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
          <h5>Document Management</h5>
          <p>Document & Agreement Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-credit-card fs-1 mb-3"></i>
          <h5>Payment Tracking</h5>
          <p>Payment & Installment Tracking</p>
        </div>
      </div>

    </div>

    <div class="text-center mt-5">
      <p class="fw-semibold">
        👉 ERP helps manage listings, tenant interactions, and transactions efficiently in one system.
      </p>
    </div>

  </div>
</section>

<section class="section benefits-section">
  <div class="container">

    <h2 class="section-title text-center mb-5">
      Key Benefits for Property Management
    </h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-primary h-100 text-center">
          <div class="card-body">
            <i class="bi bi-house fs-1 text-primary mb-3"></i>
            <h5>Easy Property Listing</h5>
            <p>Add and manage properties with complete details and images</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-success h-100 text-center">
          <div class="card-body">
            <i class="bi bi-people fs-1 text-success mb-3"></i>
            <h5>Lead Management</h5>
            <p>Capture, track, and convert property inquiries</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-warning h-100 text-center">
          <div class="card-body">
            <i class="bi bi-graph-up fs-1 text-warning mb-3"></i>
            <h5>Sales Tracking</h5>
            <p>Monitor deals, bookings, and revenue in real time</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-danger h-100 text-center">
          <div class="card-body">
            <i class="bi bi-search fs-1 text-danger mb-3"></i>
            <h5>Smart Search</h5>
            <p>Filter and find properties quickly with advanced search</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-info h-100 text-center">
          <div class="card-body">
            <i class="bi bi-file-earmark-text fs-1 text-info mb-3"></i>
            <h5>Document Management</h5>
            <p>Store agreements, documents, and client data securely</p>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card benefit-card border-dark h-100 text-center">
          <div class="card-body">
            <i class="bi bi-lightning-charge fs-1 text-dark mb-3"></i>
            <h5>Faster Closures</h5>
            <p>Streamline processes to close deals faster</p>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<section class="section why-section bg-light">
  <div class="container">
    
    <h2 class="section-title text-center mb-5">
      Why Choose Prop99X ERP?
    </h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-database fs-1 mb-3 text-primary"></i>
          <h5>Centralized Data</h5>
          <p>Centralized Property & Client Data</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-graph-up fs-1 mb-3 text-success"></i>
          <h5>Deal Tracking</h5>
          <p>Real-Time Deal Tracking</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-lightning-charge fs-1 mb-3 text-warning"></i>
          <h5>Automated Workflows</h5>
          <p>Automated Workflows & Notifications</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-people fs-1 mb-3 text-danger"></i>
          <h5>Multi-User Access</h5>
          <p>Multi-User Access for Teams</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-cloud fs-1 mb-3 text-info"></i>
          <h5>Cloud-Based Platform</h5>
          <p>Cloud-Based & Secure Platform</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-arrows-expand fs-1 mb-3 text-dark"></i>
          <h5>Scalable Solution</h5>
          <p>Scalable for Agencies & Builders</p>
        </div>
      </div>

    </div>

    <div class="text-center mt-5">
      <p class="fw-semibold">
        👉 Modern proptech platforms automate workflows like leasing, payments, and client management.
      </p>
    </div>

  </div>
</section>

<section class="section features-section">
  <div class="container">
    
    <h2 class="section-title text-center mb-5">
      Complete Real Estate Modules
    </h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-building fs-1 mb-3"></i>
          <h5>Property Management</h5>
          <p>Property Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-people fs-1 mb-3"></i>
          <h5>CRM & Leads</h5>
          <p>CRM & Lead Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-diagram-3 fs-1 mb-3"></i>
          <h5>Sales & Booking</h5>
          <p>Sales & Booking Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-credit-card fs-1 mb-3"></i>
          <h5>Payments</h5>
          <p>Payment & Installments</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
          <h5>Documents & Contracts</h5>
          <p>Document & Contract Management</p>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm p-3">
          <i class="bi bi-bar-chart-line fs-1 mb-3"></i>
          <h5>Reports & Analytics</h5>
          <p>Reports & Analytics</p>
        </div>
      </div>

    </div>

    <div class="text-center mt-5">
      <p class="fw-semibold">
        👉 ERP modules connect sales, CRM, and financial tracking for real estate businesses.
      </p>
    </div>

  </div>
</section>

<section class="section how-section">
  <div class="container">

    <h2 class="section-title text-center mb-5">
      Simple Workflow, Powerful Results
    </h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card how-card text-center h-100">
          <div class="card-body">
            <div class="step-badge">1</div>
            <i class="bi bi-building fs-1 mb-3 text-primary"></i>
            <h5>Add Property Listings</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card how-card text-center h-100">
          <div class="card-body">
            <div class="step-badge">2</div>
            <i class="bi bi-people fs-1 mb-3 text-success"></i>
            <h5>Capture Leads Automatically</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card how-card text-center h-100">
          <div class="card-body">
            <div class="step-badge">3</div>
            <i class="bi bi-chat-dots fs-1 mb-3 text-warning"></i>
            <h5>Manage Client Interactions</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card how-card text-center h-100">
          <div class="card-body">
            <div class="step-badge">4</div>
            <i class="bi bi-credit-card fs-1 mb-3 text-danger"></i>
            <h5>Track Deals & Payments</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card how-card text-center h-100">
          <div class="card-body">
            <div class="step-badge">5</div>
            <i class="bi bi-bar-chart-line fs-1 mb-3 text-info"></i>
            <h5>Analyze Performance</h5>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>

<section class="section target-section">
  <div class="container">

    <h2 class="section-title text-center mb-5">
      Built For
    </h2>

    <div class="row g-4 text-center">

      <div class="col-md-4 col-sm-6">
        <div class="card target-card bg-primary-subtle h-100">
          <div class="card-body">
            <i class="bi bi-person-badge fs-1 text-primary mb-3"></i>
            <h5>Real Estate Agents</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card target-card bg-success-subtle h-100">
          <div class="card-body">
            <i class="bi bi-people fs-1 text-success mb-3"></i>
            <h5>Property Dealers & Brokers</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card target-card bg-warning-subtle h-100">
          <div class="card-body">
            <i class="bi bi-building fs-1 text-warning mb-3"></i>
            <h5>Builders & Developers</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card target-card bg-info-subtle h-100">
          <div class="card-body">
            <i class="bi bi-diagram-3 fs-1 text-info mb-3"></i>
            <h5>Real Estate Agencies</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card target-card bg-danger-subtle h-100">
          <div class="card-body">
            <i class="bi bi-briefcase fs-1 text-danger mb-3"></i>
            <h5>Property Consultants</h5>
          </div>
        </div>
      </div>

    </div>

  </div>
</section>


<section class="section cta-section text-center text-white">
  <div class="container">
    
    <h2 class="mb-3">Ready to Manage Properties Smarter?</h2>
    
    <p class="mb-4">
      Grow your real estate business with automation, insights, and full control using Prop99X.
    </p>

    <div class="d-flex justify-content-center gap-3 flex-wrap">
      <a href="#" class="btn btn-lg btn-light px-4 fw-bold">
        Book Demo
      </a>

      <a href="#" class="btn btn-lg btn-outline-light px-4 fw-bold">
        Contact Us
      </a>
    </div>

  </div>
</section>

 



 
 

<!-- FEEDBACK -->
<section class="section" style="background:#f7f7f7;">
<div class="container">

<h2 class="section-title">Client's Feedback</h2>

<div class="row g-4 text-center">

@forelse($testimonials as $item)

    <div class="col-md-4">
        <div class="info-box">

            {{-- IMAGE --}}
            @if($item->image && file_exists(public_path($item->image)))
                <img src="{{ asset($item->image) }}">
            @else
                <img src="{{ asset('images/placeholder_avatar.png') }}">
            @endif

            {{-- DESCRIPTION --}}
            <p>"{{ $item->description }}"</p>

            {{-- NAME --}}
            <strong>- {{ $item->name }}</strong>

        </div>
    </div>

@empty

    <div class="col-md-12">
        <p>No testimonials available.</p>
    </div>

@endforelse

</div>

</div>
</section>


<section style="background:#00163e;">
  <div class="container">
    <div class="row align-items-center text-white py-5">
 
   <div class="col-md-6 mb-3 mb-md-0">

      <div class="responsive-flex">

        <!-- TEXT -->
        <div>
          <h3 style="color:#fff;">Get Free Software Demo</h3>
          <p style="color:#ccc; margin-bottom:0;">
            List. Manage. Sell. All in One Place
          </p>
        </div>

        <!-- BUTTON (push right) -->
        <a href="#form"
           class="ms-auto"
           style="background:#ff6b00;color:#fff;padding:10px 20px;
           border-radius:12px;text-decoration:none;white-space:nowrap;">
           Request Demo
        </a>

      </div>

    </div>

@include('ads_front_pages.footer_address')
      <!-- RIGHT SIDE (Existing Content) -->
   <!--    <div class="col-md-6 text-center text-md-end">
        <p class="mb-1 fs-5 fw-semibold">
          E-51, Phase 8, Industrial Area, Mohali, Punjab - 160072
        </p>

        <div class="d-flex justify-content-md-end justify-content-center gap-3 flex-wrap">
          <span>
            <a href="tel:9646522110" class="text-white text-decoration-none">
              +91 9646522110 , +91 9501381389
            </a>
          </span>
          <span>
            <a href="mailto:sortiqsolutions@gmail.com" class="text-white text-decoration-none">
              sortiqsolutions@gmail.com
            </a>
          </span>
        </div>
      </div> -->

    </div>
  </div>
</section>
<div class="sticky-cta">
  <a href="#form" class="apply-btn">Request Demo</a>
</div>
<a href="https://wa.me/919646522110?text=Hello%20SortIQ%20Solutions%2C%20I%20want%20more%20details."
   class="whatsapp-float"
   id="whatsappBtn"
   target="_blank">
   <i class="fa-brands fa-whatsapp"></i>
</a>

</body>
<footer class="site-footer">
  <p>© 2026 | Sortiq Solutions Pvt. Ltd. | All Rights Reserved.</p>

</footer>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    let certIndex = 0;
    const track = document.querySelector(".certi-track");
    const items = document.querySelectorAll(".certi-item");

    function getVisible() {
        return window.innerWidth < 768 ? 1 : 3;
    }

    function updateSlider() {
        const visible = getVisible();
        const itemWidth = items[0].offsetWidth + 20;
        const maxIndex = items.length - visible;

        certIndex = Math.max(0, Math.min(certIndex, maxIndex));
        track.style.transform = `translateX(-${certIndex * itemWidth}px)`;
    }

    document.querySelector(".next").addEventListener("click", () => {
        const visible = getVisible();
        const maxIndex = items.length - visible;
        certIndex = certIndex >= maxIndex ? 0 : certIndex + 1;
        updateSlider();
    });

    document.querySelector(".prev").addEventListener("click", () => {
        const visible = getVisible();
        const maxIndex = items.length - visible;
        certIndex = certIndex <= 0 ? maxIndex : certIndex - 1;
        updateSlider();
    });

    window.addEventListener("resize", updateSlider);
    updateSlider();
});



</script>
 <script>
window.addEventListener("scroll", function () {
    const btn = document.getElementById("whatsappBtn");

    if (window.scrollY > 200) {
        btn.classList.add("show");
    } else {
        btn.classList.remove("show");
    }
});
</script>
 <script>
window.addEventListener("scroll", function () {
    const btn = document.getElementById("whatsappBtn");
    btn.classList.add("show");
     
});
</script>