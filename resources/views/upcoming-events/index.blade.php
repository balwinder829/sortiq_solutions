@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between mb-3">
        <h4>Upcoming Events</h4>
        <div>
            <a href="{{ route('upcoming-events.calendar') }}" class="btn btn-secondary">
                Calendar View
            </a>
            <a href="{{ route('upcoming-events.create') }}" class="btn btn-primary">
                + Add Event
            </a>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Notify</th>
                <th>Status</th>
                <th width="200">Actions</th>
            </tr>
        </thead>
        <tbody>
        @foreach($upcomingEvents as $event)
            <tr>
                <td>{{ $event->name }}</td>
                <td>{{ $event->event_date->format('d M Y') }}</td>
                <td>{{ $event->notify ? 'Yes' : 'No' }}</td>
                <td>
                    @if($event->dismissed)
                        <span class="badge bg-danger">Dismissed</span>
                    @else
                        <span class="badge bg-success">Active</span>
                    @endif
                </td>
               <td class="text-nowrap">

    {{-- VIEW --}}
    <a href="{{ route('upcoming-events.show', $event) }}"
       class="btn btn-sm btn-outline-primary"
       title="View">
        <i class="fa fa-eye"></i>
    </a>

    {{-- EDIT (future only) --}}
    @if(!$event->event_date->isPast())
        <a href="{{ route('upcoming-events.edit', $event) }}"
           class="btn btn-sm btn-outline-warning"
           title="Edit">
            <i class="fa fa-edit"></i>
        </a>
    @endif

    {{-- DISMISS --}}
    @if(!$event->event_date->isPast() && !$event->dismissed)
        <form method="POST"
              action="{{ route('upcoming-events.dismiss', $event) }}"
              class="d-inline"
              onsubmit="return confirm('Dismiss notifications for this event?')">
            @csrf
            <button class="btn btn-sm btn-outline-danger"
                    title="Dismiss Notifications">
                <i class="fa fa-bell-slash"></i>
            </button>
        </form>
    @endif

    {{-- RE-ENABLE --}}
    @if(!$event->event_date->isPast() && $event->dismissed)
        <form method="POST"
              action="{{ route('upcoming-events.enable', $event) }}"
              class="d-inline"
              onsubmit="return confirm('Re-enable notifications for this event?')">
            @csrf
            <button class="btn btn-sm btn-outline-success"
                    title="Re-enable Notifications">
                <i class="fa fa-bell"></i>
            </button>
        </form>
    @endif

    {{-- DELETE --}}
    <form method="POST"
          action="{{ route('upcoming-events.destroy', $event) }}"
          class="d-inline"
          onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
        @csrf
        @method('DELETE')
        <button class="btn btn-sm btn-outline-dark"
                title="Delete Event">
            <i class="fa fa-trash"></i>
        </button>
    </form>

</td>


            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
