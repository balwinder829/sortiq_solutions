@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-3">Imported Leads (Batch: {{ $batchId }})</h3>

    <a href="{{ route('leads.import.history') }}" class="btn btn-secondary mb-3">
        ← Back to Import History
    </a>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="batchLeadsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th>Follow-up</th>
                    <th>Assigned To</th>
                    <th>Imported By</th>
                    <th>Imported At</th>
                </tr>
            </thead>

            <tbody>
                @forelse($leads as $lead)
                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $lead->name ?? '-' }}</td>

                    <td>
                        <a href="tel:{{ $lead->phone }}" class="text-primary">{{ $lead->phone }}</a>
                    </td>

                    <td>
                        @if($lead->email)
                            <a href="mailto:{{ $lead->email }}">{{ $lead->email }}</a>
                        @else
                            -
                        @endif
                    </td>

                    <td>{{ $lead->source ?? 'Excel Import' }}</td>

                    <td>
                        <span class="badge 
                            @if($lead->status == 'new') bg-secondary
                            @elseif($lead->status == 'contacted') bg-primary
                            @elseif($lead->status == 'follow_up') bg-warning text-dark
                            @elseif($lead->status == 'onboarded') bg-success
                            @elseif($lead->status == 'not_interested') bg-danger
                            @endif
                        ">
                            {{ ucfirst(str_replace('_',' ', $lead->status)) }}
                        </span>
                    </td>

                    <td>
                        {{ $lead->follow_up_date ? $lead->follow_up_date->format('d M Y') : '-' }}
                    </td>

                    <td>{{ $lead->assignedTo->username ?? 'Not Assigned' }}</td>

                    <td>{{ $lead->creator->username ?? $lead->creator->name ?? 'N/A' }}</td>

                    <td>{{ $lead->created_at->format('d M Y h:i A') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">
                        No leads found for this batch.
                    </td>
                </tr>
                @endforelse
            </tbody>

        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#batchLeadsTable').DataTable({
        pageLength: 25,
        scrollX: true
    });
});
</script>
@endpush
