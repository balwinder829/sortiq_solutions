@extends('layouts.app')

@section('content')
<style>
    .nav-tabs .nav-link {
    background:#f4f6f9;
    border:1px solid #dee2e6;
    margin-right:5px;
    border-radius:6px 6px 0 0;
}

.nav-tabs .nav-link:hover{
    background:#e9ecef;
}

.nav-tabs .nav-link.active{
    background:#ffffff;
    border-bottom:2px solid #0d6efd;
    font-weight:600;
}
</style>
<div class="container">

    <h3 class="mb-4">User Permissions</h3>

    {{-- Success message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- ========================= --}}
    {{-- STEP 1: SELECT USER --}}
    {{-- ========================= --}}
    <form method="GET" action="{{ url('/admin/manager-permissions') }}">
        <div class="mb-3">
            <label class="form-label">Select User</label>

            <select name="user_id"
                    class="form-control"
                    onchange="this.form.submit()">

                <option value="">-- Select User --</option>

                @foreach($managers as $manager)
                    <option value="{{ $manager->id }}"
                        {{ request('user_id') == $manager->id ? 'selected' : '' }}>

                        {{ $manager->name }}
                        ({{ $manager->getRoleNames()->implode(', ') }})

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

                    {{-- ===================== --}}
                    {{-- TAB HEADERS --}}
                    {{-- ===================== --}}
                    <ul class="nav nav-tabs mb-3">

                        @foreach($permissions as $group => $items)

                            <li class="nav-item">

                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        data-bs-toggle="tab"
                                        data-bs-target="#tab-{{ Str::slug($group) }}"
                                        type="button">

                                    {{ $group }}

                                </button>

                            </li>

                        @endforeach

                    </ul>


                    {{-- ===================== --}}
                    {{-- TAB CONTENT --}}
                    {{-- ===================== --}}
                    <div class="tab-content">

@foreach($permissions as $group => $items)

<div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
     id="tab-{{ Str::slug($group) }}">

    {{-- TAB SELECT --}}
    <div class="mb-3">

        <label class="fw-bold">

            <input type="checkbox"
                   class="tab-checkbox"
                   data-group="{{ Str::slug($group) }}">

            Select All {{ $group }}

        </label>

    </div>

    @php
        $menuItems = $items->groupBy('menu_item');
    @endphp

    <div class="accordion" id="accordion-{{ Str::slug($group) }}">

    @foreach($menuItems as $menu => $menuPermissions)

        <div class="accordion-item mb-2">

            <h2 class="accordion-header">

                <button class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapse-{{ Str::slug($group) }}-{{ $menu }}"
                        onclick="if(event.target.type==='checkbox'){event.stopPropagation();}">

                    <label class="me-3">

                        <input type="checkbox"
                               class="menu-checkbox"
                               data-menu="{{ $menu }}"
                               data-group="{{ Str::slug($group) }}">

                    </label>

                    {{ ucwords(str_replace('_',' ',$menu)) }}

                </button>

            </h2>

            <div id="collapse-{{ Str::slug($group) }}-{{ $menu }}"
                 class="accordion-collapse collapse">

                <div class="accordion-body">

                    <div class="row">

                        @foreach($menuPermissions as $permission)

                        <div class="col-md-3 mb-2">

                            <label class="d-flex align-items-center gap-2">

                                <input type="checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->name }}"

                                       class="perm-checkbox
                                              perm-group-{{ Str::slug($group) }}
                                              perm-menu-{{ $menu }}"

                                       {{ $selectedUser->hasPermissionTo($permission->name) ? 'checked' : '' }}>

                                {{ str_replace($menu.'.','',$permission->name) }}

                            </label>

                        </div>

                        @endforeach

                    </div>

                </div>

            </div>

        </div>

    @endforeach

    </div>

</div>

@endforeach

</div>


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

// ==========================
// TAB SELECT → CHECK ALL PERMISSIONS IN GROUP
// ==========================
document.querySelectorAll('.tab-checkbox').forEach(tab => {

    tab.addEventListener('click', function(e){
        e.stopPropagation(); // prevent accordion toggle
    });

    tab.addEventListener('change', function(){

        let group = this.dataset.group;

        document.querySelectorAll('.perm-group-' + group).forEach(cb => {
            cb.checked = this.checked;
        });

        updateParentChecks();
    });

});


// ==========================
// MENU SELECT → CHECK ALL PERMISSIONS IN MENU
// ==========================
document.querySelectorAll('.menu-checkbox').forEach(menu => {

    menu.addEventListener('click', function(e){
        e.stopPropagation(); // prevent accordion toggle
    });

    menu.addEventListener('change', function(){

        let menuName = this.dataset.menu;

        document.querySelectorAll('.perm-menu-' + menuName).forEach(cb => {
            cb.checked = this.checked;
        });

        updateParentChecks();
    });

});


// ==========================
// UPDATE MENU + TAB BASED ON CHILD PERMISSIONS
// ==========================
function updateParentChecks() {

    // MENU LEVEL
    document.querySelectorAll('.menu-checkbox').forEach(menu => {

        let menuName = menu.dataset.menu;

        let perms = document.querySelectorAll('.perm-menu-' + menuName);

        let checked = [...perms].filter(p => p.checked).length;

        menu.checked = (checked === perms.length);

    });

    // TAB LEVEL
    document.querySelectorAll('.tab-checkbox').forEach(tab => {

        let group = tab.dataset.group;

        let perms = document.querySelectorAll('.perm-group-' + group);

        let checked = [...perms].filter(p => p.checked).length;

        tab.checked = (checked === perms.length);

    });

}


// ==========================
// INDIVIDUAL PERMISSION CLICK
// ==========================
document.querySelectorAll('.perm-checkbox').forEach(cb => {

    cb.addEventListener('change', function(){

        updateParentChecks();

    });

});


// ==========================
// PAGE LOAD INIT
// ==========================
document.addEventListener("DOMContentLoaded", function(){

    updateParentChecks();

    // auto open accordion if permission checked
    document.querySelectorAll('.perm-checkbox:checked').forEach(cb => {

        let collapse = cb.closest('.accordion-collapse');

        if(collapse){
            collapse.classList.add('show');
        }

    });

});

</script>

@endpush