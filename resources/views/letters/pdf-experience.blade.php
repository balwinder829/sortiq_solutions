<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Experience Letter</title>
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
        <h3>EXPERIENCE LETTER</h3>
    </div>

    <p>
        This is to certify that <strong>{{ $letter->emp_name }}</strong>
        (Employee Code: <strong>{{ $letter->emp_code ?? 'N/A' }}</strong>)
        was employed with our organization as <strong>{{ $letter->position }}</strong>
        from <strong>{{ \Carbon\Carbon::parse($letter->joining_date)->format('d M Y') }}</strong>
        to <strong>{{ \Carbon\Carbon::parse($letter->relieving_date)->format('d M Y') }}</strong>.
    </p>

    <p>
        During the tenure of employment, {{ $letter->emp_name }} carried out the assigned
        responsibilities with sincerity, professionalism, and commitment.
        The employee demonstrated a positive attitude and maintained good conduct
        throughout the period of service.
    </p>

    <p>
        The total duration of employment with the organization is
        <strong>{{ $letter->experience_time }}</strong>.
    </p>

    <p>
        This letter is being issued upon the employee’s request for whatever
        purpose it may serve.
    </p>

    <p>
        Issued on:
        <strong>{{ \Carbon\Carbon::parse($letter->issue_date)->format('d M Y') }}</strong>
    </p>

    <div class="signature">
        <p>
            For <strong>________________________</strong><br>
            <strong>HR Department</strong><br>
            Authorized Signatory
        </p>
    </div>

</body>
</html>
