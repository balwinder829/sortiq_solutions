<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Salary Slip</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">	
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
			margin:0px;
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
	<div class="head-shape" style="position: fixed; top: 0;">
		<img style="width: 100%; display: block;" src="{{ public_path('images/salary_images/head-shape-test.png') }}"/>
	</div>
	<div class="head-main" style="padding-top: 0px;">
		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td width="68%" align="left">
						<div class="h-logo">
							<img style="width: 100%; max-width: 200px;" src="{{ public_path('images/salary_images/logo-sortiq.png') }}" width="200"/>
						</div>
					</td>
					<td width="32%" align="left">
						<div class="h-detials" style="max-width: 210px; width: 100%;">
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/salary_images/cl.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block;width: 180px;">+91 96465 22110</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/salary_images/email.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block; width: 180px;">info@sortiqsolutions.com</span></p>
							<p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ public_path('images/salary_images/globe.png') }}" style="width:15px; margin-top:2px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px; display: inline-block; width: 180px;">www.sortiqsolutions.com</span></p>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	
	<div class="certi-body" style=" background:url('{{ public_path('images/salary_images/bg-shape.png') }}')  no-repeat center; background-size:860px; padding-top: 20px;">

		<div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
			<table width="100%" cellpadding="0" cellspacing="0">
				<tr>
					<td  style="text-align: center;">
						<h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Salary Slip</strong></h2>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">
				<tr>
					<td width="68%" align="left" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						MONTH: <strong>{{ $month }}, {{ $year }}</strong>
					</td>
					<td width="32%" align="left" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: right;">
						DOJ: <strong>{{ \Carbon\Carbon::parse($employee->joining_date)->format('d-M-Y') }}</strong>
					</td>
				</tr>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px; border-collapse: collapse;;">
				<thead>
					<tr>
						<td colspan="4" width="100%" style="font-size: 14px; line-height: 15px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>EMPLOYEE DETAILS</strong>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>EMPLOYEE CODE</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $employee->emp_code }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>PAID DAYS</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $paidDays }}
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>NAME</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $employee->emp_name }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>PAY MODE</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $payMode }}

						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>DESIGNATION</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $employee->position }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>A/C NO.</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ $salary->account_number }}
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px; border-collapse: collapse;;">
				<thead>
					<tr>
						<td colspan="7" width="100%" style="font-size: 14px; line-height: 14px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>LEAVE DETAILS</strong>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>Leave Type</strong>
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Opening Balance
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>Comp. Leaves Acc.</strong>
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Monthly Leaves Accrued
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Total Leaves Accrued
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Leaves Availed
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Closing Balance
						</td>
					</tr>
					<tr>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>CL</strong>
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
						<td width="14.2%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							-
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px; border-collapse: collapse;;">
				<thead>
					<tr>
						<td colspan="7" width="100%" style="font-size: 14px; line-height: 24px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px; text-align:center;">
							<strong>SALARY DETAILS</strong>
						</td>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>EARNINGS</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>RS.</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>DEDUCTIONS</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>RS</strong>
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							BASIC
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($salary->basic_salary, 2) }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							TDS
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							HRA
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($salary->hra, 2) }}

						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Leaves
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Medical Allow.
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							ECB
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Special Allowances
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($salary->allowance, 2) }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							RETENTION
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Professional Tax
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							0
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							GROSS PAY
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($grossSalary, 2) }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							Other Ded.
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($deduction, 2) }}
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							NET PAY
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($netSalary, 2) }}
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							
						</td>
					</tr>
					<tr>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							TO BE PAID
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							<strong>{{ number_format($netSalary, 2) }}</strong>
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							TOTAL
						</td>
						<td width="25%" style="font-size: 14px; line-height: 18px; padding: 5px; font-family: 'Inter', sans-serif; border: 1px solid #d1d1d1; padding-left: 10px; padding-right: 10px;">
							{{ number_format($deduction, 2) }}
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
				<tr>
					<td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
						<strong>For Sortiq Solutions Pvt. Ltd.</strong>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<img style="max-width: 180px; width:100%;" src="{{ public_path('images/salary_images/salary-slip-signature.png') }}"/>
					</td>
				</tr>
			</table>

		</div>
	</div>
	<!-- <div class="footer-shape" style="margin-top: 40px;">
		<img style="width: 100%; display: block;" src="{{ public_path('images/salary_images/footer-shape-1-test.png') }}"/>
	</div> -->
</div>

</body>
</html>
