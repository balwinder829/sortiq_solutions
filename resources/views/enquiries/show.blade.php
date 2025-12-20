@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ========================= --}}
    {{-- ENQUIRY DETAILS --}}
    {{-- ========================= --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Enquiry Details (Admin View)</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4 mb-2">
                    <strong>Name:</strong><br>
                    {{ $enquiry->name }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Mobile:</strong><br>
                    {{ $enquiry->mobile }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Email:</strong><br>
                    {{ $enquiry->email ?? '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>College:</strong><br>
                    {{ $enquiry->collegeData->college_name ?? '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Study:</strong><br>
                    {{ $enquiry->study ?? '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Semester:</strong><br>
                    {{ $enquiry->semester ?? '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Assigned To:</strong><br>
                    {{ $enquiry->assignedTo->name ?? 'Not Assigned' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Lead Status:</strong><br>
                    <span class="badge bg-info">
                        {{ ucfirst($enquiry->lead_status) }}
                    </span>
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Last Call Status:</strong><br>
                    {{ $enquiry->last_call_status ?? '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Last Contacted At:</strong><br>
                    {{ $enquiry->last_contacted_at
                        ? $enquiry->last_contacted_at->format('d M Y h:i A')
                        : '-' }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Next Follow-Up:</strong><br>
                    @if($enquiry->next_followup_at)
                        @if($enquiry->next_followup_at->isPast())
                            <span class="badge bg-danger">
                                {{ $enquiry->next_followup_at->format('d M Y') }} (Overdue)
                            </span>
                        @elseif($enquiry->next_followup_at->isToday())
                            <span class="badge bg-warning text-dark">
                                Today
                            </span>
                        @else
                            <span class="badge bg-success">
                                {{ $enquiry->next_followup_at->format('d M Y') }}
                            </span>
                        @endif
                    @else
                        <span class="badge bg-secondary">Not Set</span>
                    @endif
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Source:</strong><br>
                    {{ ucfirst($enquiry->source_type) }}
                </div>

                <div class="col-md-4 mb-2">
                    <strong>Registered:</strong><br>
                    @if($enquiry->registered_at)
                        <span class="badge bg-success">
                            {{ $enquiry->registered_at->format('d M Y h:i A') }}
                        </span>
                    @else
                        <span class="badge bg-secondary">No</span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- REGISTRATION DETAILS --}}
    {{-- ========================= --}}
    @if($enquiry->registration)
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">Registration Details</h5>
        </div>

        <div class="card-body">
            <div class="row">

                <div class="col-md-4">
                    <strong>Amount Paid:</strong><br>
                    ₹ {{ number_format($enquiry->registration->amount_paid, 2) }}
                </div>

                <div class="col-md-4">
                    <strong>Payment Mode:</strong><br>
                    {{ ucfirst($enquiry->registration->payment_mode) }}
                </div>

                <div class="col-md-4">
                    <strong>Payment Status:</strong><br>
                    {{ ucfirst($enquiry->registration->payment_status) }}
                </div>

                <div class="col-md-4 mt-2">
                    <strong>Collected By:</strong><br>
                    {{ $enquiry->registration->collector->name ?? '-' }}
                </div>

                <div class="col-md-4 mt-2">
                    <strong>Registered At:</strong><br>
                    {{ $enquiry->registration->registered_at->format('d M Y h:i A') }}
                </div>

            </div>
        </div>
    </div>
    @endif


    {{-- ========================= --}}
    {{-- FOLLOW-UP HISTORY --}}
    {{-- ========================= --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Follow-Up History</h5>
        </div>

        <div class="card-body">

            @forelse($enquiry->followups as $followup)
                <div class="border rounded p-3 mb-3">

                    <strong class="text-primary">
                        {{ $followup->created_at->format('d M Y h:i A') }}
                    </strong><br>

                    <strong>Call:</strong>
                    <span class="badge bg-info">{{ $followup->call_status }}</span><br>

                    <strong>Status:</strong>
                    <span class="badge bg-warning text-dark">
                        {{ ucfirst($followup->status) }}
                    </span><br>

                    @if($followup->next_followup_date)
                        <strong>Next Follow-Up:</strong>
                        {{ \Carbon\Carbon::parse($followup->next_followup_date)->format('d M Y') }}<br>
                    @endif

                    @if($followup->note)
                        <strong>Notes:</strong>
                        <div class="text-muted">{{ $followup->note }}</div>
                    @endif

                    <small class="text-muted">
                        Logged by {{ $followup->user->name }}
                    </small>

                </div>
            @empty
                <p class="text-muted">No follow-ups found.</p>
            @endforelse

        </div>
    </div>

    {{-- ========================= --}}
    {{-- ACTIVITY TIMELINE --}}
    {{-- ========================= --}}
    <div class="card">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Activity Timeline</h5>
        </div>

        <div class="card-body">
            <ul class="list-group">

                @foreach($enquiry->activities as $activity)
                    <li class="list-group-item">

                        <strong>{{ ucfirst(str_replace('_', ' ', $activity->type)) }}</strong><br>

                        @if($activity->old_value || $activity->new_value)
                            <small>
                                {{ $activity->old_value }} → {{ $activity->new_value }}
                            </small><br>
                        @endif

                        @if($activity->details)
                            <div>{{ $activity->details }}</div>
                        @endif

                        <small class="text-muted">
                            {{ $activity->created_at->format('d M Y h:i A') }}
                            | {{ $activity->user->name }}
                        </small>

                    </li>
                @endforeach

            </ul>
        </div>
    </div>

</div>
@endsection
