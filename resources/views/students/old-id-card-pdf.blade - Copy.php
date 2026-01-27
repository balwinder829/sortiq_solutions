<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0; padding:0; font-family: Arial, sans-serif;">

<div style="
    width:85.6mm;
    height:54mm;
    border:1px solid #333;
    border-radius:6px;
    background:#f9fafb;
    position:relative;
">

    <!-- HEADER -->
    <div style="
        background:#6b51df;
        color:#ffffff;
        text-align:center;
        font-size:10px;
        font-weight:bold;
        padding:4px;
        border-top-left-radius:6px;
        border-top-right-radius:6px;
    ">
        Sortiq Solutions Pvt. Ltd
    </div>

    <!-- BODY -->
    <div style="padding:6px;">
        <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <!-- PHOTO -->
                <td style="width:30%; text-align:center; vertical-align:top;">
                    <div style="
                        width:60px;
                        height:70px;
                        border:1px solid #999;
                    "></div>
                </td>

                <!-- DETAILS -->
                <td style="width:70%; padding-left:6px; font-size:9px; vertical-align:top;">
                    <div style="font-size:11px; font-weight:bold;">
                         {{ $student->student_name }}
                    </div>


                    <div>
                        <strong>Session:</strong> {{ $student->sessionData->session_name ?? '-' }}
                    </div>

                    <div style="margin-top:4px;">
                        <strong>Course:</strong> {{ $student->courseData->course_name}}
                    </div>

                    <div>
                        <strong>Phone:</strong> {{ $student->contact ?? '-' }}
                    </div>

                </td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
