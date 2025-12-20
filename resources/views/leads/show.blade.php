@extends('layouts.app')

@section('content')
<div class="container">

    <h3>Lead Detail</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h5>{{ $lead->name ?? '-' }}</h5>
            <p><strong>Phone:</strong> {{ $lead->phone ?? '-' }}</p>
            <p><strong>Email:</strong> {{ $lead->email ?? '-' }}</p>
            <p><strong>Status:</strong> {{ ucfirst(str_replace('_',' ',$lead->status)) }}</p>
            <p><strong>Next Follow-up:</strong> {{ $lead->follow_up_date ? $lead->follow_up_date->format('d M Y') : '-' }}</p>
            <p><strong>Assigned To:</strong> {{ $lead->assignedTo->username ?? '-' }}</p>
            <p><strong>Created By:</strong> {{ $lead->creator->username ?? '-' }}</p>
            <p><strong>Source:</strong> {{ $lead->source ?? '-' }}</p>
            <p><strong>Notes:</strong> {{ $lead->notes ?? '-' }}</p>
        </div>
    </div>

    {{-- Call history --}}
    <div class="card mb-3">
        <div class="card-header">Call History</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Call Status</th>
                        <th>Lead Status</th>
                        <th>Follow-up Date</th>
                        <th>Remark</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lead->calls as $call)
                    <tr>
                        <td>{{ $call->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $call->user->username ?? '-' }}</td>
                        <td>{{ $call->call_status }}</td>
                        <td>{{ $call->lead_status }}</td>
                        <td>{{ $call->follow_up_date ? $call->follow_up_date->format('d M Y') : '-' }}</td>
                        <td>{{ $call->remark }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No calls logged yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add new call --}}
    <div class="card">
        <div class="card-header">Log a Call / Update Status</div>
        <div class="card-body">
            <form method="POST" action="{{ route('leads.calls.store', $lead->id) }}">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Call Status</label>
                        <select name="call_status" class="form-control" required>
                            <option value="">--Select--</option>
                            @foreach(['connected','no_answer','busy','switched_off','wrong_number'] as $cs)
                                <option value="{{ $cs }}">{{ ucfirst(str_replace('_',' ', $cs)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Lead Status (after this call)</label>
                        <select name="lead_status" class="form-control">
                            <option value="">--No Change--</option>
                            @foreach(['new','contacted','follow_up','not_interested','onboarded'] as $ls)
                                <option value="{{ $ls }}">{{ ucfirst(str_replace('_',' ', $ls)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Next Follow-up Date</label>
                        <input type="date" name="follow_up_date" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Remark</label>
                    <textarea name="remark" class="form-control" rows="3"></textarea>
                </div>

                <button class="btn btn-primary">Save Call</button>
            </form>
        </div>
    </div>
</div>
@endsection
