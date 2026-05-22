<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CONFIRMATION LETTER</title>
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
    
    <div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 40px;">
        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>CONFIRMATION LETTER</strong></h2>
                    </td>
                </tr>
            </table>
            @php
                use Carbon\Carbon;
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
                        $sessionName = optional($letter->student->sessionData)->session_display_name ?? ''; 
                        $format = 'd F Y';
         $sessionStart = optional($letter->student->sessionData)->start_date 
          ? Carbon::parse($letter->student->start_date)->format($format) 
          : '';

         $sessionEnd = optional($letter->student->sessionData)->end_date
          ? Carbon::parse($letter->student->end_date)->format($format)
          : '';

                @endphp

                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">                        
                        To <br>                     
                        Internship & Placement Officer <br>                       
                        {{ $collegeOrPlace }}
                    </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">                        
                        Subject: <strong>Confirmation of {{ ucwords($sessionName) }} Training & Job Opportunity</strong>                    
                    </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">                        Dear Sir/Madam,                 </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif; text-align:justify;">We are pleased to confirm that <strong>{{ ucwords($letter->student->student_name) }},</strong> a student of your esteemed institution, has been enrolled in our <strong>{{ ucwords($sessionName) }}</strong> Training Program for the session <strong>{{ $sessionStart }}</strong> to <strong>{{ $sessionEnd }}</strong>.                 </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif; text-align:justify;">
                         During this training period, the candidate will also be provided with an opportunity to work in our client companies in <b>non-technical roles</b>, primarily in the areas of <b>Data Entry, Documentation, Verification Support, and Backend Operations</b>.
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif; text-align:justify;">The working duration for the assigned role will be <b>7–9 hours per day</b>. Based on the candidate’s performance, attendance, discipline, and work quality, the candidate will receive a monthly salary/stipend ranging from <b>₹9,000 to ₹18,000 per month</b>.</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif; text-align:justify;">The candidate’s progress and overall performance will be closely monitored and evaluated by the management throughout the duration of the training and job assignment.</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif; text-align:justify;">We look forward to successful cooperation and appreciate your support.</td>
                  </tr>
               </table>
                          
            
           @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>

</html>