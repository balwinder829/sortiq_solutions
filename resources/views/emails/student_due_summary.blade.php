<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 750px;
            margin: auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #2b2b2b;
        }
        h3 {
            margin-top: 25px;
            color: #2b2b2b;
        }
        .btn {
            display: inline-block;
            padding: 12px 22px;
            border-radius: 5px;
            background: #007bff;
            color: #fff !important;
            text-decoration: none;
            margin: 10px 0 25px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background: #ffffff;
        }
        th {
            background: #28a745;
            color: white;
            padding: 10px;
            font-size: 14px;
            text-align: left;
        }
        td {
            border-bottom: 1px solid #e9ecef;
            padding: 10px;
            font-size: 14px;
        }
        .section-title {
            border-left: 5px solid #28a745;
            padding-left: 10px;
            font-size: 18px;
            font-weight: bold;
        }
        .section-title.overdue {
            border-color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Pending Fees Summary</h2>

    <div style="text-align:center;">
        <a href="{{ route('admin.pendingfees.list') }}" class="btn">View All Pending Fees</a>
    </div>

    {{-- Due Today Section --}}
    @if($dueToday->count() > 0)
        <h3 class="section-title">Due Today ({{ $dueToday->count() }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Session</th>
                    <th>Batch</th>
                    <th>Contact</th>
                    <th>Pending</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dueToday->take(10) as $s)
                    <tr>
                        <td>{{ $s->student_name }}</td>
                        <td>{{ $s->sessionData->session_name ?? '-' }}</td>
                        <td>{{ $s->batchData->batch_name ?? '-' }}</td>
                        <td>{{ $s->contact }}</td>
                        <td>₹{{ number_format($s->pending_fees, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($s->next_due_date)->format('d M, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Overdue Section --}}
    @if($overdue->count() > 0)
        <h3 class="section-title overdue">Overdue ({{ $overdue->count() }})</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Session</th>
                    <th>Batch</th>
                    <th>Contact</th>
                    <th>Pending</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($overdue->take(10) as $s)
                    <tr>
                        <td>{{ $s->student_name }}</td>
                       <td>{{ $s->sessionData->session_name ?? '-' }}</td>
                        <td>{{ $s->batchData->batch_name ?? '-' }}</td>
                        <td>{{ $s->contact }}</td>
                        <td>₹{{ number_format($s->pending_fees, 2) }}</td>
                        <td>{{ \Carbon\Carbon::parse($s->next_due_date)->format('d M, Y') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

</body>
</html>
