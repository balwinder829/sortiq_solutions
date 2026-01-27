<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Placement / Training Consent</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Student Placement / Training Consent</strong></h2>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Date: <strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</strong>
                    </td>
                </tr>
                 
                </table>
                
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       I, <b>{{ ucwords($letter->student->student_name) }}</b>, son/daughter of <b>{{ ucwords($letter->student->f_name) }}</b>, enrolled in <b>{{ ucwords($letter->student?->collegeData?->FullName ?? 'N/A') }},</b>
                        hereby give my consent and full agreement to the following terms regarding my placement/training with <b>Sortiq Solutions Pvt. Ltd</b>.:
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       I understand that I have been placed for a training period of <b> {{ ucwords($letter->student?->sessionData?->session_display_name ?? 'N/A') }}</b> with Sortiq Solutions Pvt. Ltd. starting from <b>{{ \Carbon\Carbon::parse($letter->student->start_date)->format('d M Y') }}</b>.
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      In case I receive a job offer/employment opportunity from <b>Sortiq Solutions Pvt. Ltd</b>. or any other organization during this training period (for example, after 2 months), my training will be considered completed/closed from that date, and I will not be required to continue the remaining training period.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                     Once I join the job, my status will shift from <b>Trainee to Employee</b>, and the training program will automatically end.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      I fully agree and acknowledge that:<br>

                        <br> • If I make any mistake, fail to perform, or violate company rules resulting in termination or dismissal from the job, I will not be entitled to resume or continue my training.<br>

                        <br> • <b>Sortiq Solutions Pvt. Ltd.</b> and my institute/college are not responsible for providing me with any alternate training opportunity in such a case.<br>

                        <br> • The confirmation of my job or continuation after training is completely dependent on company policy and my performance.<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      I agree to abide by all rules, regulations, and professional conduct required by Sortiq Solutions Pvt. Ltd. during my training and any subsequent employment.
                    </td>
                </tr>
</table>
                <pagebreak />
            
              
            
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      <b>Student Details:</b><br>
                        Name: {{ ucwords($letter->student->student_name) }}<br>
                        Mobile No.: {{ ucwords($letter->student->contact) }}<br>

                        Student Signature: ___________________________<br>
                        Date: ___________________________<br>
                    </td>
                </tr>
               
                 
                
                
                <!-- <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               padding-top:10px;
                               font-family:'Inter', sans-serif;">
                        We congratulate you and wish you a great career with us. We look forward to embarking on this next chapter together!
                    </td>
                </tr> -->
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
            
        </div>
    </div>
</div>

</body>
</html>
