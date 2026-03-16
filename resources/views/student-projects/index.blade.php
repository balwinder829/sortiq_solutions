@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-3 align-items-center">

    <!-- Page Title -->
    <div class="col-md-4">
        <h1 class="page_heading mb-0">Student Projects</h1>
    </div>

    <!-- Filter -->
    <div class="col-md-4">
        <form method="GET" action="{{ route('student-projects.index') }}">
            <div class="d-flex gap-2">

                <select name="type" class="form-select" onchange="this.form.submit()">
                    <option value="">All Project Types</option>

                    <option value="major" {{ request('type')=='major' ? 'selected' : '' }}>
                        Major
                    </option>

                    <option value="mini" {{ request('type')=='mini' ? 'selected' : '' }}>
                        Mini
                    </option>
                </select>

                <a href="{{ route('student-projects.index') }}"
                   class="btn btn-secondary">
                   Reset
                </a>

            </div>
        </form>
    </div>

    <!-- Add Button -->
    <div class="col-md-4">
        <div class="d-flex justify-content-end">
            <a href="{{ route('student-projects.create') }}"
               class="btn"
               style="background-color:#6b51df;color:#fff;">
               Add Project
            </a>
        </div>
    </div>

</div>


@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif


<table id="project_table" class="table table-bordered table-striped">

<thead>
<tr>
<th>ID</th>
<th>Title</th>
<th>Type</th>
<th>Technology</th>
<th>Difficulty</th>
<th>Duration</th>
<th>Status</th>
<th width="120">Actions</th>
</tr>
</thead>

<tbody>

@foreach($projects as $project)

<tr>

<td>{{ $project->id }}</td>

<td>{{ $project->title }}</td>

<td>
<span class="badge bg-info">
{{ ucfirst($project->project_type) }}
</span>
</td>

<td>{{ $project->technology }}</td>

<td>{{ ucfirst($project->difficulty) }}</td>

<td>{{ $project->estimated_days }} days</td>

<td>
@if($project->status)
<span class="badge bg-success">Active</span>
@else
<span class="badge bg-danger">Inactive</span>
@endif
</td>

<td>

<a href="{{ route('student-projects.edit',$project->id) }}"
class="btn btn-sm">
<i class="fa fa-edit"></i>
</a>

<form action="{{ route('student-projects.destroy',$project->id) }}"
method="POST"
style="display:inline-block">

@csrf
@method('DELETE')

<button type="submit"
class="btn btn-sm"
 data-swal-delete data-swal-confirm="Do you want to delete this project?"
>

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
    $('#project_table').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        paging: true,       
        info: false,           
        lengthChange: false
    });

    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
    
});
</script>
@endsection