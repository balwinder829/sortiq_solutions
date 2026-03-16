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
            <h1 class="page_heading">References</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('references.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">+ Add Reference</a>
            </div>
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table id="reference-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function () {
        $('#reference-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('references.data') }}",
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2, orderable: false, searchable: false }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100]
        });
    });
</script>
@endpush
@endsection
