@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Helpdesk Categories</h1>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.helpdesk.categories.create') }}" class="btn btn-primary">
                Add Category
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped datatable" id="catTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Slug</th>
                <th width="160">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $key => $row)
                <tr>
                    <td>{{ $key+1 }}</td>
                    <td>{{ $row->name }}</td>
                    <td>{{ $row->slug }}</td>
                    <td>
                        <a href="{{ route('admin.helpdesk.categories.edit',$row->id) }}" class="btn btn-sm"><i class="fas fa-edit"></i></a>

                        <form action="{{ route('admin.helpdesk.categories.destroy',$row->id) }}"
                              method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"
                                data-swal-confirm="Are you sure you want to delete this?"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection
@push('styles')
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#catTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush