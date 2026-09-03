@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    /* Tabs - no surrounding border/card */
    .batch-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
    }

    .batch-tabs .nav-link {
        border: none !important;
        border-bottom: 3px solid transparent !important;
        border-radius: 0 !important;
        background: transparent !important;
        color: #0d6efd;
        padding: 10px 22px;
    }

    .batch-tabs .nav-link:hover {
        border-bottom-color: #adb5bd !important;
    }

    .batch-tabs .nav-link.active {
        color: #212529;
        font-weight: 500;
        border-bottom: 3px solid #0d6efd !important;
    }

    .batch-action-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
</style>


<div class="container">

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="row mb-2">

        <div class="col-md-6">

            <h1 class="page_heading">
                Batches
            </h1>

        </div>


        <div class="col-md-6">

            <div class="d-flex justify-content-end">

                @can('batches.create')

                    <a href="{{ route('batches.create') }}"
                       class="btn mb-3"
                       style="background-color: #6b51df; color: #fff;">

                        Add Batch

                    </a>

                @endcan

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- ALERTS --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- TABS --}}
    {{-- ========================================================= --}}

    <ul class="nav nav-tabs batch-tabs">

        {{-- NORMAL --}}
        <li class="nav-item">

            <a
                href="{{ route('batches.index', array_merge(request()->except('page'), ['tab' => 'normal'])) }}"
                class="nav-link {{ ($tab ?? 'normal') === 'normal' ? 'active' : '' }}"
            >
                Normal
            </a>

        </li>


        {{-- CLOSED --}}
        <li class="nav-item">

            <a
                href="{{ route('batches.index', array_merge(request()->except('page'), ['tab' => 'closed'])) }}"
                class="nav-link {{ ($tab ?? 'normal') === 'closed' ? 'active' : '' }}"
            >
                Closed
            </a>

        </li>


        {{-- DELETED --}}
        <li class="nav-item">

            <a
                href="{{ route('batches.index', array_merge(request()->except('page'), ['tab' => 'deleted'])) }}"
                class="nav-link {{ ($tab ?? 'normal') === 'deleted' ? 'active' : '' }}"
            >
                Deleted
            </a>

        </li>

    </ul>


    {{-- ========================================================= --}}
    {{-- FILTERS --}}
    {{-- ========================================================= --}}

    <form method="GET"
          id="filterForm"
          class="mb-3">

        {{-- Preserve selected tab --}}
        <input type="hidden"
               name="tab"
               value="{{ $tab ?? 'normal' }}">


        <div class="row">

            {{-- ================================================= --}}
            {{-- TRAINER --}}
            {{-- ================================================= --}}

            <div class="col-md-3">

                <label>
                    <strong>Mentor</strong>
                </label>

                <select name="trainer"
                        class="form-control filterchange">

                    <option value="">
                        All Mentors
                    </option>

                    @foreach($trainers as $trainer)

                        <option
                            value="{{ $trainer->id }}"
                            {{ request('trainer') == $trainer->id ? 'selected' : '' }}
                        >
                            {{ ucwords($trainer?->name ?? 'Unknown') }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ================================================= --}}
            {{-- TECHNOLOGY --}}
            {{-- ================================================= --}}

            <div class="col-md-3">

                <label>
                    <strong>Technology</strong>
                </label>

                <select name="technology"
                        class="form-control filterchange">

                    <option value="">
                        All Technologies
                    </option>

                    @foreach($courses as $course)

                        <option
                            value="{{ $course->id }}"
                            {{ request('technology') == $course->id ? 'selected' : '' }}
                        >
                            {{ $course->course_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- ================================================= --}}
            {{-- START TIME --}}
            {{-- ================================================= --}}

            <div class="col-md-3">

                <label class="form-label">
                    Start Time
                </label>

                <input type="text"
                       id="filter_start_time"
                       name="start_time"
                       value="{{ request('start_time') }}"
                       class="form-control filterchangetext"
                       placeholder="hh:mm AM/PM"
                       autocomplete="off">

            </div>


            {{-- ================================================= --}}
            {{-- END TIME --}}
            {{-- ================================================= --}}

            <div class="col-md-3">

                <label class="form-label">
                    End Time
                </label>

                <input type="text"
                       id="filter_end_time"
                       name="end_time"
                       value="{{ request('end_time') }}"
                       class="form-control filterchangetext"
                       placeholder="hh:mm AM/PM"
                       autocomplete="off">

            </div>


            {{-- ================================================= --}}
            {{-- STATUS --}}
            {{-- Only applicable to Normal tab --}}
            {{-- ================================================= --}}

            @if(($tab ?? 'normal') === 'normal')

                <div class="col-md-3 mt-3">

                    <label>
                        <strong>Status</strong>
                    </label>

                    <select name="status"
                            class="form-control filterchange">

                        <option value="">
                            All Status
                        </option>

                        <option value="active"
                            {{ request('status') == 'active' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="inactive"
                            {{ request('status') == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>

                        <option value="completed"
                            {{ request('status') == 'completed' ? 'selected' : '' }}>
                            Completed
                        </option>

                        <option value="cancelled"
                            {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                            Cancelled
                        </option>

                    </select>

                </div>

            @endif


            {{-- ================================================= --}}
            {{-- MODE --}}
            {{-- ================================================= --}}

            <div class="col-md-3 mt-3">

                <label>
                    <strong>Mode</strong>
                </label>

                <select name="mode"
                        class="form-control filterchange">

                    <option value="">
                        All Modes
                    </option>

                    <option value="online"
                        {{ request('mode') == 'online' ? 'selected' : '' }}>
                        Online
                    </option>

                    <option value="offline"
                        {{ request('mode') == 'offline' ? 'selected' : '' }}>
                        Offline
                    </option>

                </select>

            </div>


            {{-- ================================================= --}}
            {{-- STUDENT SORT --}}
            {{-- ================================================= --}}

            <div class="col-md-3 mt-3">

                <label>
                    <strong>Total Students</strong>
                </label>

                <select name="student_sort"
                        class="form-control filterchange">

                    <option value="">
                        Sort By Students
                    </option>

                    <option value="low_to_high"
                        {{ request('student_sort') == 'low_to_high' ? 'selected' : '' }}>
                        Low to High
                    </option>

                    <option value="high_to_low"
                        {{ request('student_sort') == 'high_to_low' ? 'selected' : '' }}>
                        High to Low
                    </option>

                </select>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- RESET --}}
        {{-- ================================================= --}}

        <div class="row mt-3">

            <div class="col-md-12 text-end">

                <a
                    href="{{ route('batches.index', ['tab' => $tab ?? 'normal']) }}"
                    class="btn btn-secondary"
                >
                    <i class="fa fa-refresh"></i>
                    Reset
                </a>

            </div>

        </div>

    </form>


    {{-- ========================================================= --}}
    {{-- BATCH TABLE --}}
    {{-- ========================================================= --}}

    <div class="table-responsive">

        <table
            class="table table-bordered table-striped"
            id="batchesTable"
        >

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

                    <th>Actions</th>

                </tr>

            </thead>


            <tbody>

            @foreach($batches as $batch)

                <tr>

                    {{-- ================================================= --}}
                    {{-- ID --}}
                    {{-- ================================================= --}}

                    <td>
                        {{ $loop->iteration }}
                    </td>


                    {{-- ================================================= --}}
                    {{-- BATCH NAME --}}
                    {{-- ================================================= --}}

                    <td>
                        {{ $batch->batch_name }}
                    </td>


                    {{-- ================================================= --}}
                    {{-- SESSION --}}
                    {{-- ================================================= --}}

                    <td>
                        {{ $batch->sessionData->session_name ?? '-' }}
                    </td>


                    {{-- ================================================= --}}
                    {{-- START TIME --}}
                    {{-- ================================================= --}}

                    <td>

                        @if($batch->start_time)

                            {{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}

                        @else

                            -

                        @endif

                    </td>


                    {{-- ================================================= --}}
                    {{-- END TIME --}}
                    {{-- ================================================= --}}

                    <td>

                        @if($batch->end_time)

                            {{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }}

                        @else

                            -

                        @endif

                    </td>


                    {{-- ================================================= --}}
                    {{-- TECHNOLOGY --}}
                    {{-- ================================================= --}}

                    <td>

                        @forelse($batch->courses ?? [] as $course)

                            <span class="badge bg-primary">
                                {{ $course->course_name }}
                            </span>

                        @empty

                            <span class="text-muted">
                                -
                            </span>

                        @endforelse

                    </td>


                    {{-- ================================================= --}}
                    {{-- BATCH ASSIGNED --}}
                    {{-- ================================================= --}}

                    <td>
                        {{ ucwords($batch->trainerData?->name ?? '-') }}
                    </td>


                    {{-- ================================================= --}}
                    {{-- BATCH MODE --}}
                    {{-- ================================================= --}}

                    <td>
                        {{ ucfirst($batch->batch_mode ?? '-') }}
                    </td>


                    {{-- ================================================= --}}
                    {{-- STATUS --}}
                    {{-- ================================================= --}}

                    <td>

                        {{ ucwords($batch->status ?? '-') }}

                    </td>


                    {{-- ================================================= --}}
                    {{-- TOTAL STUDENT --}}
                    {{-- ================================================= --}}

                    <td>

                        @if(($tab ?? 'normal') === 'deleted')

                            {{-- Deleted batch:
                                 count is visible but NOT clickable --}}

                            <span class="badge bg-success">
                                {{ $batch->students_count }}
                            </span>

                        @else

                            {{-- Normal / Closed:
                                 count remains clickable using EXISTING route --}}

                            <a
                                href="{{ route('common_filtered_student', [
                                    'batch_assign' => $batch->id
                                ]) }}"
                                class="text-decoration-none"
                            >

                                <span class="badge bg-success">
                                    {{ $batch->students_count }}
                                </span>

                            </a>

                        @endif

                    </td>


                    {{-- ================================================= --}}
                    {{-- ACTIONS --}}
                    {{-- ================================================= --}}

                    <td class="text-center">

                        <div class="d-flex justify-content-center align-items-center gap-1">


                            {{-- ================================================= --}}
                            {{-- NORMAL TAB --}}
                            {{-- ================================================= --}}

                            @if(($tab ?? 'normal') === 'normal')


                                {{-- EDIT --}}
                                @can('batches.edit')

                                    <a
                                        href="{{ route('batches.edit', $batch->id) }}"
                                        class="btn btn-sm batch-action-btn"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Edit"
                                    >

                                        <i class="fa fa-edit"></i>

                                    </a>

                                @endcan


                                {{-- CLOSE --}}
                                @can('batches.edit')

                                    <form
                                        action="{{ route('batches.close', $batch->id) }}"
                                        method="POST"
                                        class="batch-action-form d-inline"
                                        data-title="Close Batch?"
                                        data-text="Are you sure you want to close this batch?"
                                        data-confirm="Yes, Close It"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-sm batch-action-btn"
                                            data-bs-toggle="tooltip"
                                            title="Close Batch"
                                        >
                                            <i class="fa fa-lock"></i>
                                        </button>
                                    </form>

                                @endcan


                                {{-- DELETE --}}
                                @can('batches.delete')

                                    <form
                                        action="{{ route('batches.destroy', $batch->id) }}"
                                        method="POST"
                                        class="batch-action-form d-inline"
                                        data-title="Delete Batch?"
                                        data-text="Are you sure you want to delete this batch?"
                                        data-confirm="Yes, Delete It"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm batch-action-btn"
                                            data-bs-toggle="tooltip"
                                            title="Delete"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </form>

                                @endcan


                            @endif


                            {{-- ================================================= --}}
                            {{-- CLOSED TAB --}}
                            {{-- ================================================= --}}

                            @if(($tab ?? 'normal') === 'closed')


                                {{-- EDIT --}}
                                @can('batches.edit')

                                    <a
                                        href="{{ route('batches.edit', $batch->id) }}"
                                        class="btn btn-sm batch-action-btn"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Edit"
                                    >

                                        <i class="fa fa-edit"></i>

                                    </a>

                                @endcan


                                {{-- REOPEN --}}
                                @can('batches.edit')

                                    <form
                                        action="{{ route('batches.reopen', $batch->id) }}"
                                        method="POST"
                                        class="batch-action-form d-inline"
                                        data-title="Reopen Batch?"
                                        data-text="Are you sure you want to reopen this batch?"
                                        data-confirm="Yes, Reopen It"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-sm batch-action-btn"
                                            data-bs-toggle="tooltip"
                                            title="Reopen Batch"
                                        >
                                            <i class="fa fa-unlock"></i>
                                        </button>
                                    </form>

                                @endcan


                                {{-- DELETE --}}
                                @can('batches.delete')

                                    <form
                                        action="{{ route('batches.destroy', $batch->id) }}"
                                        method="POST"
                                        class="batch-action-form d-inline"
                                        data-title="Delete Batch?"
                                        data-text="Are you sure you want to delete this batch?"
                                        data-confirm="Yes, Delete It"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="btn btn-sm batch-action-btn"
                                            data-bs-toggle="tooltip"
                                            title="Delete"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </form>

                                @endcan


                            @endif


                            {{-- ================================================= --}}
                            {{-- DELETED TAB --}}
                            {{-- ================================================= --}}

                            @if(($tab ?? 'normal') === 'deleted')


                                {{-- RESTORE --}}
                                @can('batches.delete')

                                    <form
                                        action="{{ route('batches.restore', $batch->id) }}"
                                        method="POST"
                                        class="batch-action-form d-inline"
                                        data-title="Restore Batch?"
                                        data-text="Are you sure you want to restore this batch?"
                                        data-confirm="Yes, Restore It"
                                    >
                                        @csrf

                                        <button
                                            type="submit"
                                            class="btn btn-sm batch-action-btn"
                                            data-bs-toggle="tooltip"
                                            title="Restore Batch"
                                        >
                                            <i class="fa fa-undo"></i>
                                        </button>
                                    </form>

                                @endcan


                            @endif

                        </div>

                    </td>

                </tr>

            @endforeach

            </tbody>

        </table>

    </div>

</div>


{{-- ============================================================= --}}
{{-- STUDENT MODAL --}}
{{-- ============================================================= --}}

<div class="modal fade"
     id="studentsModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <div class="modal-header bg-primary text-white">

                <h5 class="modal-title">
                    Student List
                </h5>

                <button
                    type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <table
                    class="table table-bordered table-hover"
                    id="studentsTable"
                >

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


<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
>

@endsection


@push('scripts')


{{-- ============================================================= --}}
{{-- DATATABLE --}}
{{-- ============================================================= --}}

<script>

$(document).ready(function () {

    $('#batchesTable').DataTable({

        pageLength: 50,

        lengthMenu: [5, 10, 25, 50, 100]

    });

});

</script>


{{-- ============================================================= --}}
{{-- TOOLTIP --}}
{{-- ============================================================= --}}

<script>

var tooltipTriggerList =
    [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );

var tooltipList =
    tooltipTriggerList.map(function (tooltipTriggerEl) {

        return new bootstrap.Tooltip(tooltipTriggerEl);

    });

</script>


{{-- ============================================================= --}}
{{-- STUDENT MODAL --}}
{{-- ============================================================= --}}

<script>

let studentsTableDT = null;


$(document).on('click', '.view-students', function () {

    let batchId = $(this).data('id');


    if (studentsTableDT) {

        studentsTableDT.destroy();

        studentsTableDT = null;

    }


    $('#studentList').empty();


    $('#studentsModal').modal('show');


    $.ajax({

        url: '/batches/' + batchId + '/students',

        type: 'GET',

        success: function (students) {

            let html = '';


            if (students.length === 0) {

                html = `
                    <tr>
                        <td colspan="5"
                            class="text-center text-danger">

                            No Students Found

                        </td>
                    </tr>
                `;


                $('#studentList').html(html);

                return;

            }


            $.each(students, function (i, s) {

                html += `
                    <tr>

                        <td>${i + 1}</td>

                        <td>
                            ${s.student_name ?? '-'}
                        </td>

                        <td>
                            ${s.email_id ?? '-'}
                        </td>

                        <td>
                            ${s.college_data
                                ? s.college_data.full_name
                                : '-'}
                        </td>

                        <td>
                            ${s.status ?? '-'}
                        </td>

                    </tr>
                `;

            });


            $('#studentList').html(html);


            setTimeout(function () {

                studentsTableDT =
                    $('#studentsTable').DataTable({

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


{{-- ============================================================= --}}
{{-- FLATPICKR --}}
{{-- ============================================================= --}}

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


flatpickr(
    "#filter_start_time",
    filterTimeConfig
);


flatpickr(
    "#filter_end_time",
    filterTimeConfig
);

</script>


{{-- ============================================================= --}}
{{-- AUTO FILTER --}}
{{-- ============================================================= --}}

<script>

$(document).ready(function () {

    let timer;


    $('.filterchange').on('change', function () {

        $('#filterForm').submit();

    });


    $('.filterchangetext').on('input', function () {

        clearTimeout(timer);


        timer = setTimeout(function () {

            $('#filterForm').submit();

        }, 700);

    });

});

$(document).on('submit', '.batch-action-form', function (e) {

    e.preventDefault();

    const form = this;

    const title = $(form).data('title') || 'Are you sure?';

    const text = $(form).data('text') || '';

    const confirmText = $(form).data('confirm') || 'Yes, Continue';


    Swal.fire({

        title: title,

        text: text,

        icon: 'warning',

        showCancelButton: true,

        confirmButtonText: confirmText,

        cancelButtonText: 'Cancel',

        reverseButtons: true

    }).then((result) => {

        if (result.isConfirmed) {

            form.submit();

        }

    });

});
</script>


@endpush