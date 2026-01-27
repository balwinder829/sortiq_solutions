<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Increment Letter</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">	

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">

    {{-- HEADER --}}
    <div class="head-main" style="padding-top: 110px;">
        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="68%" align="left">
                        <img style="width: 100%; max-width: 200px;"
                             src="{{ public_path('images/certificates_images/logo-sortiq.png') }}"
                             width="200"/>
                    </td>
                    <td width="32%" align="left">
                        <div style="max-width: 210px;">
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/cl.png') }}" style="width:15px;"/>
                                &nbsp;+91 96465 22110
                            </p>
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/email.png') }}" style="width:15px;"/>
                                &nbsp;info@sortiqsolutions.com
                            </p>
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/globe.png') }}" style="width:15px;"/>
                                &nbsp;www.sortiqsolutions.com
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

        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">

            <table width="100%">
                <tr>
                    <td align="center">
                        <h2 style="font-family:'Katibeh',serif;
                                   font-size:40px;
                                   margin-bottom:30px;">
                            <strong>Increment Letter</strong>
                        </h2>
                    </td>
                </tr>
            </table>

            <table width="100%" style="margin-top:35px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        Date: <strong>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        Dear <strong>{{ ucwords($letter->employee->emp_name) }}</strong>,
                    </td>
                </tr>
            </table>

            <table width="100%" style="margin-top:25px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px;">
                        We are pleased to inform you that, based on your performance
                        and contribution to the organization, your salary has been
                        revised as per the details below.
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        <strong>Previous Salary:</strong>
                            Rs. {{
                                optional($letter->employee->salaryStructure)->total_salary
                                    ? number_format($letter->employee->salaryStructure->total_salary, 2)
                                    : 'N/A'
                            }}/-

                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px;">
                        <strong>Revised Salary:</strong>
                        Rs. {{ number_format($letter->new_salary, 2) }}/-
                    </td>
                </tr>

                @if(!empty($letter->increment_percentage))
                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px;">
                        <strong>Increment Percentage:</strong>
                        {{ $letter->increment_percentage }}%
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:10px;">
                        <strong>Effective From:</strong>
                        {{ $letter->effective_date
                            ? \Carbon\Carbon::parse($letter->effective_date)->format('d M Y')
                            : 'N/A'
                        }}

                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:20px;">
                        All other terms and conditions of your employment remain
                        unchanged and continue to be in force.
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        We appreciate your efforts and look forward to your continued
                        contribution to the organization.
                    </td>
                </tr>

                <!-- SIGN OFF -->
                <tr><td height="25"></td></tr>
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

           <!--  <table width="100%" style="margin-top:35px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        Yours sincerely,<br><br>
                        <strong>For Sortiq Solutions Pvt. Ltd.</strong><br>
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
