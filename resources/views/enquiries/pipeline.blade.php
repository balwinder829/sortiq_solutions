@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Lead Status Pipeline</h3>

    <div class="row">

        @foreach($statuses as $status)
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-header text-white 
                        @if($status=='new') bg-primary
                        @elseif($status=='followup') bg-warning
                        @elseif($status=='closed') bg-danger
                        @elseif($status=='joined') bg-success
                        @endif
                    ">
                        <strong>{{ strtoupper($status) }}</strong>
                    </div>

                    <div class="card-body pipeline-column" 
                         data-status="{{ $status }}"
                         style="min-height: 300px; background: #f8f9fa;">

                        @foreach($enquiries[$status] ?? [] as $enq)
                            <div class="card mb-2 p-2 draggable" 
                                 draggable="true"
                                 data-id="{{ $enq->id }}"
                                 style="cursor: grab;">
                                <strong>{{ $enq->name }}</strong><br>
                                <small>{{ $enq->mobile }}</small><br>
                                <a href="{{ route('admin.enquiries.show', $enq->id) }}"
                                   class="btn btn-sm btn-outline-primary mt-1">
                                    View
                                </a>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        @endforeach

    </div>

</div>
@endsection

@push('scripts')
<script>
let dragged = null;

document.addEventListener('dragstart', function (e) {
    dragged = e.target;
    e.target.style.opacity = .5;
});

document.addEventListener('dragend', function (e) {
    e.target.style.opacity = "";
});

document.querySelectorAll('.pipeline-column').forEach(col => {
    col.addEventListener('dragover', e => e.preventDefault());

    col.addEventListener('drop', function (e) {
        e.preventDefault();

        if (!dragged) return;

        this.appendChild(dragged);

        let enquiryId = dragged.getAttribute('data-id');
        let newStatus = this.getAttribute('data-status');

        // AJAX update
        fetch("{{ route('admin.enquiries.updateStatus') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                id: enquiryId,
                status: newStatus
            })
        }).then(res => res.json()).then(res => {
            console.log("Updated!");
        });

    });
});
</script>
@endpush
