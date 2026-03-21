<!DOCTYPE html>
<html>
<head>
    <title>Access Denied</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8fafc;
            text-align: center;
            padding: 50px;
        }
        .box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e3342f;
        }
        p {
            color: #555;
        }
        .ip {
            margin-top: 10px;
            font-weight: bold;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>403 - Access Denied</h1>
        <p>Your IP is not allowed to access this application.</p>
        <div class="ip">IP: {{ $ip }}</div>
    </div>
</body>
</html>