@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Users</h1>
        </div>
        <div class="col-md-6">
               <!--  <div class="d-flex justify-content-end">
                    
                    <a href="{{ route('users.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add User</a>
            </div> -->
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <table id="usersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Status</th> <!-- Active / Inactive / Deleted -->
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($users as $user)
            <tr>

                <!-- ID -->
                <td>{{ $user->id }}</td>

                <!-- Username -->
                <td>
                    @if($user->trashed())
                        <span class="text-danger">{{ $user->username }}</span>
                    @else
                        {{ $user->username }}
                    @endif
                </td>

                <!-- Role -->
                <td>{{ $user->roles->name }}</td>

                <!-- Status Column (Active / Inactive / Deleted) -->
                <td>
                    @if($user->trashed())
                        <span class="badge bg-danger">Deleted</span>
                    @elseif($user->status === 'inactive')
                        <span class="badge bg-warning text-dark">Inactive</span>
                    @else
                        <span class="badge bg-success">Active</span>
                    @endif
                </td>

                <!-- Created At -->
                <td>{{ $user->created_at->format('d M Y') }}</td>

                <!-- Actions -->
                <td>

                    @if(!$user->trashed())

                    @if($user->role == 4 && auth()->user()->role == 1)
                        <a href="{{ route('admin.manager.permissions.edit') }}"
                           class="btn btn-sm btn-outline-primary"
                           data-bs-toggle="tooltip"
                           title="Manage Manager Permissions">
                            <i class="fas fa-key"></i>
                        </a>
                    @endif

                        <!-- Edit -->
                        <a href="{{ route('users.edit', $user) }}" 
                           class="btn btn-sm" 
                           data-bs-toggle="tooltip" 
                           title="Edit User">
                            <i class="fas fa-edit"></i>
                        </a>

                        <!-- Soft Delete -->
                        <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm" 
                                    onclick="return confirm('Delete user?')" 
                                    data-bs-toggle="tooltip" 
                                    title="Delete User">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>

                    @else
                        <!-- Restore -->
                        <form action="{{ route('users.restore', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm btn-success" 
                                    data-bs-toggle="tooltip" 
                                    title="Restore User">
                                <i class="fas fa-undo"></i> 
                            </button>
                        </form>
                    @endif

                </td>

            </tr>
            @endforeach
        </tbody>
    </table>


</div>
@endsection

@push('styles')
<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS (for tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {  
 var table = $('#usersTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        // "scrollX": true
    });
    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endpush
