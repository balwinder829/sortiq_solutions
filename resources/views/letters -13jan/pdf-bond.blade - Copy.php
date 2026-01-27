<!DOCTYPE html>
<html>
<head>
<title>Bond Letter</title>

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

    {{-- HEADER --}}
    <div class="head-shape">
        <img style="width: 100%; display: block;"
             src="{{ public_path('images/certificates_images/head-shape.png') }}"/>
    </div>

    <div class="head-main" style="padding-top: 40px;">
        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="68%" align="left">
                        <img style="width: 100%; max-width: 200px;"
                             src="{{ public_path('images/certificates_images/logo-sortiq.png') }}"
                             width="200"/>
                    </td>
                    <td width="32%" align="left">
                        <div style="max-width: 210px;">
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/cl.png') }}" style="width:15px;"/>
                                &nbsp;+91 96465 22110
                            </p>
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/email.png') }}" style="width:15px;"/>
                                &nbsp;info@sortiqsolutions.com
                            </p>
                            <p style="margin:0;font-family:'Inter',sans-serif;">
                                <img src="{{ public_path('images/certificates_images/globe.png') }}" style="width:15px;"/>
                                &nbsp;www.sortiqsolutions.com
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BODY --}}
    <div class="certi-body"
         style="background:url('{{ public_path('images/certificates_images/bg-shape.jpg') }}') no-repeat center;
                background-size:860px;
                padding-top:60px;">

        <div class="inner-container" style="padding-left: 30px; padding-right: 30px;">

            <table width="100%">
                <tr>
                    <td align="center">
                        <h2 style="font-family:'Katibeh',serif;
                                   font-size:40px;
                                   margin-bottom:30px;">
                            <strong>Bond Agreement</strong>
                        </h2>
                    </td>
                </tr>
            </table>

            <table width="100%" style="margin-top:35px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        <strong>Date:</strong>
                        {{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}
                    </td>
                </tr>
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        <strong>Employee Name:</strong>
                        {{ ucwords($letter->emp_name) }}
                    </td>
                </tr>
                <tr>
                    <td style="font-family:'Inter',sans-serif;">
                        <strong>Position:</strong>
                        {{ $letter->position }}
                    </td>
                </tr>
            </table>

            {{-- CONTENT --}}
            <table width="100%" style="margin-top:35px;">
                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px;">
                        This Bond Agreement is entered into between
                        <strong>Sortiq Solutions Pvt. Ltd.</strong> and
                        <strong>{{ ucwords($letter->emp_name) }}</strong>
                        as a condition of employment.
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        <strong>Bond Period:</strong>
                        {{ \Carbon\Carbon::parse($letter->bond_start_date)->format('d M Y') }}
                        to
                        {{ \Carbon\Carbon::parse($letter->bond_end_date)->format('d M Y') }}
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        <strong>Bond Amount:</strong>
                        Rs. {{ number_format($letter->bond_amount, 2) }}/-
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        <strong>Terms & Conditions:</strong><br>
                        {!! nl2br(e($letter->bond_terms)) !!}
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        If the employee leaves the organization before completion
                        of the bond period, the above-mentioned bond amount shall
                        be payable to the company as compensation.
                    </td>
                </tr>

                <tr>
                    <td style="font-family:'Inter',sans-serif; line-height:24px; padding-top:15px;">
                        This agreement is binding and enforceable as per company
                        policies and applicable laws.
                    </td>
                </tr>
            </table>

            {{-- SIGNATURE --}}
            <table width="100%" style="margin-top:40px;">
                <tr>
                    <td width="70%">
                        <strong>For Sortiq Solutions Pvt. Ltd.</strong><br><br>
                        Authorized Signatory
                    </td>
                    <td width="30%" align="right">
                        <strong>Agreed & Accepted</strong><br><br>
                        {{ ucwords($letter->emp_name) }}
                    </td>
                </tr>
            </table>

        </div>
    </div>

    {{-- FOOTER --}}
    <div class="footer-shape" style="position:fixed; bottom:0;">
        <img style="width:100%;"
             src="{{ public_path('images/certificates_images/footer-shape-1.png') }}"/>
    </div>

</div>

</body>
</html>
