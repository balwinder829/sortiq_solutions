@extends('layouts.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">Project Reviews</h1>

<table id="project_table" class="table table-bordered table-striped">

<thead>
<tr>
<th>ID</th>
<th>Submission</th>
<th>Rating</th>
<th>Feedback</th>
</tr>
</thead>

<tbody>

@foreach($reviews as $review)

<tr>

<td>{{ $review->id }}</td>

<td>{{ $review->submission_id }}</td>

<td>
<span class="badge bg-success">
{{ $review->rating }}/5
</span>
</td>

<td>{{ $review->feedback }}</td>

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