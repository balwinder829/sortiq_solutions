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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Non-Consent Form</strong></h2>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Date: <strong>{{ \Carbon\Carbon::now()->format('d M Y') }}</strong>
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Dear Candidates,
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        As you all know that Sortiq Solutions Pvt Ltd’s Learning Division provides you with world class Industrial Training/ Internship and gives you an opportunity to work with our multiple live projects in Ecommerce, ERP Applications, Food Industry, and Real Estate in order to develop incomparable technical competencies in you. Now we have taken the initiative of offering ensured placement assistance in the leading IT companies where you can begin your professional career and exhibit your technical brilliance.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        However, this assistance is for those candidates only who are up to the parameters of our Placement Policy that strictly adhere to sound knowledge of the technology, regularity, and praiseworthy organizational behavior. You are also expected to attend the additional sessions based for Interview Preparation. Assignments, Projects and Exercises given to the Trainee shall be completed and submitted within the specified time period. The Management shall be under no obligation to extend the time so fixed.
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       However, if you are not abiding by our rules and policies, and not attending the regular and the additional interview preparation sessions, it shows that you are not interested in taking placement assistance from us. Hence, you are required to sign this Non-Consent Form that clearly states that the Company is not liable to provide any placement assistance to you.
                    </td>
                </tr>
               
                <tr>
                    @php
                        $student = $letter->student;
                        $gender = strtolower($student->gender ?? '');
                        $relation = 'son';

                        if ($gender === 'female') {
                            $relation = $student->is_married ? 'wife' : 'daughter';
                        }
                    @endphp
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       <b> I, {{ ucwords($letter->student->student_name) }} {{ $relation }} of {{ $letter->student->father_name_with_title ?? '-' }}, pursuing Industrial Training/Internship from Sortiq Solutions Pvt. Ltd. in {{ ucwords($letter->student?->course_name ?? 'N/A') }} Technology declare you not to make me appear in the Screening Interviews.
                        My date of joining Sortiq Solutions Pvt. Ltd. is {{ \Carbon\Carbon::parse($letter->student->start_date)->format('d M Y') }} </b>
                    </td>
                </tr>
                 
                </table>
              
            <pagebreak />
            
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
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
                        <!-- <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
                        </div> -->
                        <div style="display:inline-block; width:100%;">
                             <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                                <tr>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/iso-certified-company-image.png') }}" style="width:160px; padding-right: 20px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/MSME_small.png') }}" style="width:170px; padding-right: 5px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/GF-min.png') }}" style="width:150px; padding-right: 3px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/EN_legend_small.png') }}" style="width:145px;"/></td>
                                </tr>
                             </table>
                        </div>
                    </td>
                </tr>
            </table>
            
        </div>
    </div>
</div>

</body>
</html>
