@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Batch Attendance - {{ $batch->batch_name }}</h1>

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<table class="table table-bordered table-striped" id="cv_index">

<thead>
<tr>
<th>#</th>
<th>Date</th>
<th>Total Students</th>
<th>Present</th>
<th>Absent</th>
<th>Actions</th>
</tr>
</thead>


<tbody>

@foreach($sessionsAtt as $session)

@php

$total = $session->records->count();

$present = $session->records
->whereIn('status',['present','late'])
->count();

$absent = $session->records
->where('status','absent')
->count();

@endphp

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ \Carbon\Carbon::parse($session->session_date)->format('d M Y') }}</td>

<td>{{ $total }}</td>

<td class="text-success">{{ $present }}</td>

<td class="text-danger">{{ $absent }}</td>

<td>

<a href="{{ route('trainer.attendance.mark',$batch->id) }}?date={{ $session->session_date }}"
class="btn btn-sm btn-primary">

View / Edit

</a>

</td>

</tr>

@endforeach

</tbody>
</table>

</div>
<script>
	$(document).ready(function () {

    // SERVER-SIDE DATATABLE — only current page is loaded from server
    $('#cv_index').DataTable({
    
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order:[]
    });
    });
</script>
@endsection
