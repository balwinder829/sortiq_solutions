@extends('layouts.app')

@section('content')
<style>
.batch-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}
.batch-circle:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}
 
table.dataTable td {
    text-transform: capitalize;
}
 </style>

<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Trainers</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                    <a href="{{ route('trainers.create') }}" style="background-color: #6b51df; color: #fff;" class="btn btn-primary mb-3">Add Trainer</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive" style="max-height:500px; overflow-y:auto;">
        <table id="trainers-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Technology</th>
                    <th>Total Assign Batches</th>

                    <th>Today Pending Batches</th> {{-- NEW COLUMN --}}

                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($trainers as $trainer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $trainer->user->name ?? 'N/A' }}</td>
                        <td>{{ ucfirst($trainer->gender ?? '-') }}</td>
                        <td>{{ $trainer->user->phone ?? 'N/A' }}</td>
                        <td>{{ $trainer->user->email ?? 'N/A' }}</td>
                        <td>{{ $trainer->courseData->course_name ?? '-' }}</td>

                        {{-- ================= TOTAL SESSION BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle batch-link"
                                 data-id="{{ $trainer->id }}"
                                 data-name="{{ $trainer->user->name ?? 'N/A' }}"
                                 data-type="all"   {{-- DEFAULT --}}
                                 title="View All Batches">
                                {{ $trainer->session_batches_count }}
                            </div>
                        </td>

                        {{-- ================= REMAINING TODAY BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle batch-link"
                                 data-id="{{ $trainer->id }}"
                                 data-name="{{ $trainer->user->name ?? 'N/A' }}"
                                 data-type="remaining"   {{-- NEW --}}
                                 title="View Today's Remaining Batches">
                                {{ $trainer->today_remaining_batches_count ?? 0 }}
                            </div>
                        </td>

                        {{-- Actions --}}
                        <td class="text-center">
                            <div class="d-flex justify-content-center align-items-center" style="gap: 6px;">

                                {{-- Edit --}}
                                <a href="{{ route('trainers.edit', $trainer->id) }}"
                                   class="btn btn-sm" title="Edit">
                                    <i class="fa fa-edit"></i>
                                </a>

                                {{-- Delete --}}
                               <!--  <form action="{{ route('trainers.destroy', $trainer->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm"
                                        onclick="return confirm('Delete this trainer?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form> -->

                            </div>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- MODAL --}}
<div class="modal fade" id="batchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Batches of <span id="trainerName"></span>
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div id="batchModalContent" class="text-center">Loading...</div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#trainers-table').DataTable({
        "pageLength": 50,
        "lengthMenu": [5, 10, 25, 50, 100]
    });
});
</script>

<script>
$(document).on('click', '.batch-link', function () {

    var trainerId   = $(this).data('id');
    var trainerName = $(this).data('name');
    var type        = $(this).data('type'); // NEW

    $('#trainerName').text(trainerName);
    $('#batchModal').modal('show');
    $('#batchModalContent').html('<p class="text-center">Loading...</p>');

    $.ajax({
        url: "/trainers/" + trainerId + "/batches-ajax",
        type: "GET",
        data: { type: type }, // PASSING THE TYPE
        success: function(data) {
            $('#batchModalContent').html(data);
        }
    });
});
</script>

@endpush
