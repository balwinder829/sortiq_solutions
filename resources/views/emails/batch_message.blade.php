<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Batch Message</title>
</head>
<body>

<h3>Batch Notification</h3>

<p><strong>Batch:</strong> {{ $batchName }}</p>
<p><strong>Trainer:</strong> {{ $trainerName }}</p>

<hr>

<div style="line-height:1.6">
{!! nl2br(e($messageText)) !!}
</div>
<br>

<p>
Regards <br>
{{ $trainerName }} <br>
Training Team
</p>

</body>
</html>