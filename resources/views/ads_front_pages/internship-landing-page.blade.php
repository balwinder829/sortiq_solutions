<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@if(!empty($page->location))
    <meta name="geo.placename" content="{{ $page->location }}">
    <meta name="geo.region" content="IN">
@endif
<title>Internship | Sortiq Solutions Pvt Ltd</title>

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
  padding-bottom: 65px;
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
  /*background:url('https://sortiqsolutions.com/wp-content/uploads/2025/12/internship.avif') center/cover no-repeat;*/
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

/*.responsive-flex {
  display: flex;
  align-items: center;
  flex-wrap: wrap;
  gap: 10px;
}
*/
.responsive-flex {
  display: flex;
  align-items: center;
  justify-content: space-between;  /* normal case (1 line) */
  flex-wrap: wrap;
  gap: 10px;
}
/*
@media (max-width: 992px) {
  .responsive-flex {
    justify-content: center;    
    text-align: center;
  }
}*/
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
</style>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-5X8PFCR5');</script>
<!-- End Google Tag Manager -->
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-5X8PFCR5"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<!-- LOGO -->
<div class="site-header">
  <a href="/"><img src="images/front_ss-logo.png" alt="Sortiq"></a>
  <!-- <a href="/"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png" alt="Sortiq"></a> -->
</div>

<!-- HERO -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-8 hero-content">
         <h3>{{ $page->heading !== null && $page->heading !== '' 
    ? ucwords($page->heading) 
    : 'Kickstart Your IT Career with Real Project Internship' }}</h3>


    <!-- <p>{{ $page->content !== null && $page->content !== '' 
    ? ucwords($page->content) 
    : 'Work with experts, gain real project exposure & develop skills to become industry-ready.' }}</p>
         -->

    {!! $page->content !== null && $page->content !== '' 
    ? ucwords($page->content) 
    : 'Work with experts, gain real project exposure & develop skills to become industry-ready.' !!}
      
      </div>

      <!-- FORM -->
      <div class="col-lg-4" id="form">
        <div class="form-box">
          <h3 class="text-center">Register for Internship</h3>

 <form id="internshipForm"
      method="POST"
      action="{{ route('internship-registrations.store') }}">

    @csrf

    <input type="hidden" name="page_type" value="internship">
    <input type="hidden" name="slug" value="{{ $page->slug }}">
    <div class="row g-2">

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
                   maxlength="10"
                   pattern="[0-9]{10}"
                   inputmode="numeric"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                   placeholder="10 digit number"
                   required>

            @error('phone')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

       {{-- College --}}
        <div class="col-md-6">
            <input type="text"
                   name="college_name"
                   placeholder="College Name"
                   value="{{ old('college') }}"
                   required>
            <!-- <select name="college" class="form-control" required>
                <option value="" disabled selected>Select College</option>

                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ old('college') == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach
            </select> -->

            @error('college')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>


        {{-- Technology / Course --}}
        <div class="col-md-12">
            <!-- <select name="technology" class="form-control">
                <option value="" disabled selected>Select Technology / Domain</option>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ old('technology') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select> -->

            <input type="text"
                   name="technology"
                   placeholder="Course Name"
                   value="{{ old('technology') }}"
                   required>

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
                Enroll Now
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

<!-- WHY JOIN -->
<section class="section">
<div class="container">
<h2 class="section-title">WHY CHOOSE US</h2>
<div class="row g-4 text-center">
<div class="col-md-3"><div class="info-box"><i class="bi bi-cpu"></i><h5>Live Projects</h5><p>Machine Learning, Python, Chatbots, Research Work.</p></div></div>
<div class="col-md-3"><div class="info-box"><i class="bi bi-briefcase"></i><h5>Placement Assistance</h5><p>Resume, interviews & referral help.</p></div></div>
<div class="col-md-3"><div class="info-box"><i class="bi bi-award"></i><h5>Resume + Interview</h5><p>Experience letter with actual project exposure.</p></div></div>
<div class="col-md-3"><div class="info-box"><i class="bi bi-person-workspace"></i><h5>Industry Mentors</h5><p>Mentorship, career guidance, and industry insights.</p></div></div>
</div>
</div>
</section>


<!-- AREAS -->
<section class="internship-areas py-5 text-white services-area">
  <div class="container text-center">
    <h2 class="fw-bold">Internship Areas</h2>
    <p>Choose your field & learn with real developers.</p>

  <div class="row mt-4 g-4 text-start mx-auto" style="max-width:900px;">

  <div class="col-md-4">
    <ul>
      <li>Web Designing</li>
      <li>Web Development</li>
      <li>Digital Marketing</li>
      <li>React JS</li>
      <li>Python</li>
      <li>HR & Finance</li>
      <li>Graphic Designing</li>
      <li>WordPress (PHP)</li>
       <li>Mobile Development</li>
       <li>Business Analyst</li>
    </ul>
  </div>

  <div class="col-md-4">
    <ul>
      <li>Java</li>
      <li>.NET</li>
      <li>HR & Finance</li>
      <li>Networking</li>
      <li>Android Dev</li>
      <li>React Native</li>
      <li>PHP Full Stack</li>
      <li>Laravel</li>
      <li>Software Testing</li>
       <li>Flutter</li>
    </ul>
  </div>

  <div class="col-md-4">
    <ul>
      <li>AI / ML</li>
      <li>Machine Learning</li>
      <li>Data Science</li>
      <li>Shopify Dev</li>
      <li>Cloud Computing</li>
      <li>CodeIgniter</li>
      <li>AutoCAD</li>
      <li>MERN Full Stack</li>
      <li>Node JS</li>
      <li>DevOps</li>
     
     
    </ul>
  </div>

</div>

  </div>
</section>

<!-- TRUST/ SOCAIL PROOF -->
<!-- TRUST / SOCIAL PROOF -->
<section class="section">
  <div class="container">
    <h2 class="section-title text-center mb-4">TRUST / SOCIAL PROOF</h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-people-fill fs-1 mb-3"></i>
            <h5 class="card-title"><h5 class="card-title">
  <span class="counter" data-target="5000">0</span>+ Students Trained</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-code-slash fs-1 mb-3"></i>
            <h5 class="card-title">Live Projects Experience</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card h-100 text-center shadow-sm">
          <div class="card-body">
            <i class="bi bi-mortarboard-fill fs-1 mb-3"></i>
            <h5 class="card-title">Industry-Level Training</h5>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>


<section class="section urgency-section">
  <div class="container">
    <h2 class="section-title text-center mb-5">Limited Time Opportunity</h2>

    <div class="row g-4">

      <div class="col-md-4 col-sm-6">
        <div class="card urgency-card text-center h-100">
          <div class="card-body">
            <div class="icon-box">
              <i class="bi bi-calendar-check"></i>
            </div>
            <h5>Batch Starting Soon</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card urgency-card text-center h-100">
          <div class="card-body">
            <div class="icon-box">
              <i class="bi bi-exclamation-circle"></i>
            </div>
            <h5>Limited Seats Only</h5>
          </div>
        </div>
      </div>

      <div class="col-md-4 col-sm-6">
        <div class="card urgency-card text-center h-100">
          <div class="card-body">
            <div class="icon-box">
              <i class="bi bi-lightning-charge"></i>
            </div>
            <h5>Book Your Seat Today</h5>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

 

<section class="section" style="background:#f7f7f7;">
<div class="container">

<h2 class="section-title">Student Feedback</h2>

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

      <!-- LEFT SIDE (Your CTA) -->
      <!-- <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <h3 style="color:#fff;">Start Your IT Career Today</h3>
        <p style="color:#ccc;">45 Days Industrial Training with Live Projects</p>
        <a href="#form" 
           style="background:#ff6b00;color:#fff;padding:10px 20px;
           border-radius:12px;text-decoration:none;display:inline-block;">
           Apply Now
        </a>
      </div> -->
   <div class="col-md-6 mb-3 mb-md-0">

  <div class="responsive-flex">

    <!-- TEXT -->
    <div>
      <h3 style="color:#fff;">Start Your IT Career Today</h3>
      <p style="color:#ccc; margin-bottom:0;">
        45 Days Industrial Training with Live Projects
      </p>
    </div>

    <!-- BUTTON (push right) -->
    <a href="#form"
       class="ms-auto"
       style="background:#ff6b00;color:#fff;padding:10px 20px;
       border-radius:12px;text-decoration:none;white-space:nowrap;">
       Apply Now
    </a>

  </div>

</div>
@include('ads_front_pages.footer_address')
      
    </div>
  </div>
</section>

<div class="sticky-cta">
  <a href="#form" class="apply-btn">Apply Now</a>
  <a href="tel:+919646522110" class="call-btn">Call Now</a>
</div>
<a href="https://wa.me/919646522110?text=Hello%20SortIQ%20Solutions%2C%20I%20want%20more%20details."
   class="whatsapp-float show"
   id="whatsappBtn"
   target="_blank">
   <i class="fa-brands fa-whatsapp"></i>
</a>
<footer class="site-footer">
  <p>© 2026 | Sortiq Solutions Pvt. Ltd. | All Rights Reserved.</p>

  
</footer>
</body>





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
    btn.classList.add("show");
    // if (window.scrollY > -20) {
    //     btn.classList.add("show");
    // } else {
    //     btn.classList.remove("show");
    // }
});
</script>
 <script id="counterjs">
const counters = document.querySelectorAll('.counter');

const startCounting = (counter) => {
  const target = +counter.getAttribute('data-target');
  let count = 0;

  const update = () => {
    const increment = target / 100;

    if (count < target) {
      count += increment;
      counter.innerText = Math.ceil(count);
      requestAnimationFrame(update);
    } else {
      counter.innerText = target;
    }
  };

  update();
};

// Trigger when visible
const observer = new IntersectionObserver(entries => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      startCounting(entry.target);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

counters.forEach(counter => {
  observer.observe(counter);
});
</script>