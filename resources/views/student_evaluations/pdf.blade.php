<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Evaluation Management</title>
<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f8;
}
.container{
    width:97%;
    margin:20px auto;
    background:#fff;
    padding:20px;
}
h2{
    text-align:center;
    margin-bottom:20px;
}
table{
    width:100%;
    border-collapse:collapse;
    font-size:14px;
}
th,td{
    border:1px solid #ccc;
    padding:7px 8px;
    text-align:center;
}
th{
    background:#f0f0f0;
}
.checkbox-group{
    display:flex;
    justify-content:center;
    gap:8px;
}
.footer{
    margin-top:30px;
}

.checkbox-group table {
    width: 100%;
    border: 0;
}

.checkbox-group td {
    border: 0;
    padding: 1px 0;
    text-align: left;
    vertical-align: middle;
}

.checkbox-group input {
    vertical-align: middle;
}

.checkbox {
    display: inline-block;
    width: 11px;
    height: 11px;
    border: 1px solid #000;
    text-align: center;
    line-height: 10px;
    font-size: 10px;
    font-weight: bold;
    vertical-align: middle;
}

.checkbox.empty {
    border: 1px solid #000;
}

.checkbox {
    display:inline-block;
    width:12px;
    height:12px;
    border:1px solid #000;
    text-align:center;
    line-height:11px;
    font-size:11px;
    vertical-align:middle;
}
</style>
</head>

<body>

<div class="container">
<h2>Student Performance Evaluation</h2>

<table>
<thead>
<tr>
<th>Student ID</th>
<th>Name</th>
<th>Attendance %</th>
<th>Behavior</th>
<th>Technical</th>
<th>Live Project</th>
<th>Soft Skills</th>
<th>GitHub</th>
<th>Projects</th>
<th>Assignments</th>
</tr>
</thead>

<tbody>
<tr>
<td>{{ $evaluation->student->sno ?? '' }}</td>
<td>{{ ucwords($evaluation->student->student_name ?? 'N/A') }}</td>
<td>{{ $evaluation->attendance_percentage ?? '' }}%</td>


<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
           <!--  <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->behavior === 'good' ? '☑' : '☐' !!}
            </td> -->
            <td style="border:0; width:20px; vertical-align:middle;">
    <span class="checkbox">
         {!! $mode === 'full' && $evaluation->behavior === 'good' ? '&#10004;' : '' !!}
    </span>
</td>
            <td style="border:0; vertical-align:middle;">Good</td>
        </tr>
        <tr>
           <!--  <td style="border:0; vertical-align:middle;">
                    {!! $mode === 'full' && $evaluation->behavior === 'avg' ? '☑' : '☐' !!}
            </td> -->
            <td style="border:0; width:20px; vertical-align:middle;">
    <span class="checkbox">
         {!! $mode === 'full' && $evaluation->behavior === 'avg' ? '&#10004;' : '' !!}
    </span>
</td>
            <td style="border:0; vertical-align:middle;">Avg</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->behavior === 'bad' ? '☑' : '☐' !!}

            </td>
            <td style="border:0; vertical-align:middle;">Bad</td>
        </tr>
    </table>
</td>


<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->technical === 'good' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Good</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->technical === 'avg' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Avg</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->technical === 'bad' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Bad</td>
        </tr>
    </table>
</td>


<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->live_project === 'good' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Good</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->live_project === 'avg' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Avg</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->live_project === 'bad' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Bad</td>
        </tr>
    </table>
</td>


<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->soft_skills === 'good' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Good</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->soft_skills === 'avg' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Avg</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->soft_skills === 'bad' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Bad</td>
        </tr>
    </table>
</td>


<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->github === 'good' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Good</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->github === 'avg' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Avg</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->github === 'bad' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Bad</td>
        </tr>
    </table>
</td>

<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->projects === 'completed' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Completed</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->projects === 'partial' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Partial</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->projects === 'pending' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Pending</td>
        </tr>
    </table>
</td>

<td style="vertical-align:top; text-align:left;">
    <table style="border:0; width:100%;">
        <tr>
            <td style="border:0; width:20px; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->assignments === 'completed' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Completed</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->assignments === 'partial' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Partial</td>
        </tr>
        <tr>
            <td style="border:0; vertical-align:middle;">
                {!! $mode === 'full' && $evaluation->assignments === 'pending' ? '☑' : '☐' !!}
            </td>
            <td style="border:0; vertical-align:middle;">Pending</td>
        </tr>
    </table>
</td>

 

</tr>

</tbody>
</table>

<div class="footer">
<p><strong>Mentor Name: </strong>{{ ucwords($evaluation->trainer->name) ?? '____________________' }}</p>
<p><strong>Signature:</strong> ______________________</p>
</div>

</div>

</body>
</html>
