@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">User Activity — {{ $user->username }}</h3>

    <a href="{{ route('admin.activity') }}" class="btn btn-secondary mb-3">← Back</a>

    {{-- Filters --}}
    <form method="GET" class="row mb-4">

        <div class="col-md-3">
            <select name="action" class="form-control">
                <option value="">-- All Actions --</option>
                <option value="lead_created" {{ request('action')=='lead_created' ? 'selected' : '' }}>Lead Created</option>
                <option value="call_made" {{ request('action')=='call_made' ? 'selected' : '' }}>Call Made</option>
                <option value="lead_updated" {{ request('action')=='lead_updated' ? 'selected' : '' }}>Lead Updated</option>
                <option value="lead_updated_after_call" {{ request('action')=='lead_updated_after_call' ? 'selected' : '' }}>Updated After Call</option>
            </select>
        </div>

        <div class="col-md-3">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>

        <div class="col-md-3">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>

        <div class="col-md-1 d-grid">
            <button class="btn btn-primary">Filter</button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="{{ route('activity.user', $user->id) }}" class="btn btn-secondary">Reset</a>
        </div>

    </form>

    {{-- Timeline --}}
    <ul class="timeline">

        @foreach($timeline as $log)
        <li class="timeline-item mb-4">

            <div class="card shadow-sm">
                <div class="card-body">

                    <strong>{{ $user->username }}</strong>
                    → {{ ucwords(str_replace('_',' ', $log->action)) }}

                    <span class="float-end text-muted">
                        {{ $log->created_at->format('d M Y, h:i A') }}
                    </span>

                    <hr>

                    <p class="mb-1">
                        <strong>Lead:</strong>
                        <a href="{{ route('leads.show', $log->lead_id) }}">
                            Lead #{{ $log->lead_id }}
                        </a>
                    </p>

                    {{-- New value --}}
                    @if($log->new_value)
                        <label class="fw-bold">Details:</label>
                        <pre class="small bg-light p-2">{!! nl2br(e($log->new_value)) !!}</pre>
                    @endif

                    {{-- Old vs New changes (only when old_value exists) --}}
                    @if($log->old_value)
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Old:</strong>
                                <pre class="small bg-light p-2">{!! nl2br(e($log->old_value)) !!}</pre>
                            </div>

                            <div class="col-md-6">
                                <strong>New:</strong>
                                <pre class="small bg-light p-2">{!! nl2br(e($log->new_value)) !!}</pre>
                            </div>
                        </div>
                    @endif

                </div>
            </div>

        </li>
        @endforeach

    </ul>

    {{-- Pagination --}}
   <div class="mt-3 d-flex justify-content-center">
    {{ $timeline->onEachSide(1)->links('pagination::bootstrap-4') }}
</div>


</div>

<style>
.timeline { 
    border-left: 3px solid #6b51df; 
    padding-left: 20px; 
}
.timeline-item { 
    position: relative; 
}
.timeline-item::before {
    content: ''; 
    width: 14px; 
    height: 14px;
    background: #6b51df; 
    border-radius: 50%;
    position: absolute; 
    left: -9px; 
    top: 8px;
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
