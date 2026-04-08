<!DOCTYPE html>
<html>
<head>
<title>MEMORANDUM OF UNDERSTANDING (MoU)</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
}
ul {
    margin-top: 6px;
    padding-left: 18px;
}
li {
    margin-bottom: 6px;
}
</style>
</head>
<body>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">

    <!-- HEADER -->
    <div class="head-shape">
        <img style="width: 100%; display: block;" src="images/head-shape.png"/>
    </div>

    <div class="head-main" style="padding-top: 40px;">
        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="68%" align="left">
                        <div class="h-logo">
                            <img style="width: 100%; max-width: 200px;" src="images/logo-sortiq.png" width="200"/>
                        </div>
                    </td>
                    <td width="32%" align="left">
                        <div class="h-detials" style="max-width: 210px; width: 100%;">
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="images/cl.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">+91 96465 22110</span></p>
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="images/email.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">info@sortiqsolutions.com</span></p>
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="images/globe.png" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">www.sortiqsolutions.com</span></p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- BODY -->
    <div class="certi-body" style="background:url('../images/bg-shape.jpg') no-repeat center; background-size:860px; padding-top:60px;">
        <div style="padding:0 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <h3 style="font-family: 'Katibeh', serif; text-align: center; font-size: 30px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>MEMORANDUM OF UNDERSTANDING (MoU)</strong></h3>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;">

               <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Date: </strong> {{ \Carbon\Carbon::parse($mou->start_date)->format('d M Y') }},
                    </td>
                </tr>
                
                <!-- BETWEEN -->
                <tr><td height="20"></td></tr>
                <tr>
                    <td style="text-align:left;font-family:Inter;font-weight:600;">BETWEEN</td>
                </tr>
                <tr><td height="10"></td></tr>

               <tr>
                    <td style="text-align:left; font-size: 14px; line-height: 24px; font-family: 'Inter', sans-serif;">
                        <b>Sortiq Solutions Pvt. Ltd., Sahibzada Ajit Singh Nagar, Punjab – 160071</b>
                    </td>
                </tr>

                <!-- AND -->
                <tr><td height="15"></td></tr>
                <tr>
                    <td style="text-align:left;font-family:Inter;font-weight:600;">AND</td>
                </tr>
                <tr><td height="10"></td></tr>

                <tr>
                    <td style="text-align:left; font-size: 14px; line-height: 24px; font-family: 'Inter', sans-serif;">
                        <b>{{ ucwords($mou->college->FullName) }}</b>
                    </td>
                </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">

                 <tr>
                     
                    <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                       This Memorandum of Understanding (MoU) is executed on <b>{{ \Carbon\Carbon::parse($mou->start_date)->format('d M Y') }}</b> by and between <b>Sortiq Solutions Pvt. Ltd., E-51, Phase-8, Industrial Area, Sahibzada Ajit Singh Nagar, Punjab – 160071</b> (hereinafter referred to as Sortiq Solutions), a leading IT company engaged in software development, digital solutions, industrial training, workshops, and placement services, and <b>{{ ucwords($mou->college->FullName) }}</b>, established under Punjab Act No. 37 of 2011.
                    </td>
                </tr>
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600; padding-bottom:10px;">Party I</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  
                                  <b>Sortiq Solutions Pvt. Ltd. (hereinafter referred to as Sortiq Solutions)</b>, is committed to providing high-quality IT solutions, skill development programs, industry-driven training, workshops, internships, and placement opportunities. The company focuses on effectively bridging the gap between academic knowledge and professional industry requirements.
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;">Party II</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  <b>{{ ucwords($mou->college->FullName) }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>PURPOSE</b></td>
                            </tr>
                           <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  This Memorandum of Understanding between Sortiq Solutions and {{ ucwords($mou->college->FullName) }} envisages academic and professional collaboration for training, workshops, internships, research, and placement opportunities for students.
                                </td>
                            </tr>
                            
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>SCOPE OF COLLABORATION</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                 Both parties agree to the following:</td>

                               <tr> <td colspan="2" style="font-size: 14px; line-height: 24px;padding-left:18px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                1. Organize online/offline <b>seminars, workshops, training programs, and placement drives</b> to enhance students’ skills and industry readiness.</td></tr>
                                </table>

 <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                2. Provide <b>6-month industrial training/internships</b> to {{ ucwords($mou->college->FullName) }} students at Sortiq Solutions Pvt. Ltd. under the guidance of experienced professionals.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                3. Facilitate <b>faculty/student visits</b> for knowledge exchange and collaborative academic activities.</td></tr>
                               <tr> <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                4. Encourage <b>joint research projects and publications</b> in emerging technology domains.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                5. Share <b>resources, tools, and expertise</b> for capacity building and innovation.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                6. Provide opportunities for <b>live project participation</b> to students for real-world exposure.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                7. Collaborate on <b>cutting-edge technology training programs</b>, including AI, Machine Learning, Data Analytics, Cyber Security, Cloud Computing, Web/Mobile Development, and Digital Marketing.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                8. Support <b>placement drives, pre-placement talks, and career guidance sessions</b> at {{ ucwords($mou->college->FullName) }}.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                9. Encourage <b>mentorship, resume-building, interview preparation, and soft skills training</b> for students.</td></tr>
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                10. Explore possibilities of <b>intellectual property generation</b> through collaborative research, with joint rights as per mutual agreements.</td></tr>
                                
                                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                11. Both parties shall work in <b>mutual cooperation, coordination, transparency</b>, and good faith to achieve the objectives outlined in this agreement.</td></tr>
                     
                                 <tr><td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; padding-left:18px; font-family: 'Inter', sans-serif;">
                                12. Any amendments or modifications shall be <b>made in writing and duly signed</b> by authorized representatives of both parties.</td></tr>
                     
                                  
                            
                    
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>DURATION OF AGREEMENT</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  This MoU shall remain valid for <b>three (3) years</b> from the date of signing. Renewal/amendments will be made on mutually agreed terms between Sortiq Solutions and {{ ucwords($mou->college->FullName) }}.
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>TERMINATION</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  Either party may terminate this MoU by providing <b>30 days’ written notice</b> to the other party.<br>

                                Termination will not affect ongoing programs, which shall be completed as agreed.
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>CONFIDENTIALITY</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  Both parties agree to maintain strict confidentiality of any proprietary, technical, or sensitive information shared during the course of this collaboration. Such information shall not be disclosed to any third party without prior written consent of the concerned party.
                                </td>
                            </tr>

                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>AMENDMENTS</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  Any modification or amendment to this MoU shall be made in writing and signed by authorized representatives of both parties.
                                </td>
                            </tr>
                        </table>
                            <pagebreak />
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                            <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>GENERAL TERMS</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                    This MoU is not legally binding but reflects the intent of collaboration.
                                    Both parties agree to work in good faith and mutual cooperation.<br>
                                    Any disputes arising shall be resolved amicably through mutual discussion.<br>
                                </td>
                            </tr>

                             <tr>
                                <td style="text-align:left;font-family:Inter;font-weight:600;padding-bottom:10px;"><b>SIGNATORIES</b></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-size: 14px; line-height: 24px; padding-bottom:15px; font-family: 'Inter', sans-serif;">
                                  IN WITNESS WHEREOF, the parties hereto have duly signed this Memorandum of Understanding on the date mentioned above in mutual agreement.
                                </td>
                            </tr>
                             

                
                </table>
                   
                <div style="page-break-inside: avoid; break-inside: avoid;">
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px;">
                <!-- CLAUSES -->
                <tr><td height="20"></td></tr>               

                <!-- SIGN OFF -->
                <tr><td height="25"></td></tr>
                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; font-family: 'Inter', sans-serif;"><strong>Warm Regards</strong>,</td></tr>
                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; font-family: 'Inter', sans-serif;">Priyanka</td></tr>
                <tr><td colspan="2" style="font-size: 14px; line-height: 24px; font-family: 'Inter', sans-serif;">Manager – Human Resources</td></tr>

            </table>

 
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td width="40%">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                            <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;"/>
                            
                        </div>
                        <div style="display:inline-block; width:100%;">
                            <br>
                            <h3 style="font-size: 16px; font-family: 'Inter', sans-serif;">Human Resource Department</h3>
                        </div>
                    </td>
                     <td width="40%" align="right">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For {{ ucwords($mou->college->FullName) }}</h4><br>
                            <!-- <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;"/> -->
                            
                        </div>
                        <div style="display:inline-block; width:100%;">
                            <br>
                            <!-- <h3 style="font-size: 16px; font-family: 'Inter', sans-serif;">Human Resource Department</h3> -->
                        </div>
                    </td>
                </tr>
            </table>
        </div>
            <!-- STAMP BLOCK (UNCHANGED) -->
           <!--  <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px; page-break-inside:avoid;">
                <tr>
                    <td>
                        <h4 style="margin:0;font-family:Inter;font-size:16px;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                        <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;">
                        
                        <h3 style="font-family:Inter;font-size:16px;">Human Resource Department</h3>
                    </td>
                </tr>
            </table> -->

        </div>
    </div>

</div>

</body>
</html>
