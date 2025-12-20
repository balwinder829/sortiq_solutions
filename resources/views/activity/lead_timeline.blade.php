@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Lead Timeline — {{ $lead->name ?? 'Lead #'.$lead->id }}</h3>

   <!--  <a href="{{ route('leads.show', $lead->id) }}" class="btn btn-secondary mb-3">
        ← Back to Lead
    </a> -->

    @if($timeline->isEmpty())
        <div class="alert alert-info">No activity recorded.</div>
    @endif

    <ul class="timeline">
        @foreach($timeline as $log)
        <li class="timeline-item mb-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <strong>{{ $log->user->username }}</strong>
                    <span class="text-muted"> → {{ ucwords(str_replace('_',' ', $log->action)) }}</span>

                    <span class="float-end text-muted">
                        {{ $log->created_at->format('d M Y, h:i A') }}
                    </span>

                    <hr>

                    @if($log->new_value)
                        <pre class="small bg-light p-2">{{ $log->new_value }}</pre>
                    @endif

                </div>
            </div>

        </li>
        @endforeach
    </ul>

</div>

<style>
.timeline { border-left: 3px solid #6b51df; padding-left: 20px; }
.timeline-item { position: relative; }
.timeline-item::before {
    content: ''; width: 14px; height: 14px;
    background: #6b51df; border-radius: 50%;
    position: absolute; left: -9px; top: 8px;
}
</style>

@endsection
@push('scripts')
<script>
    // Fix sidebar layout issue on open-in-new-tab
    window.addEventListener("load", function () {
        setTimeout(function () {
            window.dispatchEvent(new Event('resize'));
        }, 200);
    });
</script>
@endpush
