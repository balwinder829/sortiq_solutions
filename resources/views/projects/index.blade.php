@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Projects</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('projects.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Project
                </a>
            </div>
        </div>
    </div>
   
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="projectsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Project Name</th>
                <th>Tech Stack</th>
                <th>Guthub Link</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($projects as $project)
            <tr>
                <td></td>
                <td>{{ $project->name }}</td>
                <td>{{ $project->tech_stack }}</td>
                <td>{{ $project->github_link }}</td>
                <td>
                    <!-- Edit -->
                    <a href="{{ route('projects.edit', $project) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Delete -->
                    <form action="{{ route('projects.destroy', $project) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                data-swal-confirm="Delete Project?"
                                data-bs-toggle="tooltip"
                                title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</div>
@endsection

@push('styles')
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"> -->
@endpush

@push('scripts')
<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
 -->
<script>
$(document).ready(function() {
    var table = $('#projectsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
         // paging: false,       
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
@endpush
