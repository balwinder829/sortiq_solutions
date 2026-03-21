<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>

<p>Dear {{ $recipient->recipient_name ?? 'Sir/Madam' }},</p>

{!! $body !!}

<br><br>

<p>Regards,<br>
Team</p>

</body>
</html>