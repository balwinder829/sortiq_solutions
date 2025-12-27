@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Test Categories</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('test-categories.create') }}" 
                   class="btn mb-3"
                   style="background-color:#6b51df;color:white;">
                   Add Category
                </a>
            </div>
        </div>
    </div>

    

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="categoryTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($categories as $cat)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $cat->name }}</td>
                    <td>{{ $cat->slug }}</td>

                    <td class="text-center">

                        <a href="{{ route('test-categories.edit', $cat->id) }}"
                           class="btn btn-sm"
                           title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('test-categories.destroy', $cat->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm"
                                    onclick="return confirm('Delete this category?')"
                                    title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                        
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function(){
    $('#categoryTable').DataTable();
});
</script>
@endpush
