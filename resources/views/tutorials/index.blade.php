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
                                onclick="return confirm('Delete Tutorial?')"
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
    
    {{ $tutorials->links('pagination::bootstrap-5') }}

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
{{-- You might remove DataTables if you prefer simple pagination (like the $tutorials->links() above) --}}
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTables only if you have many records and prefer client-side handling
    // If using Eloquent pagination ($tutorials->links()), you might omit DataTables.
    $('#tutorialsTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100],
        paging: false,       
        info: false,           
        lengthChange: false
    });

    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
});
</script>
@endpush