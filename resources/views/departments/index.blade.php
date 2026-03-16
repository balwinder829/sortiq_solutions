@extends('layouts.app')

@section('content')
<div class="container">
    <a href="{{ route('departments.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add Department</a>
    <h1>Department</h1>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="departments-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>departments</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>
@endsection

@section('scripts')


@push('scripts')
<!-- jQuery & DataTables -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
 $(document).ready(function () {
        $('#departments-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('departments.data') }}",
            columns: [
                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3, orderable: false, searchable: false }
            ],
            pageLength: 25,
            lengthMenu: [5, 10, 25, 50, 100]
        });
    });
</script>
@endpush

@endsection
