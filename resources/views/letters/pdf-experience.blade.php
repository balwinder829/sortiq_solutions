<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Experience Letter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff; padding-top: 130px">
	
	<div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>EXPERIENCE LETTER</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This is to certify that <strong>{{ $letter->emp_name }}</strong> (Employee Code: <strong>{{ $letter->emp_code ?? 'N/A' }}</strong>) was employed with our organization as <strong>{{ $letter->position }}</strong> from <strong>{{ \Carbon\Carbon::parse($letter->joining_date)->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($letter->relieving_date)->format('d M Y') }}</strong>.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						During the tenure of employment, {{ $letter->emp_name }} carried out the assigned responsibilities with sincerity, professionalism, and commitment. The employee demonstrated a positive attitude and maintained good conduct throughout the period of service.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						The total duration of employment with the organization is <strong>{{ $letter->experience_time }}</strong>.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This letter is being issued upon the employee’s request for whatever purpose it may serve.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						Issued on: <strong>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</strong>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
				<tr>
					<td colspan="2" style="font-size: 16px; line-height: 28px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>For ________________________</strong><br>
						<strong>HR Department</strong><br>
						Authorized Signatory
					</td>
				</tr>
			</table>
		</div>
	</div>
	
</div>
</body>
</html>
