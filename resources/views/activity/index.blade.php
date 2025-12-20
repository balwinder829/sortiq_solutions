@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Sales User Activity Log</h3>

    {{-- Filters --}}
    <form method="GET" class="row mb-4">

        <div class="col-md-3">
            <select name="user_id" class="form-control">
                <option value="">-- All Sales Users --</option>
                @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                        {{ $user->username }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
        </div>

        <div class="col-md-2">
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control">
        </div>

        <div class="col-md-2 d-grid">
            <button class="btn btn-primary">Filter</button>
        </div>

        <div class="col-md-2 d-grid">
            <a href="{{ route('admin.activity') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="activityTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sales User</th>
                    <th>Lead</th>
                    <th>Action</th>
                    <th>Old Value</th>
                    <th>New Value</th>
                    <th>Note</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
    @if($log->user)
        <a href="{{ route('activity.user', $log->user_id) }}" >
            {{ $log->user->username }}
        </a>
    @else
        N/A
    @endif
</td>

                    <td>
    <a href="{{ route('activity.lead', $log->lead_id) }}" target="_blank">
        Lead #{{ $log->lead_id }}
    </a>
</td>

                    <td>{{ ucwords(str_replace('_',' ', $log->action)) }}</td>

                    <td>
                        @if($log->old_value)
                            <pre class="small">{{ json_encode(json_decode($log->old_value), JSON_PRETTY_PRINT) }}</pre>
                        @else
                            -
                        @endif
                    </td>

                    <td>
                        @if($log->new_value)
                            <pre class="small">{{ json_encode(json_decode($log->new_value), JSON_PRETTY_PRINT) }}</pre>
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ $log->note ?? '-' }}</td>
                    <td>{{ $log->created_at->format('d-m-Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $logs->links() }}
</div>
@endsection

@push('scripts')
<script>
    $('#activityTable').DataTable({
        pageLength: 25,
        scrollX: true,
    });
</script>
@endpush
