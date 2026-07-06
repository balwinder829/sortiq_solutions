<!DOCTYPE html>
<html>
<head>
<title>Experience letter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
}
</style>
</head>
<body>
@php
	$gender = strtolower($letter->employee->gender ?? '');
	$title = ($gender === 'female') ? ($letter->employee->is_married ? 'Mrs.' : 'Miss.') : 'Mr.';
@endphp
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
	<div class="certi-body" style=" background:url('../images/bg-shape.png')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 20px; padding-right: 20px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Experience Letter</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Date:</strong> {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h3 style="font-family: 'Inter', sans-serif; text-align: center; font-size: 22px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>TO WHOMSOEVER IT MAY CONCERN</h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This is to certify that <strong>{{ $title }} {{ ucwords($letter->employee->emp_name) }}</strong> was working with <strong>"Sortiq Solutions Pvt. Ltd."</strong> from <strong>{{ \Carbon\Carbon::parse($letter->employee->joining_date)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($letter->relieving_date)->format('d M Y') }}</strong> His/her last served designation was <strong>{{ ucwords($letter->employee->position) }}</strong> with the salary <strong>{{ optional($letter->employee->salaryStructure)->total_salary
						    ? number_format($letter->employee->salaryStructure->total_salary, 2)
						    : 'N/A'
						}}</strong> per month.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						Throughout his/her tenure, he/she demonstrated a high level of professionalism, dedication, and skill in carrying out their responsibilities. He/she contributed positively to the team and handled their tasks with great competence and efficiency consistently throughout.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
						His/her attention to detail and proactive approach to tasks set them apart as a valuable team member. His/her positive attitude, integrity, and willingness to go above and beyond contributed greatly to our organizational success
					</td>
				</tr>
				<tr>
					<td colspan="1" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						We wish him/her success in all their future endeavors.
					</td>
				</tr>
				<!-- SIGN OFF -->
                <tr><td height="10"></td></tr>
                <tr><td style="font-family:Inter;font-size:14px;"><strong>Warm Regards</strong>,</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Priyanka</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Manager – Human Resources</td></tr>
			</table>

			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
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
					<!-- <td width="30%" align="right">
						<div style="display:inline-block; width:100%;">
							<h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
						</div>
					</td> -->
				</tr>
			</table>
		</div>
	</div>
	
</div>

</body>
</html>
