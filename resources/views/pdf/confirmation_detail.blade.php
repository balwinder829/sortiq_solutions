<!DOCTYPE html>
<html>
<head>
<title>Confirmation Letter</title>
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
.ct-main-title {
    font-family: "Katibeh", serif;
    text-align: center;
    font-size: 80px;
    font-weight: 400;
    color: #2c2e35;
    margin: 0 0 50px;
}
.wrapper {
    width: 100%;
    overflow: hidden;
    background-color: #fff;
}

.ct-head-shape img {
    width: 100%;
    display: block;
}

.inner-container {
    padding: 0 5%;
}

.ct-head-flex {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-end;
}

.ct-head-main {
    padding-top: 50px;
}

.ct-h-logo img {
    width: 100%;
    max-width: 300px;
}
.ct-certi-body {
    padding-top: 60px;
}

.ct-bdy-sr {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    margin: 0 0 40px;
}

.ct-bdy-sr p {
    margin: 0;
    font-size: 16px;
}

.ct-bdy-content p {
    font-size: 18px;
    line-height: 28px;
    margin: 0 0 25px;
}

.ct-hghlt {
    margin: 110px 0 0;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding-right: 5%;
}
.ct-hghlt-left img {
    width: 100%;
    max-width: 380px;
    display: block;
    margin: 0 0 25px;
}
.ct-hghlt-left h4 {
    margin: 0;
    font-size: 20px;
    line-height: 28px;
}

.ct-hghlt-left h3 {
    margin: 90px 0 0;
    font-size: 28px;
}

.ct-hghlt-right img {
    max-width: 210px;
}
.ct-footer-shape img {
    width: 100%;
    display: block;
}

.ct-footer-shape {
    margin-top: 110px;
}
</style>
</head>
<body>
@php 
	 
 @endphp
<div class="wrapper">
	<div class="head-shape">
		<img src="{{ public_path('images/confirmation_images/head-shape.png') }}"/>
	</div>
	<div class="ct-head-main">
		<div class="inner-container">
			<div class="ct-head-content">
				<div class="ct-head-flex">
					<div class="ct-h-logo">
						<img src="{{ public_path('images/confirmation_images/logo-sortiq.png') }}"/>
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
	@endphp
	<div class="ct-certi-body">
		<div class="inner-container">
			<h2 class="ct-main-title">CONFIRMATION LETTER</h2>
			<div class="ct-bdy-content">
				<p>To</br>
				Training & Placement Officer</br>
				{{ $collegename }}</p>
				<p>Subject: <strong>CONFIRMATION OF {{ strtoupper($durationName) }} INDUSTRIAL TRAINING</strong></p>
				<p>Dear Sir/Madam,</p>
				<p>We are pleased to confirm that {{ $title }} <strong>{{ ucwords($student->student_name) }},</strong> {{ $relation }} <strong>{{ ucwords($student->f_name) }}</strong> and a student of
your esteemed institution, has been enrolled in our {{ $durationName }} industrial training for the session <strong>{{ $sessionStart }} to {{ $sessionEnd }}</strong></p>
				<p>The candidate's performance will be closely evaluated by the management throughout the
duration of the internship.</p>

				<div class="ct-hghlt">
					<div class="ct-hghlt-left">
						<p>Regards</p>
						<img src="{{ public_path('images/confirmation_images/stamp-signatue.png') }}"/>
						<h4>HR Manager</h4>
						<h4>Priyanka</h4>
						<h4>M: +91-9501381389</h4>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="ct-footer-shape">
		<img src="{{ public_path('images/confirmation_images/footer-shape-1.png') }}"/>
	</div>
</div>

</body>
</html>