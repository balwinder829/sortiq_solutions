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
             @php
                use Carbon\Carbon;
                       
                        $joining_date = $letter->start_date
                            ? \Carbon\Carbon::parse($letter->start_date)->format('d M Y')
                            : \Carbon\Carbon::now()->format('d M Y');

                        $end_date = $letter->start_date
                            ? \Carbon\Carbon::parse($letter->end_date)->format('d M Y')
                            : '';

                        $issue_date = $letter->issue_date
                            ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y')
                            : \Carbon\Carbon::now()->format('d M Y');

                        $training_start_date = $letter->training_start_date
                            ? \Carbon\Carbon::parse($letter->training_start_date)->format('d M Y')
                            : '';

                        $sessionName = optional($letter->sessionData)->session_display_name ?? ''; 
                        $format = 'd F Y';

                        $sessionStart = optional($letter->sessionData)->start_date 
                          ? Carbon::parse($letter->start_date)->format($format) 
                          : '';

                        $sessionEnd = optional($letter->sessionData)->end_date
                          ? Carbon::parse($letter->end_date)->format($format)
                          : '';

                        $collegeOrPlace = $letter->college ?? '-';
                                            

                @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Date: <strong>
                                {{ $issue_date }}
                            </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        To,
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        <b>{{ ucwords($letter->student_name) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">

                        {{ $collegeOrPlace }}

                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <br>It is with great pleasure that we extend to you an offer for a <b>six-month internship</b> with <strong> Sortiq Solutions Pvt. Ltd.</strong> This internship is designed to provide you with practical learning experiences, real-time project exposure, and professional growth under the mentorship of industry experts.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Internship Details </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                     <strong style="margin-left: 20px;">Duration:</strong> {{ ucwords($letter->training_duration ?? '6') }} Months<br>
                                    <strong style="margin-left: 20px;">Type:</strong> Free Internship<br>
                                    <strong style="margin-left: 20px;">Position:</strong> {{ ucwords($letter->position ?? 'N/A') }}<br>
                                    <strong style="margin-left: 20px;">Location:</strong> Mohali / Remote (based on project requirement)<br>
                                    <strong style="margin-left: 20px;">Start Date:</strong> {{ $training_start_date }}<br>
                                </td>
                            </tr>
                        </table>
                        <br>
                        During this internship, you will work on live projects, collaborate with our team, and enhance your technical and professional capabilities.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Pre-Placement Offer (PPO)</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        After the <b>first three months</b>, your performance will be evaluated based on: 
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Technical skills<br>
                                    • Consistency and dedication<br>
                                    • Project contribution<br>
                                    • Professional behavior
                                </td>
                            </tr>
                        </table>
                        <br>
                        Based on your evaluation, you may receive a <b>Pre-Placement Offer (PPO)</b> to continue with us under enhanced terms.
                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Stipend (During PPO Phase)</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                         
                        If selected for the PPO phase, you will receive a stipend ranging from:<br>
                         <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                   <b> • ₹10,000 to ₹25,000 per month</b>
                                </td>
                            </tr>
                        </table>
                        The exact stipend will depend on your performance, skill set, and contribution during the initial internship period.
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>4. Post-Internship Employment</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        Upon successful completion of the full six-month internship, you will be eligible for a full-time role with:<br>
                        <b>Minimum Annual Package: ₹3,00,000 (Three Lakhs per Annum)</b><br>
                        Higher compensation may be offered based on performance and company requirements.
                    </td>
                </tr>

                 <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>5. Terms & Conditions</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;"> 
                       
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Maintain professionalism, punctuality, and discipline throughout the internship.<br>
                                    • All company data, project information, and internal processes must remain confidential.<br>
                                    • Adherence to company policies is mandatory.<br>
                                    • The company reserves the right to discontinue the internship in case of policy violations or unsatisfactory performance.<br>
                                    • If you are not regular during the internship, your internship will be discontinued as per the company guidelines.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        We are excited to welcome you to our organization and look forward to a productive association. Please confirm your acceptance of this offer at your earliest convenience.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        For any queries or additional information, feel free to contact us.
                    </td>
                </tr>
            </table>
               
              @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>
</html>
