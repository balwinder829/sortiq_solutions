<!DOCTYPE html>
<html>
<head>
<title>{{ ucwords($letter->subject ?? 'Letter') }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>{{ ucwords($letter->subject ?? 'Letter') }}</strong></h2>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Date</strong>
                            {{ $letter->issue_date 
                                ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                : \Carbon\Carbon::now()->format('d M Y') 
                            }}
                        
                    </td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Mr./Ms</strong> {{ ucwords($letter->student->student_name) }}
                    </td>
                </tr>
                 
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Dear</strong> {{ ucwords($letter->student->student_name) }},
                    </td>
                </tr>
                 </table>

                 <div class="bond-terms-wrapper">
    
                            {!! $letter->letter_content !!}
                         
            </div>
            <div style="page-break-inside: avoid; break-inside: avoid;">
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">
               <!--  <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       {!! $letter->letter_content !!}
                    </td>
                </tr> -->
               

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
                        <!-- <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
                        </div> -->
                        <!-- <div style="display:inline-block; width:100%;">
                             <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                                <tr>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/iso-certified-company-image.png') }}" style="width:160px; padding-right: 20px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/MSME_small.png') }}" style="width:170px; padding-right: 5px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/GF-min.png') }}" style="width:150px; padding-right: 3px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/EN_legend_small.png') }}" style="width:145px;"/></td>
                                </tr>
                             </table>
                        </div> -->
                    </td>
                </tr>
            </table>
        </div>
             
        </div>
    </div>
    
</div>

</body>
</html>