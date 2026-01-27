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
                    @if($employee->photo)
                        <img src="{{ public_path('images/employee_images/'.$employee->photo) }}"
                             style="
                                width:60px;
                                height:70px;
                                border:1px solid #999;
                                object-fit:cover;
                             ">
                    @else
                        <div style="
                            width:60px;
                            height:70px;
                            border:1px solid #999;
                        "></div>
                    @endif
                </td>

                <!-- DETAILS -->
                <td style="width:70%; padding-left:6px; font-size:9px; vertical-align:top;">
                    <div style="font-size:11px; font-weight:bold;">
                        {{ $employee->emp_name }}
                    </div>

                    <div style="font-size:9px; color:#555;">
                        {{ $employee->position }}
                    </div>

                    <div style="margin-top:4px;">
                        <strong>Emp Code:</strong> {{ $employee->emp_code }}
                    </div>

                    <div>
                        <strong>Phone:</strong> {{ $employee->user->phone ?? '-' }}
                    </div>

                    <div>
                        <strong>Blood:</strong> {{ $employee->blood_group ?? '-' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>
 

</div>

</body>
</html>
