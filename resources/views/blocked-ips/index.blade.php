@extends('layouts.app')

@section('title', 'Blocked IPs')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col">
            <h4 class="page_heading mb-0">Blocked IP Addresses</h4>
        </div>
        <div class="col-auto">
            <a href="{{ route('admin.blocked-ips.create') }}" class="btn btn-primary">Block IP</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>IP Address</th>
                <th>User (when blocked)</th>
                <th>Reason</th>
                <th>Blocked At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($blockedIps as $ip)
            <tr>
                <td>{{ $loop->iteration + ($blockedIps->currentPage() - 1) * $blockedIps->perPage() }}</td>
                <td><code>{{ $ip->ip_address }}</code></td>
                <td>{{ $ip->actor_name ?? '—' }}</td>
                <td>{{ $ip->reason ?? '—' }}</td>
                <td>{{ $ip->blocked_at->format('d M Y H:i') }}</td>
                <td>
                    <form method="POST" action="{{ route('admin.blocked-ips.destroy', $ip) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        @include('partials.preserve-pagination')
                        <button type="submit" class="btn btn-sm btn-danger" data-swal-confirm="Unblock this IP?">Unblock</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No blocked IPs.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{ $blockedIps->links() }}
</div>
@endsection
