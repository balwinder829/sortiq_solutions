@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Project Submissions</h1>

<table id="project_table" class="table table-bordered table-striped">

<thead>
<tr>
<th>ID</th>
<th>Project</th>
<th>GitHub</th>
<th>Live Link</th>
<th>Date</th>
</tr>
</thead>

<tbody>

@foreach($submissions as $submission)

<tr>

<td></td>

<td>{{ $submission->assignment->project->title }}</td>

<td>
<a href="{{ $submission->github_link }}" target="_blank">
View Repo
</a>
</td>

<td>
<a href="{{ $submission->live_link }}" target="_blank">
Live
</a>
</td>

<td>
{{ $submission->submitted_at }}
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