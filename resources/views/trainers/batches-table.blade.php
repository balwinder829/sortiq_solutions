@if($batches->count() == 0)
    <div class="alert alert-info">No batches found.</div>
@else
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Batch Name</th>
                <th>Session</th> {{-- NEW --}}
                <th>Start Time</th>
                <th>End Time</th>
                <th>Technology</th>
            </tr>
        </thead>
        <tbody>
            @foreach($batches->sortBy('start_time') as $batch)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $batch->batch_name }}</td>
                 <td>
                    {{ ucwords($batch->sessionData->session_name) ?? ucwords($batch->session_name) ?? '-' }}
                </td>
                <td>{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</td>
                <td>{{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }}</td>
                <td>{{ $batch->courseData->course_name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif
