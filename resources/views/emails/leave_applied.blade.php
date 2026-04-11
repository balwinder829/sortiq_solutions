<!DOCTYPE html>
<html>
<head>
    <title>Leave Application</title>
</head>
<body>

    <h3>New Leave Request</h3>

    <p><strong>Employee:</strong> {{ $leave->emp_name }}</p>
    <p><strong>Emp Code:</strong> {{ $leave->emp_code }}</p>
    <p><strong>Email:</strong> {{ $leave->email }}</p>

    <p><strong>From:</strong> {{ \Carbon\Carbon::parse($leave->from_date)->format('j F Y') }}</p>
    <p><strong>To:</strong> {{ \Carbon\Carbon::parse($leave->to_date)->format('j F Y') }}</p>
    <p><strong>Total Days:</strong> {{ $leave->total_days }}</p>

    <p><strong>Reason:</strong> {{ $leave->reason }}</p>

</body>
</html>