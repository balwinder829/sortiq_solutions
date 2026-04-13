<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex, nofollow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@if(!empty($page->location))
    <meta name="geo.placename" content="{{ $page->location }}">
    <meta name="geo.region" content="IN">
@endif
<title>Services | Sortiq Solutions Pvt Ltd</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>

/* ========== LOGO HEADER ========= */
.site-header{
    position:absolute;
    top:20px; left:30px;
    z-index:2000;
}
.site-header img{ height:55px; }

/* ========== HERO SECTION ========= */
.hero-section {
  background:url('https://sortiqsolutions.com/wp-content/uploads/2025/12/internship.avif') center/cover no-repeat;
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
  border:none;border-radius:40px;
  color:#fff;padding:14px 35px;font-size:18px;font-weight:600;
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
    height:150px !important;
    resize:none;
}

/* Submit Button */
.form-submit{
    background:#ff5800;
    color:#fff;
    border:none;
    width:100%;
    padding:15px;
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
  background:#00163e;text-align:center;color:#fff;padding:22px 0;font-size:14px;margin-top:0;
  border-top:1px solid rgba(255,255,255,0.3);
}

/* Responsive */
@media(max-width:991px){
  .hero-content{text-align:center;padding-right:0;}
  .hero-btn{margin:auto;display:block;width:200px;}
  .form-box{margin-top:35px;}
  .hero-content h2{font-size:30px;}
  .hero-section{padding:80px 0;}
  .site-header img{height:45px;}
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
    padding: 25px 15px;
    color: #fff;
    font-size: 15px;
    position: relative;
}

/* Footer WhatsApp Button - Right Side */
.footer-whatsapp {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: #25D366;
    padding: 10px 18px;
    border-radius: 30px;
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.footer-whatsapp i {
    font-size: 20px;
}

.footer-whatsapp:hover {
    background: #1ebe5d;
}

/* Mobile Fix */
@media (max-width: 600px) {
    .footer-whatsapp {
        position: static;
        margin-top: 12px;
        transform: none;
        display: inline-block;
    }
}
form input,
form textarea,
form select {
    width: 100%;
    height: 52px;                /* 🔑 same height */
    padding: 0 16px;             /* 🔑 vertical handled by line-height */
    border-radius: 12px;
    border: 1px solid #dcdcdc;
    font-size: 15px;
    background: #fff;
    color: #333;
    outline: none;
    box-sizing: border-box;      /* 🔑 critical */
    line-height: 52px;           /* 🔑 centers text vertically */
}
form textarea {
    height: 150px !important;
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
  <a href="/"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/ss-logo.png" alt="Sortiq"></a>
</div>

<!-- HERO -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6 hero-content">
        <h2>{{ $page->heading !== null && $page->heading !== '' 
    ? ucwords($page->heading) 
    : 'Kickstart Your IT Career with Real Project Internship' }}</h2>


    <p>{{ $page->content !== null && $page->content !== '' 
    ? ucwords($page->content) 
    : 'Work with experts, gain real project exposure & develop skills to become industry-ready.' }}</p>

        <a href="tel:+919646522110" class="hero-btn">Call Now</a>
      
      </div>

      <!-- FORM -->
      <div class="col-lg-5 offset-lg-1" id="form">
        <div class="form-box">
          <h3 class="text-center">Service Request</h3>

      

 <form id="internshipForm"
      method="POST"
      action="{{ route('services-registrations.store') }}">

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
            <select name="technology" class="form-control" required>
                <option value="" disabled selected>Select Technology / Domain</option>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ old('technology') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
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
                Submit Application
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
<h2 class="section-title">Why Join This Internship?</h2>
<div class="row g-4 text-center">
<div class="col-md-4"><div class="info-box"><i class="bi bi-cpu"></i><h5>Live AI Projects</h5><p>Machine Learning, Python, Chatbots, Research Work.</p></div></div>
<div class="col-md-4"><div class="info-box"><i class="bi bi-briefcase"></i><h5>Placement Support</h5><p>Resume, interviews & referral help.</p></div></div>
<div class="col-md-4"><div class="info-box"><i class="bi bi-award"></i><h5>Certificate + Letter</h5><p>Experience letter with actual project exposure.</p></div></div>
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
      <li>Graphics</li>
      <li>Graphic Designing</li>
      <li>WordPress (PHP)</li>
       <li>Mobile Development</li>
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
      <li>Cloud</li>
      <li>CodeIgniter</li>
      <li>Testing</li>
      <li>MERN Full Stack</li>
      <li>Node JS</li>
      <li>DevOps</li>
     
     
    </ul>
  </div>

</div>

  </div>
</section>

<!-- FEEDBACK -->
<!-- <section class="section" style="background:#f7f7f7;">
<div class="container">
<h2 class="section-title">Student Feedback</h2>
<div class="row g-4 text-center">
<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/1.png"><p>"Got internship & placement in 2 months!"</p><strong>- Nitika</strong></div></div>


<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/6.png"><p>"Successfully placed at SortIQ Solutions after completing the internship."</p><strong>- Neha</strong></div></div>
<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/8.png"><p>"Portfolio ready - boosted my confidence!"</p><strong>- Manish</strong></div></div>


<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/2-1.png"><p>"Completed internship and secured placement at SortIQ Solutions based on excellent performance"</p><strong>- Anshika</strong></div></div>
<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/4.png"><p>"Offered a full-time role at SortIQ Solutions after a successful internship journey."</p><strong>- Aman</strong></div></div>

<div class="col-md-4"><div class="info-box"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/12/3.png"><p>"Portfolio ready - boosted my confidence!"</p><strong>- Usha</strong></div></div>
</div>
</div>
</section> -->

<!-- FEEDBACK -->
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

<!-- CERTIFICATIONS -->
<!--<section class="section" style="background:#00163e;">-->
<!--  <div class="container">-->

<!--    <h2 class="section-title text-white">Global Certifications & Recognitions</h2>-->

    <!--<div class="certi-slider">-->

    <!--  <button class="certi-btn prev">&#10094;</button>-->

      <!--<div class="certi-track">-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/15-300x300.png"></div>-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/12-300x300.png"></div>-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/4-300x300.png"></div>-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/9-300x300.png"></div>-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/10-300x300.png"></div>-->
      <!--  <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/8-300x300.png"></div>-->
      <!--   <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/6-300x300.png"></div>-->
      <!--    <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/2025/11/14-300x300.png"></div>-->
          
      <!--     <div class="certi-item"><img src="https://sortiqsolutions.com/wp-content/uploads/elementor/thumbs/wix-partner-rendr7au0yttdewf0w3mmbcwpb2d3jpuj8vy8thjyc.png"></div>-->
      <!--</div>-->

    <!--  <button class="certi-btn next">&#10095;</button>-->

    <!--</div>-->

<!--  </div>-->
<!--</section>-->

<!-- CONTACT INFO -->
<section style="background:#00163e;">
  <div class="container">
    <div class="row align-items-center text-white py-5">

      <!--<div class="col-md-6 justify-content-center gap-4 flex-wrap mb-3 mb-md-0">-->
      <!--  <img width="80" src="https://sortiqsolutions.com/wp-content/uploads/2025/11/digital-marketing-logo-min.png">-->
      <!--  <img width="60" src="https://sortiqsolutions.com/wp-content/uploads/2025/11/GF-min.png">-->
      <!--  <img width="60" src="https://sortiqsolutions.com/wp-content/uploads/2025/11/upwork-logo-min.png">-->
      <!--  <img width="60" src="https://sortiqsolutions.com/wp-content/uploads/2025/06/iso-certified-company-image.webp">-->
      <!--  <img width="60" src="https://sortiqsolutions.com/wp-content/uploads/2025/11/EN_legend_small.png">-->
      <!--</div>-->

      <div class="col-md-12 text-md-end text-center">
        <p class="mb-1 fs-5 fw-semibold">E-51, Phase 8, Industrial Area, Mohali, Punjab - 160072</p>

        <div class="d-flex justify-content-md-end justify-content-center gap-3">
          <span> <a href="tel:9646522110" class="text-white text-decoration-none">+91 9646522110 , +91 9501381389</a></span>
          <span> <a href="mailto:sortiqsolutions@gmail.com" class="text-white text-decoration-none">sortiqsolutions@gmail.com</a></span>
        </div>
      </div>

    </div>
  </div>
</section>
</body>
<footer class="site-footer">
  <p>© 2025 | Sortiq Solutions Pvt. Ltd. | All Rights Reserved.</p>

  <a href="https://wa.me/919646522110?text=Hello%20SortIQ%20Solutions%2C%20I%20want%20more%20details."
     class="footer-whatsapp"
     target="_blank">
     <i class="fa-brands fa-whatsapp"></i> Chat on WhatsApp
  </a>
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
 