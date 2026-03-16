@extends('layouts.app')

@section('content')

<div class="container">

<h4 class="mb-4">
Mark Attendance - {{ $batch->batch_name }}
</h4>

{{-- DATE SELECTOR --}}
<form method="GET" class="mb-3">

<label class="form-label">Attendance Date</label>

<input type="date"
name="date"
class="form-control"
value="{{ $date->toDateString() }}"
max="{{ now()->toDateString() }}"
onchange="this.form.submit()">

</form>


{{-- ATTENDANCE FORM --}}
<form method="POST" action="{{ route('trainer.attendance.save') }}">

@csrf

<input type="hidden" name="batch_id" value="{{ $batch->id }}">

<input type="hidden" name="attendance_date" value="{{ $date->toDateString() }}">


<table class="table table-bordered">

<thead>
<tr>
<th>#</th>
<th>Student</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@foreach($students as $student)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $student->student_name }}</td>

<td>

<select name="attendance[{{ $student->id }}]" class="form-control">

<option value="present"
{{ isset($records[$student->id]) && $records[$student->id]=='present' ? 'selected':'' }}>
Present
</option>

<option value="absent"
{{ isset($records[$student->id]) && $records[$student->id]=='absent' ? 'selected':'' }}>
Absent
</option>

<option value="late"
{{ isset($records[$student->id]) && $records[$student->id]=='late' ? 'selected':'' }}>
Late
</option>

</select>

</td>

</tr>

@endforeach

</tbody>

</table>

<button class="btn btn-success">
Save Attendance
</button>

</form>

</div>

@endsection