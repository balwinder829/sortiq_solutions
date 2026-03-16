<!DOCTYPE html>
<html>
<head>
<title>Employment Bond Letter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
}
ul {
    margin-top: 6px;
    padding-left: 18px;
}
li {
    margin-bottom: 6px;
}
 p {
        margin: 0 0 14px 0;
    }
</style>
<style>
    table {
    page-break-inside: auto;
}
tr {
    page-break-inside: avoid;
}
@page {
    margin: 0;
}
@page {
    margin-top: 15mm; /* margin for all pages EXCEPT first */
    margin-left: 0mm;
    margin-right: 0mm;
    margin-bottom: 15mm;
}

@page :first {
    margin-top: 0; /* first page with header */
}

@page :last {
    margin-bottom: 0mm;
}

.certi-body {
    padding-left: 5mm;
    padding-right: 5mm;
}


/* Spacer before bond terms */
/*.bond-terms-wrapper::before {
    content: "";
    display: block;
    height: 30mm;
}*/

/* Remove spacer on first page */
/*@page :first {
    .bond-terms-wrapper::before {
        display: none;
         margin-top: 0;
    }
}*/
</style>

</head>
<body>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">

    <!-- HEADER -->
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

    <!-- BODY -->
    <div class="certi-body" style="background:url('../images/bg-shape.jpg') no-repeat center; background-size:860px; padding-top:60px;">
        <div style="padding:0 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Employee Bond Agreement</strong></h2>
                    </td>
                </tr>
            </table>
            @php
                $employmentLine = null;

                if ($letter->employee->work_mode === 'online' && $letter->employee->job_type === 'part_time') {
                    $employmentLine = "Online, Part Time ({$letter->employee->working_hours_per_day} Hours Per Day)";
                } elseif ($letter->employee->work_mode === 'online') {
                    $employmentLine = "Online";
                } elseif ($letter->employee->job_type === 'part_time') {
                    $employmentLine = "Part Time ({$letter->employee->working_hours_per_day} Hours Per Day)";
                }
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">

               <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Date: </strong> {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                       <strong>Mr./Ms: </strong> {{ ucwords($letter->employee->emp_name) }}
                    </td>
                </tr>
                 @if($employmentLine)
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Employment Type: </strong> {{ $employmentLine }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left;  padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <strong>Starting Date: </strong> {{ \Carbon\Carbon::parse(
                                $letter->bond_start_date ?? $letter->employee->joining_date
                            )->format('d M Y') }}
                    </td>
                </tr>

                <!-- BETWEEN -->
                <tr><td height="20"></td></tr>
                <tr>
                    <td style="text-align:center;font-family:Inter;font-weight:600;">BETWEEN</td>
                </tr>
                <tr><td height="10"></td></tr>

               <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd., an Indian Company having its principal place of office at <strong>E - 51, Ground Floor, Industrial Area, Phase 8 Mohali, Punjab - 160072</strong>, hereinafter (hereinafter referred to as "the Company"),
                    </td>
                </tr>

                <!-- AND -->
                <tr><td height="15"></td></tr>
                <tr>
                    <td style="text-align:center;font-family:Inter;font-weight:600;">AND</td>
                </tr>
                <tr><td height="10"></td></tr>

                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>{{ ucwords($letter->employee->emp_name) }}</strong>, Intern of Sortiq Solutions Pvt. Ltd. (hereinafter referred to as "the Intern").
                    </td>
                </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">

                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>Recitals</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   WHEREAS, the Company desires to secure the commitment of the Intern for a period following the Bond agreement;
                                </td>
                            </tr>
                            <tr style="padding-top:6px;">
                                <td width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                   NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained, the parties hereto agree as follows:
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                
                </table>
                 <div class="bond-terms-wrapper">
    
                            {!! $letter->bond_terms !!}
                         
            </div>
            <div style="page-break-inside: avoid; break-inside: avoid;">
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                <!-- CLAUSES -->
                <tr><td height="20"></td></tr>

                
                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">
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
        </div>
            <!-- STAMP BLOCK (UNCHANGED) -->
           <!--  <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px; page-break-inside:avoid;">
                <tr>
                    <td>
                        <h4 style="margin:0;font-family:Inter;font-size:16px;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                        <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;">
                        <br><br>
                        <h3 style="font-family:Inter;font-size:16px;">Human Resource Department</h3>
                    </td>
                </tr>
            </table> -->

        </div>
    </div>

</div>

</body>
</html>
