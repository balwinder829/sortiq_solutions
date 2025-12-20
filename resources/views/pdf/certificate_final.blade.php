<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate of Training</title>
    {{-- Inline styling for PDF --}}
    <style>
      @page { size: A4 portrait; margin: 0; }



@font-face {
    font-family: 'Katibeh';
    font-style: normal;
    font-weight: 400;
    src: url('{{ public_path("fonts/Katibeh-Regular.ttf") }}') format('truetype');
}
body {
    margin:0px;
    padding:0px;
    font-family: "Inter", sans-serif;
    font-weight: 400;
    color: #2c2e35;
}

p {
    margin: 0;
    font-size: 16px;
    color: #2c2e35;
}

.main-title {
    font-family: "Katibeh", serif;
    text-align: center;
    font-size: 100px;
    font-weight: 400;
    color: #2c2e35;
    margin: 0 0 50px;
}

.wrapper {
    width: 100%;
    overflow: hidden;
    background-color: #fff;
}

.head-shape img {
    width: 100%;
    display: block;
}

.inner-container {
    padding: 0 5%;
}

.head-flex {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}

/* DOMPDF fallback for flex */
.head-flex,
.bdy-sr,
.hghlt {
    display: block !important;
}

.head-flex > div,
.bdy-sr > div,
.hghlt > div {
    display: inline-block;
    vertical-align: top;
    width: 49%; /* adjust width as needed */
}

.hghlt-left {
    width: 49%;
}

.hghlt-right {
    width: 49%;
    text-align: right;
    margin-top:250px;
    margin-right: 25px;
}

.head-main {
    padding-top: 60px;
}

.h-logo img {
    width: 100%;
    max-width: 300px;
}

.h-detials ul {
    margin: 0;
    padding: 0;
    list-style: none;
    left:1020px;
}

.h-detials ul li {
    display: flex;
    align-items: center;
    margin: 8px 0;
}

.h-detials ul li span {
    padding-left: 10px;
    color: #2c2e35;
    font-size: 16px;
}

.h-detials ul li img {
    width: 25px;
}

.certi-body {
    position: relative;
    padding-top: 60px;
}

/* .certi-body .watermark {
    position: absolute;
    bottom: 50px;       
    left: 50%;
    transform: translateX(-50%);
    width: 40%;       

} */
        .watermark {
            position: absolute;
            top: 200px;
            left: 0;
            right: 0;
            margin: 0 auto;
            width: 400px;
            opacity: 0.1;
            z-index: 0;
        }
.certi-body .content {
    position: relative;
    z-index: 1;          /* ensures content is above watermark */
}
.bdy-sr {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin: 0 0 40px;
}

.bdy-sr p {
    margin: 0;
    font-size: 18px;
}

.bdy-content p {
    font-size: 18px;
    line-height: 45px;
    margin: 0 0 25px;
}

.hghlt {
    margin: 326px 0 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding-right: 5%;
}

.hghlt-left h5 {
    margin:0;
    font-size: 20px;
}

.hghlt-right img {
    max-width: 210px;
}

.footer-shape img {
    width: 100%;
    display: block;
}

.footer-shape {
    margin-top: 150px;
}

.hghlt-right{
     position:absolute; 
     right:50px;
     bottom:30%;
}
.sr-right{
    right:150px;
}
    </style>
</head>
<body>

@php
    $durations = [
        20 => '21 Days', 
        13 => '2 Weeks', 
        29 => '4 Weeks', 
        44 => '6 Weeks',
        59 => '8 Weeks', 
        89 => '3 Months', 
        119 => '4 Months', 
        179 => '6 Months',
        269 => '9 Months', 
        364 => '1 Year'
    ];
@endphp

<div class="wrapper">
    {{-- Header shape --}}
    <div class="head-shape">
        <img src="{{ public_path('images/head-shape.png') }}" alt="Header Shape">
    </div>
    {{-- Header content --}}
    <div class="head-main">
        <div class="inner-container">
        <div class="head-flex" style="width:100%;">
            <div class="h-logo" style="display:inline-block;">
                <img src="{{ public_path('images/logo-sortiq.png') }}" alt="Logo">
            </div>
            <div class="h-detials" style="display:inline-block; float:right; text-align:right; width:300px;">
                <table cellspacing="0" cellpadding="5" style="border-collapse: collapse; width:100%;">
                    <tr>
                        <td><img src="{{ public_path('images/cl.png') }}" width="20" alt="Phone"></td>
                        <p>+91 96465 22110</p>
                    </tr>
                    <tr>
                        <td><img src="{{ public_path('images/email.png') }}" width="20" alt="Email"></td>
                        <p>info@sortiqsolutions.com</p>
                    </tr>
                    <tr>
                        <td><img src="{{ public_path('images/globe.png') }}" width="20" alt="Website"></td>
                        <p>www.sortiqsolutions.com</p>
                    </tr>
                </table>
            </div>
        </div>
        </div>
    </div>
    {{-- Certificate body --}}
    <div class="certi-body" style="position: relative; 
            background-image: url('{{ public_path('images/bg-shape.png') }}'); 
            background-repeat: no-repeat; 
            background-position: bottom 50px center; 
            background-size: 40%; 
            opacity: 0.05;">
        <!-- Watermark -->
            <!-- <img src="{{ public_path('images/bg-shape.png') }}" 
     alt="Watermark"> -->
            <div class="inner-container">
                <h4 class="main-title">Certificate of Training</h4>
                <div class="bdy-sr" style="width:100%; overflow:hidden;">
                    <div class="sr-left" style="display:inline-block;">
                        <p>S.No:  <strong>{{ $student->sno ?? '________' }}</strong></p>
                    </div>
                    <div class="sr-right" style="display:inline-block; float:right; text-align:right;">
                        <p>Date: <strong>{{ \Carbon\Carbon::now()->format('d-m-Y') }}</strong></p>
                    </div>
                </div>
                <div class="bdy-content">
                    <p>
                        This is to certify that <strong>Mr./Ms. {{ ucfirst($student->student_name) }}</strong>
                        from <strong>{{ $student->from_institute }}</strong> has undertaken an internship program of
                        <strong>{{ $durations[$student->duration] ?? $student->duration }}</strong> under the technical department from
                        <strong>{{ \Carbon\Carbon::parse($student->start_date)->format('d M Y') }}</strong> to
                        <strong>{{ \Carbon\Carbon::parse($student->end_date)->format('d M Y') }}</strong>
                        in <strong>{{ $student->technology }}</strong> from the company
                        <strong>"Sortiq Solutions Pvt. Ltd."</strong>.
                    </p>
                    <p>
                        During this period, he/she demonstrated a high level of professionalism, enthusiasm,
                        and a strong commitment to learning. Throughout the internship, he/she showed remarkable
                        growth and contributed significantly to the tasks assigned.
                    </p>
                    <p>
                        This certificate acknowledges the intern's commitment to professional development and
                        successful acquisition of the knowledge and skills presented during the program.
                    </p>
                    <p>We congratulate him/her on their achievement and wish continued growth and success.</p>
                    <div class="hghlt">
                        <div class="hghlt-left">
                            <h5>For Sortiq Solutions Pvt. Ltd.</h5>
                            <h5 style="margin-top:35px;">Human Resource Department</h5>
                        </div>
                        <div class="hghlt-right">
                            <img src="{{ public_path('images/certified.png') }}" alt="Certified">
                        </div>
                    </div>
                </div>
            </div>
    </div>
    {{-- Footer shape --}}
    <div class="footer-shape">
        <img src="{{ public_path('images/footer-shape-1.png') }}" alt="Footer Shape">
    </div>
</div>

</body>
</html>
