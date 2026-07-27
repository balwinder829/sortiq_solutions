@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Blocked Numbers</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('admin.blocked-numbers.create') }}"
       class="btn btn-primary mb-3">
        Block New Number
    </a>
            </div>
        </div>
    </div>
     

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   

    <table class="table table-bordered" id="pagesTable">
        <thead>
        <tr>
            <th>#</th>
            <th>Number</th>
            <th>Occurrences</th>
            <th>Blocked At</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($blockedNumbers as $blocked)
            <tr>
                <td></td>
                <td>{{ $blocked->number }}</td>
                <td>{{ $blocked->occurrence_count }}</td>
                <td>{{ \Carbon\Carbon::parse($blocked->blocked_at)->format('d M Y h:i A') }}</td>
                <td>
                    <a href="{{ route('admin.blocked-numbers.show', $blocked) }}"
                       class="btn btn-sm btn-info">
                        View
                    </a>

                    <a href="{{ route('admin.blocked-numbers.edit', $blocked) }}"
                       class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form method="POST"
      action="{{ route('admin.blocked-numbers.destroy', $blocked) }}"
      class="d-inline">
    @csrf
    @method('DELETE')

    <button type="submit"
            class="btn btn-sm btn-danger"
            data-swal-delete
            data-swal-confirm="Delete this blocked number?">
        Delete
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
    var table = $('#pagesTable').DataTable({
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
});
</script>
@endsection
