<!DOCTYPE html>
<html>
<head>
<title>Appointment Letter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">
<style>
body {
    margin: 0;
    padding: 0;
}
</style>
</head>
<body>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">
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
    <div class="certi-body" style=" background:url('../images/bg-shape.jpg')  no-repeat center; background-size:860px; padding-top: 60px;">
        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <h2 style="font-family: 'Katibeh', serif; text-align: center; font-size: 40px; font-weight: 700; color: #2c2e35; margin: 0 0 30px;"><strong>Two-Year Bond Agreement</strong></h2>
                    </td>
                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">
                <tr>
                    <td colspan="2" align="left" style="font-size: 14px;  padding-bottom:15px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>Date</strong> {{ \Carbon\Carbon::now()->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left;  padding-bottom:15px; font-family: 'Inter', sans-serif;">
                        <strong>Starting Date</strong> {{ \Carbon\Carbon::parse($letter->joining_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                       BETWEEN
                    </td>
                </tr>

                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        Sortiq Solutions Pvt. Ltd., an Indian Company having its principal place of office at <strong>E - 51, Ground Floor, Industrial Area, Phase 8 Mohali, Punjab - 160072</strong>, hereinafter (hereinafter referred to as "the Company"),
                    </td>
                </tr>
                <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        AND 
                    </td>
                </tr>

                 <tr>
                    <td align="left" style="font-size: 14px; line-height: 24px; text-align:left; font-family: 'Inter', sans-serif;">
                        <strong>{{ ucwords($letter->emp_name) }}</strong>, Intern of Sortiq Solutions Pvt. Ltd. (hereinafter referred to as "the Intern").
                    </td>
                </tr>
                  
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:35px;">

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif;">
                    <strong>Recitals:</strong>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; text-align:justify; padding-bottom:15px;">
                    WHEREAS, the Company desires to secure the commitment of the Intern for a period following the Bond agreement;
                    <br><br>
                    NOW, THEREFORE, in consideration of the mutual covenants and promises herein contained, the parties hereto agree as follows:
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>1. Term:</strong><br>
                    The Intern agrees for the bond with the Company for a period of two (2) years.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>2. Obligations:</strong><br>
                    During the term of this Agreement, the Intern agrees to diligently and faithfully perform the duties and responsibilities assigned by the Company.
                    <br><br>
                    The Intern acknowledges the time, effort, and resources invested by the Company in training and development and agrees to fulfill all assigned responsibilities.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>3. Termination:</strong><br>
                    <strong>a. Early Termination by Intern:</strong> If the Intern terminates employment before completion of the bond period, the Intern agrees to reimburse the Company for training costs, salary paid till the last working day, and other related expenses.
                    <br><br>
                    <strong>b. Termination by Company:</strong> The Company may terminate employment during the bond period for valid reasons including poor performance, misconduct, or breach of Company policies.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>4. Confidentiality:</strong><br>
                    The Intern agrees not to disclose or misuse any confidential or proprietary information during or after employment. Breach may result in penalties or legal action.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>5. Performance Management:</strong><br>
                    The Company shall be the sole arbitrator in assessing performance, efficiency, or loyalty for decisions related to increments, promotions, or termination.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>6. Non-Compete and Non-Solicitation:</strong>
                    <ul style="margin-top:6px; padding-left:18px;">
                        <li style="margin-bottom:6px;">Engage in any business that directly competes with the Company.</li>
                        <li>Solicit or attempt to hire any employee of the Company.</li>
                    </ul>
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>7. Governing Law:</strong><br>
                    This Agreement shall be governed by the laws of India. Any dispute shall be adjudicated at SAS Nagar (Mohali), Punjab.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif; padding-bottom:15px;">
                    <strong>8. Entire Agreement:</strong><br>
                    This Agreement constitutes the entire understanding between the parties and supersedes all prior agreements.
                </td>
            </tr>

            <tr>
                <td colspan="2" style="font-size:14px; line-height:24px; font-family:'Inter', sans-serif;">
                    <strong>IN WITNESS WHEREOF,</strong> the parties hereto have executed this Agreement as of the date first written above.
                </td>
            </tr>

            <tr>
                <td colspan="2"
                    style="font-size:14px;
                           line-height:24px;
                           font-family:'Inter', sans-serif;">
                    Warm Regards,
                </td>
            </tr>
            <tr>
                <td colspan="2"
                    style="font-size:14px;
                           line-height:24px;
                           font-family:'Inter', sans-serif;">
                    Priyanka
                </td>
            </tr>
            <tr>
                <td colspan="2"
                    style="font-size:14px;
                           line-height:24px;
                           padding-bottom:15px;
                           font-family:'Inter', sans-serif;">
                    Manager – Human Resources
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
                    <!-- <td width="30%" align="right">
                        <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">Agreed and Accepted</h4>
                        </div>
                    </td> -->
                </tr>
            </table>

        </div>
    </div>
    <div class="footer-shape" style="margin-top: 407px;position: fixed; bottom: 0px;">
        <img style="width: 100%; display: block;" src="images/footer-shape-1.png"/>
    </div>
</div>

</body>
</html>