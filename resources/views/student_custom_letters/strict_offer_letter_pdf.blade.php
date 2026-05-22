<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>OFFER LETTER</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>OFFER LETTER</strong></h2>
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
                        <b>Date:</b> {{ $issue_date }}<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Dear:</b> {{ ucwords($letter->student_name) }}<br>
                    </td>
                </tr>
                     
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Greetings from Sortiq Solutions Pvt. Ltd.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Congratulations!
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        With reference to your interview and interaction with Sortiq Solutions Pvt. Ltd., we are pleased to inform you that you have been selected for the Training cum Employment Program in our organization.!
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Your selection is based on your profile, communication, technical understanding, and overall evaluation process. Detailed appointment confirmation and employment documentation shall be provided after successful completion of the required training and probation process as per company policies.
                    </td>
                </tr>
                    <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       Terms & Conditions
                    </td>
                </tr>
                
                  
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Training & Probation Period</b>
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
                                            Your training period will commence from {{ $training_start_date }}.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                            Initial training duration will be {{ $letter->training_duration }} months.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;   font-family: 'Inter', sans-serif;">
                                            After successful completion of training, a probation period of
                                            {{ $letter->probation_period }} months may be applicable.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;  font-family: 'Inter', sans-serif;">
                                            The company reserves the right to extend training or probation
                                            depending upon performance, discipline, attendance, project delivery,
                                            learning ability, and professional conduct.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;  font-family: 'Inter', sans-serif;">
                                            The training period is intended to evaluate technical skills,
                                            communication, teamwork, adaptability, discipline, and overall
                                            suitability for company projects.
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
                        <b>2. Technical Evaluation & Performance</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    During the training/probation period, your performance may be evaluated on the basis of:
                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Technical skills
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
                                                Daily task completion
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Project performance
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
                                                Assignment submissions
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Attendance and punctuality
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Communication skills
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Learning capability
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Team coordination
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Professional behavior
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
                                                Mock interviews
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
                                                Presentation rounds
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Client simulation tasks
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Technical tests
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Project reviews
                                            </td>
                                        </tr>

                                    </table>
                                    Minimum required performance standards must be maintained throughout the training and probation period.<br>
                                </td>
                            </tr>
                        </table>
                       
                        
                    </td>
                </tr>
                  
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Attendance & Discipline Policy</b>
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
                                                Regular attendance is mandatory.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Minimum 98% attendance may be required during training/probation.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily reporting to assigned mentor/team lead is compulsory.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily diary/task sheet/work update may be maintained by the candidate.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Unapproved leave, continuous absence, or irregular attendance may affect continuation, stipend, confirmation, recommendation, or placement support.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Professional discipline and company policies must be followed at all times.
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
                        <b>4. Working Days & Timing</b>
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
                                                Working Days: Monday to Saturday
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Working Hours: {{ $letter->working_hours }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Additional practical sessions, mock interviews, project discussions, or client meetings may be scheduled whenever required.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                During training/probation, leave restrictions may apply depending upon project requirements and evaluation schedules.
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
                        <b>5. Stipend / Salary Policy</b>
                        <br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    Stipend or salary (if applicable) shall depend upon company policy and performance evaluation.<br>
                                    The company reserves the right to:<br>
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
                                                Hold
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
                                                Withdraw
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Cancel
                                            </td>
                                        </tr>

                                    <tr>
                                        <td width="30"></td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                                          <b>stipend/salary in case of:</b>
                                        </td>
                                    </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Poor performance
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Incomplete assignments/tasks
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
                                                Misconduct
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Policy violation
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Unsatisfactory project contribution
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Breach of confidentiality
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Failure in evaluations
                                            </td>
                                        </tr>

                                    </table>
                                    

                                </td>

                            </tr>
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                    No stipend/salary shall be considered final until approved by HR and management after evaluation.<br>
                                    <b>Annual Package: ₹3,00,000 (Three Lakhs per Annum)</b>
                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>
                 
                

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>6. Company Assets & Confidentiality</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                   You shall maintain strict confidentiality regarding:<br>
                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Company projects
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Client information
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Source code
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Internal documents
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Login credentials
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Databases
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Training materials
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Company processes
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                   Sharing or misuse of confidential information may result in immediate termination and legal action.<br>
                                    Company assets, systems, software access, IDs, and resources provided during training/employment remain the property of the company and must be returned upon request.
                                </td>
                            </tr>
                        </table>

                        

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>7. Code of Conduct</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                                  You are expected to maintain:<br>
                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Professional behavior
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Respectful communication
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Ethical work standards
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
                                                Proper dress code (if applicable)
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Responsible use of company systems and internet
                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  <b>The following may lead to disciplinary action or termination:</b>
                                </td>
                            </tr>
                        </table>


                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Misconduct
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Data misuse
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Harassment
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Fake reporting
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Indiscipline
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Proxy attendance
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Unauthorized recordings
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Client miscommunication
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Negative behavior affecting company reputation
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
                        <b>8. Bond / Service Agreement (If Applicable)</b>
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
                                                You may be required to sign a Training and Service Agreement/Bond with the company for a minimum duration of {{ $letter->bond_duration }} months/years after confirmation.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Terms related to service agreement, notice period, training cost recovery, or project commitments shall be governed by separate documentation wherever applicable.
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
                        <b>9. Placement & Employment Conditions</b>
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
                                                Training and placement assistance may be provided based on performance and eligibility.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Employment confirmation is subject to successful completion of training/probation and management approval.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                The company reserves full rights regarding deployment, extension, department allocation, project assignment, or discontinuation.
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
                        <b>10. Documents Required at Joining</b>
                    </td>
                </tr>
                
               <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">

                        

                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  Candidate may be required to submit:
                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">

                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Educational certificates/mark sheets
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Government ID proof
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Passport size photographs
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Resume/CV
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Address proof
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                College documents (if applicable)
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Internship recommendation/reference letter (if applicable)
                                            </td>
                                        </tr>



                                    </table>

                                </td>
                            </tr>
                            <tr>
                                <td width="30"></td>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  Original documents may be verified for official purposes whenever required.
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
                        <b>11. General Conditions</b>
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
                                           
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                This offer is provisional and subject to verification of all information/documents submitted by the candidate.
                                            </td>
                                        </tr>

                                        <tr>
                                           
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Any false information, fake documents, misconduct, or policy violation may lead to immediate cancellation of offer/training/employment without prior notice.
                                            </td>
                                        </tr>

                                        <tr>
                                            
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                The company reserves the right to modify internal policies, schedules, evaluation methods, or operational requirements whenever necessary.
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
                                  Please sign and submit a copy of this letter as acceptance of the above terms and conditions.<br>

                                We look forward to your contribution and professional association with Sortiq Solutions Pvt. Ltd.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Candidate Acceptance</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                  I, <b>{{ ucwords($letter->student_name ?? '') }}</b>, have read and understood all the terms and conditions mentioned above and agree to comply with company policies.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Signature</b><br>
                        
                         Name: {{ ucwords($letter->student_name ?? '') }} <br>
                        Signature: ___________________________<br>
                        Date: {{ $letter->issue_date 
                                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                    : \Carbon\Carbon::now()->format('d M Y') 
                                }}<br>
                        Place: Mohali<br>
                    </td>
                </tr>
            </table>
              
            
           @include('student_letters_footer_logos.footer_content_without_logo')
            
        </div>
    </div>
</div>

</body>

</html>