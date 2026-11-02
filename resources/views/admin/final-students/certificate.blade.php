<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: sans-serif; color: #172554; }
        .certificate { border: 7px double #1e3a8a; padding: 32px 42px; height: 480px; text-align: center; }
        .eyebrow { text-transform: uppercase; letter-spacing: 4px; font-size: 13px; color: #475569; }
        h1 { font-size: 34px; letter-spacing: 2px; margin: 14px 0 8px; }
        h2 { font-size: 23px; margin: 18px 0; color: #1d4ed8; }
        .name { font-size: 29px; font-weight: bold; border-bottom: 1px solid #94a3b8; display: inline-block; padding: 0 22px 8px; margin: 18px 0; }
        .body-copy { font-size: 16px; line-height: 1.8; color: #334155; }
        .details { font-size: 14px; line-height: 1.7; margin-top: 12px; }
        .footer { margin-top: 40px; width: 100%; }
        .footer td { width: 33%; text-align: center; padding-top: 18px; font-size: 12px; }
        .line { border-top: 1px solid #64748b; width: 80%; margin: 0 auto 8px; }
    </style>
</head>
<body>
<div class="certificate">
    <div class="eyebrow">Official Document</div>
    <h1>{{ strtoupper($certificateType) }}</h1>
    <h2>Certificate of Achievement / Confirmation</h2>
    <div class="body-copy">This is to certify that</div>
    <div class="name">{{ $student->student_name }}</div>
    <div class="body-copy">
        has been recognized for {{ strtolower($certificateType) }} in connection with
        <strong>{{ $courseName }}</strong>.
    </div>
    @if($student->college_name)
        <div class="details">Institution / College: <strong>{{ $student->college_name }}</strong></div>
    @endif
    @if($customDetails)
        <div class="details">{{ $customDetails }}</div>
    @endif
    @if($student->benefit_details)
        <div class="details">{{ $student->benefit_details }}</div>
    @endif
    <table class="footer">
        <tr>
            <td><div class="line"></div>Authorized Signatory</td>
            <td><div class="line"></div>Date: {{ \Carbon\Carbon::parse($certificateDate)->format('d M Y') }}</td>
            <td><div class="line"></div>Organization Seal</td>
        </tr>
    </table>
</div>
</body>
</html>
