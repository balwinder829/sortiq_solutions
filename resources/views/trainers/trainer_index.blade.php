@extends('layouts.app')

@section('content')

<style>
.status-running {
    color: #198754;
    font-weight: 600;
}
.status-upcoming {
    color: #0d6efd;
    font-weight: 600;
}
.status-completed {
    color: #6c757d;
    font-weight: 600;
}

table.dataTable td {
    text-transform: capitalize;
}
</style>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>My Batches</h3>
    </div>

    <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
        <table id="trainer-batches-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Batch Name</th>
                    <th>Session</th>
                    <th>Technology</th>
                    <th>Session Time</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($batches as $batch)
                    @php
                        // Today session timing
                        $today = now()->format('Y-m-d');

                        $startTime = \Carbon\Carbon::parse($today.' '.$batch->start_time);
                        $endTime   = \Carbon\Carbon::parse($today.' '.$batch->end_time);

                        $now = now();
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $batch->batch_name }}</td>

                        <td>{{ $batch->sessionData?->session_name ?? '-' }}</td>

                        <td>{{ $batch->courseData?->course_name ?? '-' }}</td>

                        {{-- SESSION TIME --}}
                        <td>
                            {{ $startTime->format('h:i A') }} - {{ $endTime->format('h:i A') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="text-center">
                            @if($now->between($startTime, $endTime))
                                <span class="status-running">Running</span>
                            @elseif($now->lt($startTime))
                                <span class="status-upcoming">Upcoming</span>
                            @else
                                <span class="status-completed">Completed</span>
                            @endif
                        </td>
                    </tr>
                @endforeach

              
            </tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#trainer-batches-table').DataTable({
        pageLength: 10,
        lengthMenu: [5, 10, 25, 50]
    });
});
</script>
@endpush
