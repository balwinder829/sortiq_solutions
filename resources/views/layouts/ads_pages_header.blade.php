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
<link rel="icon" type="image/jpeg" href="{{ asset('certificate_fav.jpeg') }}">

<style>

    .whatsapp-float {
    position: fixed;
    bottom: 25px;
    right: 20px;
    background: #25D366;
    color: #fff;
    padding: 14px 18px;
    border-radius: 50px;
    font-size: 16px;
    font-weight: 600;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.25);
    z-index: 9999;

    opacity: 0;
    transform: translateY(20px);
    transition: all .4s ease;
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