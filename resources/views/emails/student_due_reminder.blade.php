<p>Dear {{ $student->student_name }},</p>

@if($type === 'due_today')
    <p>This is a reminder that your fee payment is <strong>due today</strong>.</p>
@else
    <p>Your fee payment is <strong>overdue</strong>. Please clear it as soon as possible.</p>
@endif

<p><strong>Pending Amount:</strong> ₹{{ number_format($student->pending_fees) }}</p>
<p><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') }}</p>

<p>Please contact the office if you have already paid.</p>

<p>Regards,<br>

