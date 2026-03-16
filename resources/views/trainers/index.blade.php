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
        <form method="GET"id="filterForm" class="row g-2 align-items-end">

            {{-- COURSE FILTER --}}
            <div class="col-md-6">
                <!-- <label class="fw-bold">Course (Technology)</label> -->
                <select name="course" id="filtercourse" class="form-control filterchange">
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
                <!-- <button type="submit" class="btn btn-primary">
                    Search
                </button> -->

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
<div class="col-md-8 mb-4">
    <p class="mb-1 fw-bold">Mentors Login URL</p>

    <div class="input-group">
        <a href="{{ route('mentors.login') }}"
           target="_blank"
           id="loginUrl"
           class="form-control text-primary text-decoration-none">
            {{ route('mentors.login') }}
        </a>

        <button class="btn btn-outline-secondary"
                type="button"
                 data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Copy Mentors Login URL"
                onclick="copyLoginUrl()">
            <i class="fa fa-copy"></i>
        </button>
    </div>

    <small id="copyMessage" class="text-success d-none">
        Copied to clipboard!
    </small>
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

            <tbody></tbody>
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
    var params = new URLSearchParams(window.location.search);
    var table = $('#trainers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('trainers.data') }}",
            data: function (d) {
                d.course =  $('#filtercourse').val();
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7, orderable: false, searchable: false },
            { data: 8, orderable: false, searchable: false },
            { data: 9, orderable: false, searchable: false },
            { data: 10, orderable: false, searchable: false },
            { data: 11, orderable: false, searchable: false }
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100]
    });
     $('#filtercourse').change(function () {
        table.ajax.reload();
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
 <script>
function copyLoginUrl() {
    const url = document.getElementById('loginUrl').textContent.trim(); // ✅ removes extra spaces;

    navigator.clipboard.writeText(url).then(function() {
        const msg = document.getElementById('copyMessage');
        msg.classList.remove('d-none');

        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);
    });
}
</script>

@endpush
