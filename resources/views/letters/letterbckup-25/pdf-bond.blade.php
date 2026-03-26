<!DOCTYPE html>
<html>
<head>
<title>Employment Bond Letter</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Two Year Bond Agreement</strong></h2>
                    </td>
                </tr>
            </table>
            @php
                $employmentLine = null;

                if ($letter->employee->work_mode === 'online' && $letter->employee->job_type === 'part_time') {
                    $employmentLine = "Online, Part Time ({$letter->employee->working_hours_per_day} Hours Per Day)";
                } elseif ($letter->employee->work_mode === 'online') {
                    $employmentLine = "Online";
                } elseif ($letter->employee->job_type === 'part_time') {
                    $employmentLine = "Part Time ({$letter->employee->working_hours_per_day} Hours Per Day)";
                }
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">

               <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Date: </strong> {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                       <strong>Mr./Ms: </strong> {{ ucwords($letter->employee->emp_name) }}
                    </td>
                </tr>
                @if($employmentLine)
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Employment Type: </strong> {{ $employmentLine }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left;  padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <strong>Starting Date: </strong>
                            {{ \Carbon\Carbon::parse(
                                $letter->bond_start_date ?? $letter->employee->joining_date
                            )->format('d M Y') }}

                    </td>
                </tr>

                <!-- BETWEEN -->
                <tr><td height="20"></td></tr>
                <tr>
                    <td style="text-align:left;font-family:Inter;font-weight:600;">BETWEEN</td>
                </tr>
                <tr><td height="10"></td></tr>

               <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd., an Indian Company having its principal place of office at <strong>E - 51, Ground Floor, Industrial Area, Phase 8 Mohali, Punjab - 160072</strong>, hereinafter (hereinafter referred to as "the Company"),
                    </td>
                </tr>

                <!-- AND -->
                <tr><td height="15"></td></tr>
                <tr>
                    <td style="text-align:left;font-family:Inter;font-weight:600;">AND</td>
                </tr>
                <tr><td height="10"></td></tr>

                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>{{ ucwords($letter->employee->emp_name) }}</strong>, Intern of Sortiq Solutions Pvt. Ltd. (hereinafter referred to as "the Intern").
                    </td>
                </tr>
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">

                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>Recitals</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   WHEREAS, the Company desires to secure the commitment of the Intern for a period following the Bond agreement;
                                </td>
                            </tr>
                            <tr style="padding-top:6px;">
                                <td width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                   NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained, the parties hereto agree as follows:
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px;">
                <!-- CLAUSES -->
                <tr><td height="20"></td></tr>

                
                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>1. Term:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   The Intern agrees for the bond with the Company for a period of two (2) years.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>2. Obligations:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
    
                                    During the term of this Agreement, the Intern agrees to diligently and faithfully perform the duties and responsibilities assigned by the Company.
                                </td>
                            </tr>

                            <tr style="margin-top:6px;">
                                <td  width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                    The Intern agrees for the time and efforts invested by the company for becoming an asset. In consideration of the Company’s investment in training and development of the Intern, the Intern agrees to fulfill the full terms of the employment and responsibilities assigned.
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>3. Termination:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    <strong>a. Early Termination by Intern:</strong>
                                    If the Intern terminates their employment with the Company before the completion of the bond period for any reason, the Intern agrees to reimburse the Company for expenses incurred in training, Salary paid till the last working day and other directly related expenses during the time.
                                </td>
                            </tr>

                            <tr style="margin-top:6px;">
                                <td  width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                    <strong>b. Termination by Company:</strong>
                                    The Company reserves the right to terminate the Intern's employment at any time during the bond period by specifying a valid reason, which shall not be limited to poor performance, misconduct, breach of Company policies and other as applicable by the company for the employment.
                                </td>
                            </tr>
                        </table>

                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               padding-top:50px;
                               font-family:'Inter', sans-serif;">

                        <strong>4. Confidentiality:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   The Intern acknowledges that during the employment, they may have access to confidential and proprietary information belonging to the Company. The Intern agrees not to disclose or use any such confidential information outside the scope of the employment, both during and after the term of this Agreement. Breach of any company policy can lead to impose penalty or legal actions under the terms of the employment by the company.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>5. Performance Management:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    During the term of the agreement the company is the sole arbitrator of the assessment to be made of your working efficiency, utility or loyalty to the company while taking a decision to give you increment or promotion to higher grade or terminating your services.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>6. Non-Compete and Non-Solicitation:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    During the term of the agreement and for 24 months following the end of the employment, the Intern agrees not to:
                                </td>
                                 </tr>
                                 <tr>
                                 <td colspan="2"  style="padding-left:14px;">
                                     <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                                        <tr>
                                            <td width="18" valign="top"></td>
                                            <td valign="top">
                                               Engage in any business that directly competes with the Company.
                                            </td>
                                        </tr>
                                        <tr style="padding-top:6px;">
                                            <td width="18" valign="top" style="padding-top:6px;"></td>
                                            <td valign="top" style="padding-top:6px;">
                                               Solicit or attempt to hire any employee of the Company.
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                    
                                 
                            </tr>

                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>7. Governing Law:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    This Agreement shall be governed by and construed in accordance with the prevailing Laws of India connected therewith. In case any dispute connected with this agreement arises between the parties, the same shall be adjudicated at SAS, Mohali, Punjab
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>8. Entire Agreement:</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    This Agreement constitutes the entire agreement between the parties with respect to the subject matter hereof and supersedes all prior agreements, understandings, negotiations, and discussions, whether oral or written, between the parties.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                 
                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>IN WITNESS WHEREOF</strong>, the parties hereto have executed this Agreement as of the date first written above.
                    </td>
                </tr>

                

                

                <!-- SIGN OFF -->
                <tr><td height="25"></td></tr>
                <tr><td style="font-family:Inter;font-size:14px;"><strong>Warm Regards</strong>,</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Priyanka</td></tr>
                <tr><td style="font-family:Inter;font-size:14px;">Manager – Human Resources</td></tr>

            </table>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px;">
                <tr>
                    <td width="100%">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                            <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;"/>
                            
                        </div>
                        <div style="display:inline-block; width:100%;">
                            <br>
                            <h3 style="font-size: 16px; font-family: 'Inter', sans-serif;">Human Resource Department</h3>
                        </div>
                    </td>
                    <td width="30%" align="right">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
                        </div>
                    </td>
                </tr>
            </table>
            <!-- STAMP BLOCK (UNCHANGED) -->
           <!--  <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:30px; page-break-inside:avoid;">
                <tr>
                    <td>
                        <h4 style="margin:0;font-family:Inter;font-size:16px;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                        <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;">
                        <br><br>
                        <h3 style="font-family:Inter;font-size:16px;">Human Resource Department</h3>
                    </td>
                </tr>
            </table> -->

        </div>
    </div>

</div>

</body>
</html>
