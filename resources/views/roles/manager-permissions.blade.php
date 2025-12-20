{{-- resources/views/admin/roles/manager-permissions.blade.php --}}
@extends('layouts.app')


@section('content')
<div class="container">
<h3 class="mb-4">Manager Permissions</h3>


<form method="POST" action="{{ route('admin.manager.permissions.update') }}">
@csrf


<div class="row">
@foreach($permissions as $module => $modulePermissions)

    <div class="card mb-3">
        <div class="card-header bg-light fw-bold text-uppercase">
            {{ str_replace('_', ' ', $module) }}
        </div>

        <div class="card-body row">
            @foreach($modulePermissions as $permission)
                <div class="col-md-4 mb-2">
                    <label class="form-check">
                        <input class="form-check-input"
                               type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               {{ in_array($permission->id, $assignedPermissions) ? 'checked' : '' }}>
                        {{ $permission->label }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>

@endforeach

</div>


<button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
</form>
</div>
@endsection