<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Completion of {{ ucwords($letter->student?->sessionData?->session_display_name ?? 'N/A') }} Training / Internship</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Completion of {{ ucwords($letter->student?->sessionData?->session_display_name ?? 'N/A') }} Training / Internship</strong></h2>
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
                       I, <b>{{ ucwords($letter->student->student_name) }}</b>, hereby declare that I have successfully completed <b>{{ ucwords($letter->student?->sessionData?->session_display_name ?? 'N/A') }} of Training / Internship at Sortiq Solutions Pvt Ltd</b> in the domain of: <b>{{ ucwords($letter->student?->courseData?->course_name ?? 'N/A') }}</b> from <b>{{ \Carbon\Carbon::parse($letter->student->start_date)->format('d M Y') }}</b> to <b>{{ \Carbon\Carbon::parse($letter->student->end_date)->format('d M Y') }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       I further confirm that after completion of my training/internship, I have received a <b>placement opportunity in one of the client companies of Sortiq Solutions Pvt Ltd</b> based on my performance, skills, and interview process.
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      I also hereby agree and confirm that: 

                       <br> - I will <b>not disclose, discuss, or share any company policies, internal processes, confidential information, or training materials</b> of Sortiq Solutions Pvt Ltd with any third party.<br>
                       <br> - I will <b>not make false allegations, negative statements, or blame my mentor, trainer, or the company </b>for any reason after completion of the training/internship.<br>
                       <br> - I understand that any violation of confidentiality or misconduct may lead to necessary action as per company policy.<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                     I confirm that:<br>
                        - I have completed the training/internship as per company guidelines.<br>
                        - I have received required knowledge, guidance, and industry exposure during the training period.<br>
                        - I have submitted all assigned tasks, projects, and reports.<br>
                        - I have returned all company assets (if any).<br>
                        - I have no pending dues, claims, or obligations towards the company.<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      I understand that the training/internship and placement support were provided based on merit and performance, and I shall not hold the company responsible for future employment continuity or terms offered by the client company.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      This consent is given willingly and without any pressure.
                    </td>
                </tr>

                 </table>
                <pagebreak />
            
              
            
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      <b>Student Details</b><br>
                        Name: {{ ucwords($letter->student->student_name) }}<br>
                        College / University: {{ ucwords($letter->student?->collegeData?->college_display_name ?? 'N/A') }}<br>
                        Course: {{ ucwords($letter->student?->courseData?->course_name ?? 'N/A') }}<br>
                        Mobile No.: {{ ucwords($letter->student->contact) }}<br>

                        <b>Signature</b><br>
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
