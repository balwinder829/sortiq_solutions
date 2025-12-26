<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Offer Letter</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
        }
        .header {
            text-align: center;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .signature {
            margin-top: 50px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h3>OFFER LETTER</h3>
    </div>

    <p>
        Date:
        <strong>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</strong>
    </p>

    <p>
        Dear <strong>{{ $letter->emp_name }}</strong>,
    </p>

    <p>
        We are pleased to inform you that you have been selected for the position of
        <strong>{{ $letter->position }}</strong> with our organization.
    </p>

    <p>
        Your appointment will be effective from
        <strong>{{ \Carbon\Carbon::parse($letter->joining_date)->format('d M Y') }}</strong>.
        You will be required to report to the HR Department on the above-mentioned date.
    </p>

    <p>
        The terms and conditions of your employment, including compensation and other
        benefits, will be governed by the company policies in force from time to time.
    </p>

    <p>
        Kindly confirm your acceptance of this offer by reporting on the joining date
        as mentioned above.
    </p>

    <p>
        We welcome you to the organization and look forward to a successful association.
    </p>

    <div class="signature">
        <p>
            Yours sincerely,<br>
            <strong>For ________________________</strong><br>
            <strong>HR Department</strong><br>
            Authorized Signatory
        </p>
    </div>

</body>
</html>
