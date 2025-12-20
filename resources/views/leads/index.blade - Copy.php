@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
    <h3>Leads</h3>

    <div>

        <a href="{{ route('leads.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Add Lead
        </a>
        
        <a href="{{ route('leads.import.form') }}" class="btn btn-primary me-2">
            <i class="fa fa-upload"></i> Import Leads
        </a>

        
    </div>
</div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="row mb-3">
        <div class="col-md-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name / Email / Phone">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control">
                <option value="">--Status--</option>
                @foreach(['new','contacted','follow_up','not_interested','onboarded'] as $st)
                    <option value="{{ $st }}" {{ request('status') == $st ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_',' ', $st)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="assigned_to" class="form-control">
                <option value="">--Assigned To--</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                        {{ $user->username }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-1 d-grid">
            <button class="btn btn-secondary w-100" style="background-color: #6b51df; color: #fff;">Filter</button>
        </div>
        <div class="col-md-1 d-grid">
            <a href="{{ route('leads.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table id="leadsTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Next Follow-up</th>
                    <th>Assigned To</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($leads as $lead)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lead->name ?? '-' }}</td>
                    <td>{{ $lead->phone ?? '-' }}</td>
                    <td>{{ $lead->email ?? '-' }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</td>
                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('d-m-Y') : '-' }}</td>
                    <td>{{ $lead->assignedTo->username ?? '-' }}</td>
                    <td>
                        <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-sm"><i class="fa fa-eye"></i></a>
                        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('leads.destroy', $lead->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm" onclick="return confirm('Delete this lead?')"><i class="fa fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $leads->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#leadsTable').DataTable({
        pageLength: 25,
        lengthMenu: [10,25,50,100],
        scrollX: true
    });
});
</script>
@endpush
