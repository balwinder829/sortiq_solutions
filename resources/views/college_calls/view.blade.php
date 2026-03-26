@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-3">Call Detail</h3>

    <div class="card">
        <div class="card-body">

            <p><strong>College:</strong> {{ $log->college->full_name }}</p>

            <p><strong>Type:</strong> {{ strtoupper($log->type) }}</p>

            <p><strong>Name:</strong> {{ $log->recipient_name }}</p>

            <p><strong>Contact:</strong> {{ $log->contact_number }}</p>

            <p><strong>Status:</strong>
                <span class="badge bg-{{ $log->status == 'connected' ? 'success' : 'danger' }}">
                    {{ ucfirst($log->status) }}
                </span>
            </p>

            <p><strong>Called At:</strong>
                {{ optional($log->called_at)->format('d M Y h:i A') }}
            </p>

            <p><strong>Purpose:</strong> {{ $log->campaign->purpose }}</p>

            <p><strong>Notes:</strong> {{ $log->notes }}</p>

        </div>
    </div>

</div>

@endsection