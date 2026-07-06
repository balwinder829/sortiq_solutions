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
            padding: 10px 10px;
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
                <td width="20%" style="padding-right: 10px;">
                    
                    <img src="images/logo-sortiq.png" alt="Sortiq" height='60'>
                    <!-- <img src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='60' height='60' viewBox='0 0 100 100'><circle cx='50' cy='50' r='40' stroke='%23333' stroke-width='4' fill='none'/><text x='50%25' y='55%25' font-weight='bold' font-size='40' text-anchor='middle' fill='%23333'>S</text></svg>" width="65" height="65" alt="Logo"> -->
                </td>
                <td width="45%">
                    <div class="header-title">SORTIQ SOLUTIONS PVT. LTD.</div>
                    <div style="font-size: 9.5px; color: #444; margin-top: 4px;">
                         <table style="margin-bottom: 0px;">
                            <tr><td><span>E-51, Industrial Area, Phase 8, SAS Nagar Mohali, Punjab-160071</span><br></td></tr>
                            <tr><td><span style="padding-top: 10px; margin-top: 10px; margin-bottom: 10px;"> +91 9646522110 &nbsp;|&nbsp; +91 9501381389</span><br></td></tr>
                            <tr><td><span>info@sortiqsolutions.com &nbsp;|&nbsp; www.sortiqsolutions.com</span></td></tr>
                         </table>
                    </div>
                </td>
                <td width="35%" style="vertical-align: top;">
                    <div class="receipt-badge"><span style="padding: 0 0 0 10px;">RECEIPT</span></div>
                    <table style="margin-top: 10px; font-size: 11px;">
                        <tr>
                            <td width="40%">Receipt No.</td>
                            <td class="line-fill">: <strong>{{ $student->sno }}</strong></td>
                        </tr>
                        <tr>
                            <td>Date</td>
                            <td class="line-fill">: <strong>{{ now()->format('d M Y') }}</strong></td>
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
                <td class="line-fill">: <strong>{{ $amountInWords ?? '' }}</strong></td>
                <td width="12%" class="text-left">Rs.</td>
                <td class="line-fill" width="20%"><strong>{{ $amount ?? '' }}/-</strong></td>
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
                <td class="line-fill">: <strong>{{ $transaction_no ?? '________' }}</strong></td>
                <td width="12%" class="text-left">Dated</td>
                <td class="line-fill" width="20%">: <strong>{{ now()->format('d M Y') }}</strong></td>
            </tr>
            <tr>
                <td>Towards Course / Program</td>
                <td class="line-fill">: <strong>{{ $student->course_name ?? '________' }}</strong></td>
                <td class="text-left">Duration of Training</td>
                <td class="line-fill">: <strong>{{ ucwords($sessionName) ?? '________' }}</strong></td>
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
            <!-- <tr>
                <td>Batch / Session</td>
                <td class="line-fill" width="40%">: {{ $batch_session ?? '' }}</td>
                <td class="text-center" width="15%">Duration (From - To)</td>
                <td class="line-fill" width="27%">: {{ \Carbon\Carbon::parse($student->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($student->end_date)->format('d M Y') }}</td>
            </tr> -->
        </table>

        <br>

        <table style="margin-top: 10px;">
            <tr>
                <td width="48%" style="vertical-align: top; padding: 0 10px 0 0; border: 2px solid #777; ">
                    <div class="box-title"><span style="padding: 0 0 0 10px;">FEE DETAILS</span></div>
                    <div class="box-container">
                        <table style="font-size: 10.5px;">
                            <tr>
                                <td width="45%">Total Course Fees</td>
                                <td>: Rs.</td>
                                <td class="line-fill" width="45%"><strong>{{ $TotalFeesAamount ?? '' }}-/</strong></td>
                            </tr>
                            <tr>
                                <td>Amount Received</td>
                                <td>: Rs.</td>
                                <td class="line-fill"><strong>{{ $amount ?? '' }}/-</strong></td>
                            </tr>
                            <tr>
                                <td>Pending Fees</td>
                                <td>: Rs.</td>
                                <td class="line-fill"><strong>{{ $PendingFeesAmount ?? '' }}/-</strong></td>
                            </tr>
                            <tr>
                                <td>Pending Fees Due Date</td>
                                <td>:</td>
                                <td class="line-fill"><strong>{{ $student->next_due_date ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') : '-' }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </td>

                <td width="52%" style="vertical-align: top; padding: 0 0 0 10px; border: 2px solid #777; ">
                    <div class="box-title"><span style="padding: 10px;">STUDENT DETAILS</span></div>
                    <div class="box-container">
                        <table style="font-size: 10.5px;">
                            <tr>
                                <td width="38%">Student Name</td>
                                <td class="line-fill">: <strong>{{$title}} {{ ucfirst($student->student_name) ?? '' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Father / Mother Name</td>
                                <td class="line-fill">: <strong>{{ ucfirst($student->f_name) ?? '' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Student Contact No.</td>
                                <td class="line-fill">: <strong>{{ $student->contact ?? '' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Email ID</td>
                                <td class="line-fill">: <strong>{{ $student->email_id ?? '' }}</strong></td>
                            </tr>
                            <tr>
                                <td>College / University Name</td>
                                <td class="line-fill">: <strong>{{ $student->collegeData->FullName ?? '' }}</strong></td>
                            </tr>
                            <tr>
                                <td>Course / Program</td>
                                <td class="line-fill">: <strong>{{ $student->course_name ?? '________' }}</strong></td>
                            </tr>
                            <!-- <tr>
                                <td>Remarks</td>
                                <td class="line-fill">: {{ $remarks ?? '' }}</td>
                            </tr> -->
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <div class="amount-received-box">
            Amount Received &nbsp;&nbsp;&nbsp; Rs. <span style="width: 120px; display: inline-block;">{{ $amount ?? '' }}/-</span>
        </div>

        <table style="margin-top: 20px;">
            <tr>
                <td width="40%" style="vertical-align: bottom; height: 70px; text-align: center;">
                    <img src="{{ public_path('images/certificates_images/recipt-signature.png') }}" style="width:160px;"/>
                    <div style="border-bottom: 1px dashed #777; width: 150px; margin-bottom: 5px;"></div>
                    <strong>Authorized Signatory</strong>
                    <div style="font-size: 9px; color: #555; margin-top: 2px;">(Sortiq Solutions Pvt. Ltd.)</div>
                </td>
                
                <td width="20%" class="text-center" style="vertical-align: middle;">
                    <div style="width: 75px; height: 75px; border-radius: 50%; margin: 0 auto; padding: 2px;">
                       <!-- <div style="display:inline-block; width:100%;">
                            <h4 style="margin: 0; font-size: 16px; font-family: 'Inter', sans-serif;">For Sortiq Solutions Pvt. Ltd.</h4><br>
                            <img src="{{ public_path('images/certificates_images/certificate-stamp.png') }}" style="width:200px;"/>
                        </div> -->
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
                <td width="25%">QUALITY TRAINING</td>
                <td width="25%">INDUSTRY EXPERTS</td>
                <td width="25%">PRACTICAL LEARNING</td>
                <td width="25%">BETTER FUTURE</td>
            </tr>
        </table>

        <div class="motto">Simplify &bull; Solve &bull; Succeed</div>
    </div>

</body>
</html>