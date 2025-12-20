@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between mb-3">
        <h3>Events</h3>

        <div>
            <a href="{{ route('events.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Add Event
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="eventsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Event Title</th>
                    <th>Date</th>
                    <th>Total Images</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($events as $event)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $event->title }}</td>
                    <td>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d-m-Y') : '-' }}</td>

                    <td>{{ $event->images->count() }} Images</td>

                    <td>
                        <a href="{{ route('events.show', $event->id) }}" class="btn btn-sm">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('events.edit', $event->id) }}" class="btn btn-sm">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm" onclick="return confirm('Delete this event?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $events->links() }}
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#eventsTable').DataTable({
        pageLength: 25,
        lengthMenu: [10,25,50,100],
        scrollX: true
    });
});
</script>
@endpush
