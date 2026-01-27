@extends('layouts.app')

@section('content')
<div class="container">

<h4 class="mb-3">Interview Details</h4>

{{-- Candidate Summary --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">
                <p><strong>Name:</strong> {{ $interview->candidate_name }}</p>
                <p><strong>Experience:</strong> {{ $interview->candidate_experience ?? '-' }}</p>
                <p><strong>Contact:</strong> {{ $interview->candidate_contact ?? '-' }}</p>
                <p><strong>Email:</strong> {{ $interview->candidate_email ?? '-' }}</p>
            </div>

            <div class="col-md-6">
                <p><strong>Interviewer:</strong> {{ $interview->interviewer_name }}</p>
                <p><strong>Interview Date:</strong>
                    {{ $interview->interview_date
                        ? \Carbon\Carbon::parse($interview->interview_date)->format('d M Y')
                        : '-' }}
                </p>
                <p>
                    <strong>Final Result:</strong>
                    {{ $interview->final_result ? ucfirst($interview->final_result) : '-' }}
                </p>
            </div>

        </div>
    </div>
</div>

{{-- Interview Rounds --}}
<h5 class="mb-3">Interview Rounds</h5>

@if($interview->rounds->count())
    @foreach($interview->rounds as $round)
        <div class="card mb-3 border">

            <div class="card-body">

                {{-- Round Header --}}
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <strong class="me-2">
                            {{ strtoupper($round->round_type) }} ROUND
                        </strong>

                        <p class="mb-1 text-muted">
						    <small>
						        Round Date:
						        {{ $round->round_date
						            ? \Carbon\Carbon::parse($round->round_date)->format('d M Y')
						            : '-' }}
						    </small>
						</p>


                        <span class="badge bg-light text-dark border">
                            {{ strtoupper($round->round_type) }}
                        </span>
                    </div>

                    <a href="{{ route('interviews.rounds.edit', $round) }}"
                       class="btn btn-sm btn-outline-secondary">
                        Edit
                    </a>
                </div>

                {{-- Rating --}}
                <p class="mb-1">
                    <strong>Overall Rating:</strong>
                    {{ $round->rating !== null ? $round->rating.'/10' : '-' }}
                </p>

                {{-- Remarks --}}
                @if($round->remarks)
                    <p class="mb-2">
                        <strong>Remarks:</strong><br>
                        {{ $round->remarks }}
                    </p>
                @endif

                {{-- Technology Ratings --}}
                @if(in_array($round->round_type, ['technical','machine']) && $round->technologies->count())
                    <hr>
                    <div class="row">
                        @foreach($round->technologies as $tech)
                            <div class="col-md-4 mb-2">
                                <div class="border rounded p-2 bg-light">
                                    <strong>{{ $tech->name }}</strong><br>
                                    Rating:
                                    {{ $tech->pivot->rating !== null
                                        ? $tech->pivot->rating.'/10'
                                        : '-' }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    @endforeach
@else
    <p class="text-muted">No rounds added yet.</p>
@endif

{{-- Bottom Actions --}}
<div class="mt-4 d-flex gap-2">
    <a href="{{ route('interviews.index') }}" class="btn btn-secondary">
        Back
    </a>

    <a href="{{ route('interviews.rounds.create', $interview) }}"
       class="btn btn-primary">
        Add New Round
    </a>
</div>

</div>
@endsection
