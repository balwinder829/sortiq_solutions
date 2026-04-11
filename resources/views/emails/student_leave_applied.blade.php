<!DOCTYPE html>
<html>
<head>
    <title>Student Leave Application</title>
</head>
<body style="font-family: Arial;">

    <h3>New Student Leave Request</h3>

    <p><strong>Student Name:</strong> {{ $leave->student_name }}</p>
    <p><strong>SNO:</strong> {{ $leave->sno }}</p>

    @if($leave->contact)
        <p><strong>Contact:</strong> {{ $leave->contact }}</p>
    @endif

    @if($leave->email)
        <p><strong>Email:</strong> {{ $leave->email }}</p>
    @endif

    <hr>

    <p><strong>From:</strong> {{ \Carbon\Carbon::parse($leave->from_date)->format('j F Y') }}</p>
    <p><strong>To:</strong> {{ \Carbon\Carbon::parse($leave->to_date)->format('j F Y') }}</p>

    <p><strong>Total Days:</strong> {{ $leave->total_days }}</p>

    <p><strong>Reason:</strong></p>
    <p>{{ $leave->reason ?? 'N/A' }}</p>

</body>
</html>