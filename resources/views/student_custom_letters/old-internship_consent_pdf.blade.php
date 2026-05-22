<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>STUDENT TRAINING / INTERNSHIP CONSENT & SATISFACTION FORM</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>STUDENT TRAINING / INTERNSHIP CONSENT</strong></h2>
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
                        College/Institute Name: {{ $collegeOrPlace }}<br>
                        Course/Branch:{{ ucwords($course) }}<br>
                        Contact Number: {{ ucwords($letter->student->contact) }}<br>
                        Email ID: {{ ucwords($letter->student->email_id) }}<br>
                        Training/Internship Domain: _____________________<br>
                        Duration: ______________________________________<br>
                        Batch Mode: {{ $mode }}<br>
                        Joining Date:{{ $joining_date }}<br>
                        Completion Date:{{ $end_date }}<br>
                        </strong>
                    </td>
                </tr>
                 
                 
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I, <b>{{ ucwords($letter->student->student_name) }}</b>, hereby confirm and declare that I have attended the training/internship program conducted by Sortiq Solutions Pvt. Ltd.

                        I confirm the following points:
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Training & Syllabus Completion </b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        The assigned syllabus/topics were explained and covered properly from trainer/mentor side.
                        Practical sessions, assignments, projects, and guidance were provided during the training period.<br>
                        Doubts and queries raised by me were addressed during the program duration.
                        I received learning support related to the enrolled domain/course.
                    </td>
                </tr>


               

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Project Work</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        Project modules, workflow, and implementation guidance were provided.
                        PPT/project presentation support and documentation guidance were explained where applicable.
                        I understand that project completion also depends upon my own participation, attendance, task submission, and practice.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Satisfaction Declaration</b><br>
                        I confirm that:<br>
                    </td>
                </tr>
</table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        I am satisfied with the training/internship support provided by Sortiq Solutions Pvt. Ltd.
                        I have received proper guidance according to the enrolled program.<br>
                        Any pending task, doubt, assignment, project modification, or incomplete work (if any) has already been discussed before signing this form.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>4. Pending Work Declaration</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        I confirm that no pending doubt, syllabus, project, task, or support issue remains from my side before signing this form.<br>
                        OR<br>
                        Pending items (if any) are mentioned below before signing:<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>5. General Consent</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        I understand that internship/training is educational in nature.
                        Placement/job opportunities are not guaranteed unless specifically mentioned in writing by the company.
                        I will not misuse company materials, certificates, projects, or official branding.
                        I confirm that all information provided by me is correct.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Final Declaration</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        I have read and understood all the above points carefully. I am signing this form with my own consent and satisfaction.
                    </td>
                </tr>
 




 
               
                
                 <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b> Student Signature</b><br>
                      
                         Student Name: {{ ucwords($letter->student->student_name ?? '') }} <br>
                        Signature: ___________________________<br>
                        Date: {{ $letter->issue_date 
                                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                    : \Carbon\Carbon::now()->format('d M Y') 
                                }}<br>
                        Place: Mohali<br>
                    </td>
                </tr>
            </table>
              
            
           @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>

</html>