@extends('layouts.app')

@section('content')

<style>
table.dataTable td {
    text-transform: capitalize;
}
</style>

<div class="container">

<div class="row mb-2 align-items-center">

    <div class="col-md-8">
        <h1 class="page_heading">Assigned Projects</h1>
    </div>

    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">

            <a href="{{ route('student-project-assignments.create') }}"
               class="btn"
               style="background-color:#6b51df;color:#fff;">
               Assign Project
            </a>

        </div>
    </div>

</div>


@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif


<div class="table-responsive">

<table  id="project_table" class="table table-bordered table-striped">

<thead>

<tr>
<th>ID</th>
<th>Project</th>
<th>Student</th>
<th>Deadline</th>
<th>Status</th>
<th width="120">Actions</th>
</tr>

</thead>


<tbody>

@foreach($assignments as $assignment)

<tr>

<td></td>

<td>
{{ $assignment->project->title ?? '-' }}
</td>

<td>
{{ $assignment->student->student_name ?? '-' }}
</td>

<td>
{{ $assignment->deadline ?? '-' }}
</td>

<td>

@if($assignment->status == 'assigned')
<span class="badge bg-secondary">Assigned</span>

@elseif($assignment->status == 'in_progress')
<span class="badge bg-warning">In Progress</span>

@elseif($assignment->status == 'submitted')
<span class="badge bg-primary">Submitted</span>

@elseif($assignment->status == 'reviewed')
<span class="badge bg-success">Reviewed</span>

@endif

</td>


<td>

<a href="{{ route('student-project-assignments.show',$assignment->id) }}"
class="btn btn-sm"
data-bs-toggle="tooltip"
title="View">

<i class="fa fa-eye"></i>

</a>

<form action="{{ route('student-project-assignments.destroy',$assignment->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button type="submit"
class="btn btn-sm"
onclick="return confirm('Delete assignment?')">

<i class="fa fa-trash"></i>

</button>

</form>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>


<div class="mt-3">

</div>


</div>
<script>
    $(document).ready(function() {
    var table = $('#project_table').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        // paging: true,       
        // info: false,           
        // lengthChange: false,
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