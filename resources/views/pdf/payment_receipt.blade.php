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
            <div class="rc-head-main" style="padding-top: 60px">
                <div class="inner-container" style="padding-left: 20px; padding-right: 20px">
                    <div class="rc-head" style="margin-top: 0px; display: inline-block; width: 100%">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="text-align: center">
                                    <h2
                                        style="
                                            font-family: &quot;Katibeh&quot;, serif;
                                            text-align: center;
                                            font-size: 60px;
                                            font-weight: 700;
                                            color: #2c2e35;
                                            margin: 0 0 30px;
                                        "
                                    >
                                        <strong>SORTIQ SOLUTIONS PVT.LTD.</strong>
                                    </h2>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: center;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-top: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    E-51, Ground Floor<br />
                                    Industrial Area, Phase 8 <br />
                                    SAS Nagar Mohali, Punjab - 160072
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="rc-body" style="margin-top: 90px; display: inline-block; width: 100%">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong>Receipt No.</strong>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    <strong>Date:</strong> {{ now()->format('d M Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    Received with thanks from
                                    <strong>Mr/Ms/Messers {{ $student->student_name }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    Rs. <strong>{{ $amount }}</strong> (Rupees <strong>{{ $amountInWords }}</strong>)
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    vide Cash/Cheque/Do No. <strong>{{ $transaction_no ?? '________' }}</strong> Date:
                                    <strong>{{ now()->format('d M Y') }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    Course <strong>{{ $student->courseData->course_name ?? '________' }}</strong>
                                </td>
                            </tr>
                            <tr>
                                <td
                                    style="
                                        text-align: left;
                                        font-size: 24px;
                                        line-height: 36px;
                                        padding-bottom: 20px;
                                        font-family: &quot;Inter&quot;, sans-serif;
                                    "
                                >
                                    Duration <strong>{{ $student->durationData->name ?? '________' }}</strong>
                                </td>
                            </tr>
                        </table>
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 140px">
                            <tr>
                                <td style="text-align: left">
									<div style="display:inline-block; width:100%;">
										<h4 style="font-size: 30px; line-height: 42px">(Authorised Signatory)</h4>
										<br>
									</div>
									<img src="{{ public_path('images/certificates_images/recipt-signature.png') }}" style="width:180px;"/>
									<div style="display:inline-block; width:100%;">
										<br>
										<h4 style="font-size: 30px; line-height: 42px">Note: Fees is Non-Refundable</h4>
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
