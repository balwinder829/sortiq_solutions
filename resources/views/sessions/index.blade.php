@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Sessions</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                   <a href="{{ route('sessions.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add Session</a>
            </div>
        </div>
    </div>
    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">

                <table id="sessions-table" class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Total Batches</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sessions as $session)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{$session->session_name}}</td>
                <td>{{ \Carbon\Carbon::parse($session->session_start)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($session->session_end)->format('d M Y') }}</td>
                 

                <td>{{ ucfirst($session->status) }}</td>
                <td>
    <span class="badge rounded-pill bg-primary view-batches"
          style="cursor:pointer; font-size:14px;"
          data-id="{{ $session->id }}">
        {{ $session->batches_count ?? 0 }}
    </span>
</td>

                <td class="text-center">
                    <div class="mb-2">
                        <a href="{{ route('sessions.edit', $session->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="return confirm('Delete this Session?')">
                                        <i class="fa fa-trash"></i>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    
</div>
</div>
@push('scripts')
<script>
    $(document).ready(function () {
        $('#sessions-table').DataTable({
            "pageLength": 50,
            "lengthMenu": [5, 10, 25, 50, 100],
             // "scrollX": true // <-- Add this
        });
    });
</script>
<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
@endpush

<!-- Batch List Modal -->
<div class="modal fade" id="batchesModal" tabindex="-1" aria-labelledby="batchesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="batchesModalLabel">Batch List</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <table class="table table-bordered table-hover">
              <thead class="table-light">
                  <tr>
                      <th>#</th>
                      <th>Batch Name</th>
                      <th>Technology</th>
                      <th>Trainer</th>
                      <th>Start Time</th>
                      <th>End Time</th>
                  </tr>
              </thead>
              <tbody id="batchList">
                  <!-- AJAX -->
              </tbody>
          </table>
      </div>
    </div>
  </div>
</div>


<script>
    function formatTime12Hour(time) {
        if (!time) return '-';

        let [hour, minute] = time.split(':');
        hour = parseInt(hour);

        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;

        return `${hour}:${minute} ${ampm}`;
    }

$(document).on('click', '.view-batches', function() {
    let sessionId = $(this).data('id');

    $('#batchList').html(
        '<tr><td colspan="6" class="text-center">Loading...</td></tr>'
    );

    $.ajax({
        url: '/sessions/' + sessionId + '/batches',
        type: 'GET',
        success: function(batches) {
            let html = '';

            if(batches.length === 0) {
                html = '<tr><td colspan="6" class="text-center text-danger">No Batches Found</td></tr>';
            } else {
                $.each(batches, function(i, batch) {
                    html += `
                        <tr>
                            <td>${i + 1}</td>
                            <td>${batch.batch_name}</td>
                            <td>${batch.course_data ? batch.course_data.course_name : '-'}</td>
                            <td>${batch.trainer_data ? batch.trainer_data.trainer_name : '-'}</td>
                            <td>${formatTime12Hour(batch.start_time)}</td>
                            <td>${formatTime12Hour(batch.end_time)}</td>
                        </tr>`;
                });
            }

            $('#batchList').html(html);
            $('#batchesModal').modal('show');
        }
    });
});
</script>



@endsection
