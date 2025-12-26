<!doctype html>
<html>
    <head>
        <title>Receipt</title>
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link
            href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap"
            rel="stylesheet"
        />
    </head>
    <body>
        <div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff">
            <div class="rc-head-main" style="padding-top: 50px">
                <div class="inner-container" style="padding-left: 20px; padding-right: 20px">
                    <div class="rc-head" style="margin-top: 0px; display: inline-block; width: 100%">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align: center">
                                    <h2
                                        style="
                                            font-family: &quot;Katibeh&quot;, serif;
                                            text-align: center;
                                            font-size: 50px;
                                            font-weight: 700;
                                            color: #2c2e35;
                                            margin: 0 0 20px;
                                        "
                                    >
                                        <strong>SORTIQ SOLUTIONS PVT. LTD.</strong>
                                    </h2>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: center;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-top: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    E-51, 2nd Floor<br />
                                    Industrial Area, Phase 8 <br />
                                    SAS Nagar Mohali, Punjab - 160072
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="rc-body" style="margin-top: 40px; display: inline-block; width: 100%">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong>Receipt No.</strong> 152
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 22px;
                                        line-height: 34px;
                                        padding-bottom: 10px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong>Date:</strong> {{ now()->format('d M Y') }}
                                </td>
                            </tr>
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
                                    Received with thanks from <strong>Mr/Ms/Messers <u>{{ $student->student_name }}</u></strong> Rs. <strong><u>{{ $amount }}</u></strong> (Rupees <strong><u>{{ $amountInWords }}</u></strong>) vide Cash/Cheque/Do No. <strong><u>{{ $transaction_no ?? '________' }}</u></strong> Dated: <strong><u>{{ now()->format('d M Y') }}</u></strong> Course <strong><u>{{ $student->courseData->course_name ?? '________' }}</u></strong> Duration <strong><u>{{ $student->durationData->name ?? '________' }}</u></strong>
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
</html>
