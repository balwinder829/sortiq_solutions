<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>INTERN RESPONSIBILITY & STIPEND POLICY LETTER</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>INTERN RESPONSIBILITY & STIPEND POLICY LETTER</strong></h2>
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
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Intern Name: {{ ucwords($letter->student->student_name) }}<br>
                        Father’s Name: {{ ucwords($letter->student->f_name) }}<br>
                        Internship Domain: ____________________________<br>
                        College/Institute: {{ $collegeOrPlace }}<br>
                        Internship Duration: ___________________________<br>
                        Joining Date: {{ $joining_date }}<br>
                        Reporting Mentor: _____________________________<br>
                        Internship Mode: {{ $mode }}<br>
                        
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        <b>Internship Responsibilities & Conditions</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
    
                        I, <b>{{ ucwords($letter->student->student_name) }}</b>, hereby agree to follow the rules, responsibilities, and professional guidelines during my internship period at Sortiq Solutions Pvt. Ltd.

                    </td>
                </tr>
                
                  
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Reporting & Communication</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px;font-family: 'Inter', sans-serif;">
                        The intern must report regularly to the assigned mentor/trainer/team lead.<br>
                        Daily/weekly task updates may be required.<br>
                        Intern must attend meetings, training sessions, and assigned discussions on time.<br>
                        Proper professional communication must be maintained with mentors, team members, and management.<br>
                    </td>
                </tr>
               

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Attendance & Leave Policy</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        Regular attendance is mandatory during internship duration.<br>
                        No unnecessary leave will be allowed during probation/training period unless approved by management.<br>
                        In case of emergency leave, prior information should be provided whenever possible.<br>
                        Continuous absence, late reporting, or irregular attendance may affect evaluation and stipend eligibility.<br>
                       
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Technical Performance Evaluation</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Intern performance may be evaluated based on:
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    Technical skills and learning ability<br>
                                    Task completion<br>
                                    Project contribution<br>
                                    Assignment submission<br>
                                    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                <tr>
                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                    <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    Practical implementation<br>
                                    Communication and reporting<br>
                                    Discipline and professionalism<br>
                                    Attendance and punctuality</b>
                                </td>
                            </tr>
                        </table>
                        <b>The company may conduct:</b>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    Weekly evaluations<br>
                                    Practical assessments<br>
                                    Technical interviews/tests<br>
                                    Project reviews<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>4. Behavior & Professional Conduct</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Intern must maintain professional behavior within company premises and online platforms.
                        Misconduct, abusive behavior, indiscipline, data misuse, or unprofessional activity may lead to warning or internship termination.<br>

                        Company policies and mentor instructions must be followed properly.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>5. Stipend Policy</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;"> 
                       Stipend (if applicable) will depend upon intern performance, attendance, discipline, reporting, and assigned work completion.<br>
                       <b>The company reserves the right to:</b>
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    Hold<br>
                                    Reduce<br>
                                    Postpone<br>
                                    Revise<br>                                     
                                    Cancel stipend<br>                                     
                                </td>
                            </tr>
                        </table>
                        <b>in cases of:</b><br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    Poor technical performance<br>
                                    Incomplete tasks/projects<br>
                                    Low attendance<br>
                                    Policy violations<br>
                                    Unprofessional behavior<br>
                                    Lack of participation or learning involvement<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>6. Internship Continuation & Termination</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Internship continuation depends upon satisfactory performance and discipline.
                        The company may terminate or discontinue internship in case of misconduct, poor performance, confidentiality breach, or repeated negligence.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>7. Confidentiality</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Intern shall not share company data, client information, source code, internal documents, or training materials without authorization.<br>
                        Any misuse of company information may result in legal or disciplinary action.
                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       <b>Declaration</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I have read and understood all the above terms and conditions carefully. I agree to follow company policies and professional responsibilities during my internship period at Sortiq Solutions Pvt. Ltd.

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I have read and understood all the above terms and conditions carefully. I agree to follow company policies and professional responsibilities during my internship period at Sortiq Solutions Pvt. Ltd.

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Intern Signature<br>
                      <b> 
                         Intern Name: {{ ucwords($letter->student->student_name ?? '') }} <br>
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