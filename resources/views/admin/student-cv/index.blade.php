@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Generated CVs</h1>

@if(session('success'))

<div class="alert alert-success">
{{ session('success') }}
</div>
@endif

<table class="table table-bordered table-striped" id="cv_index">

<thead>

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Created</th>
<th width="180">Actions</th>
</tr>

</thead>

<tbody>

@foreach($cvs as $cv)

<tr>

<td>{{ $cv->id }}</td>

<td>{{ $cv->full_name }}</td>

<td>{{ $cv->email }}</td>

<td>{{ $cv->phone }}</td>

<td>{{ $cv->created_at->format('d M Y') }}</td>
<td>

<a href="{{ route('student.cv.preview',$cv->id) }}"
class="btn btn-sm "
target="_blank"
data-bs-toggle="tooltip"
title="Preview CV">

<i class="fa fa-eye"></i>

</a>


<a href="{{ route('student.cv.download',[$cv->id, $cv->template_key ?: 'classic']) }}"
class="btn btn-sm"
data-bs-toggle="tooltip"
title="Download CV">

<i class="fa fa-download"></i>

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
