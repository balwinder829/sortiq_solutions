<!DOCTYPE html>
<html>
<head>
<title>General Aptitude Test</title>
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
	<div class="head-shape">
		<img style="width: 100%; display: block;" src="images/head-shape-test.png"/>
	</div>
	<div class="head-main" style="padding-top: 50px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="68%" align="left">
						<div class="h-logo">
							<img style="width: 100%; max-width: 200px;" src="{{ public_path('images/certificates_images/logo-sortiq.png') }}" width="200"/>
						</div>
					</td>
					<td width="32%" align="right">
						<div class="h-detials" style="max-width: 220px; width: 100%;">
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/cl.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">+91 96465 22110</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/email.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">info@sortiqsolutions.com</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/certificates_images/globe.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">www.sortiqsolutions.com</span></p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.png') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>{{ ucwords($test->title) }}</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Name</strong> <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span>
					</td>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>College</strong> <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span>
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Course</strong> <span>_ _ _ _ _ _ _ _ _</span>
					</td>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Semester</strong> <span>_ _ _ _ _ _ _ _ _</span>
					</td>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Branch</strong> <span>_ _ _ _ _ _ _ _ _</span>
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Email</strong> <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ </span>
					</td>
					<td align="left" style="font-size: 16px; line-height: 26px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Contact no </strong> <span>_ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _ _</span>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h4 style="font-family: 'Inter', sans-serif; text-align: center; font-size: 20px; font-weight: 700; color: red; margin: 0 0 10px;"><strong>Note:</strong> test will be only 1:00 hr</h2>
					</td>
				</tr>
			</table>
			 @foreach($questions as $qIndex => $question)
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="4" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
						<strong>{{ $qIndex + 1 }}. {{ $question->question }}

                </strong> 
					</td>
				</tr>
				<tr>
					@foreach($question->options as $oIndex => $option)
					<td style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; width:25%;">
						{{ chr(97 + $oIndex) }}) {{ $option->option_text }}
					</td>
					 @endforeach
					 
				</tr>
			</table>
			 @endforeach
		 

			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
				<tr>
					<td width="70%">
						<div style="display:inline-block; width:100%;">
							<h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For Sortiq Solutions Pvt. Ltd. </h4><br>
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
	<div class="footer-shape" style="margin-top: 40px;">
		<img style="width: 100%; display: block;" src="images/footer-shape-1-test.png"/>
	</div>
</div>

</body>
</html>