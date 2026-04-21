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
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 35px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Bond Agreement (Intern)</strong></h2>
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

            @php
                $amount = (int) ($letter->bond_amount ?? 20000);

                $bondAmountFormatted = '₹' . (
                    strlen($amount) > 3
                        ? preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', substr($amount, 0, -3)) . ',' . substr($amount, -3)
                        : $amount
                );

                $startDate = $letter->bond_start_date 
                ? \Carbon\Carbon::parse($letter->bond_start_date)->format('d M Y') 
                : now()->format('d M Y');

                $bondPeriod = ($letter->bond_period ?? '2.00') . ' years';
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:15px;">
                <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:5px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                       <strong>EMP Code: </strong> {{ ucwords($letter->employee->emp_code) }}
                    </td>
                </tr>
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
                        <strong>Sortiq Solutions Pvt. Ltd.</strong>, an Indian Company having its principal place of office at E-51, Second Floor , Phase - 8, Industrial Area, S.A.S. Nagar, Mohali, Punjab 160071, hereinafter referred to as “the Company”,
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
                        <strong>{{ ucwords($letter->employee->emp_name) }}</strong>, Intern of Sortiq Solutions Pvt. Ltd., hereinafter referred to as “the Intern”.
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
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;font-family:'Inter', sans-serif;">
                                   
                                   WHEREAS, the Company desires to secure the continued commitment of the Intern for a defined period following this Bond Agreement;
                                </td>
                            </tr>
                            <tr style="padding-top:6px;">
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:5px;font-family:'Inter', sans-serif;">   
                                   NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained, the parties hereto agree as follows:
                                </td>
                            </tr>
                           
                        </table>
                       
                    </td>
                </tr>

                
                </table>
                <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:5px;">
                <!-- CLAUSES -->
                <tr><td height="0"></td></tr>

                
                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>1. Term</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px; padding-left:18px;font-family:'Inter', sans-serif;">   
                                   The Intern agrees to a bond with the Company for a period of {{ $bondPeriod }}, commencing from the Starting Date mentioned above.
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
                    </td>
                </tr>
                 <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                        During the term of this Agreement:
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    •  The Intern agrees to diligently perform all duties and responsibilities assigned by the Company.<br>
                                    • The Intern acknowledges the time, effort, and resources invested by the Company in their training and development, and agrees to fulfill the full term of this Agreement.<br>
                                    
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
                               padding-top:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>3. Termination</strong>
                    </td>
                </tr>
                 <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                         <strong>a. Early Termination by Intern:</strong><br>
                         If the Intern voluntarily terminates employment before completing the bond period, the Intern agrees to reimburse the Company for:
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Training costs incurred (fixed amount: {{ $bondAmountFormatted }} or actual cost, whichever is higher)<br>
                                    • Salary paid until the last working day<br>
                                    • Any other directly related expenses incurred by the Company<br>
                                  
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                 <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                         <strong>b. Termination by Company:</strong><br>
                         The Company reserves the right to terminate the Intern at any time during the bond period for valid reasons, including but not limited to:
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Poor performance<br>
                                    • Misconduct<br>
                                    • Breach of Company policies<br>
                                  
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

                        <strong>4. Security Cheque Usage Clause</strong>
                    </td>
                </tr>
                 <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                        In the event that the Employee resigns before completion of the agreed bond period, or leaves the Company without serving the required notice period or without formal approval, the Employee agrees to compensate the Company as per the terms of this Agreement.</td></tr>
                 <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                        The Employee hereby authorizes the Company to present the submitted security cheque solely for the purpose of recovering the agreed training and onboarding cost, as specified in this Agreement.</td></tr>
                 <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                        The cheque shall be used only in case of breach of bond terms and not otherwise. The Company agrees to use the cheque in a fair and reasonable manner in accordance with applicable laws.</td>
                    
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>5. Mutual Separation & Non-Enforcement of Bond</strong>
                    </td>
                </tr>
                 <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                       In the event that the Employee resigns by providing the required notice period of three (3) months and obtains formal approval from the Company, or where the separation is mutually agreed upon by both parties, the conditions of the bond shall not be enforced.</td></tr>
                 <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                        In such cases, the Employee shall not be liable to pay any training or onboarding costs, and any security cheque or deposit provided shall not be presented or used for recovery purposes.</td></tr>
                 <tr>
                        <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">

                        The Company agrees to process full and final settlement as per applicable policies upon add at the end of the boundcompletion of all formalities.
                    </td>
                </tr>

                 

                 <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>6. Training Cost / Security Deposit</strong>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18; font-family:'Inter', sans-serif;">
                         <strong>a. Purpose:</strong><br>
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • To safeguard the investment in training and development of the Intern, a security deposit is required.<br>
                                   
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18; font-family:'Inter', sans-serif;">
                         <strong>b. Security Deposit:</strong><br>
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Amount: {{ $bondAmountFormatted }} (or actual training cost, whichever is higher)<br>
                                    • Payment Method: Bank transfer / cheque (Cheque No: {{ ucwords($letter->check_number) }} / UTR: ______) or via deduction from salary, if mutually agreed.<br>
                                   
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18; font-family:'Inter', sans-serif;">
                         <strong>c. Refund:</strong><br>
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Upon successful completion of the bond period, the security deposit will be refunded in full.<br>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18; font-family:'Inter', sans-serif;">
                         Any dues owed by the Intern will be adjusted against this deposit.<br>
                    </td>
                </tr>
                

                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                                
                               font-family:'Inter', sans-serif;">

                        <strong>7. Confidentiality</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                                   The Intern may have access to confidential information of the Company and agrees not to disclose or use it outside the scope of employment. Breach may result in penalties or legal action.

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

                        <strong>8. Performance Management</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                            <tr>
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                                   The Company is the sole arbitrator of the Intern’s performance and any decisions regarding increments, promotions, or termination.
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

                        <strong>9. Non-Compete and Non-Solicitation</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                         During the term of the agreement and for 24 months following the end of the employment, Intern agrees not to:
                       <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td width="30"></td>
                                <td style="font-size: 14px; line-height: 24px;">
                                    • Engage in any business that directly competes with the Company<br>
                                    • Solicit or attempt to hire any employee of the Company<br>                                  
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                         
                    </td>
                </tr>

                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>10. Governing Law</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
                                    This Agreement is governed by the prevailing laws of India. Any dispute arising hereunder shall be adjudicated at SAS Nagar, Mohali, Punjab.
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

                        <strong>11. Entire Agreement</strong>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:6px;">
                            <tr>
                                 <td colspan="2" style="font-size:14px; line-height:24px; padding-bottom:15px;padding-left:18px; font-family:'Inter', sans-serif;">
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

                        <strong>Note: In the event that the notice period is not fully served, the final salary, experience letter, relieving letter, and other relevant documents will not be issued.</strong>
 
                    </td>
                </tr>
                 </table>
            <pagebreak />

            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:0px;">
                <tr>
                    <td colspan="2"
                        style="font-size:14px;
                               line-height:24px;
                               padding-bottom:15px;
                               font-family:'Inter', sans-serif;">

                        <strong>IN WITNESS WHEREOF</strong>, the parties have executed this Agreement as of the date first written above.
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
