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

                        $issue_date = $letter->issue_date
                            ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y')
                            : '';
                @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        <b>Date:</b> {{ $issue_date }}<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        <b>Dear:</b> {{ ucwords($letter->student->student_name) }}<br>
                    </td>
                </tr>
                     
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Greetings from Sortiq Solutions Pvt. Ltd.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Congratulations!
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        With reference to your interview and interaction with Sortiq Solutions Pvt. Ltd., we are pleased to inform you that you have been selected for the Training cum Employment Program in our organization.!
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Your selection is based on your profile, communication, technical understanding, and overall evaluation process. Detailed appointment confirmation and employment documentation shall be provided after successful completion of the required training and probation process as per company policies.
                    </td>
                </tr>
                    <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
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
                                 Your training period will commence from _______________.<br>

                                Initial training duration will be ______ months.<br>
                                After successful completion of training, a probation period of ______ months may be applicable.<br>
                                The company reserves the right to extend training or probation depending upon performance, discipline, attendance, project delivery, learning ability, and professional conduct.
                                The training period is intended to evaluate technical skills, communication, teamwork, adaptability, discipline, and overall suitability for company projects.
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
                        During the training/probation period, your performance may be evaluated on the basis of:
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                   Technical skills<br>
                                    Practical implementation<br>
                                    Daily task completion<br>
                                    Project performance<br>
                                    Assignment submissions<br>
                                    Attendance and punctuality<br>
                                    Communication skills<br>
                                    Learning capability<br>                                  
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
                                   
                                    Team coordination<br>
                                    Professional behavior<br>
                                  
                                </td>
                            </tr>
                        </table>
                        <b>The company may conduct:</b>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                   Weekly evaluations<br>
                                    Mock interviews<br>
                                    Practical assessments<br>
                                    Presentation rounds<br>
                                    Client simulation tasks<br>
                                    Technical tests<br>
                                    Project reviews<br>
                                </td>
                            </tr>
                        </table>
                        Minimum required performance standards must be maintained throughout the training and probation period.<br>
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
                                   Regular attendance is mandatory.<br>
                                    Minimum 98% attendance may be required during training/probation.<br>
                                    Daily reporting to assigned mentor/team lead is compulsory.<br>
                                    Daily diary/task sheet/work update may be maintained by the candidate.<br>
                                    Unapproved leave, continuous absence, or irregular attendance may affect continuation, stipend, confirmation, recommendation, or placement support.<br>
                                    Professional discipline and company policies must be followed at all times.<br>
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
                                   Working Days: Monday to Saturday <br>
                                    Working Hours: ______________________<br>
                                    Additional practical sessions, mock interviews, project discussions, or client meetings may be scheduled whenever required.<br>
                                    During training/probation, leave restrictions may apply depending upon project requirements and evaluation schedules.<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>5. Stipend / Salary Policy</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Stipend or salary (if applicable) shall depend upon company policy and performance evaluation.<br>
                        The company reserves the right to:<br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                 Hold<br>
                                Revise<br>
                                Reduce<br>
                                Postpone<br>
                                Withdraw<br>
                                Cancel<br>
                                </td>
                            </tr>
                        </table>
                         
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <b>stipend/salary in case of:</b>
                         <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                               
                               
                               
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
                                     Poor performance<br>
                                Incomplete assignments/tasks<br>
                                     Low attendance<br>
                                Misconduct<br>
                                Policy violation<br>
                                Unsatisfactory project contribution<br>
                                Breach of confidentiality<br>
                                Failure in evaluations<br>
                                </td>
                            </tr>
                        </table>
                        No stipend/salary shall be considered final until approved by HR and management after evaluation.<br>
                         <b>Annual Package: ₹3,00,000 (Three Lakhs per Annum)</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>6. Company Assets & Confidentiality</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                            You shall maintain strict confidentiality regarding:<br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                  Company projects<br>
                                    Client information<br>
                                    Source code<br>
                                    Internal documents<br>
                                    Login credentials<br>
                                    Databases<br>
                                    Training materials<br>
                                    Company processes<br>
                                </td>
                            </tr>
                        </table>
                        Sharing or misuse of confidential information may result in immediate termination and legal action.<br>
                        Company assets, systems, software access, IDs, and resources provided during training/employment remain the property of the company and must be returned upon request.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>7. Code of Conduct</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        You are expected to maintain:<br>
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                  Professional behavior<br>
                                Respectful communication<br>
                                Ethical work standards<br>
                                Proper dress code (if applicable)<br>
                                Responsible use of company systems and internet<br>
                                </td>
                            </tr>
                        </table>
                        The following may lead to disciplinary action or termination:
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                                                 
                                Misconduct<br>
                                Data misuse<br>
                                Harassment<br>
                                Fake reporting<br>
                                Indiscipline<br>
                                Proxy attendance<br>
                                Unauthorized recordings<br>
                                Client miscommunication<br>
                                Negative behavior affecting company reputation<br>
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
                        <b>8. Bond / Service Agreement (If Applicable)</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                           
                                You may be required to sign a Training and Service Agreement/Bond with the company for a minimum duration of ______ months/years after confirmation.<br>

                                Terms related to service agreement, notice period, training cost recovery, or project commitments shall be governed by separate documentation wherever applicable.<br>
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
                                   Training and placement assistance may be provided based on performance and eligibility.<br>
                                    Employment confirmation is subject to successful completion of training/probation and management approval.<br>
                                    The company reserves full rights regarding deployment, extension, department allocation, project assignment, or discontinuation.<br>
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
                        Candidate may be required to submit:
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                   Educational certificates/mark sheets<br>
                                    Government ID proof<br>
                                    Passport size photographs<br>
                                    Resume/CV<br>
                                    Address proof<br>
                                    College documents (if applicable)<br>
                                    Internship recommendation/reference letter (if applicable)<br>

                                </td>
                            </tr>
                        </table>
                        Original documents may be verified for official purposes whenever required.
                    </td>
                </tr>
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
                                  This offer is provisional and subject to verification of all information/documents submitted by the candidate.<br>
                                    Any false information, fake documents, misconduct, or policy violation may lead to immediate cancellation of offer/training/employment without prior notice.<br>
                                    The company reserves the right to modify internal policies, schedules, evaluation methods, or operational requirements whenever necessary.<br>
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
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                 
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
                                  I, <b>{{ ucwords($letter->student->student_name ?? '') }}</b>, have read and understood all the terms and conditions mentioned above and agree to comply with company policies.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Signature<br>
                      <b> 
                         Name: {{ ucwords($letter->student->student_name ?? '') }} <br>
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