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
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
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
                    <th>Actions</th>
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

                        <td> @foreach($batch->courses as $course)
                            <span class="badge bg-primary">{{ $course->course_name }}</span>
                        @endforeach</td>

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
                        <td class="text-center">

<div class="dropdown">

<button class="btn btn-sm btn-secondary dropdown-toggle"
        data-bs-toggle="dropdown">
    Actions
</button>

<ul class="dropdown-menu">

<li>
<a class="dropdown-item"
   href="{{ route('trainer.attendance.mark',$batch->id) }}">
   Mark Attendance
</a>
</li>

<li>
<a class="dropdown-item"
   href="{{ route('trainer.attendance.batch',$batch->id) }}">
   View Attendance
</a>
</li>

<li>
<button class="dropdown-item sendEmailBtn"
        data-batch="{{ $batch->id }}">
    Send Email
</button>
</li>

</ul>
<a href="{{ route('batch.show',$batch->id) }}"
   class="btn btn-sm btn-primary"
   title="View Batch">

<i class="fa fa-eye"></i>

</a>

</div>

</td>
                       <!--  <td>
                        <a href="{{ route('trainer.attendance.mark',$batch->id) }}"
                           class="btn btn-sm btn-primary">

                        Mark Attendance

                        </a>
                        <a href="{{ route('trainer.attendance.batch',$batch->id) }}"
                            class="btn btn-sm btn-info">

                            View Attendance

                            </a>
                        <button 
                            class="btn btn-sm btn-success sendEmailBtn"
                            data-batch="{{ $batch->id }}">
                            Send Email
                            </button>
                        </td> -->
                    </tr>
                @endforeach

              
            </tbody>
        </table>
        <div class="modal fade" id="emailModal">
<div class="modal-dialog">
<div class="modal-content">

<form method="POST" action="{{ route('trainer.sendBatchEmail') }}">
@csrf

<input type="hidden" name="batch_id" id="email_batch_id">

<div class="modal-header">
<h5 class="modal-title">Send Email to Batch</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<label>Subject</label>
<input type="text"
name="subject"
class="form-control"
placeholder="Enter email subject"
required>

<label class="mt-3">Message</label>
<textarea
name="message"
class="form-control"
rows="5"
required></textarea>
</div>

<div class="modal-footer">

<button type="button" 
class="btn btn-secondary" 
data-bs-dismiss="modal">
Cancel
</button>

<button class="btn btn-primary">
Send Email
</button>

</div>

</form>

</div>
</div>
</div>
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

$('.sendEmailBtn').click(function(){

    let batchId = $(this).data('batch');

    $('#email_batch_id').val(batchId);

    $('#emailModal').modal('show');

});
</script>
@endpush
