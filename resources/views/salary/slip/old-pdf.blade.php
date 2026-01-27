<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>

<h3>Salary Slip – {{ date('F', mktime(0,0,0,$salarySlip->month,1)) }} {{ $salarySlip->year }}</h3>

<p><strong>Name:</strong> {{ $salarySlip->emp_name }}</p>
<p><strong>Employee Code:</strong> {{ $salarySlip->emp_code }}</p>
<p><strong>Designation:</strong> {{ $salarySlip->position }}</p>
<p><strong>Date:</strong> {{ now()->format('d F Y') }}</p>


<br>

<table>
    <tr>
        <th>Earnings</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td>Basic Salary</td>
        <td>{{ number_format($salarySlip->basic_salary, 2) }}</td>
    </tr>
    <tr>
        <td>HRA</td>
        <td>{{ number_format($salarySlip->hra, 2) }}</td>
    </tr>
    <tr>
        <td>Allowance</td>
        <td>{{ number_format($salarySlip->allowance, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Gross Salary</strong></td>
        <td><strong>{{ number_format($salarySlip->gross_salary, 2) }}</strong></td>
    </tr>
</table>

<br>

<table>
    <tr>
        <th>Deductions</th>
        <th>Amount</th>
    </tr>
    <tr>
        <td>Total Deduction</td>
        <td>{{ number_format($salarySlip->deduction, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Net Salary</strong></td>
        <td><strong>{{ number_format($salarySlip->net_salary, 2) }}</strong></td>
    </tr>
</table>

<p style="margin-top:30px; font-size:11px;">
    This is a system-generated salary slip and does not require a signature.
</p>

</body>
</html>
