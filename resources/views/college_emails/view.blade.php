@extends('layouts.app')

@section('content')

<div class="container">

<h4>Email Detail</h4>

<div class="card p-3">

    <p><strong>College:</strong> {{ $recipient->college->full_name }}</p>

    <p><strong>Recipient:</strong> {{ $recipient->recipient_name }} ({{ strtoupper($recipient->type) }})</p>

    <p><strong>Email:</strong> {{ $recipient->email }}</p>

    <p><strong>Status:</strong>
        <span class="badge bg-{{ $recipient->status == 'sent' ? 'success' : ($recipient->status == 'failed' ? 'danger' : 'secondary') }}">
            {{ ucfirst($recipient->status) }}
        </span>
    </p>

    <p><strong>Sent At:</strong> {{ optional($recipient->sent_at)->format('d M Y h:i A') }}</p>

    <hr>

    <p><strong>Subject:</strong> {{ $recipient->campaign->subject }}</p>

    <p><strong>Purpose:</strong> {{ $recipient->campaign->purpose->name ?? '-' }}</p>

    <p><strong>Sender:</strong> {{ $recipient->campaign->sender->email ?? '-' }}</p>

    <hr>

    <p><strong>Body:</strong></p>

    <div style="background:#f8f9fa; padding:15px;">
        {!! $recipient->rendered_body !!}        
    </div>

    @if($recipient->error_message)
        <hr>
        <p><strong>Error:</strong></p>
        <div class="text-danger">{{ $recipient->error_message }}</div>
    @endif

</div>

</div>

@endsection