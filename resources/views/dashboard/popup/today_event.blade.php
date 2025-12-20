<div class="card border-primary shadow-lg">

    <div class="card-header bg-primary text-white d-flex justify-content-between">
        <h5 class="mb-0">
            <i class="bx bx-calendar-event me-2"></i>Today's Events
        </h5>
        <button type="button" class="btn-close btn-close-white"
                onclick="dismissTodayPopup()"></button>
    </div>

    <div class="card-body bg-light text-dark">
        <p><strong>{{ $todayEvents->count() }}</strong> event(s) today</p>

        <div class="d-flex justify-content-end gap-2">
            <button class="btn btn-sm btn-secondary" onclick="dismissTodayPopup()">Dismiss</button>
            <a href="{{ route('admin.events.notifications') }}" class="btn btn-sm btn-primary">View</a>
        </div>
    </div>

</div>

<script>
function dismissTodayPopup() {
    fetch('{{ route("admin.event.notification.dismiss") }}', {
        method: "POST",
        headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"}
    }).then(() => {
        let box = document.getElementById('event-today-popup');
        box.classList.add('animate__fadeOutRight');
        setTimeout(() => box.remove(), 300);
    });
}
</script>
