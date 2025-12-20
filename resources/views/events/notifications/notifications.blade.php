@extends('layouts.app')

@section('content')
<div class="container">

    <h2 class="mb-4">
        <i class="bx bx-calendar-event me-2"></i>
        Event Notifications
    </h2>

    {{-- =======================
         TODAY EVENTS
    ======================== --}}
    <div class="card mb-4 shadow-sm border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="bx bx-calendar-check me-2"></i>
                Today's Events
            </h5>
        </div>

        <div class="card-body">
            @if($todayEvents->isEmpty())
                <p class="text-muted">No events today.</p>
            @else
                <div class="row">
                    @foreach($todayEvents as $e)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route($e->event_type.'.events.show', $e->id) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm">

                                {{-- Cover Image --}}
                                @if($e->cover_image)
                                <img src="{{ asset($e->cover_image) }}"
                                     class="card-img-top"
                                     style="height:160px;object-fit:cover;">
                                @endif

                                <div class="card-body">
                                    <h6 class="fw-bold">{{ $e->title }}</h6>
                                    
                                    <span class="badge bg-primary">
                                        {{ ucfirst($e->event_type) }} Event
                                    </span>

                                    <p class="text-muted mt-2 mb-0">
                                        {{ \Carbon\Carbon::parse($e->event_date)->format('d M Y') }}
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>



    {{-- =======================
         TOMORROW EVENTS
    ======================== --}}
    <div class="card mb-4 shadow-sm border-warning">
        <div class="card-header bg-warning">
            <h5 class="mb-0 text-dark">
                <i class="bx bx-calendar-exclamation me-2"></i>
                Tomorrow's Events
            </h5>
        </div>

        <div class="card-body">
            @if($tomorrowEvents->isEmpty())
                <p class="text-muted">No events tomorrow.</p>
            @else
                <div class="row">
                    @foreach($tomorrowEvents as $e)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route($e->event_type.'.events.show', $e->id) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm">

                                {{-- Cover Image --}}
                                @if($e->cover_image)
                                <img src="{{ asset($e->cover_image) }}"
                                     class="card-img-top"
                                     style="height:160px;object-fit:cover;">
                                @endif

                                <div class="card-body">
                                    <h6 class="fw-bold">{{ $e->title }}</h6>
                                    
                                    <span class="badge bg-warning text-dark">
                                        {{ ucfirst($e->event_type) }} Event
                                    </span>

                                    <p class="text-muted mt-2 mb-0">
                                        {{ \Carbon\Carbon::parse($e->event_date)->format('d M Y') }}
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>



    {{-- =======================
         UPCOMING EVENTS
    ======================== --}}
    <div class="card mb-4 shadow-sm border-success">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0">
                <i class="bx bx-calendar-star me-2"></i>
                Upcoming Events
            </h5>
        </div>

        <div class="card-body">
            @if($upcomingEvents->isEmpty())
                <p class="text-muted">No upcoming events.</p>
            @else
                <div class="row">
                    @foreach($upcomingEvents as $e)
                    <div class="col-md-4 mb-3">
                        <a href="{{ route($e->event_type.'.events.show', $e->id) }}" class="text-decoration-none text-dark">
                            <div class="card h-100 shadow-sm">

                                {{-- Cover Image --}}
                                @if($e->cover_image)
                                <img src="{{ asset($e->cover_image) }}"
                                     class="card-img-top"
                                     style="height:160px;object-fit:cover;">
                                @endif

                                <div class="card-body">
                                    <h6 class="fw-bold">{{ $e->title }}</h6>

                                    <span class="badge bg-success">
                                        {{ ucfirst($e->event_type) }} Event
                                    </span>

                                    <p class="text-muted mt-2 mb-0">
                                        {{ \Carbon\Carbon::parse($e->event_date)->format('d M Y') }}
                                    </p>
                                </div>

                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
