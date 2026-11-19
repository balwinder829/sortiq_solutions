<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ADDITIONAL JOB OFFER LETTER</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Additional Job Offer Letter</strong></h2>
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

          $gender = strtolower($letter->student->gender ?? '');

            $title = ($gender === 'female')
                        ? ($letter->student->is_married ? 'Mrs' : 'Miss')
                        : 'Mr';

            $relation = ($gender === 'female')
                            ? ($letter->student->is_married ? 'W/O' : 'D/O')
                            : 'S/O';

                @endphp

                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Date:  <strong>
                                {{ $letter->issue_date 
                                    ? \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') 
                                    : \Carbon\Carbon::now()->format('d M Y') 
                                }}
                            </strong>
                    </td>
                </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">                        
                        To <br>                     
                        <b>{{ ucwords($letter->student->student_name) }} </b><br>
                    </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">                        
                        <strong>Subject:</strong> Additional Job Opportunity Alongside 6-Month Training/Internship Program                    
                    </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;"> Dear {{ ucwords($letter->student->student_name) }}, </td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">Greetings from <b>Sortiq Solutions Pvt. Ltd</b></td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                         This is to acknowledge that you have successfully registered for our 6-Month Industrial Training/Internship Program.
                     </td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                         Alongside your training, we are pleased to offer you an additional job opportunity through our company references with another associated company. The available non-technical job profiles include:
                     </td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        1. Data Entry Executive<br>
                        2. Document Verification Executive<br>
                        3. Chat Process Executive<br>
                        4. Voice Process Executive<br>
                     </td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <b>Job Details:</b><br>
                        Working Hours: 6–8 hours per day.<br>
                        Workplace: <b>Referred Company.</b><br>
                        Monthly Salary: ₹10,000 – ₹16,000.<br>
                        Selection: Subject to the referred company’s interview and selection process.<br>
                        Salary Structure: Based on the selected job profile, skills, and company policies.<br>
                     </td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                         Please note that your training with Sortiq Solutions Pvt. Ltd. and the employment opportunity with the referred company will be treated as separate engagements. Employment-related terms and conditions will be governed by the referred company.
                     </td>
                  </tr>

                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:10px; font-family: 'Inter', sans-serif;">
                         We wish you success in your professional journey.
                     </td>
                  </tr>
               </table>
                       <pagebreak>   
            
           @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>

</html>