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
            <h1 class="page_heading">Batches</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                   <a href="{{ route('batches.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add Batch</a>
            </div>
        </div>
    </div>

    

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif


    {{-- ==================== FILTERS ==================== --}}
    <form method="GET" action="{{ route('batches.index') }}" class="mb-3">
        <div class="row">

            {{-- Trainer Filter --}}
            <div class="col-md-3">
                <label><strong>Mentor</strong></label>
                <select name="trainer" class="form-control">
                    <option disabled selected value="">All Mentors</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ request('trainer') == $trainer->id ? 'selected' : '' }}>
                            {{ ucwords($trainer?->name ?? 'Unknown') }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Technology Filter --}}
            <div class="col-md-3">
                <label><strong>Technology</strong></label>
                <select name="technology" class="form-control">
                    <option  disabled selected value="">All Technologies</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ request('technology') == $course->id ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>
<!-- 
            <div class="col-md-3">
    <label class="form-label">Start Time</label>
    <input type="time"
           name="start_time"
           value="{{ request('start_time') }}"
           class="form-control">
</div>

<div class="col-md-3">
    <label class="form-label">End Time</label>
    <input type="time"
           name="end_time"
           value="{{ request('end_time') }}"
           class="form-control">
</div> -->

        <div class="col-md-3">
            <label class="form-label">Start Time</label>
            <input type="text"
                   id="filter_start_time"
                   name="start_time"
                   value="{{ request('start_time') }}"
                   class="form-control"
                   placeholder="hh:mm AM/PM"
                   autocomplete="off">
        </div>

        <div class="col-md-3">
            <label class="form-label">End Time</label>
            <input type="text"
                   id="filter_end_time"
                   name="end_time"
                   value="{{ request('end_time') }}"
                   class="form-control"
                   placeholder="hh:mm AM/PM"
                   autocomplete="off">
        </div>


            {{-- Status Filter --}}
            <div class="col-md-3">
                <label><strong>Status</strong></label>
                <select name="status" class="form-control">
                    <option  disabled selected value="">All Status</option>
                    <option value="active"    {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive"  {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            {{-- Mode Filter --}}
            <div class="col-md-3">
                <label><strong>Mode</strong></label>
                <select name="mode" class="form-control">
                    <option  disabled selected value="">All Modes</option>
                    <option value="online"  {{ request('mode') == 'online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('mode') == 'offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div>

        </div>

        {{-- Buttons Row --}}
        <div class="row mt-3">
            <div class="col-md-12 text-end">

                {{-- Search Button --}}
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>

                {{-- Reset Button --}}
                <a href="{{ route('batches.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>

            </div>
        </div>
    </form>
    {{-- ==================== END FILTERS ==================== --}}



    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="batchesTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Batch Name</th>
                    <th>Session</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Technology</th>
                    <th>Batch Assigned</th>
                    <th>Batch Mode</th>
                    <th>Batch Status</th>
                    <th>Total Student</th>
                    <!-- <th>Duration</th> -->
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($batches as $batch)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $batch->batch_name }}</td>
                    <td>{{ $batch->sessionData->session_name ?? '-' }}</td>
                    <td>{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</td>
                    <td>{{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }}</td>
                    <td>
                        @forelse($batch->courses ?? [] as $course)
                            <span class="badge bg-primary">{{ $course->course_name }}</span>
                        @empty
                            <span class="text-muted">-</span>
                        @endforelse
                    </td>


                    <td>{{ ucwords($batch->trainerData?->name ?? '-') }}</td>
                    <td>{{ $batch->batch_mode ?? '-' }}</td>
                    <td>{{ ucwords($batch->status) ?? '-' }}</td>

                    <!-- <td>
                        <span class="badge rounded-pill bg-primary view-students"
                              style="cursor:pointer;"
                              data-id="{{ $batch->id }}">
                            {{ $batch->students_count }}
                        </span>
                    </td> -->

                    <td>
                        <a href="{{ route('common_filtered_student', [
                            'batch_assign' => $batch->id
                        ]) }}"
                           class="text-decoration-none">
                            <span class="badge bg-success">
                                {{ $batch->students_count }}
                            </span>
                        </a>
                    </td>


                    <!-- <td>{{ $batch->durationData->name ?? '-' }}</td> -->

                    <td class="text-center">
                        <div class="mb-3" style="width:80px;">
                            <a href="{{ route('batches.edit', $batch->id) }}"
                               class="btn btn-sm"
                               data-bs-toggle="tooltip"
                               data-bs-placement="top"
                               title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('batches.destroy', $batch->id) }}"
                                  method="POST"
                                  style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="btn btn-sm"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Delete"
                                        onclick="return confirm('Are you sure?')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


{{-- ==================== STUDENT MODAL ==================== --}}
<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Student List</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered table-hover" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>College</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="studentList"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>
{{-- ==================== END MODAL ==================== --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

@endsection

@push('scripts')

<script>
    $(document).ready(function () {
        $('#batchesTable').DataTable({
            "pageLength": 50,
            "lengthMenu": [5, 10, 25, 50, 100]
        });
    });
</script>

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<script>
let studentsTableDT = null;

$(document).on('click', '.view-students', function() {
    let batchId = $(this).data('id');

    // Destroy old datatable safely
    if (studentsTableDT) {
        studentsTableDT.destroy();
        studentsTableDT = null;
    }

    // Clear table body completely
    $('#studentList').empty();

    // Show modal first (important)
    $('#studentsModal').modal('show');

    $.ajax({
        url: '/batches/' + batchId + '/students',
        type: 'GET',
        success: function(students) {

            let html = '';

            if (students.length === 0) {

                // 🔥 IMPORTANT: EXACTLY 1 TD WITH COLSPAN = 5
                html = `
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            No Students Found
                        </td>
                    </tr>
                `;

                $('#studentList').html(html);

                // ❌ DO NOT INITIALIZE DATATABLE WHEN EMPTY
                return;
            }

            // Build rows with EXACTLY 5 <td>
            $.each(students, function(i, s) {
                html += `
                    <tr>
                        <td>${i+1}</td>
                        <td>${s.student_name ?? '-'}</td>
                        <td>${s.email_id ?? '-'}</td>
                        <td>${s.college_data ? s.college_data.full_name : '-'}</td>
                        <td>${s.status ?? '-'}</td>
                    </tr>
                `;
            });

            $('#studentList').html(html);

            // 🔥 INIT DATATABLE AFTER SMALL DELAY (modal fully visible)
            setTimeout(function () {

                studentsTableDT = $('#studentsTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    autoWidth: false,
                    responsive: true,
                    destroy: true
                });

            }, 150);
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const filterTimeConfig = {
    enableTime: true,
    noCalendar: true,
    dateFormat: "h:i K",
    time_24hr: false,
    disableMobile: true,
    allowInput: false
};

flatpickr("#filter_start_time", filterTimeConfig);
flatpickr("#filter_end_time", filterTimeConfig);
</script>


@endpush
