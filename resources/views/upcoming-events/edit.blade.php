@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Upcoming Event</h4>

    <form method="POST" action="{{ route('upcoming-events.update', $event) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Event Name</label>
            <input type="text" name="name" class="form-control"
                   value="{{ $event->name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description"
                      class="form-control">{{ $event->description }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Event Date</label>
            <input type="date" name="event_date" class="form-control"
                   value="{{ $event->event_date->format('Y-m-d') }}" required>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="notify" class="form-check-input"
                   {{ $event->notify ? 'checked' : '' }}>
            <label class="form-check-label">Enable Daily Notification</label>
        </div>

        <button class="btn btn-success">Update</button>
        <a href="{{ route('upcoming-events.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
