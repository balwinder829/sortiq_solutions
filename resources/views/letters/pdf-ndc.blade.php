<!DOCTYPE html>
<html>
<head>
<title>Non-Disclosure and Confidentiality Agreement</title>
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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Non-Disclosure and Confidentiality Agreement</strong></h2>
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
                       <strong>Employee Name: </strong> {{ ucwords($letter->employee->emp_name) }}
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
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                       <strong>Designation: </strong> {{ ucwords($letter->employee->position) }}
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
                    <td  style="font-size: 14px; line-height: 24px; text-align:center; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd., principal office at E-51, Ground Floor, Industrial Area, Phase 8, Mohali, Punjab - 160071, hereinafter referred to as “the Company”,
                    </td>
                </tr>

                <!-- AND -->
                <tr><td height="15"></td></tr>
                <tr>
                    <td style="text-align:center;font-family:Inter;font-weight:600;">AND</td>
                </tr>
                <tr><td height="10"></td></tr>

                <tr>
                    <td style="font-size: 14px; line-height: 24px; text-align:center; font-family: 'Inter', sans-serif;">
                        <strong>{{ ucwords($letter->employee->emp_name) }}</strong>, hereinafter referred to as “the Employee”.
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

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   WHEREAS, the Employee will have access to highly confidential information, proprietary projects, client data, systems, and Company intellectual property;<br><br>

                                   AND WHEREAS, the Company requires the Employee to maintain strict confidentiality and prevent any unauthorized personal use or external exposure;<br><br>

                                   NOW, THEREFORE, the parties agree as follows:
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

                        <strong>1. Definition of Confidential Information</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   Confidential Information includes, but is not limited to:<br><br>

                                    Project plans, source code, designs, prototypes, algorithms, and technical specifications<br><br>

                                    Client data, contracts, proposals, pricing, communications, and sensitive personal client information<br><br>

                                    Company strategies, financials, business plans, and operational methods<br><br>

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

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                     Internal systems, software, login credentials, IDs, and access permissions<br><br>

                                    Any work, ideas, or outputs produced during employment<br><br>

                                    Any information used in the course of employment that is not publicly available
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

                        <strong>2. Obligations of the Employee</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
    
                                   The Employee agrees to:<br><br>

                                    Strictly maintain confidentiality of all Company information during and after employment.<br><br>

                                    Use confidential information solely for Company purposes.<br><br>

                                    Not use any information for personal purposes, including but not limited to:<br><br>

                                    Freelancing or personal projects<br><br>

                                    Portfolio showcases or public demonstrations<br><br>

                                    Any personal gain outside the Company<br><br>

                                    Immediately report any accidental disclosure or data loss to the Company.<br><br>

                                    Return all Company property, data, and work upon leaving the Company, including digital copies.
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

                        <strong>3. Prohibited Actions</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    The Employee shall not:<br><br>

                                    Copy, reproduce, or distribute confidential data for personal use<br><br>

                                    Share Company projects, designs, or intellectual property outside authorized work<br><br>

                                    Use Company work in freelancing, portfolio, competitions, or personal services<br><br>

                                    Leak project information, client details, or internal processes to competitors or external parties<br><br>

                                    Misuse Company login credentials, software, or access systems

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

                        <strong>4. Liability and Penalties</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                             

                            <tr style="margin-top:10px;">
                                <td  width="18" valign="top" style="padding-top:10px;"></td>
                                <td valign="top" style="padding-top:10px;">
                                  Maximum Claim / Damages:<br><br>

                                    Any breach may result in the Employee being liable for up to ₹50,00,000 (Rupees Fifty Lakh) or actual loss incurred, whichever is higher.<br><br>

                                    Legal Consequences:<br><br>

                                    Unauthorized disclosure, data theft, or misuse may be prosecuted under the Indian Penal Code (IPC) and IT Act 2000, including criminal action.<br><br>

                                    Employment Consequences:<br><br>

                                    Immediate termination without notice<br><br>

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

                        

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                             

                            <tr style="margin-top:10px;">
                                <td  width="18" valign="top" style="padding-top:10px;"></td>
                                <td valign="top" style="padding-top:10px;">
                                    
                                     Forfeiture of all dues, salary, and benefits<br><br>

                                     Potential career blacklist or reference restrictions<br><br>

                                    Company may file civil and criminal proceedings<br><br>

                                    Indemnification:<br><br>

                                    The Employee shall indemnify the Company for all losses, damages, legal fees, or penalties resulting from the violation of this NDA.
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

                        <strong>5. Duration of Obligation</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                  The Employee’s obligations under this NDA remain during employment and for 5 years post-termination, or until the confidential information becomes public by legitimate means.
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

                        <strong>6. Governing Law and Jurisdiction</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                   This NDA is governed by Indian laws, and any disputes shall be adjudicated exclusively at SAS, Mohali, Punjab.
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

                        <strong>7. Entire Agreement</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td width="18" valign="top"></td>
                                <td valign="top">
                                    This Agreement constitutes the full understanding regarding confidentiality, intellectual property, and non-personal use obligations and supersedes all prior agreements.

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
