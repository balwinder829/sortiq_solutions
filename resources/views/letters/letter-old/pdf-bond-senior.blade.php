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
@page {
    margin: 0;
}
@page {
    margin-top: 15mm; /* margin for all pages EXCEPT first */
    margin-left: 0mm;
    margin-right: 0mm;
    margin-bottom: 15mm;
}

@page :first {
    margin-top: 0; /* first page with header */
}

@page :last {
    margin-bottom: 0mm;
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Two-Year Bond Agreement (Senior Employee)</strong></h2>
                    </td>
                </tr>
            </table>
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
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left;  padding-bottom:10px; font-family: 'Inter', sans-serif;">
                        <strong>Joining Date: </strong>
                            {{ \Carbon\Carbon::parse(
                                $letter->employee->joining_date ?? $letter->employee->joining_date
                            )->format('d M Y') }}

                    </td>
                </tr>

                <!-- BETWEEN -->
                <tr><td height="20"></td></tr>
                <tr>
                    <td style="text-align:center;font-family:Inter;font-weight:600;">BETWEEN</td>
                </tr>
                <tr><td height="10"></td></tr>

               <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd., having its principal office at E-51, Ground Floor, Industrial Area, Phase 8, Mohali, Punjab - 160072, hereinafter referred to as “the Company”,
                    </td>
                </tr>

                <!-- AND -->
                <tr><td height="15"></td></tr>
                <tr>
                    <td style="text-align:center;font-family:Inter;font-weight:600;">AND</td>
                </tr>
                <tr><td height="10"></td></tr>

                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>{{ ucwords($letter->employee->emp_name) }}</strong>, an experienced professional employed by Sortiq Solutions Pvt. Ltd., hereinafter referred to as “the Employee”.
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
                                   WHEREAS, the Company invests in onboarding, training, and professional development for senior employees;
                                </td>
                            </tr>
                            <tr style="padding-top:6px;">
                                <td width="18" valign="top" style="padding-top:10px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                   AND WHEREAS, the Employee desires to work with the Company and agrees to complete the bond period;
                                </td>
                            </tr>
                            <tr style="padding-top:6px;">
                                <td width="18" valign="top" style="padding-top:10px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                   NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained, the parties agree as follows:
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

                        <strong>1. Term</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   The Employee agrees to a bond of two (2) years, starting from the Joining Date.
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

                        <strong>2. Obligations</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
    
                                   The Employee shall diligently perform all duties assigned by the Company and adhere to Company policies.
                                </td>
                            </tr>

                             
                            <tr style="margin-top:6px;">
                                <td  width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:10px;">
                                    The Employee acknowledges the Company’s investment in training, development, and other onboarding processes and agrees to fulfill the full bond term.
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

                        <strong>3. Termination</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    <strong>a. Early Termination by Employee:</strong>
                                    If the Employee voluntarily resigns before completing the bond period, they shall reimburse the Company for:<br><br>

                                    Training / onboarding / development cost: ₹50,000 (or actual cost incurred, whichever is higher)<br><br>

                                    Salary paid till the last working day<br><br>

                                    Any other expenses directly related to onboarding or professional development

                                </td>
                            </tr>
</table>
 </td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               margin-top: 3px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">

                            <tr style="margin-top:6px;">
                                <td  width="18" valign="top" style="padding-top:6px;"></td>
                                <td valign="top" style="padding-top:6px;">
                                    <strong>b. Termination by Company:</strong>
                                    The Company reserves the right to terminate employment at any time for valid reasons, including:
                                    Poor performance<br><br>

                                    Misconduct<br><br>

                                    Breach of Company policies<br><br>

                                    Any other reason deemed valid by the Company

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

                        <strong>4. Training / Security Deposit</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    Purpose: To safeguard the Company’s investment in senior employee development.<br>
                                </td>
                            </tr>

                            <tr style="margin-top:10px;">
                                <td  width="18" valign="top" style="padding-top:10px;"></td>
                                <td valign="top" style="padding-top:10px;">
                                    
                                    Amount: ₹50,000 (or actual training/development cost, whichever is higher)<br><br>
                                    
                                    Payment Method: Bank transfer / cheque (Cheque No:  {{ ucwords($letter->check_number) }} / UTR: ______) or deduction from salary if agreed.<br><br>

                                    Refund: Upon completion of the bond period, the deposit will be refunded in full. Any outstanding dues will first be adjusted against this deposit.<br><br>


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

                        <strong>5. Confidentiality</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   The Employee may access confidential and proprietary Company information. The Employee agrees not to disclose or use such information outside employment. Breach may result in penalties or legal action.

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

                        <strong>6. Performance Management</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   The Company is the sole arbitrator of the Employee’s performance. Decisions regarding increments, promotions, or termination remain at the discretion of the Company.
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

                        <strong>7. Non-Compete and Non-Solicitation</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    During employment and for 24 months after leaving, the Employee shall not:<br><br>
                                </td>
                                 </tr>

                                 <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    <br>Join or start a business that directly competes with the Company<br><br>

                                    Solicit, recruit, or attempt to hire any employee of the Company<br><br>

                                    Divulge any confidential client, project, or Company strategy information
                                </td>
                                 </tr>
                                <!--  <tr>
                                 <td colspan="2"  style="padding-left:14px;">
                                     <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                                        <tr>
                                            <td width="18" valign="top"></td>
                                            <td valign="top">
                                               Join or start a business that directly competes with the Company
                                            </td>
                                        </tr>
                                        <tr style="padding-top:6px;">
                                            <td width="18" valign="top" style="padding-top:6px;"></td>
                                            <td valign="top" style="padding-top:10px;">
                                               Solicit, recruit, or attempt to hire any employee of the Company
                                            </td>
                                        </tr>
                                        <tr style="padding-top:6px;">
                                            <td width="18" valign="top" style="padding-top:6px;"></td>
                                            <td valign="top" style="padding-top:10px;">
                                               Divulge any confidential client, project, or Company strategy information
                                            </td>
                                        </tr>
                                    </table>
                                 </td>
                                    
                                 
                            </tr> -->

                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>8. Governing Law</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    This Agreement is governed by Indian laws. Any disputes will be adjudicated at SAS, Mohali, Punjab.
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

                        <strong>9. Entire Agreement</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    This Agreement constitutes the full understanding between the parties regarding the bond and supersedes any prior agreements, written or oral.
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

                        <strong>IN WITNESS WHEREOF</strong>, the parties have executed this Agreement as of the date above.
                    </td>
                </tr>

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
