<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background:#f4f4f7; font-family: Arial, sans-serif;">

<!-- Outer Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f4f7; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- Email Container -->
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden;">

                <!-- Header -->
                <tr>
                    <td style="background:#4a6cf7; color:white; padding:20px 30px; text-align:center;">
                        <h2 style="margin:0; font-size:22px; font-weight:600;">
                            {{ $subjectLine }}
                        </h2>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding:25px 30px; color:#333; font-size:15px;">

                        <p>Hello Admin,</p>
                        <p>Here is your scheduled event reminder:</p>

                        <!-- EVENT CARDS -->
                        @foreach($events as $event)

                        @php
                            $image = url($event->cover_image);
                            $route = url("admin/{$event->event_type}/events/{$event->id}");
                        @endphp

                        <table width="100%" cellpadding="0" cellspacing="0" 
                               style="background:#fafafa; border:1px solid #ececec; border-radius:8px; margin-bottom:20px;">
                            <tr>
                                <td style="padding:15px;">

                                    <!-- Event image -->
                                    <img src="{{ $image }}" alt="Event Image"
                                         style="width:100%; max-width:540px; height:200px; object-fit:cover; border-radius:6px;">

                                    <!-- Event title -->
                                    <h3 style="margin:15px 0 5px; font-size:20px; color:#2d3748;">
                                        {{ $event->title }}
                                    </h3>

                                    <!-- Event details -->
                                    <p style="margin:0 0 10px; color:#555;">
                                        <strong>Type:</strong> {{ ucfirst($event->event_type) }} Event<br>
                                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->event_date)->format('d M, Y') }}
                                    </p>

                                    <!-- Button -->
                                    <a href="{{ $route }}"
                                       style="
                                           display:inline-block;
                                           background:#4CAF50;
                                           color:white;
                                           padding:10px 18px;
                                           text-decoration:none;
                                           border-radius:6px;
                                           font-size:14px;
                                           margin-top:10px;
                                       ">
                                        View Event
                                    </a>

                                </td>
                            </tr>
                        </table>

                        @endforeach

                        <p style="margin-top:25px; color:#555;">
                            Regards,<br>
                            <strong>Event Notification System</strong>
                        </p>

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="background:#f0f0f0; padding:15px 30px; text-align:center; color:#888; font-size:13px;">
                        © {{ date('Y') }} Sortiq Solutions. All rights reserved.
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
