@extends('layouts.app')

@section('title', 'Add Allowed IP')

@section('content')
<div class="container">
    <h4 class="page_heading mb-3">Add IP to Whitelist (Script Access)</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-info small">
        When IP whitelist is enabled (<code>IP_WHITELIST_ENABLED=true</code>), only IPs listed here (and any in <code>ALLOWED_IPS</code> in .env) can access the site. Add the client's IP or CIDR range before they install the script on their server.
    </div>

    <form method="POST" action="{{ route('admin.allowed-ips.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">IP address or CIDR <span class="text-danger">*</span></label>
            <input type="text"
                   name="ip_address"
                   class="form-control @error('ip_address') is-invalid @enderror"
                   value="{{ old('ip_address', $prefillIp ?? '') }}"
                   placeholder="e.g. 203.0.113.10 or 203.0.113.0/24"
                   required>
            @error('ip_address')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">Single IP (e.g. 192.168.1.100) or CIDR range (e.g. 192.168.1.0/24).</div>
        </div>

        <div class="mb-3">
            <label class="form-label">Label (optional)</label>
            <input type="text"
                   name="label"
                   class="form-control"
                   value="{{ old('label') }}"
                   placeholder="e.g. Client ABC, Main office">
        </div>

        <button type="submit" class="btn btn-primary">Add to whitelist</button>
        <a href="{{ route('admin.allowed-ips.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection