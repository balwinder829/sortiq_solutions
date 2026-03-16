@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col-md-2">
            <h1 class="page_heading">Helpdesk</h1>
        </div>

        <div class="col-md-8">
        <form method="GET" id="filterForm" class="mb-3">

<div class="row">

{{-- CATEGORY --}}
<div class="col-md-4">
<!-- <label>Category</label> -->

<select name="category" id="categoryFilter" class="form-control">

<option value="">--Choose Category--</option>

@foreach($categories as $id=>$name)

<option value="{{ $id }}"
{{ request('category')==$id ? 'selected':'' }}>
{{ $name }}
</option>

@endforeach

</select>
</div>

{{-- STATUS --}}
<div class="col-md-4">
<!-- <label>Status</label> -->

<select name="status" id="statusFilter" class="form-control">

<option value="">--Choose Status--</option>

<option value="draft"
{{ request('status')=='draft'?'selected':'' }}>
Draft
</option>

<option value="published"
{{ request('status')=='published'?'selected':'' }}>
Published
</option>

</select>
</div>

<div class="col-md-4 d-flex align-items-end">

<a href="{{ route('admin.helpdesk.articles.index') }}"
class="btn btn-secondary">
Reset
</a>

</div>

</div>

</form>
</div>
        <div class="col-md-2 text-end">
            <a href="{{ route('admin.helpdesk.articles.create') }}" class="btn btn-primary">
                Add
            </a>
        </div>
    </div>

    <table class="table table-bordered table-striped datatable" id="helpdesk_table">
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th width="160">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($items as $key=>$row)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ ucwords($row->title) }}</td>
                <td>{{ ucwords($row->technology->name ?? '') }}</td>
                <td>{{ ucwords($row->status) }}</td>
                <td>
                    <a href="{{ route('admin.helpdesk.articles.edit',$row->id) }}"
                       class="btn btn-sm" title="Edit"><i class="fa fa-edit"></i></a>

                    <form action="{{ route('admin.helpdesk.articles.destroy',$row->id) }}"
                        method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm" title="Delete" data-swal-confirm="Are you sure?">
                            <i class="fa fa-trash"></i>
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
<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#helpdesk_table').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
<script>

$(document).ready(function(){

$('#categoryFilter, #statusFilter').on('change', function(){

$('#filterForm').submit();

});

});

</script>
@endpush