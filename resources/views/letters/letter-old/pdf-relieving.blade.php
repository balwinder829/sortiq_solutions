<!DOCTYPE html>
<html>
<head>
<title>Relieving letter</title>
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
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Relieving Letter</strong></h2>
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
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h3 style="font-family: 'Inter', sans-serif; text-align: center; font-size: 22px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>TO WHOMSOEVER IT MAY CONCERN</h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						To,
					</td>
				</tr>
				<tr>
					<td colspan="2" align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						Mr./Ms. {{ ucwords($letter->employee->emp_name) }}
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This is to formally acknowledge that <strong>Mr./Ms {{ ucwords($letter->employee->emp_name) }}</strong> was employed with <strong>"Sortiq Solutions Pvt. Ltd."</strong> from <strong>{{ \Carbon\Carbon::parse($letter->employee->joining_date)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($letter->relieving_date)->format('d M Y') }}</strong> as <strong>{{ ucwords($letter->employee->position) }}</strong>.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						We hereby confirm that he/she has been relieved from his/her duties effective <strong>{{ \Carbon\Carbon::parse($letter->relieving_date)->format('d M Y') }}</strong> and has no pending obligations towards the company.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
						During his/her tenure, Mr./Ms. <strong>{{ ucwords($letter->employee->emp_name) }}</strong> demonstrated dedication, professionalism, and a positive work attitude. We appreciate his/her contributions and wish him/her all the best in future endeavors.
					</td>
				</tr>
				<tr>
					<td colspan="1" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						We thank him/her for the services rendered to Sortiq Solutions Pvt. Ltd.
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
					<td width="30%" align="right">
						<div style="display:inline-block; width:100%;">
							<h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
</div>

</body>
</html>

