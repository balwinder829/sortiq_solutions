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

<div class="row mb-2 align-items-end">

    {{-- LEFT: PAGE TITLE --}}
    <div class="col-md-2">
        <h1 class="page_heading">Mentors</h1>
    </div>

    {{-- MIDDLE: FILTER FORM --}}
    <div class="col-md-8">
        <form method="GET" action="{{ route('trainers.index') }}" class="row g-2 align-items-end">

            {{-- COURSE FILTER --}}
            <div class="col-md-6">
                <!-- <label class="fw-bold">Course (Technology)</label> -->
                <select name="course" class="form-control">
                    <option value="">-- All Courses --</option>

                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ request('course') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- BUTTONS WITH SMALL GAP --}}
            <div class="col-md-6 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    Search
                </button>

                <a href="{{ route('trainers.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </form>
    </div>

    {{-- RIGHT: ADD MENTOR BUTTON --}}
    <div class="col-md-2">
        <div class="d-flex justify-content-end">
            <a href="{{ route('trainers.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add Mentor
            </a>
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

    <div class="table-responsive">
        <table id="trainers-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>UserName</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Technology</th>
                    <th>Total Batches</th>
                    <th>Online</th>
                    <th>Offline</th>

                    <th>Today Pending Batches</th> {{-- NEW COLUMN --}}

                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($trainers as $trainer)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td>{{ $trainer->username ?? '' }}</td>
                        <td>{{ ucwords($trainer->name ?? '') }}</td>
                        <td>{{ ucfirst($trainer->gender ?? '-') }}</td>
                        <td>{{ $trainer->phone ?? 'N/A' }}</td>
                        <td>{{ $trainer->email ?? 'N/A' }}</td>
                        <td>
                            @php
                                $techIds = $trainer->technology ? explode(',', $trainer->technology) : [];
                                $techNames = \App\Models\Course::whereIn('id', $techIds)->pluck('course_name');
                            @endphp

                            @foreach($techNames as $name)
                                <span class="badge bg-primary">{{ $name }}</span>
                            @endforeach
                        </td>

                        {{-- ================= TOTAL SESSION BATCHES ================= --}}
                        {{-- ================= TOTAL SESSION BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle batch-link"
                                 data-id="{{ $trainer->id }}"
                                 data-name="{{ $trainer->name ?? 'N/A' }}"
                                 data-type="all"
                                 title="View All Batches">
                                {{ $trainer->session_batches_count }}
                            </div>
                        </td>

                        {{-- ================= ONLINE BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle"
                                 style="background:#198754"  {{-- green --}}
                                 title="Online Batches">
                                {{ $trainer->online_batches_count }}
                            </div>
                        </td>

                        {{-- ================= OFFLINE BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle"
                                 style="background:#fd7e14"  {{-- orange --}}
                                 title="Offline Batches">
                                {{ $trainer->offline_batches_count }}
                            </div>
                        </td>


                        {{-- ================= REMAINING TODAY BATCHES ================= --}}
                        <td class="text-center">
                            <div class="batch-circle batch-link"
                                 data-id="{{ $trainer->id }}"
                                 data-name="{{ $trainer->name ?? 'N/A' }}"
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
