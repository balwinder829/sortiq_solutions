<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>STUDENT TRAINING & PLACEMENT CONSENT LETTER</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>STUDENT TRAINING & PLACEMENT CONSENT LETTER</strong></h2>
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
                        Student Name: {{ ucwords($letter->student_name) }}<br>
                        Father’s Name: {{ ucwords($letter->father_name) }}<br>
                        College/Institute Name: {{ $collegeOrPlace }}<br>
                        Course/Branch:{{ ucwords($letter->course_batch) }}<br>
                        Contact Number: {{ ucwords($letter->contact_no) }}<br>
                        Email ID: {{ ucwords($letter->email_id) }}<br>
                        Training Domain:{{ ucwords($letter->training_domain) }}<br>
                        Training Duration: {{ ucwords($letter->training_duration) }}<br>
                        Batch Mode: {{ ucwords($letter->batch_mode) }}<br>
                        Joining Date:{{ $joining_date }}<br>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Training & Placement Consent Declaration</b>
                    </td>
                </tr>
                     
                

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I, <b>{{ ucwords($letter->student_name) }}</b>, hereby confirm that I am joining the training/internship program at Sortiq Solutions Pvt. Ltd. for learning, skill development, project exposure, and placement preparation purposes.<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I agree to follow all training, internship, and placement-related rules and responsibilities mentioned below.
                    </td>
                </tr>                
                  
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Attendance Policy</b>
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
                                            Minimum 98% attendance is compulsory during the training/internship period.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                            Regular attendance in daily classes, practical sessions, mock interviews, presentations, and assessments is mandatory.
                                        </td>
                                    </tr>

                                    <tr>
                                        <td width="12" valign="top">•</td>
                                        <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px;   font-family: 'Inter', sans-serif;">
                                            Unauthorized leave or continuous absence may affect training continuation, certification, recommendation, stipend, or placement support.
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
                         <b>2. Daily Classes & Tasks</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>

                                <td style="font-size: 14px; line-height: 24px;">
                                    I agree that:
                                    <table width="100%" cellpadding="0" cellspacing="0">

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily classes/sessions must be attended regularly.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily tasks, assignments, practical work, and activities must be completed on time.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Work assigned by trainer/mentor must be submitted within deadlines.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily learning records or work reports may be maintained.
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
                        <b>3. Daily Diary / Reporting</b>
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
                                                Maintaining daily diary/report/task sheet is compulsory.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Daily progress and learning updates may be checked by mentor/trainer.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Incomplete diary/report submission may affect evaluation.
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
                        <b>4. PPT & Presentation Activities</b>
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
                                                PPT preparation and presentation activities are compulsory where applicable.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Students may be required to explain project work, modules, or technical topics during sessions.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Participation in practical demonstrations and presentations is mandatory.
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
                        <b>5. Mock Sessions & Placement Activities</b>
                         
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
                                                Mock interviews, mock presentations, aptitude tests, group discussions, and placement preparation sessions are compulsory.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Students must actively participate in placement-oriented activities.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Proper dress code and professional communication may be required during interviews and presentations.
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
                        <b>6. Discipline & Professional Behavior</b>
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
                                               Students must maintain professional behavior with trainers, mentors, staff, and other students.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Misconduct, indiscipline, abusive behavior, or misuse of company resources may lead to suspension or termination from the program.
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
                       <b>7. Project & Learning Responsibility</b>
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
                                                Project completion and practical learning also depend upon student participation, attendance, practice, and task completion.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                 Students must actively participate in assignments, coding practice, project work, and doubt sessions.
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
                        <b>8. Placement Support Conditions</b>
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
                                                Placement assistance/support may be provided based on student performance, attendance, communication skills, technical skills, and discipline.
                                            </td>
                                        </tr>

                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Job placement is not guaranteed unless specifically mentioned in writing by the company.
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="12" valign="top">•</td>
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                Students must attend interviews and placement drives when informed.
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
                        <b>9. General Consent</b>
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
                                                I agree to follow company rules, batch guidelines, and mentor instructions.
                                            </td>
                                        </tr>

                                        <tr>
                                           
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                I confirm that the information provided by me is correct.
                                            </td>
                                        </tr>

                                        <tr>
                                            
                                            <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:3px; font-family: 'Inter', sans-serif;">
                                                I understand that violation of company policies may affect training continuation, certification, stipend, recommendation, or placement support.
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
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Final Declaration</b>
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                  I have read and understood all the above terms and conditions carefully. I agree to follow the rules and responsibilities during my training/internship period at Sortiq Solutions Pvt. Ltd.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                 
            
                    <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b> Signature</b><br>
                      
                         Name: {{ ucwords($letter->student_name ?? '') }} <br>
                        Signature: ___________________________<br>
                        Date: {{ $letter->issue_date 
                                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                    : \Carbon\Carbon::now()->format('d M Y') 
                                }}<br>
                        Place: Sortiq Solutions Pvt. Ltd.<br>
                    </td>
                </tr>
            </table>
              
            
           @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>

</html>