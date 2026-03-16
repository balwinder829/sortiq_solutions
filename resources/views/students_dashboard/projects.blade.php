@extends('layouts.students.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Assigned Projects</h1>

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<table class="table table-bordered table-striped" id="cv_index">

<thead>
<tr>
<th>#</th>
<th>Project</th>
<th>Deadline</th>
<th>Status</th>
<th>Submission</th>
</tr>
</thead>


<tbody>

@foreach($projects as $row)

<tr>

<td>{{ $loop->iteration }}</td>

<td>
{{ $row->project->title ?? '-' }}
</td>

<td>
{{ \Carbon\Carbon::parse($row->deadline)->format('d M Y') }}
</td>

<td>

@if($row->status == 'pending')

<span class="badge bg-warning">
Pending
</span>

@elseif($row->status == 'submitted')

<span class="badge bg-success">
Submitted
</span>

@else

<span class="badge bg-secondary">
{{ ucfirst($row->status) }}
</span>

@endif

</td>

<td>

@if($row->submission)

<a href="{{ asset($row->submission->file_path) }}"
class="btn btn-sm btn-primary">

View

</a>

@else

<span class="text-muted">
Not Submitted
</span>

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
