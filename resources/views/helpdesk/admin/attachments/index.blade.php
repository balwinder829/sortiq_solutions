@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-4">Attachments</h1>

<a href="{{ route('admin.helpdesk.attachments.create') }}" class="btn btn-primary mb-3">
Upload File
</a>

<table class="table table-bordered table-striped datatable">
<thead>
<tr>
<th>#</th>
<th>File</th>
<th>Type</th>
<th>Downloads</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($items as $key=>$row)
<tr>
<td>{{ $key+1 }}</td>
<td>{{ $row->file_name }}</td>
<td>{{ $row->file_type }}</td>
<td>{{ $row->downloads }}</td>
<td>
<form action="{{ route('admin.helpdesk.attachments.destroy',$row->id) }}"
method="POST">
@csrf
@method('DELETE')
<button class="btn btn-danger btn-sm">Delete</button>
</form>
</td>
</tr>
@endforeach
</tbody>

</table>

</div>

@endsection
