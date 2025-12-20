<!DOCTYPE html>
<html>
<head>
<title>Certificate</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
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

.head-main {
    padding-top: 50px;
}

.h-logo img {
    width: 100%;
    max-width: 300px;
}

.h-detials ul {
    margin: 0;
    padding: 0;
    list-style: none;
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
    padding-top: 60px;
    background-image: url("../images/bg-shape.png");
    background-repeat: no-repeat;
    background-size: 40%;
    background-position: bottom 50px center;
}

.bdy-sr {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin: 0 0 40px;
}

.bdy-sr p {
    margin: 0;
    font-size: 16px;
}

.bdy-content p {
    font-size: 16px;
    line-height: 45px;
    margin: 0 0 25px;
}

.hghlt {
    margin: 150px 0 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding-right: 5%;
}

.hghlt-left h4 {
    margin: 0;
    font-size: 26px;
}

.hghlt-left h3 {
    margin: 90px 0 0;
    font-size: 28px;
}

.hghlt-right img {
    max-width: 210px;
}
.footer-shape img {
    width: 100%;
    display: block;
}

.footer-shape {
    margin-top: 110px;
}
</style>
</head>
<body>

<div class="wrapper">
	<div class="head-shape">
		<img src="{{ public_path('images/certificates_images/head-shape.png' ) }}"/>
	</div>
	<div class="head-main">
		<div class="inner-container">
			<div class="head-content">
				<div class="head-flex">
					<div class="h-logo">
						<img src="{{ public_path('images/certificates_images/logo-sortiq.png' ) }}"/>
					</div>
					<div class="h-detials">
						<ul>
							<li><img src="{{ public_path('images/certificates_images/cl.png') }}"/><span>+91 96465 22110<span></li>
							<li><img src="{{ public_path('images/certificates_images/email.png') }}"/><span>info@sortiqsolutions.com<span></li>
							<li><img src="{{ public_path('images/certificates_images/globe.png') }}"/><span>www.sortiqsolutions.com<span></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
	@php
    	$title = $student->gender === 'female' ? 'Miss' : 'Mr';
    	$relation = $student->gender === 'female' ? 'D/O' : 'S/O';
    	use Carbon\Carbon;

	    // Safe session values
		$sessionStart = optional($student->sessionData)->start_date 
		    ? Carbon::parse($student->sessionData->start_date)->format('F Y') 
		    : '';

		$sessionEnd = optional($student->sessionData)->end_date
		    ? Carbon::parse($student->sessionData->end_date)->format('F Y')
		    : '';

		// Safe college
		$collegename = optional($student->collegeData)->college_name ?? '';

		// Safe duration
		$durationName = optional($student->durationData)->name ?? '';
		$courseName = optional($student->courseData)->course_name ?? '';
	@endphp
	<div class="certi-body">
		<div class="inner-container">
			<h2 class="main-title">Certificate of Training</h2>
			<div class="bdy-sr">
				<div class="sr-left">
					<p>S.No {{ ucwords($student->sno) }}</p>
				</div>
				<div class="sr-right">
					<p>Date {{ \Carbon\Carbon::now()->format('d-m-Y') }}</p>
				</div>
			</div>
			<div class="bdy-content">
				<p>This is to certify that <strong>Mr./Ms</strong>. <strong>{{ ucwords($student->student_name) }}</strong> From <strong>{{ $collegename }}</strong> 
Who has undertaken an internship program of <strong>{{ $courseName }}</strong> under technical department from <strong>{{ $sessionStart }} </strong>
to <strong>{{ $sessionEnd }}</strong> in <strong>{{ $durationName }}</strong> from the company <strong>"Sortiq Solutions Pvt. Ltd."</strong></p>
				<p>During this period, he/she demonstrated a high level of professionalism, enthusiasm, and a strong commitment to learning.
Throughout the internship, he/she has shown remarkable growth and contributed significantly to the assignment or task
he/she worked on. He/she has gained valuable hands-on experience in the area of their interest.</p>
				<p>This certificate acknowledges the intern commitment to professional development and successful acquisition of the knowledge and skills presented during the program.</p>
				<p>We congratulate him/her on their achievement and wish continued growth and success.</p>
				
				<div class="hghlt">
					<div class="hghlt-left">
						<h4>For Sortiq Solutions Pvt. Ltd.</h4>
						<h3>Human Resource Department</h3>
					</div>
					<div class="hghlt-right">
						<img src="{{ public_path('images/certificates_images/certified.png' ) }}"/>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="footer-shape">
		<img src="{{ public_path('images/certificates_images/footer-shape-1.png' ) }}"/>
	</div>
</div>

</body>
</html>