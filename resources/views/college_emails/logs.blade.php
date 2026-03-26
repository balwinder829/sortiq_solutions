@extends('layouts.app')

@section('content')

<div class="container">

<h4>Email Logs - {{ $college->full_name }}</h4>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Type</th>
            <th>Status</th>
            <th>Sent At</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($recipients as $r)
            <tr>
                <td>{{ $r->id }}</td>
                <td>{{ $r->email }}</td>
                <td>{{ strtoupper($r->type) }}</td>
                <td>
                    <span class="badge bg-{{ $r->status == 'sent' ? 'success' : ($r->status == 'failed' ? 'danger' : 'secondary') }}">
                        {{ ucfirst($r->status) }}
                    </span>
                </td>
                <td>{{ optional($r->sent_at)->format('d M Y h:i A') }}</td>
                <td>
                    <a href="{{ route('admin.college-emails.view', $r->id) }}" class="btn btn-sm btn-primary">
                        View
                    </a>
                </td>
            </tr>
        @endforeach
    </tbody>

</table>

{{ $recipients->links() }}

</div>

@endsection