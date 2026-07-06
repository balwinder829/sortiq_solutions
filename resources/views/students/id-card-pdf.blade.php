<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Card</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Katibeh&display=swap" rel="stylesheet">    
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 14px;
            line-height: 1.8;
            margin:0px;
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
<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff; margin-top: 50px; text-align: center;">
    <div class="certi-body" style="width:100%; max-width:204px; height:324px; background-color: #fff;display: inline-block; padding:10px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" width="100" align="center" style="font-size: 11px; line-height: 9px; padding: 0px; font-family: 'Katibeh', serif;  padding-left: 0px; padding-bottom: 10px; color: #191616;">
                        <strong>SORTIQ SOLUTIONS PVT. LTD.</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="100" align="center" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-left: 0px; padding-bottom: 2px; color: #191616;">
                        <strong>E-51, Ground Floor, Industrial Area,<br> Phase 8 Mohali, Punjab - 160071</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="100" align="center" style="font-size: 13px; line-height: 16px; padding: 0px; font-family: 'Inter', sans-serif;  padding-left: 0px; padding-bottom: 5px; color: #191616; font-weight:400;">
                        STUDENT IDENTITY CARD
                    </td>
                </tr>
                <tr>
     <td colspan="2" align="center" style="height:100px; vertical-align:middle;">
    <div
        style="
            width:80px;
            height:100px;
            border:1px solid #000;
            margin:0 auto;
            font-size:9px;
            line-height:100px;
            text-align:center;
            color:#555;
        "
    >
        PHOTO
    </div>
</td>


                </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;">
                <tr>
                    <td width="40%" align="left" style="font-size: 13px; line-height: 16px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 5px; color: #191616; vertical-align: top;">
                        <strong>Name </strong>
                    </td>
                    <td width="60%" align="left" style="font-size: 13px; line-height: 16px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 5px; color: #191616;">
                        {{ ucwords($student->student_name) }}
                    </td>
                </tr>
                <tr>
                    <td width="40%" align="left" style="font-size: 13px; line-height: 16px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 5px; color: #191616; vertical-align: top;">
                        <strong>Reg ID </strong>
                    </td>
                    <td width="60%" align="left" style="font-size: 13px; line-height: 16px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 5px; color: #191616;">
                        {{ ucwords($student->sno) }}
                    </td>
                </tr>
                <tr>
                    <td width="40%" align="left" style="font-size: 13px; line-height: 16px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 5px; color: #191616; vertical-align: top;">
                        <strong>Program</strong>
                    </td>
                    <td width="60%" align="left" style="font-size: 13px; line-height: 16px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 5px; color: #191616;">
                        {{ ucwords($student->course_name) ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="40%" align="left" style="font-size: 13px; line-height: 16px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 5px; color: #191616; vertical-align: top;">
                        <strong>Valid Till</strong>
                    </td>
                    <td width="60%" align="left" style="font-size: 13px; line-height: 16px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 5px; color: #191616;">
                        {{ $student->end_date ? \Carbon\Carbon::parse($student->end_date)->toFormattedDateString() : '-' }}

                    </td>
                </tr>
            </table>    
                
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="50%" align="left" style="font-size: 11px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616;padding-right: 5px;">
                        <img style="width: 100%; max-width:55px; display:block;" src="{{ public_path('images/student_id_card_images/hr-signature.png') }}"/>
                        <span style="display: inline-block;">Issuing Authority</span>
                    </td>
                    <td  width="50%" align="right">
                        <div class="h-logo">
                            <img style="width: 100%; max-width: 120px; margin-top: 8px;" src="{{ public_path('images/student_id_card_images/logo-sortiq.png') }}"/>
                        </div>
                    </td>
                </tr>
            </table>
    </div>
            
</div>

</body>
</html>
