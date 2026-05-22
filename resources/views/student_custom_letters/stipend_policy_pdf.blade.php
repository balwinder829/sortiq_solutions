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
                use Carbon\Carbon;
                       
                        $joining_date = $letter->start_date
                            ? \Carbon\Carbon::parse($letter->start_date)->format('d M Y')
                            : \Carbon\Carbon::now()->format('d M Y');

                        $end_date = $letter->start_date
                            ? \Carbon\Carbon::parse($letter->end_date)->format('d M Y')
                            : '';

                        $issue_date = $letter->issue_date
                            ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y')
                            : '';

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
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Intern Name: {{ !empty($letter->student_name) ? ucwords($letter->student_name) : '' }}<br>
                        Internship Domain: {{ !empty($letter->training_domain) ? ucwords($letter->training_domain) : '' }}<br>
                        College/Institute Name: {{ !empty($collegeOrPlace) ? $collegeOrPlace : '' }}<br>

                        Internship Duration: {{ !empty($letter->training_duration) ? ucwords($letter->training_duration) : '' }}<br>

                        Joining Date: {{ !empty($joining_date) ? $joining_date : '' }}<br>
                        Reporting Mentor: {{ !empty($letter->reporting_mentor) ? ucwords($letter->reporting_mentor) : '' }}<br>

                        Internship Mode: {{ !empty($letter->internship_mode) ? ucwords($letter->internship_mode) : '' }}<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Internship Responsibilities & Conditions</b>
                    </td>
                </tr>
                     
                 

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       I, <b>{{ ucwords($letter->student_name) }}</b>, hereby agree to follow the rules, responsibilities, and professional guidelines during my internship period at Sortiq Solutions Pvt. Ltd.
                    </td>
                </tr>
                              
                  
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Reporting & Communication</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    <table width="100%" cellpadding="0" cellspacing="0">

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                            The intern must report regularly to the assigned mentor/trainer/team lead.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                           Daily/weekly task updates may be required.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;   font-family: 'Inter', sans-serif;">
                                           Intern must attend meetings, training sessions, and assigned discussions on time.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;  font-family: 'Inter', sans-serif;">
                                            Proper professional communication must be maintained with mentors, team members, and management.
                                        </td>
                                    </tr>

                                </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
               

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Attendance & Leave Policy</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Regular attendance is mandatory during internship duration.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                No unnecessary leave will be allowed during probation/training period unless approved by management.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                In case of emergency leave, prior information should be provided whenever possible.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Continuous absence, late reporting, or irregular attendance may affect evaluation and stipend eligibility.
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Technical Performance Evaluation</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    Intern performance may be evaluated based on:
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Technical skills and learning ability
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Task completion
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Project contribution
                                            </td>
                                        </tr>

                                         

                                    </table>
                                    
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px;">
                
                 <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Assignment submission
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Practical implementation
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Professional behavior
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Communication and reporting
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                               Discipline and professionalism
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Attendance and punctuality
                                            </td>
                                        </tr>

                                    </table>
                                    <b>The company may conduct:</b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
               <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Weekly evaluations
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Practical assessments
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                               Technical interviews/tests
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Project reviews
                                            </td>
                                        </tr>
                                         

                                    </table>
                                   
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
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Intern must maintain professional behavior within company premises and online platforms.
                                                Misconduct, abusive behavior, indiscipline, data misuse, or unprofessional activity may lead to warning or internship termination.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Company policies and mentor instructions must be followed properly.
                                            </td>
                                        </tr>
                                    </table>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       <b>5. Stipend Policy</b>
                        <br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    Stipend (if applicable) will depend upon intern performance, attendance, discipline, reporting, and assigned work completion.<br>
                                    <b>The company reserves the right to:</b>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Hold
                                            </td>
                                        </tr>

                                        

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Reduce
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Postpone
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Revise
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Cancel stipend
                                            </td>
                                        </tr>

                                    <tr>
                                        <td width="30"></td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                                          <b>in cases of:</b>
                                        </td>
                                    </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Poor technical performance
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Incomplete tasks/projects
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Low attendance
                                            </td>
                                        </tr>

                                        
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Policy violations
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Unprofessional behavior
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Lack of participation or learning involvement
                                            </td>
                                        </tr>
                                    </table>
                                    

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
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                             
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Internship continuation depends upon satisfactory performance and discipline.
                                            </td>
                                        </tr>

                                         

                                          
                                    </table>

                                </td>
                            </tr>
                             
                        </table>

                        

                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px;">
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                             
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                The company may terminate or discontinue internship in case of misconduct, poor performance, confidentiality breach, or repeated negligence.
                                            </td>
                                        </tr>

                                          
                                    </table>

                                </td>
                            </tr>
                             
                        </table>

                        

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>7. Confidentiality</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                             
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Intern shall not share company data, client information, source code, internal documents, or training materials without authorization.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                               Any misuse of company information may result in legal or disciplinary action.
                                            </td>
                                        </tr>

                                         
                                         
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                 
                      
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Declaration</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                I have read and understood all the above terms and conditions carefully. I agree to follow company policies and professional responsibilities during my internship period at Sortiq Solutions Pvt. Ltd.<br>

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                  
            
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Intern Signature</b><br>
                        
                        Intern Name: {{ ucwords($letter->student_name ?? '') }} <br>
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