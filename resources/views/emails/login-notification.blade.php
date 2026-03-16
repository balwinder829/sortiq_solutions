<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login notification</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f7; font-family: Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7; padding:40px 0;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">
                <tr>
                    <td style="background:#4a6cf7; color:white; padding:20px 30px; text-align:center;">
                        <h2 style="margin:0; font-size:22px;">Login Notification</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding:25px 30px; color:#333; font-size:15px;">
                        <p style="margin-top:0;">Someone has logged in to {{ config('app.name') }}.</p>
                        <table cellpadding="8" cellspacing="0" style="background:#f8f9fa; border-radius:6px; width:100%; max-width:400px;">
                            <tr><td style="font-weight:600;">User / Actor</td><td>{{ $actorName }}</td></tr>
                            <tr><td style="font-weight:600;">Type</td><td>{{ $guard === 'trainer' ? 'Trainer' : 'User' }}</td></tr>
                            <tr><td style="font-weight:600;">IP Address</td><td><code>{{ $ipAddress }}</code></td></tr>
                            <tr><td style="font-weight:600;">Time</td><td>{{ $loginTime }}</td></tr>
                        </table>
                        <p style="margin-bottom:0; margin-top:20px; color:#666; font-size:13px;">This is an automated notification. You can review activity in Admin → Activity.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

</body>
</html>
