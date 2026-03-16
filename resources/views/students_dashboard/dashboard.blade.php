@extends('layouts.students.app')

@section('content')

<div class="container mt-4">

<h4 class="mb-4">
Welcome {{ $student->student_name }}
</h4>

<div class="row g-3">

{{-- Batch --}}
<div class="col-md-4">
<div class="card text-center shadow-sm">
<div class="card-body">

<h6 class="text-muted">My Batch</h6>

<h4 class="fw-bold">
{{ $batch->batch_name ?? 'Not Assigned' }}
</h4>

</div>
</div>
</div>


{{-- Attendance --}}
<div class="col-md-4">
<div class="card text-center shadow-sm">
<div class="card-body">

<h6 class="text-muted">Attendance</h6>

<h3 class="fw-bold">
{{ $attendancePercentage }} %
</h3>

</div>
</div>
</div>


<div class="col-md-4">
<div class="card text-center shadow-sm">
<div class="card-body">

<h6 class="text-muted">Fee Status</h6>

@if($student->pending_fees > 0)

<h3 class="fw-bold text-danger">
₹ {{ number_format($student->pending_fees) }}
</h3>

<p class="mb-0 text-muted">
Next Due: {{ \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') }}
</p>

@else

<h3 class="fw-bold text-success">
No Pending Fees
</h3>

@endif

</div>
</div>
</div>

</div>

</div>

@endsection