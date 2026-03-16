@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Attendance - {{ ucwords($student->student_name) }}</h1>

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<div class="row mb-3">

<div class="col-md-3">
<div class="card p-3 text-center">
<h6>Attendance</h6>
<h3>{{ $percentage }}%</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<h6>Present</h6>
<h3>{{ $present }}</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<h6>Absent</h6>
<h3>{{ $absent }}</h3>
</div>
</div>

<div class="col-md-3">
<div class="card p-3 text-center">
<h6>Total Classes</h6>
<h3>{{ $total }}</h3>
</div>
</div>

</div>
<table class="table table-bordered table-striped" id="cv_index">
<thead>
<tr>
<th>#</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>

<tbody>

@foreach($records as $row)

<tr>

<td>{{ $loop->iteration }}</td>

<td>
{{ \Carbon\Carbon::parse($row->session->session_date)->format('d M Y') }}
</td>

<td>

@if($row->status == 'present')
<span class="badge bg-success">Present</span>
@elseif($row->status == 'late')
<span class="badge bg-warning">Late</span>
@else
<span class="badge bg-danger">Absent</span>
@endif

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
