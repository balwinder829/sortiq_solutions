<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Id card front</title>
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
    <div class="certi-body" style="background:url(&quot;{{ public_path('images/employee_id_card_images/id_front.png') }}&quot;)   no-repeat center; background-size:cover; width:100%; max-width:204px; height:324px; background-color: #fff;display: inline-block;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" width="100" align="center">
                        <div class="h-logo">
                            <img style="width: 100%; max-width: 120px; margin-top: 8px;" src="{{ public_path('images/employee_id_card_images/logo-sortiq.png') }}"/>
                        </div>
                    </td>
                </tr>
            </table>
           <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:9px;">
<tr>
<td colspan="2" align="center" height="180" style="height:185px; vertical-align:middle;">

    <div style="
        width:80px;
        height:80px;
        margin:auto;
        border-radius:40px;
        overflow:hidden;
    ">
        <img
            src="{{ public_path(
                $employee->photo
                    ? 'images/employee_images/' . $employee->photo
                    : 'images/employee_id_card_images/default-avatar.png'
            ) }}"
            style="
                width:80px;
                height:80px;
            "
        />
    </div>


 <!-- <div style="
    width:130px;
    height:130px;
    margin:auto;
    border:0px solid #ffffff;
    text-align:center;
    border-radius:65px;
    overflow:hidden;
">
    <img
        src="{{ public_path(
            $employee->photo
                ? 'images/employee_images/' . $employee->photo
                : 'images/employee_id_card_images/default-avatar.png'
        ) }}"
        style="
            width:127px;
            height:127px;
        "
    />
</div> -->

</td>
</tr>
</table>

            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:25px;">
                <tr>
                    <td colspan="2" width="100" align="center" style="font-size: 18px; line-height: 9px; padding: 0px; font-family: 'Inter', sans-serif;  padding-left: 0px; padding-right: 0px; color: #fff; font-weight:300;">
                        {{ ucwords($employee->emp_name) ?? '-' }}
                        <span style="height: 2px; width: 100%; background-color: #ff6d00; display: inline-block; margin-left: auto; margin-right: auto; max-width: 100px;"></span>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="100" align="center" style="font-size: 14px; line-height: 20px; padding-top: 2px; font-family: 'Inter', sans-serif; padding-left: 0px; padding-right: 0px; color: #fff;">
                        {{ ucwords($employee->position) ?? '-' }}
                    </td>
                </tr>
            </table>        
            
    </div>
    <div class="certi-body" style="width:100%; max-width:204px; height:324px; background-color: #fff; margin-top:0px;    border: 1px solid #eee; display: inline-block;     margin-left: 2px;">
        <div class="crd-inner" style="padding:5px; padding-bottom: 0;">
            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:0px;">
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Employe Code :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616;">
                        {{ $employee->emp_code }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Office Address :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        E-51, Second  Floor , Phase - 8, Industrial Area, S.A.S. Nagar, Mohali, Punjab 160071
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Emergency : <br>Contact No
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        {{ $employee->phone ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Email Address :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        {{ $employee->email ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Blood Group :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        {{ $employee->blood_group ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Date of Birth :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                         {{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('d/m/Y') : '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Residenttial Address:
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        {{ $employee->address ?? '-' }}
                    </td>
                </tr>
                <tr>
                    <td width="45%" align="left" style="font-size: 10px; line-height: 12px; padding: 0px; font-family: 'Inter', sans-serif;  padding-bottom: 4px; color: #191616; vertical-align: top;">
                        Identity Mark :
                    </td>
                    <td width="55%" align="left" style="font-size: 10px; line-height: 12px; padding-top: 0px; font-family: 'Inter', sans-serif; padding-bottom: 4px; color: #191616; vertical-align: top;">
                        
                    </td>
                </tr>
                <tr>
                    <td colspan="2" width="100%" align="right" style="font-size: 10px; line-height: 12px; padding-right: 5px; font-family: 'Inter', sans-serif; padding-bottom: 0px; color: #191616; vertical-align: top;">
                        <img style="width: 100%; max-width:55px; display:block;" src="{{ public_path('images/employee_id_card_images/hr-signature.png') }}"/>
                    </td>
                </tr>
            </table>
        </div>  
       
    </div>
            
</div>

</body>
</html>
