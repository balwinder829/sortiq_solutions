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
            <h1 class="page_heading">Tutorials Management</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                 <a href="{{ route('tutorials.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                     Add Tutorial
                </a>
            </div>
        </div>
    </div>
   

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="tutorialsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Level</th>
                <th>Technology</th>
                <th>Video ID</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($tutorials as $tutorial)
            <tr>
                <td></td>
                <td><a href="{{ route('tutorials.show', $tutorial) }}">{{ $tutorial->title }}</a></td>
                <td>{{ $tutorial->level ?? 'N/A' }}</td>
                <td>{{ $tutorial->technology ?? 'N/A' }}</td>
                <td><a href="{{ $tutorial->embed_url }}" target="_blank">{{ $tutorial->youtube_id }}</a></td>
                <td>
                    {{-- Edit --}}
                    <a href="{{ route('tutorials.edit', $tutorial) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    {{-- Show/Watch --}}
                    <a href="{{ route('tutorials.show', $tutorial) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Watch">
                        <i class="fas fa-eye"></i>
                    </a>

                    {{-- Delete --}}
                    <form action="{{ route('tutorials.destroy', $tutorial) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                data-swal-confirm="Delete Tutorial?"
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

 

@push('scripts')
 
<script>
$(document).ready(function() {
    // Initialize DataTables only if you have many records and prefer client-side handling
    // If using Eloquent pagination ($tutorials->links()), you might omit DataTables.
    var table = $('#tutorialsTable').DataTable({
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