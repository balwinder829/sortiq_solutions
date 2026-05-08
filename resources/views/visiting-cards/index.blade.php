@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Visiting Cards</h1>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('visiting-cards.create') }}"
                   class="btn"
                   style="background-color:#6b51df;color:#fff;">
                    Add Visiting Card
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="cardsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Company</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($cards as $card)
            <tr>
                <td></td>
                <td>{{ $card->name }}</td>
                <td>{{ $card->designation }}</td>
                <td>{{ $card->company_name }}</td>
                <td>{{ $card->phone_primary }}</td>
                <td>
                    <a href="{{ route('visiting-cards.show', $card) }}" class="btn btn-sm" title="View">
                        <i class="fas fa-eye"></i>
                    </a>

                    <a href="{{ route('visiting-cards.edit', $card) }}" class="btn btn-sm" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
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
    var table = $('#cardsTable').DataTable({
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
@endpush
