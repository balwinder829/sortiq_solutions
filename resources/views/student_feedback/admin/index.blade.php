@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    .feedback-message {
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .status-badge {
        min-width: 75px;
        display: inline-block;
        text-align: center;
    }

    .feedback-actions {
        white-space: nowrap;
    }

    .feedback-actions .btn {
        color: #000;
        padding: 3px 6px;
    }

    .feedback-actions .btn:hover {
        color: #000;
    }

    .feedback-actions {
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .feedback-actions form {
        display: inline-flex;
        margin: 0;
    }

    .feedback-actions .btn {
        color: #000;
        padding: 3px 6px;
        border: none;
        background: transparent;
    }

    .feedback-actions .btn:hover {
        color: #000;
        background: transparent;
    }
</style>

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-3 align-items-end">

        <div class="col-md-6">
            <h1 class="page_heading">Student Feedback</h1>
        </div>

    </div>


    {{-- FILTERS --}}
    <div class="row mb-4 align-items-end">

        <div class="col-md-2">
            <h1 class="page_heading">Filters</h1>
        </div>

        <div class="col-md-10">

            <form method="GET"
                  id="filterForm"
                  class="row g-2 align-items-end">

                {{-- STATUS --}}
                <div class="col-md-3">

                    <select name="status"
                            id="filterstatus"
                            class="form-control filterchange">

                        <option value="">
                            -- All Status --
                        </option>

                        <option value="new"
                            {{ request('status') == 'new' ? 'selected' : '' }}>
                            New
                        </option>

                        <option value="reviewed"
                            {{ request('status') == 'reviewed' ? 'selected' : '' }}>
                            Reviewed
                        </option>

                        <option value="resolved"
                            {{ request('status') == 'resolved' ? 'selected' : '' }}>
                            Resolved
                        </option>

                    </select>

                </div>


                {{-- COURSE --}}
                <div class="col-md-3">

                    <input type="text"
                           name="course"
                           id="filtercourse"
                           class="form-control"
                           value="{{ request('course') }}"
                           placeholder="Course">

                </div>


                {{-- BATCH --}}
                <div class="col-md-3">

                    <input type="text"
                           name="batch"
                           id="filterbatch"
                           class="form-control"
                           value="{{ request('batch') }}"
                           placeholder="Batch">

                </div>


                {{-- RESET --}}
                <div class="col-md-2">

                    <a href="{{ route('admin.student_feedback.index') }}"
                       class="btn btn-secondary">

                        Reset

                    </a>

                </div>

            </form>

        </div>

    </div>

    <div class="col-md-8 mb-4">
    <p class="mb-1 fw-bold">Student Feedback URL</p>

    <div class="input-group">

        {{-- CLICK URL TO OPEN IN NEW TAB --}}
        <a href="{{ route('student-feedback.create') }}"
           target="_blank"
           id="feedbackUrl"
           class="form-control text-primary text-decoration-none"
           style="cursor: pointer;">
            {{ route('student-feedback.create') }}
        </a>

        {{-- COPY BUTTON --}}
        <button class="btn btn-outline-secondary"
                type="button"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                title="Copy Student Feedback URL"
                onclick="copyFeedbackUrl()">

            <i class="fa fa-copy"></i>

        </button>

    </div>

    <small id="feedbackCopyMessage"
           class="text-success d-none">
        Copied to clipboard!
    </small>
</div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- TABLE --}}
    <div class="table-responsive">

        <table id="feedback-table"
               class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Name</th>

                    <th>Mobile</th>

                    <th>Email</th>

                    <th>Course</th>

                    <th>Batch</th>

                    <th>Message</th>

                    <th>Status</th>

                    <th>Date</th>

                    <th>Actions</th>

                </tr>

            </thead>

            <tbody></tbody>

        </table>

    </div>

</div>


@endsection


@push('scripts')

<script>

$(document).ready(function () {

    var table = $('#feedback-table').DataTable({

        processing: true,

        serverSide: true,

        ajax: {

            url: "{{ route('admin.student_feedback.data') }}",

            data: function (d) {

                d.status = $('#filterstatus').val();

                d.course = $('#filtercourse').val();

                d.batch = $('#filterbatch').val();

            }

        },

        columns: [

            { data: 0 },

            { data: 1 },

            { data: 2 },

            { data: 3 },

            { data: 4 },

            { data: 5 },

            {
                data: 6,
                orderable: false,
                searchable: true
            },

            {
                data: 7,
                orderable: false,
                searchable: false
            },

            {
                data: 8,
                orderable: true,
                searchable: false
            },

            {
                data: 9,
                orderable: false,
                searchable: false
            }

        ],

        pageLength: 50,

        lengthMenu: [5, 10, 25, 50, 100]

    });


    /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

    $('#filterstatus').change(function () {

        table.ajax.reload();

    });


    /*
    |--------------------------------------------------------------------------
    | Course / Batch Filter
    |--------------------------------------------------------------------------
    */

    $('#filtercourse, #filterbatch').on('keyup', function () {

        clearTimeout(window.feedbackFilterTimer);

        window.feedbackFilterTimer = setTimeout(function () {

            table.ajax.reload();

        }, 400);

    });


    /*
    |--------------------------------------------------------------------------
    | SweetAlert Actions
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.feedback-action-form',
        function (e) {

            e.preventDefault();

            const form = this;

            const title =
                $(form).data('title') ||
                'Are you sure?';

            const text =
                $(form).data('text') ||
                '';

            const confirmText =
                $(form).data('confirm') ||
                'Yes, Continue';


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

        }
    );

});

</script>
<script>
function copyFeedbackUrl() {

    const url = document
        .getElementById('feedbackUrl')
        .textContent
        .trim();

    navigator.clipboard.writeText(url).then(function () {

        const msg = document.getElementById('feedbackCopyMessage');

        msg.classList.remove('d-none');

        setTimeout(() => {
            msg.classList.add('d-none');
        }, 2000);

    });

}
</script>

@endpush