@extends('layouts.app')

@section('title', 'Block IP')

@section('content')
<div class="container">
    <h4 class="page_heading mb-3">Block IP Address</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-info small">
        Blocked IPs cannot access the site (they will see a "Blocked" message). On localhost, <code>127.0.0.1</code> is never blocked to avoid locking yourself out.
    </div>

    @if(!empty($actorName))
        <div class="alert alert-warning small">
            Blocking IP for user: <strong>{{ $actorName }}</strong>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.blocked-ips.store') }}">
        @csrf
        @if(!empty($actorName))
            <input type="hidden" name="actor_name" value="{{ $actorName }}">
        @endif

        <div class="mb-3">
            <label class="form-label">IP Address <span class="text-danger">*</span></label>
            <input type="text"
                   name="ip_address"
                   class="form-control @error('ip_address') is-invalid @enderror"
                   value="{{ old('ip_address', $prefillIp ?? '') }}"
                   placeholder="e.g. 192.168.1.100"
                   required>
            @error('ip_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @if(empty($actorName))
        <div class="mb-3">
            <label class="form-label">User / Actor (optional)</label>
            <input type="text" name="actor_name" class="form-control" value="{{ old('actor_name') }}" placeholder="e.g. John (User)">
        </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Reason (optional)</label>
            <input type="text"
                   name="reason"
                   class="form-control"
                   value="{{ old('reason') }}"
                   placeholder="e.g. Abuse / spam">
        </div>

        <button type="submit" class="btn btn-primary">Block IP</button>
        <a href="{{ route('admin.blocked-ips.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
