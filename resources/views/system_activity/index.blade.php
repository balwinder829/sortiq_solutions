@extends('layouts.app')

@section('title', 'System Activity')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="page_heading mb-0">System Activity</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters (submit reloads page with params; DataTables sends them in ajax) --}}
    <form method="GET" id="filterForm" id="activityFilterForm" class="row g-2 mb-4">
        <div class="col-md-2">
            <label class="form-label small">User</label>
            <select name="user_id" class="form-control form-control-sm filterchange">
                <option value="">-- All Users --</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name ?? $u->username }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Action</label>
            <select name="action" class="form-control form-control-sm filterchange">
                <option value="">-- All --</option>
                <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Logged in</option>
                <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logged out</option>
                <option value="user_created" {{ request('action') === 'user_created' ? 'selected' : '' }}>User created</option>
                <option value="user_updated" {{ request('action') === 'user_updated' ? 'selected' : '' }}>User updated</option>
                <option value="user_deleted" {{ request('action') === 'user_deleted' ? 'selected' : '' }}>User deleted</option>
                <option value="user_restored" {{ request('action') === 'user_restored' ? 'selected' : '' }}>User restored</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">Guard</label>
            <select name="guard" class="form-control form-control-sm filterchange">
                <option value="">-- All --</option>
                <option value="web" {{ request('guard') === 'web' ? 'selected' : '' }}>Web (User)</option>
                <option value="trainer" {{ request('guard') === 'trainer' ? 'selected' : '' }}>Trainer</option>
                <option value="guest" {{ request('guard') === 'guest' ? 'selected' : '' }}>Guest</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small">IP Address</label>
            <input type="text" name="ip_address" class="form-control form-control-sm filterchangetext" placeholder="IP" value="{{ request('ip_address') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small">From date</label>
            <input type="date" name="start_date" class="form-control form-control-sm filterchange" value="{{ request('start_date') }}">
        </div>
        <div class="col-md-2">
            <label class="form-label small">To date</label>
            <input type="date" name="end_date" class="form-control form-control-sm filterchange" value="{{ request('end_date') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end gap-1">
            <!-- <button type="submit" class="btn btn-primary btn-sm">Filter</button> -->
            <a href="{{ route('admin.system-activity') }}" class="btn btn-secondary btn-sm">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="systemActivityTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date & Time</th>
                    <th>User</th>
                    <th>Guard</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>URL / Page</th>

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
    var params = new URLSearchParams(window.location.search);
    $('#systemActivityTable').DataTable({
        processing: true,
        serverSide: true,
        order: [[1, 'desc']],
        ajax: {
            url: "{{ route('admin.system-activity.data') }}",
            data: function (d) {
                d.user_id = params.get('user_id') || '';
                d.trainer_id = params.get('trainer_id') || '';
                d.action = params.get('action') || '';
                d.guard = params.get('guard') || '';
                d.ip_address = params.get('ip_address') || '';
                d.start_date = params.get('start_date') || '';
                d.end_date = params.get('end_date') || '';
            }
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2, orderable: false },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false, searchable: false }
        ],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100]
    });
});
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
        
    });
    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 500); // waits 500ms after typing stops
    });

});
</script>
@endpush
@endsection

