@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Pages</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('pages.create') }}" class="btn" style="background-color:#6b51df;color:#fff;">
                    Add Page
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="pagesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Slug</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pages as $page)
            <tr>
                <td>{{ $page->title }}</td>
                <td>{{ $page->slug }}</td>
                <td>
                    <span class="badge {{ $page->is_active ? 'bg-success' : 'bg-danger' }}">
                        {{ $page->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </td>
                <td>{{ $page->created_at->format('d M Y') }}</td>
                <td class="d-flex gap-1">

                     {{-- View Page --}}
                    <a href="{{ url('/'.$page->slug) }}"
                       class="btn btn-sm"
                       title="View Page"
                       target="_blank">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('pages.edit', $page) }}" class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>

                    {{-- DELETE --}}
                    <form action="{{ route('pages.destroy', $page) }}"
                          method="POST"
                          style="display:inline-block;"
                          onsubmit="return confirm('Are you sure you want to delete this?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm">
                            <i class="fa fa-trash"></i>
                        </button>
                    </form>

                   <form action="{{ route('pages.toggle', $page) }}" method="POST">
                        @csrf
                        <button class="btn btn-sm"
                                title="{{ $page->is_active ? 'Deactivate' : 'Activate' }}">
                            
                            @if($page->is_active)
                                <i class="fas fa-toggle-on text-success"></i>
                            @else
                                <i class="fas fa-toggle-off text-danger"></i>
                            @endif

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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#pagesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush
