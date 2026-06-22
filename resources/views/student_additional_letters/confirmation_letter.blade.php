<!DOCTYPE html>
<html>
<head>
<title>Certificate</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>

</style>
</head>
<body>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">
	<div class="head-shape">
        <img style="width: 100%; display: block;" src="images/head-shape.png"/>
    </div>
	<div class="head-main" style="padding-top: 20px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="68%" align="left">
						<div class="h-logo">
							<img style="width: 100%; max-width: 200px;" src="{{ public_path('images/certificates_images/logo-sortiq.png' ) }}" width="200"/>
						</div>
					</td>
					<td width="32%" align="left">
						<div class="h-detials" style="max-width: 210px; width: 100%;">
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/cl.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block;width: 180px;">+91 96465 22110</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/email.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block; width: 180px;">info@sortiqsolutions.com</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/globe.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block; width: 180px;">www.sortiqsolutions.com</span></p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
   
	@php
		
    	$gender = strtolower($letter->student->gender ?? '');

		$title = ($gender === 'female')
		            ? ($letter->student->is_married ? 'Mrs.' : 'Ms.')
		            : 'Mr.';
		$sodo = ($gender === 'female') ? 'D/o' : 'S/o';
		
		$relation = ($gender === 'female')
		                ? ($letter->student->is_married ? 'W/O' : 'D/O')
		                : 'S/O';
    	use Carbon\Carbon;

	    // Safe session values
		$sessionStart = optional($letter->student->sessionData)->start_date 
		    ? Carbon::parse($letter->student->start_date)->format('d F Y') 
		    : '';

		$sessionEnd = optional($letter->student->sessionData)->end_date
		    ? Carbon::parse($letter->student->end_date)->format('d F Y')
		    : '';
		$issue_date = optional($letter->issue_date)
		    ? Carbon::parse($letter->issue_date)->format('d-m-Y')
		    : \Carbon\Carbon::now()->format('d-m-Y');

		// Safe college
		$collegename = optional($letter->student->collegeData)->college_name ?? '';

		// Safe duration
		$durationName = optional($letter->student->durationData)->name ?? '';
		$courseName = $letter->student->course_name ?? '';
		$sessionName = optional($letter->student->sessionData)->session_display_name ?? '';
		$collegename = $letter->student
	    ? (
	        $letter->student->is_place
	            ? ($letter->student->place ?: '-')
	            : ($letter->student->collegeData?->college_display_name ?: '-')
	    )
	    : '-';

		// $letter->semester consider as batch in this type of the form
	@endphp
	
	<div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">	
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Letter of Confirmation</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						To
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						The Co-Ordinator Training
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						{{ $collegename }}
					</td>
				</tr>
				
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Subject:</strong> Confirmation for Summer Internship
						<br>
						<br>
						<br>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						Dear Sir/Madam,
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
						This is to confirm that <strong>{{ $title }}</strong> <strong>{{ ucwords($letter->student->student_name) }}</strong>, {{ $sodo }} <strong>{{ ucwords($letter->student->f_name) }}</strong>, student of 
						{{$letter->semester}} of {{ $collegename }} having Roll No.
						<strong>{{$letter->roll_number}}</strong> will be undergoing training of <strong>{{ $courseName }}</strong> in <strong>Sortiq Solutions Pvt. Ltd.</strong>, at Mohali from
						<strong>{{ $sessionStart }}</strong>. We also confirm that we will provide a completion certificate on the Letter
						Head of the organization with Stamp and Signature of Training coordinator at the end of the
						training.
					</td>
				</tr>
			</table>
			 @include('student_letters_footer_logos.footer_content_with_contact', compact('issue_date'))
		</div>
	</div>
	
</div>

</body>
</html>