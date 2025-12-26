@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Employees</h4>

    <a href="{{ route('employees.create') }}"
       class="btn mb-3"
       style="background-color:#6b51df;color:#fff;">
        Add Employee
    </a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="employeesTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Emp Code</th>
                <th>Name</th>
                <th>Position</th>
                <th>Joining Date</th>
                <th>Username</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($employees as $emp)
            <tr>
                <td>{{ $emp->emp_code }}</td>
                <td>{{ $emp->emp_name }}</td>
                <td>{{ $emp->position }}</td>
                <td>{{ \Carbon\Carbon::parse($emp->joining_date)->format('d M Y') }}</td>
                <td>{{ $emp->user->username }}</td>

                <td>
                    @if($emp->status === 'active')
                        <span class="badge bg-success">Active</span>
                    @elseif($emp->status === 'inactive')
                        <span class="badge bg-warning text-dark">Inactive</span>
                    @elseif($emp->status === 'resigned')
                        <span class="badge bg-warning text-dark">Resigned</span>
                    @else
                        <span class="badge bg-danger">Terminated</span>
                    @endif
                </td>

                <td>
                    <!-- Edit -->
                    <a href="{{ route('employees.edit', $emp) }}"
                       class="btn btn-sm"
                       data-bs-toggle="tooltip"
                       title="Edit Employee">
                        <i class="fas fa-edit"></i>
                    </a>

                    <!-- Delete -->
                    <form action="{{ route('employees.destroy', $emp) }}"
                          method="POST"
                          style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm"
                                onclick="return confirm('Delete employee?')"
                                data-bs-toggle="tooltip"
                                title="Delete Employee">
                            <i class="fas fa-trash"></i>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#employeesTable').DataTable({
        pageLength: 10,
        lengthMenu: [5,10,25,50,100]
    });

    new bootstrap.Tooltip(document.body, {
        selector: '[data-bs-toggle="tooltip"]'
    });
});
</script>
@endpush
