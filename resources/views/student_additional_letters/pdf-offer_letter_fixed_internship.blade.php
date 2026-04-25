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
                        Date: <strong>
                                {{ $letter->issue_date 
                                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                    : \Carbon\Carbon::now()->format('d M Y') 
                                }}
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
                        <b>{{ ucwords($letter->student->student_name) }}</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
    
                        @if($letter->student?->is_place)
                            {{ ucwords($letter->student?->place ?? '') }}
                        @else
                            {{ ucwords($letter->student?->collegeData?->college_display_name ?? '') }}
                            @if(!empty($letter->student?->collegeData?->college_short_name))
                                ({{ strtoupper($letter->student?->collegeData?->college_short_name) }})
                            @endif
                        @endif

                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Dear <b>{{ ucwords($letter->student->student_name) }}</b>,
                    </td>
                </tr>
                 
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <br>It is with great pleasure that we extend to you an offer for a six-month paid internship with Sortiq Solutions Pvt. Ltd. This internship is designed to provide you with practical learning experiences, real-time project exposure, and professional growth under the mentorship of industry experts.
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
                                     <strong style="margin-left: 20px;">Duration:</strong> 6 Months<br>
                                    <strong style="margin-left: 20px;">Type:</strong> Paid Internship<br>
                                    <strong style="margin-left: 20px;">Location:</strong> Mohali / Remote (based on project requirement)<br>
                                    <strong style="margin-left: 20px;">Start Date:</strong> {{ \Carbon\Carbon::parse($letter->student->start_date)->format('d M Y') }}<br>
                                </td>
                            </tr>
                        </table>
                        <br>
                        During this internship, you will work on live projects, collaborate with our team, and enhance your technical and professional capabilities in the field of {{ ucwords($letter->student?->course_name ?? 'N/A') }}.
                    </td>
                </tr>
                
               
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Stipend</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                         
                         <b> • ₹10,000 to ₹25,000 per month</b><br>
                        The stipend will be applicable from the first month of your internship and will continue for the entire 6-month duration, subject to satisfactory performance, attendance, and adherence to company policies.<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Performance Evaluation</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        Your performance will be evaluated periodically based on:
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
                    </td>
                </tr>
                 </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>4. Post-Internship Employment & Package</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        Upon successful completion of the six-month internship, you may be offered a full-time position with the company.<br>
                        The annual package (CTC) after completion of the internship will be decided based on your performance, skills, overall contribution, and company requirements.<br>
                        Outstanding performers may be offered competitive salary packages as per industry standards.
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
                                    • Irregular attendance may lead to discontinuation of the internship as per company guidelines.
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
                
            </table>
              @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>
</html>
