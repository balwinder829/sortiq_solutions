<!DOCTYPE html>
<html>
   <head>
      <title>Confirmation Letter</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
   </head>
   <body>
      @php 
      @endphp
      <div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">
         <div class="head-main" style="padding-top: 150px;">
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
         @php
         $title = $student->gender === 'female' ? 'Miss' : 'Mr';
         $relation = $student->gender === 'female' ? 'D/O' : 'S/O';
         use Carbon\Carbon;
         // Safe session values
         $sessionStart = optional($student->sessionData)->start_date 
         ? Carbon::parse($student->sessionData->start_date)->format('F Y') 
         : '';
         $sessionEnd = optional($student->sessionData)->end_date
         ? Carbon::parse($student->sessionData->end_date)->format('F Y')
         : '';
         // Safe college
         $collegename = optional($student->collegeData)->college_name ?? '';
         // Safe duration
         $durationName = optional($student->durationData)->name ?? '';
         @endphp	
         <div class="certi-body" style=" background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}')  no-repeat center; background-size:860px; padding-top: 60px;">
            <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
               <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                     <td colspan="2" style="text-align: center;">
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>CONFIRMATION LETTER</strong></h2>
                     </td>
                  </tr>
               </table>
               <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						To <br>						Training & Placement Officer <br>						{{ $collegename }}					</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						Subject: <strong>Confirmation of {{ strtoupper($durationName) }} Industrial Training</strong>					</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						Dear Sir/Madam,					</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						We are pleased to confirm that {{ $title }} <strong>{{ ucwords($student->student_name) }},</strong> {{ $relation }} <strong>{{ ucwords($student->f_name) }}</strong> and a student of your esteemed institution, has been enrolled in our {{ $durationName }} industrial training for the session <strong>{{ $sessionStart }} to {{ $sessionEnd }}</strong>					</td>
                  </tr>
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						The candidate's performance will be closely evaluated by the management throughout the duration of the internship.					</td>
                  </tr>
               </table>
               <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">
                  <tr>
                     <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">						Regards					</td>
                  </tr>
                  <tr>
                     <td colspan="2">						
						<img style="max-width: 200px; width:100%;" src="{{ public_path('images/confirmation_images/stamp-signatue.png') }}"/>					</td>
                  </tr>
               </table>
               <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                  <tr>
                     <td>
                        <div style="display:inline-block; width:100%;">
                           <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">HR Manager</h4>
                           <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Priyanka</h4>
                           <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">M: +91-9501381389</h4>
                        </div>
                     </td>
                  </tr>
               </table>
            </div>
         </div>
        
      </div>
   </body>
</html>