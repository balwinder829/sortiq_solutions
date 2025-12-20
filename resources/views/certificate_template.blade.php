<!DOCTYPE html>
<html>
<head>
    <title>Certificate</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding: 50px; }
        h1 { color: #333; }
        p { font-size: 18px; margin: 10px 0; }
        .blank-line { display: inline-block; border-bottom: 1px dotted #000; width: 200px; }
    </style>
</head>
<body>
    <h1>Certificate of Completion</h1>
    <p>S.No.: <strong>{{ $student->serial_no }}</strong></p>
    <p>This is to certify that Mr./Ms. <strong>{{ $student->student_name }}</strong> from <strong>{{ $student->from_institute }}</strong></p>
    <p>Who has undertaken an internship program of <strong>{{ $student->program_name }}</strong> under technical department from <strong>{{ $student->start_date }}</strong> to <strong>{{ $student->end_date }}</strong> in <strong>{{ $student->location }}</strong> from the company "Sortiq Solutions Pvt. Ltd."</p>
</body>
</html>
