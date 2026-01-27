<!DOCTYPE html>
<html>
<head>
<title>Salary Slip</title>

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

<div class="wrapper" style="width:100%; overflow:hidden; background-color:#fff;">

    {{-- HEADER SHAPE --}}
    <div class="head-main" style="padding-top: 100px;">
        <div class="inner-container" style="padding-left:30px; padding-right:30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="68%" align="left">
                        <img style="width:100%; max-width:200px;"
                             src="{{ public_path('images/certificates_images/logo-sortiq.png') }}"
                             width="200"/>
                    </td>
                    <td width="32%" align="left">
                        <div style="max-width:210px;">
                            <p style="margin:0;font-size:14px;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/cl.png') }}" style="width:15px;"/>
                                &nbsp;<span style="position:relative;top:-2px;">+91 96465 22110</span>
                            </p>
                            <p style="margin:0;font-size:14px;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/email.png') }}" style="width:15px;"/>
                                &nbsp;<span style="position:relative;top:-2px;">info@sortiqsolutions.com</span>
                            </p>
                            <p style="margin:0;font-size:14px;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/globe.png') }}" style="width:15px;"/>
                                &nbsp;<span style="position:relative;top:-2px;">www.sortiqsolutions.com</span>
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BODY --}}
    <div class="certi-body"
         style="background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}') no-repeat center;
                background-size:860px;
                padding-top:60px;">

        <div class="inner-container" style="padding-left:30px; padding-right:30px;">

            {{-- TITLE --}}
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <h2 style="font-family:'Katibeh',serif;
                                   font-size:40px;
                                   font-weight:700;
                                   color:#2c2e35;
                                   margin:0 0 30px;">
                            <strong>Salary Slip</strong>
                        </h2>
                    </td>
                </tr>
            </table>

            {{-- EMPLOYEE DETAILS --}}
            <table width="100%" cellpadding="6" cellspacing="0"
                   style="font-family:'Inter',sans-serif;font-size:14px;">
                <tr>
                    <td width="50%"><strong>Employee Name:</strong> {{ $slip->emp_name }}</td>
                    <td width="50%"><strong>Employee Code:</strong> {{ $slip->emp_code ?? '-' }}</td>
                </tr>
                <tr>
                    <td><strong>Designation:</strong> {{ $slip->designation ?? '-' }}</td>
                    <td><strong>Month / Year:</strong> {{ $slip->month }} {{ $slip->year }}</td>
                </tr>
                <tr>
                    <td><strong>Issue Date:</strong>
                        {{ \Carbon\Carbon::parse($slip->issue_date)->format('d M Y') }}
                    </td>
                    <td></td>
                </tr>
            </table>

            {{-- SALARY DETAILS --}}
            <table width="100%" cellpadding="10" cellspacing="0"
                   style="margin-top:30px;
                          border-collapse:collapse;
                          font-family:'Inter',sans-serif;
                          font-size:14px;"
                   border="1">

                <tr style="background:#f2f2f2;">
                    <th align="left">Earnings</th>
                    <th align="right">Amount (₹)</th>
                </tr>

                <tr>
                    <td>Basic Salary</td>
                    <td align="right">{{ number_format($slip->basic_salary,2) }}</td>
                </tr>

                <tr>
                    <td>Allowances</td>
                    <td align="right">{{ number_format($slip->allowances,2) }}</td>
                </tr>

                <tr style="background:#f2f2f2;">
                    <th align="left">Deductions</th>
                    <th></th>
                </tr>

                <tr>
                    <td>Deductions</td>
                    <td align="right">{{ number_format($slip->deductions,2) }}</td>
                </tr>

                <tr style="background:#f9f9f9;">
                    <th align="left">Net Salary</th>
                    <th align="right">{{ number_format($slip->net_salary,2) }}</th>
                </tr>
            </table>

            {{-- FOOT NOTE --}}
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:40px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif;font-size:13px;">
                        This is a system generated salary slip and does not require any signature.
                    </td>
                </tr>
            </table>

        </div>
    </d
