<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Declaration and Consent for Paid Internship and Offer Letter</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Declaration and Consent for Paid Internship and Offer Letter</strong></h2>
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
                        <b>The Management</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd.
                    </td>
                </tr>
                </table>
                
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>

                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">

                    @php
                        $student = $letter->student;

                        $gender = strtolower($student->gender ?? '');
                        $relation = 'son';

                        if ($gender === 'female') {
                            $relation = $student->is_married ? 'wife' : 'daughter';
                        }

                        $collegeOrPlace = $student->is_place
                            ? $student->place
                            : ($student?->collegeData?->college_display_name ?? '');

                    @endphp

                    I, <b>{{ ucwords($student->student_name) }}</b>, 
                    {{ $relation }} of <b>{{ ucwords($student->father_name_with_title) }}</b>, 
                    a student of 
                    <b>
                    {{ ucwords($collegeOrPlace) }}

                    @if(!$student->is_place && !empty($student?->collegeData?->college_short_name))
                        ({{ strtoupper($student?->collegeData?->college_short_name) }})
                    @endif
                    </b> College / University, hereby voluntarily declare and confirm the following:

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       2. I fully understand that the internship offered by the company is a <b>fee-based (paid by me) internship</b>, and I have <b>willingly agreed</b> to pay the required internship fee <b>without any force, pressure, or compulsion</b> from the company or its representatives.
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      3. I confirm that the decision to join the paid internship has been made <b>entirely by me</b>, after understanding all terms and conditions.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                     4. I further declare that <b>Sortiq Solutions Pvt. Ltd. has not forced, demanded, or misled me</b> into opting for a paid internship in exchange for the offer letter or PPO letter.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      5. I undertake that in the future, I shall <b>not raise any objection, complaint, or legal claim</b> against the company, its management, or staff regarding the paid nature of the internship or issuance of the offer letter.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      6. This declaration is made for <b>security, record, and compliance purposes</b> of the company and may be produced as valid proof if required by any authority or institution.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      I am signing this declaration <b>voluntarily and in full understanding</b>, and it shall remain binding for all future references.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      Student Details:
                        Name: {{ ucwords($letter->student->student_name) }}<br>
                        College / University: {{ ucwords($letter->student?->collegeData?->college_display_name ?? 'N/A') }}@if(!empty($letter->student?->collegeData?->college_short_name))
                            ({{ strtoupper($letter->student?->collegeData?->college_short_name) }})
                        @endif<br>
                        Course & Semester: {{ ucwords($letter->student?->course_name ?? 'N/A') }}<br>
                        Mobile No.: {{ ucwords($letter->student->contact) }}<br>

                        Student Signature: ___________________________<br>
                        Date: ___________________________<br>
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
                        @include('student_letters_footer_logos.footer_logos')
                        <!-- <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
                        </div> -->
                        <!-- <div style="display:inline-block; width:100%;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                                <tr>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/msme.jpeg') }}" style="width:160px; padding-right: 20px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/good_firm.jpeg') }}" style="width:170px; padding-right: 5px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/iso.jpeg') }}" style="width:150px; padding-right: 3px;"/></td>
                                    <td width="100%"><img src="{{ public_path('images/certificates_images/wix.jpeg') }}" style="width:145px;"/></td>
                                </tr>
                            </table>
                        </div> -->
                    </td>
                </tr>
            </table>
            
        </div>
    </div>
</div>

</body>
</html>
