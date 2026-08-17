<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Placement Assistance Cancellation Consent & Acknowledgement Form</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 35px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Placement Assistance Cancellation Consent & Acknowledgement Form</strong></h2>
                    </td>
                </tr>
            </table>
             @php
                        $collegeOrPlace = $letter->student?->is_place
                            ? ($letter->student?->place ?? '-')
                            : ($letter->student?->collegeData?->college_display_name ?? '-');

                        $course = $letter->student?->course_name ?? '-';

                        $mode = $letter->student?->is_online ? 'Online' : 'Offline';

                        $joining_date = $letter->student?->start_date
                            ? \Carbon\Carbon::parse($letter->student->start_date)->format('d M Y')
                            : \Carbon\Carbon::now()->format('d M Y');

                        $end_date = $letter->student?->start_date
                            ? \Carbon\Carbon::parse($letter->student->end_date)->format('d M Y')
                            : '';
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Student Name: {{ ucwords($letter->student->student_name) }}<br>
                        Father’s Name: {{ ucwords($letter->student->f_name) }}<br>
                        College/University: {{ $collegeOrPlace }}<br>
                        Course/Branch:{{ ucwords($course) }}<br>
                        Training Duration: ______________________________________<br>
                        Enrollment No : ______________________________________<br>
                        Batch Mode: {{ $mode }}<br>
                        Joining Date: {{ $joining_date }}<br>
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        <strong>Subject: Acknowledgement of Placement Assistance Cancellation
                            </strong>
                    </td>
                    
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                        This document serves as an acknowledgement that I understand the eligibility criteria for placement assistance under the Industrial Training / Internship / Placement-Oriented Program conducted by the company.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                        I understand that placement assistance is provided only to students who successfully complete all mandatory academic, technical, attendance, project, and professional requirements prescribed by the company.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                        After reviewing my training records, I acknowledge that I have failed to fulfill one or more of the mandatory placement eligibility requirements mentioned below.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Placement Cancellation Reasons</b>
                    </td>
                </tr>

                   <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;padding-left: 10px;">
                            The student has been declared <b>Not Eligible</b> for placement assistance due to one or more of the following reasons (Tick applicable):
                           <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"></td>
                                    <td style="font-size: 14px; line-height: 24px;">
                                       ☐ Attendance below the required minimum (95%).<br>

                                        ☐ Excessive absenteeism or unauthorized leave.<br>

                                        ☐ Regularly arriving late for training sessions.<br>

                                        ☐ Skipping theory classes.<br>

                                        ☐ Skipping practical/lab sessions.<br>

                                        ☐ Failure to complete the prescribed syllabus.<br>

                                        ☐ Failure to complete practical assignments.<br>

                                        ☐ Failure to complete daily tasks.<br>

                                        ☐ Failure to maintain the Daily Diary / Log Book.<br>

                                        ☐ Failure to submit assignments within the prescribed timeline.<br>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                     <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;  margin-top: 20px; font-family:'Inter', sans-serif;padding-left: 10px;">
                            
                           <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"></td>
                                    <td style="font-size: 14px; line-height: 24px;">

                                        ☐ Failure to complete the Minor/Major Project.<br>

                                        ☐ Unsatisfactory project quality or performance.<br>

                                        ☐ Failure to submit the Project Synopsis.<br>

                                        ☐ Failure to submit the Project Report.<br>

                                        ☐ Failure to submit the PPT Presentation.<br>

                                        ☐ Failure to submit the project source code.<br>

                                        ☐ Unsatisfactory technical performance during training.<br>

                                        ☐ Poor coding/practical assessment performance.<br>

                                        ☐ Failure to participate in weekly or monthly assessments.<br>

                                        ☐ Failure to attend mock interviews.<br>

                                        ☐ Failure to attend HR preparation sessions.<br>

                                        ☐ Failure to attend communication skills sessions.<br>

                                        ☐ Failure to attend personality development sessions.<br>

                                        ☐ Failure to appear for the Final Viva.<br>

                                        ☐ Unsatisfactory discipline or professional behaviour.<br>

                                        ☐ Violation of company policies or code of conduct.<br>

                                        ☐ Failure to submit mandatory documents.<br>

                                        ☐ Failure to follow trainer or management instructions.<br>

                                        ☐ Any other reason approved by Management:

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                            <b>Student Declaration</b>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I acknowledge that I have been informed about the above deficiencies during my training period.
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I understand that because I have failed to fulfill the mandatory placement eligibility requirements, the company is <b>not obligated to arrange interviews, recommend me to hiring companies, or provide placement assistance or any related services </b>.
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I further understand that this decision has been taken according to the company's Placement Eligibility Policy and is based on my training records, attendance, academic performance, project completion status, assessments, documentation, and professional conduct.
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I agree that I shall not raise any claim, dispute, or complaint against the company regarding placement assistance arising from my failure to meet the prescribed eligibility criteria.
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I understand that I may become eligible for placement assistance only if the company, at its sole discretion, permits me to complete the pending requirements within the prescribed time.
                        </td>
                    </tr>

                   

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                            <b>Acknowledgement</b>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;padding-left: 10px;">
                            I have read and understood the contents of this document. I voluntarily acknowledge the cancellation of my placement assistance due to my failure to satisfy the mandatory placement eligibility requirements.
                        </td>
                    </tr>
                 
                 </table>

                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      <b>Student Details</b><br>
                        <b>Student Name:</b> {{ ucwords($letter->student->student_name) }}<br>
                        <b>Student Signature:</b> ___________________________<br>
                        <b>Trainer Name & Signature:</b> ___________________________<br>
                        <b>HR Representative:</b> ___________________________<br>
                        <b>Placement Officer:</b> ___________________________<br>
                        <b>Authorized Signatory:</b> ___________________________<br>
                        <b>Date:</b> ___________________________<br>
                    </td>
                </tr>
               
                  </table>
              
             
            
             @include('student_letters_footer_logos.footer_content')
            
            
        </div>
    </div>
</div>

</body>
</html>
