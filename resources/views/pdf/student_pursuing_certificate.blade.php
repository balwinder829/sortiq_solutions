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
	
	<div class="head-main" style="padding-top: 110px;">
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
    	$gender = strtolower($student->gender ?? '');

		$title = ($gender === 'female')
		            ? ($student->is_married ? 'Mrs' : 'Miss')
		            : 'Mr';

		$relation = ($gender === 'female')
		                ? ($student->is_married ? 'W/O' : 'D/O')
		                : 'S/O';
    	use Carbon\Carbon;

	    // Safe session values
		$sessionStart = optional($student->sessionData)->start_date 
		    ? Carbon::parse($student->start_date)->format('d F Y') 
		    : '';

		$sessionEnd = optional($student->sessionData)->end_date
		    ? Carbon::parse($student->end_date)->format('d F Y')
		    : '';

		   $issue_date = optional($student->certificate_issue_date)
		    ? Carbon::parse($student->certificate_issue_date)->format('d-m-Y')
		    : \Carbon\Carbon::now()->format('d-m-Y');

		// Safe college
		$collegrealename = optional($student->collegeData)->college_name ?? '';

		// Safe duration
		$durationName = optional($student->durationData)->name ?? '';
		$courseName = $student->course_name ?? '';
		$sessionName = optional($student->sessionData)->session_display_name ?? '';
		$collegename = $student
		    ? (
		        $student->is_place
		            ? ($student->place ?: '-')
		            : (
		                $student->collegeData?->college_display_name
		                ?: $student->collegeData?->college_name
		                ?: '-'
		            )
		    )
		    : '-'; 
	@endphp
	<div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">	
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Certificate of Training</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>S.No:</strong> {{ ucwords($student->sno) }}
					</td>
				</tr>
				<tr>
					<td colspan="2" align="right" style="font-size: 14px; line-height: 24px; text-align:right; font-family: 'Inter', sans-serif;">
						<strong>Date: </strong>{{ $issue_date }}
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
						This is to certify that <strong>{{ $title }}</strong> <strong>{{ ucwords($student->student_name) }}</strong> From <strong>{{ $collegename }}</strong>
						Who is currently pursuing an internship program of <strong>{{ $courseName }}</strong> under technical department from <strong>{{ $sessionStart }}</strong>
						to <strong>{{ $sessionEnd }}</strong> in <strong>{{ ucwords($sessionName) }}</strong> from the company <strong>"Sortiq Solutions Pvt. Ltd."</strong>
					
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
						During this period, he/she is demonstrating a high level of professionalism, enthusiasm, and a strong commitment to learning. Throughout the internship, he/she is showing remarkable growth and contributed significantly to the assignment or task he/she worked on. He/she is gaining valuable hands-on experience in the area of their interest and expertise.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This certificate acknowledges the intern commitment to professional development and successful acquisition of the knowledge and skills presented during the program.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						We congratulate him/her on their achievement and wish continued growth and success.
					</td>
				</tr>
			</table>
			 @include('student_letters_footer_logos.footer_content')
			<!-- <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td width="100%">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                            <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;"/>
                            
                        </div>
                        <div style="display:inline-block; width:100%;">
                            <br>
                            <h3 style="font-size: 16px; font-family: 'Inter', sans-serif;">Human Resource Department</h3>
                        </div>
                    </td>
                    <td width="30%" align="right">
                    	@include('student_letters_footer_logos.footer_logos')
                        
                    </td>
                </tr>
            </table> -->
		</div>
	</div>
	
</div>

</body>
</html>