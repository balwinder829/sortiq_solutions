@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Upcoming Event Details</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-body">

            <h5 class="card-title">{{ $event->name }}</h5>

            <p class="card-text">
                <strong>Date:</strong>
                {{ $event->event_date->format('d M Y') }}
            </p>

            <p class="card-text">
                <strong>Description:</strong><br>
                {{ $event->description ?? '—' }}
            </p>

            <p>
                <strong>Notifications:</strong>
                {{ $event->notify ? 'Enabled' : 'Disabled' }}
            </p>

            {{-- ACTIONS --}}
            <div class="mt-3 d-flex gap-2 align-items-center">

                <a href="{{ route('upcoming-events.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>

                {{-- EDIT (future only) --}}
                @if(!$event->event_date->isPast())
                    <a href="{{ route('upcoming-events.edit', $event) }}"
                       class="btn btn-primary">
                        Edit
                    </a>
                @endif

                {{-- DISMISS (future & not dismissed) --}}
                @if(!$event->event_date->isPast() && !$event->dismissed)
                    <form method="POST"
                          action="{{ route('upcoming-events.dismiss', $event) }}"
                          onsubmit="return confirm('Dismiss notifications for this event?')">
                        @csrf
                        <button class="btn btn-danger">
                            Dismiss Notifications
                        </button>
                    </form>
                @endif

                {{-- RE-ENABLE (future & dismissed) --}}
                @if(!$event->event_date->isPast() && $event->dismissed)
                    <form method="POST"
                          action="{{ route('upcoming-events.enable', $event) }}"
                          onsubmit="return confirm('Re-enable notifications for this event?')">
                        @csrf
                        <button class="btn btn-success">
                            Re-enable Notifications
                        </button>
                    </form>
                @endif

                {{-- STATUS BADGE --}}
                @if($event->dismissed)
                    <span class="badge bg-danger">
                        Notifications Dismissed
                    </span>
                @else
                    <span class="badge bg-success">
                        Notifications Active
                    </span>
                @endif

            </div>

        </div>
    </div>
</div>
@endsection
