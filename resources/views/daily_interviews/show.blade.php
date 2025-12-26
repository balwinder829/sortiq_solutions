@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Back Button & Action Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>Interview Details: {{ $interview->candidate_name }}</h4>
        <div>
            <a href="{{ route('daily-interviews.index') }}" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Back to List</a>
            <a href="{{ route('daily-interviews.edit', $interview) }}" class="btn btn-warning"><i class="fas fa-edit"></i> Edit Record</a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5>📅 Scheduling & Tracking</h5>
        </div>
        <div class="card-body">
            <div class="row">
                
                {{-- Column 1: Scheduling --}}
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Date & Time:</strong> 
                            <span class="text-primary">{{ $interview->availability_datetime?->format('d M Y h:i A') ?? 'N/A' }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Interviewer:</strong> {{ $interview->interviewer_name ?? 'Not Assigned' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Type:</strong> <span class="badge bg-info">{{ $interview->interview_type }}</span>
                        </li>
                        <li class="list-group-item">
                            <strong>Status:</strong> 
                            @php
                                $badgeClass = match($interview->interview_status) {
                                    'Scheduled' => 'bg-warning text-dark',
                                    'Completed' => 'bg-info',
                                    'Offered' => 'bg-success',
                                    'Rejected', 'No Show' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $interview->interview_status }}</span>
                        </li>
                    </ul>
                </div>

                {{-- Column 2: Offer Details --}}
                <div class="col-md-6">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <strong>Current CTC(LPA):</strong> {{ $interview->current_ctc ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Expected CTC(LPA):</strong> {{ $interview->exp_ctc ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Notice Period( In Days):</strong> {{ $interview->notice_period ?? 'N/A' }}
                        </li>
                        <li class="list-group-item">
                            <strong>Tentative Joining:</strong> {{ $interview->joining_date?->format('M d, Y') ?? 'N/A' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5>👤 Candidate Contact</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Mobile Number:</strong> {{ $interview->mobile_no ?? 'N/A' }}</p>
                    <p><strong>Technology/Role:</strong> {{ $interview->technology ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Record Created:</strong> {{ $interview->created_at->format('d M Y h:i A') }}</p>
                    <p><strong>Record Last Updated:</strong> {{ $interview->updated_at->format('d M Y h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection