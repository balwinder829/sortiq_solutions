@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Employee Events</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <a href="{{ route('employee.events.create') }}" class="btn mb-3" style="background-color: #6b51df; color: white;">
                        Add Employee Event
                    </a>
            </div>
        </div>
    </div>

<form method="GET" class="row mb-3">

    <div class="col-md-3">
        <label>From Date</label>
        <input type="date" name="from_date" class="form-control"
               value="{{ request('from_date') }}">
    </div>

    <div class="col-md-3">
        <label>To Date</label>
        <input type="date" name="to_date" class="form-control"
               value="{{ request('to_date') }}">
    </div>

    <div class="col-md-3">
        <label>Filter</label>
        <select name="filter" class="form-control">
            <option value="">-- All --</option>
            <option value="upcoming" {{ request('filter')=='upcoming'?'selected':'' }}>Upcoming</option>
            <option value="today" {{ request('filter')=='today'?'selected':'' }}>Today</option>
            <option value="past" {{ request('filter')=='past'?'selected':'' }}>Past</option>
        </select>
    </div>

    <div class="col-md-3 d-flex align-items-end gap-2">
        <button class="btn btn-primary mt-3" style="background-color: #6b51df; color: white;">Apply Filter</button>
        <a class="btn btn-secondary mt-3" href="{{ route('employee.events.index') }}">Reset</a>
    </div>


</form>

<table class="table table-bordered" id="eventsTable">
    <thead>
        <tr>
            <th>Cover</th>
            <th>Title</th>
            <th>Date</th>
            <th>Media</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($events as $event)
        <tr>
            <td>
                @if($event->cover_image)
                <img src="{{ asset($event->cover_image) }}" height="70">
                @endif
            </td>

            <td>{{ $event->title }}</td>
            <td>{{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}</td>

            <td>{{ $event->images->count() + $event->videos->count() }}</td>

            <td>
                <a href="{{ route('employee.events.show', $event->id) }}" class="btn btn-sm"> <i class="fa fa-eye"></i></a>
                <a href="{{ route('employee.events.edit', $event->id) }}" class="btn btn-sm"><i class="fa fa-edit"></i></a>

                <form action="{{ route('employee.events.destroy', $event->id) }}"
                      method="POST"
                      class="d-inline">
                    @csrf @method('DELETE')
                    <button onclick="return confirm('Delete this event?')" 
                            class="btn btn-sm">
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
@push('scripts')
<script>
$(function(){
    $('#eventsTable').DataTable({
        pageLength: 25,
        lengthMenu: [10,25,50,100],
    });
});
</script>
@endpush