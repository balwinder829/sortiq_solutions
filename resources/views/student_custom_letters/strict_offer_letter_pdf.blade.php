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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 20px; font-weight: 700; color: #2c2e35; margin: 0 0 25px;"><strong>OFFER LETTER</strong></h2>
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
                        <b>Mr./Ms.:</b> {{ ucwords($letter->student_name) }}<br>
                    </td>
                </tr> 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Greetings from <strong>Sortiq Solutions Pvt. Ltd.</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <strong>Congratulations!</strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif; text-align: justify;">
                        With reference to your interview discussion and profile evaluation with Sortiq Solutions Pvt. Ltd., we are pleased to offer you an opportunity to join our organization for the position of <b>{{ $letter->position }}</b> at our Mohali office. Your selection has been made after reviewing your communication skills, technical understanding, learning capabilities, and overall interaction during the recruitment process.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        The organization may invest approximately ₹2 LPA towards training, professional development, infrastructure, project exposure, and skill enhancement during the overall training and probation period. Upon successful completion of the required training, internal evaluations, and probation process, the proposed annual employment package may be up to ₹3 LPA, subject to company policies, performance standards, and organizational requirements.
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                    <b>Terms & Conditions</b>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Your training period shall commence from {{ $training_start_date }} and will continue for approximately 6 months, during which your technical skills, attendance, communication, discipline, project involvement, and professional conduct shall be regularly monitored and evaluated by the organization.
                    </td>
                </tr>
            
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Upon completion of the training period, candidates shall be required to appear for an internal assessment/test conducted by the company, with a minimum qualifying score of 60%. In case the required standards or qualifying score are not achieved, the organization reserves the right to extend, revise, discontinue, or terminate the training process without initiation of the probation period.
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Candidates who successfully complete the training phase and receive company confirmation may proceed to a probation period of approximately 3 months based on project requirements and overall performance. The organization reserves the right to extend or modify the training or probation duration if expected standards, attendance, technical performance, or assigned responsibilities are not satisfactorily fulfilled.
                    </td>
                </tr>
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Any stipend during the training period shall remain subject to attendance, discipline, performance, assignment completion, and management approval as per company policies.
                    </td>
                </tr>
            </table>
            <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 20px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Working Structure & Professional Conduct</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        The standard working schedule during training and probation may consist of 6 working days per week with office timings as communicated by the management. Additional meetings, project discussions, workshops, evaluations, or official activities may be scheduled whenever required for operational or training purposes.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Candidates are expected to maintain punctuality, professional discipline, ethical conduct, respectful communication, and proper coordination with team members, mentors, trainers, and reporting authorities throughout the program duration.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Important Information</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Candidates may be assigned internal projects, client-based assignments, research activities, or operational tasks as per organizational and project requirements. The company reserves the right to change deployment location, reporting structure, department allocation, or project responsibilities whenever required.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                        Any misconduct, policy violation, fake reporting, misuse of company systems, unauthorized sharing of confidential information, or behavior affecting company reputation may result in disciplinary action or immediate discontinuation from the training/employment program without prior notice.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;text-align: justify;">
                       The candidate is expected to remain associated with the organization for a minimum duration of 2 years, including the training and probation period, subject to company policies and project requirements. Candidates may be required to submit educational certificates, photographs, and valid ID proof for official verification purposes:
                    </td>
                </tr>

                <tr>
                    <td width="12" style="vertical-align: middle;">•</td>
                    <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                        Educational certificates and qualification documents
                    </td>
                </tr>
                <tr>
                    <td width="12" style="vertical-align: middle;">•</td>
                    <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                        Recent passport-size photographs
                    </td>
                </tr>
                <tr>
                    <td width="12" style="vertical-align: middle;">•</td>
                    <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                        One valid government-issued ID proof with photocopy
                    </td>
                </tr>
                <!-- <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                        Original documents may be verified whenever required by the organization for administrative or official purposes.
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px;padding-bottom:3px; font-family: 'Inter', sans-serif;">
                        Reporting Time on First Day of Joining: 10:00 AM
                    </td>
                </tr> -->
            
                <!-- <tr>
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
                </tr> -->
            </table>
            @include('student_letters_footer_logos.footer_content_without_logo')
        </div>
    </div>
</div>

</body>

</html>