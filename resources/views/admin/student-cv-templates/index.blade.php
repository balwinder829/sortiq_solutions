@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-center">

<div class="col-md-8">
<h1 class="page_heading">CV Templates</h1>
</div>

<div class="col-md-4">
<div class="d-flex justify-content-end gap-2">

<a href="{{ route('admin.student.cv-templates.create') }}"
class="btn"
style="background-color:#6b51df;color:#fff;">
Add Template
</a>

</div>
</div>

</div>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped" id="project_table">

<thead>
<tr>
<th>ID</th>
<th>Name</th>
<th>Sample CV</th>
<th>Status</th>
<th width="120">Actions</th>
</tr>
</thead>

<tbody>

@foreach($templates as $template)

<tr>

<td>{{ $template->id }}</td>

<td>{{ $template->name }}</td>

<td>

@if($template->sample_cv)

<a href="{{ asset('uploads/cv-samples/'.$template->sample_cv) }}"
target="_blank">

Download Sample

</a>

@endif

</td>

<td>

@if($template->status)
<span class="badge bg-success">Active</span>
@else
<span class="badge bg-danger">Inactive</span>
@endif

</td>

<td>

<a href="{{ route('admin.student.cv-templates.edit',$template->id) }}"
class="btn btn-sm">
<i class="fa fa-edit"></i>
</a>

<form method="POST"
action="{{ route('admin.student.cv-templates.destroy',$template->id) }}"
style="display:inline-block">

@csrf
@method('DELETE')

<button class="btn btn-sm"
onclick="return confirm('Delete template?')">

<i class="fa fa-trash"></i>

</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>
<script>
    $(document).ready(function() {
    var table = $('#project_table').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        paging: true,       
        columnDefs: [
            {
                targets: 0, // first column
                searchable: false,
                orderable: false
            }
        ]
        
    });

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
    
});
</script>
@endsection