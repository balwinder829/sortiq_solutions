@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    .batch-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        margin-right: 4px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
    }

    .batch-circle:hover {
        opacity: 0.85;
    }
</style>


<div class="container">

    {{-- =====================================================
        PAGE HEADER
    ====================================================== --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-7">
            <h1 class="page_heading">Mentors</h1>
        </div>

        <div class="col-md-5">
            <div class="d-flex justify-content-end gap-2">

                <a href="{{ route('trainers.responsibilities_letter') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df; color:#fff;">
                    Responsibility Letter
                </a>

                <a href="{{ route('trainers.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df; color:#fff;">
                    Add Mentor
                </a>

            </div>
        </div>

    </div>


    {{-- =====================================================
        STATUS TABS
    ====================================================== --}}
    <div class="mb-3">

        <ul class="nav nav-tabs">

            <li class="nav-item">
                <a href="javascript:void(0)"
                   class="nav-link trainer-status-tab active"
                   data-status="active">
                    Active
                </a>
            </li>

            <li class="nav-item">
                <a href="javascript:void(0)"
                   class="nav-link trainer-status-tab"
                   data-status="inactive">
                    Inactive
                </a>
            </li>

        </ul>

    </div>


    {{-- =====================================================
        FILTERS
    ====================================================== --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-1">
            <h1 class="page_heading">Filters</h1>
        </div>

        {{-- COURSE --}}
        <div class="col-md-3">

            <select id="filtercourse"
                    class="form-control">

                <option value="">
                    All Courses
                </option>

                @foreach($courses as $course)
                    <option value="{{ $course->id }}">
                        {{ $course->course_name }}
                    </option>
                @endforeach

            </select>

        </div>

        {{-- RESET --}}
        <div class="col-md-2">

            <a href="{{ route('trainers.index') }}"
               class="btn btn-secondary w-100">
                Reset
            </a>

        </div>

    </div>


    {{-- =====================================================
        SUCCESS MESSAGE
    ====================================================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =====================================================
        ERROR MESSAGE
    ====================================================== --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- =====================================================
        BULK ACTION
    ====================================================== --}}
    <div class="mb-3">

        <button type="button"
                id="bulkStatusBtn"
                class="btn btn-success">

            
            Make Inactive

        </button>

        <span id="selectedTrainerCount"
              class="ms-2 text-muted">
        </span>

    </div>


    {{-- =====================================================
        TRAINER TABLE
    ====================================================== --}}
    <table id="trainers-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th width="30">
                    <input type="checkbox"
                           id="checkAll">
                </th>

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

                <th>Today Pending Batches</th>

                <th>Actions</th>

            </tr>

        </thead>

        <tbody>
            {{-- Server-side DataTable --}}
        </tbody>

    </table>

</div>


{{-- =====================================================
    BATCH MODAL
===================================================== --}}
<div class="modal fade"
     id="batchModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">

                    Batches -
                    <span id="trainerName"></span>

                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body"
                 id="batchModalContent">

                Loading...

            </div>

        </div>

    </div>

</div>


{{-- =====================================================
    BULK STATUS FORM
===================================================== --}}
<form method="POST"
      action="{{ route('trainers.bulkStatus') }}"
      id="bulkTrainerStatusForm">

    @csrf

    <input type="hidden"
           name="ids"
           id="bulkTrainerIds">

    <input type="hidden"
           name="status"
           id="bulkTrainerStatus">

</form>


@endsection


@section('scripts')

@push('scripts')

<script>

    // =====================================================
    // CURRENT STATUS TAB
    // =====================================================

    let currentStatus = 'active';


    // =====================================================
    // SELECTED TRAINERS
    //
    // IDs remain selected during pagination/search/redraw.
    // =====================================================

    let selectedTrainers = {};


    $(document).ready(function () {


        // =================================================
        // DATATABLE
        // =================================================

        let table = $('#trainers-table').DataTable({

            processing: true,

            serverSide: true,

            scrollX: true,

            ajax: {

                url: "{{ route('trainers.data') }}",

                type: 'GET',

                data: function (d) {

                    // STATUS FROM TAB
                    d.status = currentStatus;

                    // EXISTING COURSE FILTER
                    d.course = $('#filtercourse').val();

                }

            },


            columns: [

                // =================================================
                // CHECKBOX
                // =================================================

                {
                    data: 0,
                    orderable: false,
                    searchable: false
                },


                // =================================================
                // EXISTING COLUMNS
                // =================================================

                {
                    data: 1,
                    name: 'id'
                },

                {
                    data: 2,
                    name: 'username'
                },

                {
                    data: 3,
                    name: 'name'
                },

                {
                    data: 4,
                    name: 'gender'
                },

                {
                    data: 5,
                    name: 'phone'
                },

                {
                    data: 6,
                    name: 'email'
                },

                {
                    data: 7,
                    name: 'technology'
                },

                {
                    data: 8,
                    name: 'session_batches_count',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 9,
                    name: 'online_batches_count',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 10,
                    name: 'offline_batches_count',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 11,
                    name: 'today_remaining_batches_count',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 12,
                    orderable: false,
                    searchable: false
                }

            ],


            pageLength: 50,

            lengthMenu: [
                [5, 10, 25, 50, 100],
                [5, 10, 25, 50, 100]
            ]

        });


        // =====================================================
        // STATUS TAB CLICK
        // =====================================================

        $(document).on(
            'click',
            '.trainer-status-tab',
            function (e) {

                e.preventDefault();

                $('.trainer-status-tab')
                    .removeClass('active');

                $(this)
                    .addClass('active');

                currentStatus =
                    $(this).data('status');


                // Change bulk button according to tab
                updateBulkStatusButton();


                // Reset current-page Select All
                $('#checkAll')
                    .prop('checked', false);


                table.ajax.reload(null, true);

            }
        );


        // =====================================================
        // COURSE FILTER
        // =====================================================

        $('#filtercourse').on(
            'change',
            function () {

                $('#checkAll')
                    .prop('checked', false);

                table.ajax.reload();

            }
        );


        // =====================================================
        // INDIVIDUAL TRAINER CHECKBOX
        // =====================================================

        $(document).on(
            'change',
            '.record_checked',
            function () {

                let id = $(this).val();

                if ($(this).is(':checked')) {

                    selectedTrainers[id] = true;

                } else {

                    delete selectedTrainers[id];

                }

                updateBulkButtons();

                updateCheckAll();

            }
        );


        // =====================================================
        // SELECT ALL CURRENT PAGE
        //
        // Only the currently displayed page is selected.
        // =====================================================

        $('#checkAll').on(
            'change',
            function () {

                let checked =
                    $(this).is(':checked');


                $('#trainers-table tbody')
                    .find('.record_checked')
                    .each(function () {

                        let id =
                            $(this).val();


                        $(this)
                            .prop('checked', checked);


                        if (checked) {

                            selectedTrainers[id] = true;

                        } else {

                            delete selectedTrainers[id];

                        }

                    });


                updateBulkButtons();

            }
        );


        // =====================================================
        // RESTORE SELECTED CHECKBOXES AFTER DATATABLE DRAW
        //
        // Selection survives:
        // - pagination
        // - search
        // - filtering
        // - sorting
        // - AJAX redraw
        // =====================================================

        $('#trainers-table').on(
            'draw.dt',
            function () {

                $('#trainers-table tbody')
                    .find('.record_checked')
                    .each(function () {

                        let id =
                            $(this).val();


                        $(this).prop(
                            'checked',
                            !!selectedTrainers[id]
                        );

                    });


                updateCheckAll();

                updateBulkButtons();

            }
        );


        // =====================================================
        // BULK BUTTON
        //
        // Button is ALWAYS visible.
        // Text changes according to current tab.
        // =====================================================

        function updateBulkStatusButton() {

            if (currentStatus === 'active') {

                $('#bulkStatusBtn')
                    .html(
                        'Make Inactive'
                    );
                    // .removeClass('btn-success')
                    // .addClass('btn-success');

            } else {

                $('#bulkStatusBtn')
                    .html(
                        '</i> Make Active'
                    )
                    .removeClass('btn-warning')
                    .addClass('btn-success');

            }

        }


        // Initial button state
        updateBulkStatusButton();


        // =====================================================
        // SELECTED COUNT
        //
        // Button itself is never hidden.
        // =====================================================

        function updateBulkButtons() {

            let count =
                Object.keys(selectedTrainers).length;


            $('#selectedTrainerCount').text(

                count > 0
                    ? count + ' mentor(s) selected'
                    : ''

            );


            // Keep button visible
            updateBulkStatusButton();

        }


        // =====================================================
        // UPDATE SELECT ALL CHECKBOX
        //
        // Only checks whether ALL visible rows are selected.
        // =====================================================

        function updateCheckAll() {

            let checkboxes =
                $('#trainers-table tbody')
                    .find('.record_checked');


            if (checkboxes.length === 0) {

                $('#checkAll')
                    .prop('checked', false);

                return;

            }


            let allChecked = true;


            checkboxes.each(function () {

                if (!$(this).is(':checked')) {

                    allChecked = false;

                }

            });


            $('#checkAll')
                .prop('checked', allChecked);

        }


        // =====================================================
        // BULK STATUS
        //
        // Active tab    -> Make Inactive
        // Inactive tab  -> Make Active
        // =====================================================

        function bulkStatus(status) {

            let ids =
                Object.keys(selectedTrainers);


            // ---------------------------------------------
            // NO SELECTION
            // ---------------------------------------------

            if (ids.length === 0) {

                Swal.fire({

                    icon: 'warning',

                    title: 'No Record Selected',

                    text: 'Please select at least 1 record.'

                });

                return;

            }


            // ---------------------------------------------
            // STATUS TEXT
            // ---------------------------------------------

            let statusText =
                status === 'active'
                    ? 'Active'
                    : 'Inactive';


            // ---------------------------------------------
            // CONFIRMATION
            // ---------------------------------------------

            Swal.fire({

                title: 'Are you sure?',

                text:
                    'Set ' +
                    ids.length +
                    ' selected mentor(s) to ' +
                    statusText +
                    '?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Make ' + statusText,

                cancelButtonText:
                    'Cancel'

            }).then(function (result) {

                if (!result.isConfirmed) {

                    return;

                }


                $('#bulkTrainerIds')
                    .val(ids.join(','));


                $('#bulkTrainerStatus')
                    .val(status);


                $('#bulkTrainerStatusForm')
                    .submit();

            });

        }


        // =====================================================
        // ONE BULK BUTTON
        //
        // Active    -> inactive
        // Inactive  -> active
        // =====================================================

        $('#bulkStatusBtn').on(
            'click',
            function () {

                let newStatus =
                    currentStatus === 'active'
                        ? 'inactive'
                        : 'active';


                bulkStatus(newStatus);

            }
        );


        // =====================================================
        // BATCH MODAL
        // EXISTING FUNCTIONALITY
        // =====================================================

        $(document).on(
            'click',
            '.batch-link',
            function () {

                let trainerId =
                    $(this).data('trainer-id');

                let type =
                    $(this).data('type');

                let trainerName =
                    $(this).data('trainer-name');


                $('#trainerName')
                    .text(trainerName || '');


                $('#batchModalContent')
                    .html('Loading...');


                $('#batchModal')
                    .modal('show');


                $.ajax({

                    url:
                        '/trainers/' +
                        trainerId +
                        '/batches-ajax',

                    type: 'GET',

                    data: {
                        type: type
                    },

                    success: function (response) {

                        $('#batchModalContent')
                            .html(response);

                    },

                    error: function () {

                        $('#batchModalContent')
                            .html(
                                '<div class="alert alert-danger">' +
                                'Unable to load batches.' +
                                '</div>'
                            );

                    }

                });

            }
        );

    });


    // =====================================================
    // COPY LOGIN URL
    // EXISTING FUNCTIONALITY
    // =====================================================

    function copyLoginUrl(url) {

        navigator.clipboard.writeText(url)

            .then(function () {

                Swal.fire({

                    icon: 'success',

                    title: 'Copied!',

                    text: 'Login URL copied.',

                    timer: 1500,

                    showConfirmButton: false

                });

            })

            .catch(function () {

                let temp =
                    $('<input>');


                $('body').append(temp);


                temp.val(url)
                    .select();


                document.execCommand('copy');


                temp.remove();


                Swal.fire({

                    icon: 'success',

                    title: 'Copied!',

                    timer: 1500,

                    showConfirmButton: false

                });

            });

    }


    // =====================================================
    // EXISTING TRAINER ACTION CONFIRMATION
    // =====================================================

    $(document).on(
        'submit',
        '.trainer-action-form',
        function (e) {

            let form = this;


            let message =
                $(form).data('swal-confirm') ||
                'Are you sure?';


            e.preventDefault();


            Swal.fire({

                title: 'Are you sure?',

                text: message,

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel'

            }).then(function (result) {

                if (result.isConfirmed) {

                    form.submit();

                }

            });

        }
    );

</script>

@endpush

@endsection