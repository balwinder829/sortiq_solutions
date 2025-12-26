<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif;">

    <h2>🎉 New Student Joined</h2>

    <table cellpadding="8" cellspacing="0" border="0">
        <tr>
            <td><strong>Student Name</strong></td>
            <td>{{ $student->student_name }}</td>
        </tr>

        <tr>
            <td><strong>Father Name</strong></td>
            <td>{{ $student->father_name }}</td>
        </tr>

        <tr>
            <td><strong>College</strong></td>
            <td>{{ $student->collegeData->FullName ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Technology</strong></td>
            <td>{{ $student->courseData->course_name ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Duration</strong></td>
            <td>{{ $student->durationData->name ?? '-' }}</td>
        </tr>

        <tr>
            <td><strong>Date of Joining</strong></td>
            <td>{{ \Carbon\Carbon::parse($student->date_of_joining)->format('d M Y') }}</td>
        </tr>
    </table>

    <p style="margin-top:20px;">
        Regards,<br>
        <strong>Student Joining System</strong>
    </p>

</body>
</html>
