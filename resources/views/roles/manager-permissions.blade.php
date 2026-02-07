@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Manager Permissions</h3>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- STEP 1: SELECT MANAGER --}}
    {{-- ========================= --}}
    <form method="GET" action="{{ url('/admin/manager-permissions') }}">
        <div class="mb-3">
            <label class="form-label">Select Manager</label>
            <select name="user_id"
                    class="form-control"
                    onchange="this.form.submit()">
                <option value="">-- Select Manager --</option>

                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}"
                        {{ request('user_id') == $manager->id ? 'selected' : '' }}>
                        {{ $manager->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- ========================= --}}
    {{-- STEP 2: PERMISSION LIST --}}
    {{-- ========================= --}}
    @if($selectedUser instanceof \App\Models\User)

        <form method="POST" action="{{ url('/admin/manager-permissions') }}">
            @csrf

            <input type="hidden" name="user_id" value="{{ $selectedUser->id }}">

            <div class="card mt-4">
                <div class="card-header">
                    <strong>
                        Permissions for {{ $selectedUser->name }}
                    </strong>
                </div>

                <div class="card-body">

                    @foreach($permissions as $group => $groupPermissions)

                        <div class="card mb-3 border">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <strong class="text-capitalize">
                                    {{ str_replace('_',' ', $group) }} Permissions
                                </strong>

                                {{-- Optional: Select All --}}
                                <div>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick="toggleGroup('{{ $group }}', true)">
                                        Select All
                                    </button>

                                    <button type="button"
                                            class="btn btn-sm btn-outline-secondary"
                                            onclick="toggleGroup('{{ $group }}', false)">
                                        Clear
                                    </button>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row">
                                    @foreach($groupPermissions as $permission)
                                        <div class="col-md-3 mb-2">
                                            <label class="d-flex align-items-center gap-2">
                                                <input type="checkbox"
                                                       class="perm-checkbox perm-{{ $group }}"
                                                       name="permissions[]"
                                                       value="{{ $permission->name }}"
                                                       {{ $selectedUser->hasPermissionTo($permission->name) ? 'checked' : '' }}>

                                                {{ str_replace($group.'.', '', $permission->name) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @endforeach

                </div>


                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">
                        Save Permissions
                    </button>
                </div>
            </div>
        </form>
    @endif

</div>
@endsection
@push('scripts')
<script>
    function toggleGroup(group, checked) {
        document.querySelectorAll('.perm-' + group)
            .forEach(cb => cb.checked = checked);
    }
</script>
@endpush

