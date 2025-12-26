<!DOCTYPE html>
<html>
<head>
<title>Appointment Letter</title>
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
		<img style="width: 100%; display: block;" src="images/head-shape.png"/>
	</div>
	<div class="head-main" style="padding-top: 40px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="68%" align="left">
						<div class="h-logo">
							<img style="width: 100%; max-width: 200px;" src="images/logo-sortiq.png" width="200"/>
						</div>
					</td>
					<td width="32%" align="left">
						<div class="h-detials" style="max-width: 210px; width: 100%;">
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="images/cl.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">+91 96465 22110</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="images/email.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">info@sortiqsolutions.com</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="images/globe.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">www.sortiqsolutions.com</span></p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="certi-body" style=" background:url('../images/bg-shape.jpg')  no-repeat center; background-size:860px; padding-top: 60px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td colspan="2" style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Appointment Letter</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Date</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Mr./Ms</strong> {{ ucwords($letter->emp_name) }}
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Address:</strong> Plot No F-7, Industrial Area, Phase 8 Mohali, Chandigarh, 160055	
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Subject</strong> Appointment for the Position of {{ $letter->position }}
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						<strong>Dear</strong> {{ ucwords($letter->emp_name) }}
					</td>
				</tr>
				<tr>
					<td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
						Subsequent to your application and your interview with us, we are pleased to appoint you for the position of <strong>{{ $letter->position }}</strong> w.e.f date <strong>{{ \Carbon\Carbon::parse($letter->joining_date)->format('d M Y') }}</strong> based on the following conditions:
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Employment Terms:</strong> You are bonding with the company for a minimum period of {{ ucwords($letter->bond_period) }} year and the
							offered Salary Rs. {{ is_numeric($letter->salary) ? number_format($letter->salary, 2) : 'N/A' }}/- per month subjected to statutory deduction & TDS (If Applicable). You will
							be entitled to further review of your compensation as per the company practice. This will be linked to
							your performance and will be at the sole discretion of the management. Your salary and other
							benefits, if any, shall be subject to the deduction of all taxes, contributions etc.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Probation Period:</strong>{{ ucwords($letter->probation_period) }} Months will be The Probation Period in which if the
						company feels your performance is not satisfactory, the company can terminate the bond without
						giving any notice.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Statement of Responsibilities:</strong> The nature of work and responsibilities will be assigned and explained to you by your reporting officer from time to time. You are responsible for overseeing the implementation of this opportunity and the affirmative action plan.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Attendance:</strong> The company follows a strict time schedule and late comings are discouraged, unless otherwise notified by you in advance. Under special circumstances the director or staff member to whom authority has been delegated may ask you to work on holidays or extra time.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Personal Particulars:</strong> You will keep us informed of any change in your residential address, your family status or any other relevant particulars. You would also let us know the name and address of your legal heir/nominee.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Medical Fitness:</strong> Employment as per this offer is subject to your being medically fit. We are all committed to supporting a safe, healthy and positive workplace for the entire team of the company.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<br> <br><strong>Leave:</strong><br> 
						→ You can take a maximum of 12 Casual leaves in a financial year.<br>
						→ You can take a maximum of 12 Sick leaves in a financial year.<br>
						→ You're eligible for taking holidays as per the respective festival's calendar of the company or the
						 National gazetted.

					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Holidays:</strong> <br>
						→ Unapproved leaves will lead to deduction in salary.<br>
						→ Unauthorized absence or absence without permission from duty for a continuous period of 7 days
						would make you lose your lien on employment. In such case your employment shall automatically
						come to an end without any notice of 
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Termination or Notice Pay:</strong> Higher management approval in advance is necessary for all leaves
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Alternative Employment:</strong> As a full-time employee of the company, you are not permitted to undertake any other business, assume any public office, honorary or remunerative, without the written permission of the company.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Bond Term:</strong> As part of the joining formalities, you are required to sign a Bond, which aims to protect the intellectual property rights and business information of the company and its clients
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Nature of Work:</strong> You will work at a high standard of initiative, creativeness, efficiency and economy in the organization. We encourage open and honest communication between employees and employers.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Performance Management:</strong> We shall be the sole arbitrator of the assessment to be made of your working efficiency, utility or → loyalty to the company while taking a decision to give you increment or promotion to higher grade or terminating your services.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Disciplinary:</strong> He purpose of this is to ensure that all employees are treated fairly and consistently when addressing matters of misconduct or violations of company policies. The policy aims to maintain a productive and respectful work environment by clearly outlining the behaviors that are expected from employees and the process for dealing with inappropriate conduct.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Confidentiality:</strong> As an employee of the company, you will not utilize, disclose or divulge to any person(s), any information of the establishment, trade secret or know-how of the establishment.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Termination of Service:</strong> That you cannot leave the company during the bonding agreed period. If you wish to terminate the appointment after the bonding period you will have to give a prior one month notice to the company. If you wish to terminate the contract within the bonding period, you have to give up one month's salary, part of which will be adjusted from the security. Any negligence in the performance of your duties, intentional non-performance of the responsibilities, disobediences, disorderly behavior, dishonesty, indiscipline or any other conduct considered by us deterrent to our interest of or violation of any terms of the letter. Your abuse of alcohol or drug (legal or illegal) that, in the firm's reasonable judgment, materially impairs your ability to perform your duties. If you commit breach of any of the terms of this letter of appointment. Firm has the full right not to award you with an Experience Certificate on violating any of the company policies (termination from either side).
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Standing Orders:</strong> Standing Orders, rules & regulations and service conditions as in vogue from time to time shall be binding on you.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<br> <br> <strong>Code of Conduct:</strong> You should Conduct the Company's business with honesty and integrity and in a professional manner that protects the company's good public image and reputation. You have to become familiar with and comply with legal requirements and Company policy and procedures.Also, avoid any activities that could involve or lead to involvement in any unlawful practice or any harm to the Company's reputation or image. In the company, we appreciate, respect the rights, culture and dignity of all the individuals and adhere to the principles of equality and non-discrimination while dealing with any team member of the company.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Legal matter of concern:</strong> If at any time in our opinion, which is final in any matter you are found guilty of fraud, dishonest, disobedience, disorderly behavior, negligence, indiscipline, absence from duty without permission or any other conduct considered by us deterrent to our interest or of violation of one or more terms of this letter, your services may be terminated without notice and on account of reason of any of the acts or omission the company shall be entitled to recover the damages from you.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>Non Declaration:</strong> You are bound to sign, obey and follow the Non Declaration whereby you certify that you won't disclose any of the company's information outside nor misuse it after getting relieved from the company.<br>
						Note: The company has the right to amend or modify any of the above terms and conditions and the same become automatically binding on you from such date(s) as may be decided by the company.<br>
						We welcome you again and look forward to a long and useful association. Please sign and return to the undersigned the duplicate copy of this letter signifying your acceptance.
					</td>
				</tr>
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>We congratulate you on your appointment and wish you a great career with us.</strong>
					</td>
				</tr>
			</table>
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
	<div class="footer-shape" style="margin-top: 407px;position: fixed; bottom: 0px;">
		<img style="width: 100%; display: block;" src="images/footer-shape-1.png"/>
	</div>
</div>

</body>
</html>