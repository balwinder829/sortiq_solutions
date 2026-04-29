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
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    <div class="table-responsive">

                <table id="sessions-table" class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Session ID</th>
                <th>Session Display Name</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th>Total Batches</th>
                <th>Total Students</th>
                <th>Online Students</th>
                <th>Offline Students</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            

            @foreach($sessionsList as $session)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td style="
                  white-space: nowrap;
                  max-width: 200px;
                  overflow: hidden;
                  text-overflow: ellipsis;
                ">{{$session->session_name}}</td>
                <td>{{$session->id}}</td>
                <td style="
                  white-space: nowrap;
                  max-width: 200px;
                  overflow: hidden;
                  text-overflow: ellipsis;
                ">{{$session->session_display_name}}</td>
                <td>{{ \Carbon\Carbon::parse($session->session_start)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($session->session_end)->format('d M Y') }}</td>
                 

                <td>{{ ucfirst($session->status) }}</td>
                <td>
    <span class="badge rounded-pill bg-primary view-batches"
          style="cursor:pointer; font-size:14px;"
          data-id="{{ $session->id }}">
        {{ $session->batches->count() }}

    </span>
</td>
<!-- <td>
    <span class="badge bg-success">
        {{ $session->total_students }}
    </span>
</td> -->
 <td>
    
        <a href="{{ route('common_filtered_student', [
            'session' => $session->id
        ]) }}"
       class="text-decoration-none">
        <span class="badge bg-success">
            {{ $session->students_count }}
        </span>
    </a>
     
</td>  

 {{-- ONLINE --}}
    <td>
        <a href="{{ route('common_filtered_student', [
            'session' => $session->id,'is_online' => 1
        ]) }}"
       class="text-decoration-none">
        <span class="badge bg-primary">
            {{ $session->online_students_count ?? 0 }}
        </span>
        </a>
    </td>

    {{-- OFFLINE --}}
    <td>
        <a href="{{ route('common_filtered_student', [
            'session' => $session->id,'is_online' => 0
        ]) }}"
       class="text-decoration-none">
        <span class="badge bg-secondary">
            {{ $session->offline_students_count ?? 0 }}
        </span>
    </a>
    </td>

                <td class="text-center" style="width: 100px;">
                    <div class="mb-2"  style="width: 100px;">
                        <a href="{{ route('sessions.edit', $session->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-edit"></i></a>
                        <form action="{{ route('sessions.destroy', $session->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-swal-confirm="Delete this Session?">
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
            "order": []
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

 


<script>
    function formatTime12Hour(time) {
        if (!time) return '-';

        let [hour, minute] = time.split(':');
        hour = parseInt(hour);

        const ampm = hour >= 12 ? 'PM' : 'AM';
        hour = hour % 12 || 12;

        return `${hour}:${minute} ${ampm}`;
    }
 
</script>



@endsection
