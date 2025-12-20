@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-3">Upcoming Events Calendar</h4>
    <div id="calendar" style="min-height: 600px;"></div>
</div>

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const today = new Date();
    today.setHours(0,0,0,0); // normalize

    const events = @json($events ?? []);

    const calendar = new FullCalendar.Calendar(
        document.getElementById('calendar'),
        {
            initialView: 'dayGridMonth',
            height: 600,

            events: events,

            // ➕ CREATE: future dates only
            dateClick(info) {
                const clickedDate = new Date(info.dateStr);
                clickedDate.setHours(0,0,0,0);

                if (clickedDate < today) {
                    alert('You cannot create events for past dates.');
                    return;
                }

                window.location.href =
                    "{{ route('upcoming-events.create') }}?date=" + info.dateStr;
            },

            // ✏️ EDIT: future events only
            eventClick(info) {
                const eventDate = new Date(info.event.start);
                eventDate.setHours(0,0,0,0);

                if (eventDate < today) {
                    alert('Past events cannot be edited.');
                    return;
                }

                window.location.href =
                    "{{ url('upcoming-events') }}/" + info.event.id + "/edit";
            },

            // 🎨 Visual cue for past events
            eventDidMount(info) {
                const eventDate = new Date(info.event.start);
                eventDate.setHours(0,0,0,0);

                if (eventDate < today) {
                    info.el.style.opacity = '0.5';
                    info.el.style.cursor = 'not-allowed';
                }
            }
        }
    );

    calendar.render();
});
</script>
@endsection
