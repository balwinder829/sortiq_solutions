@extends('layouts.app')

@section('content')
<div class="container">

    {{-- ========================= --}}
    {{-- ENQUIRY DETAILS CARD --}}
    {{-- ========================= --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Enquiry Details (Assigned to You)</h5>
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

                    <div class="mt-2">
                        <a href="tel:{{ $enquiry->mobile }}"
                           class="btn btn-success btn-sm">📞 Call</a>

                        <a href="https://wa.me/{{ $enquiry->mobile }}"
                           target="_blank"
                           class="btn btn-info btn-sm text-white">💬 WhatsApp</a>
                    </div>
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
                    <strong>Lead Status:</strong><br>
                    <span class="badge bg-info">
                        {{ ucfirst($enquiry->lead_status) }}
                    </span>
                </div>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{-- REGISTER STUDENT (ONLY IF READY) --}}
    {{-- ========================= --}}
    @if($enquiry->lead_status === 'registered' && !$enquiry->registered_at)
        <button class="btn btn-success mb-4"
                data-bs-toggle="modal"
                data-bs-target="#registerModal">
            💰 Register Student
        </button>
    @endif


    {{-- ========================= --}}
    {{-- ADD FOLLOW-UP --}}
    {{-- ========================= --}}
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Add Follow-Up</h5>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

             @if(session('error'))
                <div class="alert alert-success">{{ session('error') }}</div>
            @endif

            <form action="{{ route('sales.enquiries.followup.store', $enquiry->id) }}" method="POST">
                @csrf

                <div class="row">

                    {{-- Call Status --}}
                    <div class="col-md-4 mb-3">
                        <label><strong>Call Status</strong></label>
                        <select name="call_status" class="form-control" required>
                            <option value="">Select Status</option>
                            @foreach($callStatuses as $status)
                                <option value="{{ $status->name }}">
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Lead Status --}}
                    <div class="col-md-4 mb-3">
                        <label><strong>Lead Status</strong></label>
                        <select name="status" class="form-control" required>
                            <option value="followup">Follow-Up</option>
                            <option value="registered">Registered</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    {{-- Next Follow-Up --}}
                    <div class="col-md-4 mb-3">
                        <label><strong>Next Follow-Up Date</strong></label>
                        <input type="date" name="next_followup_date" class="form-control">
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-12">
                        <label><strong>Notes / Remarks</strong></label>
                        <textarea name="note" rows="3" class="form-control"
                                  placeholder="Enter call notes..."></textarea>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-primary">Save Follow-Up</button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    {{-- ========================= --}}
    {{-- FOLLOW-UP HISTORY --}}
    {{-- ========================= --}}
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Follow-Up History</h5>
        </div>

        <div class="card-body">

            @forelse($enquiry->followups as $followup)
                <div class="p-3 border rounded mb-3">

                    <strong class="text-primary">
                        {{ $followup->created_at->format('d M Y, h:i A') }}
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
                <p class="text-muted">No follow-ups yet.</p>
            @endforelse

        </div>
    </div>


    {{-- ========================= --}}
    {{-- ACTIVITY TIMELINE --}}
    {{-- ========================= --}}
    <h4>Activity Timeline</h4>

    <ul class="list-group mb-5">
        @foreach($enquiry->activities as $activity)
            <li class="list-group-item">
                <strong>{{ ucfirst(str_replace('_', ' ', $activity->type)) }}</strong><br>

                @if($activity->old_value || $activity->new_value)
                    <small>Status: {{ $activity->old_value }} → {{ $activity->new_value }}</small><br>
                @endif

                @if($activity->details)
                    <div>{{ $activity->details }}</div>
                @endif

                <small class="text-muted">
                    {{ $activity->created_at->format('d M Y h:i A') }} |
                    {{ $activity->user->name }}
                </small>
            </li>
        @endforeach
    </ul>

</div>

{{-- ========================= --}}
{{-- REGISTER MODAL --}}
{{-- ========================= --}}
<div class="modal fade" id="registerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST" action="{{ route('sales.enquiries.register', $enquiry->id) }}" enctype="multipart/form-data">
                @csrf

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Register Student</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label><strong>Amount Paid</strong></label>
                        <input type="number" name="amount_paid" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label><strong>Payment Mode</strong></label>
                        <select name="payment_mode" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="bank">Bank</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label><strong>Payment Status</strong></label>
                        <select name="payment_status" class="form-control" required>
                            <option value="partial">Partial</option>
                            <option value="full">Full</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label><strong>Payment Proof (Image)</strong></label>
                        <input type="file"
                               name="payment_image"
                               class="form-control"
                               accept="image/*">
                    </div>


                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success">Confirm Registration</button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection
