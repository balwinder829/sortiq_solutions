<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Certificate Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @csrf

    <style>
        body {
            margin: 0;
            background: #ffffff;
            font-family: "Segoe UI", Arial, sans-serif;
            color: #00173e;
        }

        /* MAIN WRAPPER */
        .page-wrapper {
            width: 100%;
            padding-top: 80px;
            padding-bottom: 60px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            padding: 0 20px;
        }

        /* HEADINGS */
        h1 {
            font-size: 32px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        h2 {
            font-size: 18px;
            font-weight: 500;
            letter-spacing: 0.6px;
            margin-bottom: 40px;
            text-transform: uppercase;
        }

        /* FORM */
        .form-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .form-wrapper input {
            width: 600px;
            max-width: 100%;
            padding: 18px 16px;
            font-size: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #eef4ff;
            color: #00173e;
            outline: none;
        }

        .form-wrapper button {
            padding: 18px 44px;
            font-size: 17px;
            font-weight: 600;
            background: #00173e;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        /* RESULT MESSAGE */
        .message {
            margin-top: 35px;
            font-size: 22px;
            font-weight: 600;
            display: none;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }

        .message.success {
            color: #0a8a00;
            display: flex;
        }

        .message.error {
            color: #d40000;
            display: flex;
        }

        .check-box {
            width: 26px;
            height: 26px;
            border: 2px solid #0a8a00;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: bold;
        }

        /* TABLE */
        .table-wrapper {
            margin-top: 60px;
            display: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #00173e;
            color: #ffffff;
            padding: 16px;
            font-size: 16px;
            border: 1px solid #ffffff;
        }

        tbody td {
            background: #00173e;
            color: #ffffff;
            padding: 16px;
            font-size: 16px;
            border: 1px solid #ffffff;
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <div class="container">

        <h1>CHECK CERTIFICATE AUTHENTICITY</h1>
        <h2>ENTER YOUR SERIAL NUMBER</h2>

        <form id="certCheckForm">
            <div class="form-wrapper">
                <input type="text" id="certificate_id" placeholder="Serial Number" required>
                <button type="submit">SUBMIT</button>
            </div>
        </form>

        <div id="resultMessage" class="message"></div>

        <div class="table-wrapper" id="tableWrapper">
            <table id="certTable">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>College</th>
                        <th>Duration</th>
                        <th>Technology</th>
                        <th>Start date</th>
                        <th>End date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$('#certCheckForm').on('submit', function(e) {
    e.preventDefault();

    let certId = $('#certificate_id').val().trim();
    let msgBox = $('#resultMessage');
    let tableWrap = $('#tableWrapper');

    msgBox.hide().removeClass('success error').html('');
    tableWrap.hide();

    if (!certId) {
        msgBox.addClass('error').html('❌ Serial number is required.').show();
        return;
    }

    $.post("{{ route('certificate.check') }}", {
        certificateId: certId,
        _token: "{{ csrf_token() }}"
    })
    .done(function(res) {
        if (res.success === true) {

            msgBox
                .addClass('success')
                .html('<span class="check-box">✔</span> Certificate Verified.')
                .show();

            let d = res.data;

            $('#certTable tbody').html(`
                <tr>
                    <td>${d.first_name ?? 'N/A'}</td>
                    <td>${d.college ?? 'N/A'}</td>
                    <td>${d.duration ?? 'N/A'}</td>
                    <td>${d.technology ?? 'N/A'}</td>
                    <td>${d.start_date ?? 'N/A'}</td>
                    <td>${d.end_date ?? 'N/A'}</td>
                </tr>
            `);

            tableWrap.show();

        } else {
            msgBox.addClass('error').html('❌ ' + res.message).show();
        }
    })
    .fail(function() {
        msgBox.addClass('error').html('❌ Server error. Please try again.').show();
    });
});
</script>

</body>
</html>
