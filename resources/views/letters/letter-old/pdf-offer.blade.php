<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">	
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .signature {
            margin-top: 50px;
        }
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
	
	<div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Offer Letter</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
						Date: <strong>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
						Subject: <strong>Offer Letter</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
						Dear <strong>{{ ucwords($letter->employee->emp_name) }}</strong>,
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						With reference to your application and subsequent interview, we are delighted to extend an offer to you to join Sortiq Solutions Pvt. Ltd. as position of <strong>{{ ucwords($letter->employee->position) }}</strong> effective from <strong>{{ \Carbon\Carbon::parse($letter->employee->joining_date)->format('d M Y') }}.</strong> Your salary package will be Rs. <strong>{{ optional($letter->employee->salaryStructure)->total_salary
						    ? number_format($letter->employee->salaryStructure->total_salary, 2)
						    : 'N/A'
						}}</strong>/- per month (With applicable taxes).
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						After carefully considering your skills, experience, and the passion you demonstrated during the interview process, we are confident that you will be an invaluable addition to our team. 
					</td>
				</tr>
				<tr>
				    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
				        <b>You are requested to share the following documents (Scanned / Hard Copy) for your joining:</b>

				        <ol style="margin-top:10px; padding-left:20px;">
				            <li>One Passport size Photograph.</li>
				            <li>Copy of all the Educational Certificates.</li>
				            <li>Copy of PAN card and Aadhaar card.</li>
				            <li>Copy of Experience &amp; Relieving letter from the last employer.</li>
				            <li>Copy of Last 3 Months Salary Slips from the last employer.</li>
				        </ol>
				    </td>
				</tr>

				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
						<b>Terms & Conditions</b>
					</td>
				</tr>


				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						Your appointment will be subject to the Company’s rules, regulations, policies, and procedures as amended from time to time. You will be required to comply with all internal policies, including confidentiality, information security, and code of conduct applicable to your role.
					</td>
				</tr>


				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">	
						During the course of your employment, you may have access to confidential and proprietary information of the Company and its clients. You shall not, during or after your employment, disclose such information to any third party without prior written consent of the Company.
					</td>
				</tr>

				
				<tr>	
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">	
						Your employment shall be subject to satisfactory verification of documents and background credentials submitted by you. Any discrepancy found at any stage may result in termination of employment without prior notice.
					</td>
				</tr>
				
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						You will be expected to perform your duties with honesty, integrity, and diligence and to devote your full working time and attention to the business of the Company.
					</td>
				</tr>
			</table>
				<pagebreak />
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">


				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						This offer of employment does not constitute a contract for a fixed term and may be terminated by either party in accordance with the Company’s employment policies.
					</td>
				</tr>


				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						All terms and conditions of employment shall be governed by and construed in accordance with the laws of India.
						after document list add this  adjust in tpow pages
					</td>
				</tr>


				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<b>Kindly sign a copy of this letter in acceptance of this offer. Offer made as above will stand withdrawn consequent upon your failure to communicate with us by the date given.</b>
					</td>
				</tr>
				</table>
				
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
				<tr>
					<td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               padding-top:10px;
                               font-family:'Inter', sans-serif;">
						We congratulate you and wish you a great career with us. We look forward to embarking on this next chapter together!
					</td>
				</tr>
				<tr><td height="10"></td></tr>
                <tr><td style="font-family:Inter;font-size:14px;"><strong>Warm Regards</strong>,</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Priyanka</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Manager – Human Resources</td></tr>
				<!-- <tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						Warm Regards,
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<b>Sortiq Solutions Pvt. Ltd.</b>

					</td>
				</tr> -->
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
			<!-- <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
				<tr>
					<td colspan="2" style="font-size: 16px; line-height: 28px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						Yours sincerely,<br>
						<strong>For ________________________</strong><br>
						<strong>HR Department</strong><br>
						Authorized Signatory
					</td>
				</tr>
			</table> -->
		</div>
	</div>
</div>

</body>
</html>
