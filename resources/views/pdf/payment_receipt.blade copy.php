<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: 'sans-serif';
            font-size: 11px;
            color: #111;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        td {
            padding: 4px;
            vertical-align: middle;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        /* Underline fields */
        .line-fill {
            border-bottom: 1px solid #777;
        }
        
        /* Headers & Sections */
        .header-title {
            font-size: 24px;
            font-weight: bold;
            color: #1e1e1e;
            letter-spacing: 0.5px;
        }
        .receipt-badge {
            background-color: #1e1e1e;
            color: #ffffff;
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            padding: 10px;
            letter-spacing: 2px;
        }
        
        /* Checkbox styling alternative for mPDF */
        .checkbox {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #333;
            margin-right: 4px;
            vertical-align: middle;
        }
        
        /* Blocks for Boxed sections */
        .box-title {
            background-color: #1e1e1e;
            color: #fff;
            padding: 4px 10px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
            border-radius: 3px 3px 0 0;
        }
        .box-container {
            border: 1px solid #777;
            border-radius: 0 4px 4px 4px;
            padding: 8px;
            height: 140px; /* Aligns both boxes visually */
        }
        
        /* Center block for final amount */
        .amount-received-box {
            border: 1px solid #111;
            padding: 6px 20px;
            font-weight: bold;
            font-size: 13px;
            width: 40%;
            margin: 15px auto;
            text-align: center;
        }
        
        /* Footer features bar */
        .footer-bar {
            background-color: #1e1e1e;
            color: #fff;
            font-size: 9px;
            font-weight: bold;
            text-align: center;
        }
        .footer-bar td {
            padding: 6px 2px;
            border-right: 1px solid #fff;
        }
        .footer-bar td:last-child {
            border-right: none;
        }
        
        .motto {
            font-style: italic;
            font-size: 12px;
            color: #555;
            text-align: center;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    @php
        $sessionName = optional($student->sessionData)->session_display_name ?? ''; 
        $gender = strtolower($student->gender ?? '');
        $title = ($gender === 'female')
		            ? ($student->is_married ? 'Mrs.' : 'Ms.')
		            : 'Mr.';
    @endphp
    <div style="border: 1px solid #000; padding: 12px; position: relative;">

        <table style="margin-bottom: 0px;">
            <tr>
                <td width="15%" style="padding-right: 10px;">
                    <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' stroke='%23333' stroke-width='4' fill='none'/><text x='50%25' y='55%25' font-weight='bold' font-size='40' text-anchor='middle' fill='%23333'>S</text></svg>" width="65" height="65" alt="Logo">
                </td>
                <td width="50%">
                    <div class="header-title">SORTIQ SOLUTIONS PVT. LTD.</div>
                    <div style="font-size: 9.5px; color: #444; margin-top: 4px;">
                        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'><path d='M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z'/></svg>" width="9"/> E-51, Industrial Area, Phase 8, SAS Nagar Mohali, Punjab-160071<br>
                        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'><path d='M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z'/></svg>" width="9"/> 9646522110 &nbsp;|&nbsp; <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'><path d='M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z'/></svg>" width="9"/> 9501381389<br>
                        <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'><path d='M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z'/></svg>" width="9"/> info@sortiqsolutions.com &nbsp;|&nbsp; <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24'><path d='M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.53c-.26-.81-1-1.4-1.9-1.4h-1v-3c0-.55-.45-1-1-1h-6v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z'/></svg>" width="9"/> www.sortiqsolutions.com
                    </div>
                </td>
                <td width="35%" style="vertical-align: top;">
                    <div class="receipt-badge">RECEIPT</div>
                    <table style="margin-top: 10px; font-size: 11px;">
                        <tr>
                            <td width="40%">Receipt No.</td>
                            <td class="line-fill">: {{ $student->sno }}</td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td class="line-fill">: {{ now()->format('d M Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <hr style="border: none; border-top: 2px solid #000; margin: 5px 0 15px 0;">

        <table>
            <tr>
                <td width="18%">Received with thanks from</td>
                <td class="line-fill" colspan="3">: <strong>{{$title}} <u>{{ ucfirst($student->student_name) }}</u></strong> </td>
            </tr>
            <tr>
                <td width="18%">Rupees (in words)</td>
                <td class="line-fill">: {{ $amountInWords ?? '' }}</td>
                <td width="4%" class="text-right">Rs.</td>
                <td class="line-fill" width="20%">{{ $amount ?? '' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="18%">Payment Mode</td>
                <td>: 
                    <span class="checkbox"></span> Cash &nbsp;&nbsp; 
                    <span class="checkbox"></span> UPI &nbsp;&nbsp; 
                    <span class="checkbox"></span> Bank Transfer &nbsp;&nbsp; 
                    <span class="checkbox"></span> Cheque &nbsp;&nbsp; 
                    <span class="checkbox"></span> Other <span style="border-bottom: 1px solid #777; width: 60px; display: inline-block;">&nbsp;</span>
                </td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="18%">Transaction ID / Cheque No.</td>
                <td class="line-fill">: {{ $transaction_no ?? '________' }}</td>
                <td width="8%" class="text-center">Dated</td>
                <td class="line-fill" width="25%">: {{ now()->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Towards Course / Program</td>
                <td class="line-fill">: {{ $student->course_name ?? '________' }}</td>
                <td class="text-center">Duration of Training</td>
                <td class="line-fill">: {{ ucwords($sessionName) ?? '________' }}</td>
            </tr>
        </table>

        <table>
            <tr>
                <td width="18%">Training Mode</td>
                <td colspan="3">: 
                    <span class="checkbox"></span> <strong>{{ $mode }} &nbsp;&nbsp; </strong>
                    <!-- <span class="checkbox"></span> Offline &nbsp;&nbsp; 
                    <span class="checkbox"></span> Hybrid -->
                </td>
            </tr>
            <tr>
                <td>Batch / Session</td>
                <td class="line-fill" width="40%">: {{ $batch_session ?? '' }}</td>
                <td class="text-center" width="15%">Duration (From - To)</td>
                <td class="line-fill" width="27%">: {{ $duration_range ?? '' }}</td>
            </tr>
        </table>

        <br>

        <table style="margin-top: 10px;">
            <tr>
                <td width="48%" style="vertical-align: top; padding: 0 10px 0 0;">
                    <div class="box-title">FEE DETAILS</div>
                    <div class="box-container">
                        <table style="font-size: 10.5px;">
                            <tr>
                                <td width="45%">Total Course Fees</td>
                                <td>: Rs.</td>
                                <td class="line-fill" width="45%">{{ $TotalFeesAamount ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Amount Received</td>
                                <td>: Rs.</td>
                                <td class="line-fill">{{ $amount ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Pending Fees</td>
                                <td>: Rs.</td>
                                <td class="line-fill">{{ $PendingFeesAmount ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Pending Fees Due Date</td>
                                <td>:</td>
                                <td class="line-fill">{{ $due_date ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td width="52%" style="vertical-align: top; padding: 0 0 0 10px;">
                    <div class="box-title">STUDENT DETAILS</div>
                    <div class="box-container">
                        <table style="font-size: 10.5px;">
                            <tr>
                                <td width="38%">Student Name</td>
                                <td class="line-fill">: {{$title}} {{ ucfirst($student->student_name) ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Father / Mother Name</td>
                                <td class="line-fill">: {{ ucfirst($student->f_name) ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Student Contact No.</td>
                                <td class="line-fill">: {{ $student->contact ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Email ID</td>
                                <td class="line-fill">: {{ $student->email_id ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>College / University Name</td>
                                <td class="line-fill">: {{ $student->collegeData->FullName ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Course / Program</td>
                                <td class="line-fill">: {{ $course_name ?? '' }}</td>
                            </tr>
                            <tr>
                                <td>Remarks</td>
                                <td class="line-fill">: {{ $remarks ?? '' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="amount-received-box">
            Amount Received &nbsp;&nbsp;&nbsp; Rs. <span style="border-bottom: 1px solid #000; width: 120px; display: inline-block;">{{ $amount ?? '' }}</span>
        </div>

        <table style="margin-top: 20px;">
            <tr>
                <td width="35%" style="vertical-align: bottom; height: 70px;">
                    <div style="border-bottom: 1px dashed #777; width: 150px; margin-bottom: 5px;"></div>
                    <strong>Authorized Signatory</strong>
                    <div style="font-size: 9px; color: #555; margin-top: 2px;">(Sortiq Solutions Pvt. Ltd.)</div>
                </td>
                
                <td width="25%" class="text-center" style="vertical-align: middle;">
                    <div style="border: 2px double #777; width: 75px; height: 75px; border-radius: 50%; margin: 0 auto; padding: 2px;">
                        <div style="font-size: 7px; font-weight: bold; margin-top: 15px; color: #555;">SORTIQ SOLUTIONS</div>
                        <div style="font-size: 8px; margin-top: 5px;">★ ★</div>
                        <div style="font-size: 7px; font-weight: bold; color: #555;">PVT. LTD.</div>
                    </div>
                </td>

                <td width="40%" style="vertical-align: top;">
                    <div style="background-color: #f5f5f5; border: 1px solid #ddd; padding: 6px; font-size: 9px; line-height: 1.3;">
                        <strong style="color: #444; background: #ddd; padding: 1px 4px;">NOTE:</strong>
                        <ul style="margin: 4px 0 0 12px; padding: 0;">
                            <li>Paid Fees / Registration Fees is Non-Refundable.</li>
                            <li>This receipt is valid subject to realisation of payment.</li>
                            <li>Fees once paid will not be refunded or transferred under any circumstances.</li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>

        <br>

        <table class="footer-bar">
            <tr>
                <td width="25%">🎓 QUALITY TRAINING</td>
                <td width="25%">👥 INDUSTRY EXPERTS</td>
                <td width="25%">📖 PRACTICAL LEARNING</td>
                <td width="25%">📈 BETTER FUTURE</td>
            </tr>
        </table>

        <div class="motto">Simplify &bull; Solve &bull; Succeed</div>
    </div>

</body>
</html>

<!-- 

    
                            <tr>
                                <td colspan="2"
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    Received with thanks from <strong>Mr/Ms/Messers <u>{{ $student->student_name }}</u></strong> vide Cash/Cheque/Do No. <strong><u>{{ $transaction_no ?? '________' }}</u></strong> Dated: <strong><u>{{ now()->format('d M Y') }}</u></strong> Course <strong><u>{{ $student->course_name ?? '________' }}</u></strong> Mode <strong><u>{{ $mode }}</u></strong> Duration <strong><u>{{ ucwords($sessionName) ?? '________' }}</u></strong>.
                                </td>
                            </tr>
                             <tr>
                                <td colspan="2" style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;"
                                ><strong><u>Fee Details</u></strong></td>
                               
                            </tr>

                            <tr>
                                <td style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >Total Fee</td>
                                <td 
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong><u>{{ $TotalFeesAamount }}</u></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >Registration Fees</td>
                                <td 
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong><u>{{ $RegFeesAamount }}</u></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >Paid Fees</td>
                                <td 
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong><u>{{ $PaidFees }}</u></strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >Pending Fees</td>
                                <td 
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong><u>{{ $PendingFeesAmount }}</u></strong>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px">
                            <tr>
                                <td style="text-align: left">
									<div style="display:inline-block; width:100%;">
										<h4 style="font-size: 24px; line-height: 36px">(Authorised Signatory)</h4>
										<br>
									</div>
									<img src="{{ public_path('images/certificates_images/recipt-signature.png') }}" style="width:160px;"/>
									<div style="display:inline-block; width:100%;">
										<br>
										<h4 style="font-size: 24px; line-height: 36px">Note: Fees is Non-Refundable</h4>
									</div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html> -->
