<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Certificate Verification</title>
    <style>
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 40px;
            flex-direction: column;
        }

        .form-container h3, .form-container h4 {
            font-weight: bold;
        }

        #certCheckForm {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        #certificateId {
            max-width: 500px;
            padding: 10px;
            font-size: 16px;
        }

        .submit_Certificate {
            background-color: #00173e;
            color: white;
            border: none;
            padding: 10px 25px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        #certResult {
            margin-top: 20px;
            font-weight: bold;
        }

        #certDataTable {
            margin-top: 10px;
            border-collapse: collapse;
        }

        #certDataTable th, #certDataTable td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #certDataTable th {
            background-color: #f2f2f2;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<div class="form-container">
    <h3>CHECK CERTIFICATE AUTHENTICITY</h3>
    <h4>ENTER YOUR SERIAL NUMBER</h4>

    <form id="certCheckForm">
        <input 
            type="text" 
            id="certificateId" 
            name="certificateId" 
            placeholder="Serial Number" 
            maxlength="400" 
            required
        >
        <button type="submit" class="submit_Certificate">SUBMIT</button>
    </form>

    <div id="certResult"></div>

    <table id="certDataTable" style="display:none;">
        <thead>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>College</th>
                <th>Duration</th>
                <th>Technology</th>
                <th>Stream</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
jQuery(document).ready(function($) {
    $('#certCheckForm').on('submit', function(e) {
        e.preventDefault();

        const certId = $('#certificateId').val();

        $.ajax({
            url: "{{ route('certificate.check') }}",
            type: "POST",
            data: {
                certificateId: certId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.exists) {
                    $('#certResult').text('✅ Certificate Verified.').css('color', 'green');

                    const data = response.data;
                    $('#certDataTable tbody').html(`
                        <tr>
                            <td>${data.first_name}</td>
                            <td>${data.last_name}</td>
                            <td>${data.colleage}</td>
                            <td>${data.duration}</td>
                            <td>${data.technology}</td>
                            <td>${data.stream}</td>
                            <td>${data.start_date}</td>
                            <td>${data.end_date}</td>
                        </tr>
                    `);

                    $('#certDataTable').show();
                } else {
                    $('#certResult').text('❌ Unverified certificate.').css('color', 'red');
                    $('#certDataTable').hide();
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || 'An unexpected error occurred';
                $('#certResult').text('❌ ' + msg).css('color', 'red');
                $('#certDataTable').hide();
            }
        });
    });
});

</script>

</body>
</html>
