<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trainer Consent Letter</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Trainer / Mentor Consent & Declaration Form</strong></h2>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        Trainer / Mentor Information
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:5px; font-family: 'Inter', sans-serif;">
                        * Full Name: {{ ucwords($letter->trainer->name) }}<br>
                        
                        * Contact Number: {{ !empty($letter->trainer->phone) ? ucwords($letter->trainer->phone) : '-' }}<br>
                        * Email ID: {{ !empty($letter->trainer->email) ? ucwords($letter->trainer->email) : '-' }}<br>
                        * Batch Assigned: {{ $letter->trainer && $letter->trainer->batches->count()
                            ? $letter->trainer->batches->pluck('batch_name')->implode(', ')
                            : 'No Batch Assigned'
                        }}<br>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        Trainer / Mentor Responsibilities & Consent
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        I, <b>{{ ucwords($letter->trainer->name) }}</b>, hereby agree and confirm that I will perform my duties professionally as a Trainer / Mentor with Sortiq Solutions Pvt. Ltd. for students enrolled in internship, training, industrial training, workshop, certification, project, or placement-oriented programs.<br>

                        I understand and agree to the following responsibilities and conditions:
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>1. Training Responsibilities</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        * Conduct training sessions regularly according to assigned schedules.<br>
                        * Provide guidance for practical learning and project implementation.<br>
                        * Explain concepts, tools, technologies, assignments, and project modules properly.<br>
                        * Maintain discipline and professional communication with students.<br>
                        * Support students regarding doubts and technical guidance during training duration.<br>
                        * Ensure syllabus coverage within assigned training period.<br>
                        * Share learning materials, tasks, and practice assignments when required.<br>
                        * Monitor attendance, progress, and student participation.

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>2. Project Guidance Responsibilities</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        * Guide students in mini projects and major projects.<br>
                        * Assist students in documentation, PPT preparation, and project presentations.<br>
                        * Ensure project work is educational and training-oriented.<br>
                        * Provide proper project workflow and implementation support.<br>
                        * Help students understand project modules and development process.
                       
                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>3. Syllabus Completion Declaration</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                         
                            I agree that:
                           <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"></td>
                                    <td style="font-size: 14px; line-height: 24px;">
                                        * Assigned syllabus/topics will be completed from my side within the provided training duration.<br>
                                        * Any pending topic, practical, assignment, or doubt session will be informed to management.<br>
                                        * Proper coordination will be maintained for batch completion.<br>
                                        * Students will receive reasonable support for training-related doubts.<br>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    
                </tr>

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>4. Online / Offline Batch Conditions</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                         
                            I agree that:
                           <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"></td>
                                    <td style="font-size: 14px; line-height: 24px;">
                                       * Conduct sessions professionally in both online and offline mode.<br>
                                        * Maintain punctuality and proper communication.<br>
                                        * Avoid misuse of company name, training materials, or student data.<br>
                                        * Follow company policies and training guidelines.<br>
                                        * Maintain proper behavior with students, staff, and management.<br>

                                    </td>
                                </tr>
                            </table>
                        
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>5. Student Doubt Support</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        * Doubts will be addressed during scheduled sessions or assigned support timings.<br>
                        * Support may be provided through classroom sessions, online meetings, calls, chat groups, or LMS platforms.<br>
                        * Delays due to student absence, incomplete tasks, or technical issues will not be considered trainer negligence.<br>

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Confidentiality & Professional Ethics</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        
                            I agree that:
                           <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="30"></td>
                                    <td style="font-size: 14px; line-height: 24px;">
                                     * Company data, projects, student records, and training materials are confidential.<br>
                                    * I will not share internal company information without authorization.<br>
                                    * I will maintain professionalism during the training period.<br>
                                    * I will not misuse company branding, certificates, or official communication.<br>


                                    </td>
                                </tr>
                            </table>
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>7. Attendance & Reporting</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                      * Daily/weekly reports may be submitted to management.<br>
                        * Attendance and session updates may be maintained.<br>
                        * Delays or absence should be informed in advance whenever possible.<br>

                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>8. Limitation & General Conditions</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        * Project completion also depends upon student participation and attendance.<br>
                        * Company will not be responsible for incomplete work caused by student negligence, absenteeism, lack of participation, or technical limitations.<br>
                        * Training support is educational in nature and does not guarantee job placement.<br>
                        

                    </td>
                </tr>
                </table>
                <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">         
                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <b>Declaration</b>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; font-family:'Inter', sans-serif;">
                        I have read and understood all terms and responsibilities mentioned above. I agree to perform my assigned training and mentoring duties professionally for Sortiq Solutions Pvt. Ltd.<br>

                        I confirm that the information provided by me is correct.

                    </td>
                </tr>
                 
                 </table>
                
            
              
            
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">

                <tr>
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                      <b>Trainer / Mentor Signature</b><br>
                        Name: {{ ucwords($letter->trainer->name) }}<br>
                        Signature: ___________________________<br>
                        Date: ___________________________<br>
                        Place: ___________________________<br>
                    </td>
                </tr>
               
                  </table>
                
                
                
           
              @include('student_letters_footer_logos.footer_content')
            
        </div>
    </div>
</div>

</body>
</html>
