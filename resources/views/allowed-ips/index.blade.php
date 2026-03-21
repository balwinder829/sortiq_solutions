@extends('layouts.app')


@section('content')
@section('title', 'Allowed IPs (Script Access)')
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h4 class="page_heading mb-0">Allowed IPs – Script Access</h4>
            <p class="text-muted small mb-0">Only these IPs can access the application when IP whitelist is enabled. Add a client's IP here before they install the script.</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.allowed-ips.create') }}" class="btn btn-primary">Add IP</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>IP / CIDR</th>
                <th>Label</th>
                <th>Added by</th>
                <th>Added at</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($allowedIps as $ip)
            <tr>
                <td>{{ $loop->iteration + ($allowedIps->currentPage() - 1) * $allowedIps->perPage() }}</td>
                <td><code>{{ $ip->ip_address }}</code></td>
                <td>{{ $ip->label ?? '—' }}</td>
                <td>{{ $ip->added_by ?? '—' }}</td>
                <td>{{ $ip->created_at->format('d M Y H:i') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.allowed-ips.destroy', $ip) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        @include('partials.preserve-pagination')
                        <button type="submit" class="btn btn-sm btn-danger" data-swal-confirm="Remove this IP from whitelist?">Remove</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No allowed IPs. Add IPs so only those locations can use the script.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $allowedIps->links() }}
</div>
@endsection