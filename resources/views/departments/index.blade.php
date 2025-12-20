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
        <tbody>
            @foreach($departments as $department)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $department->name }}</td>
                 <td>{{ optional($department->created_at)->format('Y-m-d') }}</td>
                 <td class="text-center">
                        <div class="mb-2">
                        <a href="{{ route('departments.edit', $department->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-edit"></i></a>

                        <form action="{{ route('departments.destroy', $department->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                        </form>
                        </div>
                    </td>
            </tr>
            @endforeach
        </tbody>
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
            "pageLength": 25,
            "lengthMenu": [5, 10, 25, 50, 100],
        });
    });
</script>
@endpush

@endsection
