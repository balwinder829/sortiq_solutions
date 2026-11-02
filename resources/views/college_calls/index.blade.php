@extends('layouts.app')

@section('content')

<style>

    .call-status-tab {
        cursor: pointer;
    }

</style>


<div class="container">

    <!-- ========================================================= -->
    <!-- HEADER -->
    <!-- ========================================================= -->

    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h3 class="page_heading mb-0">
                Colleges Calling Panel
            </h3>

        </div>


        <div class="col-md-6 text-end">

            <button
                id="editCallStatus"
                type="button"
                class="btn btn-success">

                Edit Call Status

            </button>


            <button
                id="callSelected"
                type="button"
                class="btn btn-primary">

                Start Calling (Selected)

            </button>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- SUCCESS MESSAGE -->
    <!-- ========================================================= -->

    @if(session('success'))

        <div
            class="alert alert-success alert-dismissible fade show"
            role="alert">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- ERROR MESSAGE -->
    <!-- ========================================================= -->

    @if(session('error'))

        <div
            class="alert alert-danger alert-dismissible fade show"
            role="alert">

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- CALL STATUS TABS -->
    <!-- ========================================================= -->

    <div class="mb-3">

        <ul class="nav nav-tabs">

            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link call-status-tab"
                    data-status="called">

                    Called

                </a>

            </li>


            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link call-status-tab active"
                    data-status="not_called">

                    Not Called

                </a>

            </li>

        </ul>

    </div>


    <!-- ========================================================= -->
    <!-- FILTERS -->
    <!-- ========================================================= -->

    <div class="col-md-12 mb-3">

        <div class="row g-2 align-items-end">

            <!-- STATE -->

            <div class="col-md-2">

                <select
                    id="filter-state"
                    class="form-select">

                    <option value="">
                        State
                    </option>

                    @foreach($states as $state)

                        <option value="{{ $state->id }}">
                            {{ $state->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            <!-- DISTRICT -->

            <div class="col-md-2">

                <select
                    id="filter-district"
                    class="form-select">

                    <option value="">
                        District
                    </option>

                </select>

            </div>


            <!-- COLLEGE -->

            <div class="col-md-3">

                <select
                    id="filter-college"
                    class="form-select select2">

                    <option value="">
                        College
                    </option>

                </select>

            </div>


            <!-- DATE FROM -->

            <div class="col-md-2">

                <input
                    type="date"
                    id="date_from"
                    class="form-control">

            </div>


            <!-- DATE TO -->

            <div class="col-md-2">

                <input
                    type="date"
                    id="date_to"
                    class="form-control">

            </div>


            <!-- RANGE -->

            <div class="col-md-2">

                <select
                    id="filter-range"
                    class="form-select">

                    <option value="">
                        Range
                    </option>

                    <option value="today">
                        Today
                    </option>

                    <option value="yesterday">
                        Yesterday
                    </option>

                    <option value="current_week_past">
                        Current Week (Till Today)
                    </option>

                    <option value="last_week">
                        Last Week
                    </option>

                    <option value="last_month">
                        Last Month
                    </option>

                    <option value="last_30_days">
                        Last 30 Days
                    </option>

                </select>

            </div>


            <!-- RESET -->

            <div class="col-md-2">

                <a
                    href="{{ route('admin.college-calls.index') }}"
                    class="btn btn-secondary w-100">

                    Reset

                </a>

            </div>

        </div>

    </div>


    <!-- ========================================================= -->
    <!-- TABLE -->
    <!-- ========================================================= -->

    <div class="table-responsive">

        <table
            id="callTable"
            class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th width="30">

                        <input
                            type="checkbox"
                            id="checkAll">

                    </th>

                    <th>
                        ID
                    </th>

                    <th>
                        College
                    </th>

                    <th>
                        Call Count
                    </th>

                    <th>
                        Called To
                    </th>

                    <th>
                        Status
                    </th>

                    <th width="200">
                        Action
                    </th>

                </tr>

            </thead>

        </table>

    </div>

</div>


<!-- ============================================================= -->
<!-- CALL STATUS MODAL -->
<!-- ============================================================= -->

<div
    class="modal fade"
    id="callStatusModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Call Status
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">

                        Call Done
                        <span class="text-danger">*</span>

                    </label>


                    <select
                        id="callDoneValue"
                        class="form-select"
                        required>

                        <option value="">
                            Select Status
                        </option>

                        <option value="1">
                            Called
                        </option>

                        <option value="0">
                            Not Called
                        </option>

                    </select>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>


                <button
                    type="button"
                    id="saveCallStatus"
                    class="btn btn-success">

                    Update

                </button>

            </div>

        </div>

    </div>

</div>


@endsection


@push('scripts')

<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | CURRENT CALL STATUS TAB
    |--------------------------------------------------------------------------
    */

    let currentCallStatus = 'not_called';


    /*
    |--------------------------------------------------------------------------
    | SELECTED COLLEGES
    |--------------------------------------------------------------------------
    */

    let selectedIds = new Set();


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    let table = $('#callTable').DataTable({

        processing: true,

        serverSide: true,

        ajax: {

            url: "{{ route('admin.college-calls.index') }}",

            data: function (d) {


                /*
                |--------------------------------------------------------------------------
                | CALL STATUS TAB
                |--------------------------------------------------------------------------
                */

                d.call_status =
                    currentCallStatus;


                /*
                |--------------------------------------------------------------------------
                | OTHER FILTERS
                |--------------------------------------------------------------------------
                */

                d.state_id =
                    $('#filter-state').val();

                d.district_id =
                    $('#filter-district').val();

                d.college_id =
                    $('#filter-college').val();

                d.date_from =
                    $('#date_from').val();

                d.date_to =
                    $('#date_to').val();

                d.range =
                    $('#filter-range').val();

            }

        },


        columns: [

            {
                orderable: false,
                searchable: false
            },

            {
                orderable: true
            },

            {
                orderable: true
            },

            {
                orderable: true
            },

            {
                orderable: true
            },

            {
                orderable: true
            },

            {
                orderable: false,
                searchable: false
            }

        ],


        order: [
            [1, 'desc']
        ],


        columnDefs: [

            {
                targets: 0,
                width: "30px",
                orderable: false,
                searchable: false
            },

            {
                targets: 6,
                width: "200px",
                orderable: false,
                searchable: false
            }

        ],


        /*
        |--------------------------------------------------------------------------
        | DRAW CALLBACK
        |--------------------------------------------------------------------------
        */

        drawCallback: function () {


            /*
            |--------------------------------------------------------------------------
            | RESTORE CHECKED STATE
            |--------------------------------------------------------------------------
            */

            $('.record_checkbox').each(function () {

                let id =
                    String($(this).val());


                $(this).prop(
                    'checked',
                    selectedIds.has(id)
                );

            });


            /*
            |--------------------------------------------------------------------------
            | SELECT ALL STATE
            |--------------------------------------------------------------------------
            */

            let checkboxes =
                $('.record_checkbox');


            let allChecked =
                checkboxes.length > 0;


            checkboxes.each(function () {

                if (
                    !selectedIds.has(
                        String($(this).val())
                    )
                ) {

                    allChecked = false;

                }

            });


            $('#checkAll').prop(
                'checked',
                allChecked
            );

        }

    });


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL SELECT
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.record_checkbox',
        function () {

            let id =
                String($(this).val());


            if ($(this).is(':checked')) {

                selectedIds.add(id);

            } else {

                selectedIds.delete(id);

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL CURRENT PAGE
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on(
        'change',
        function () {

            let checked =
                this.checked;


            $('.record_checkbox').each(
                function () {

                    let id =
                        String($(this).val());


                    if (checked) {

                        selectedIds.add(id);

                    } else {

                        selectedIds.delete(id);

                    }


                    $(this).prop(
                        'checked',
                        checked
                    );

                }
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CALL STATUS TABS
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.call-status-tab',
        function () {

            let status =
                $(this).data('status');


            if (
                currentCallStatus === status
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Change tab
            |--------------------------------------------------------------------------
            */

            currentCallStatus =
                status;


            /*
            |--------------------------------------------------------------------------
            | Clear previous selection
            |--------------------------------------------------------------------------
            */

            selectedIds.clear();


            $('#checkAll').prop(
                'checked',
                false
            );


            /*
            |--------------------------------------------------------------------------
            | Active tab
            |--------------------------------------------------------------------------
            */

            $('.call-status-tab')
                .removeClass('active');


            $(this)
                .addClass('active');


            /*
            |--------------------------------------------------------------------------
            | Reload table
            |--------------------------------------------------------------------------
            */

            table.ajax.reload(
                null,
                true
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | BULK CALL
    |--------------------------------------------------------------------------
    */

    $('#callSelected').click(
        function () {

            if (
                selectedIds.size === 0
            ) {

                Swal.fire({

                    icon: 'warning',

                    title: 'No Selection',

                    text:
                        'Please select at least one college',

                    showConfirmButton: true

                });

                return;
            }


            Swal.fire({

                title: 'Proceed?',

                text:
                    'Add call record for selected colleges?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-calls.storeSelection') }}",

                    type: 'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        ids:
                            Array.from(selectedIds)

                    },

                    success: function (res) {

                        if (res.status) {

                            window.location.href =
                                "{{ route('admin.college-calls.create') }}";

                        }

                    },

                    error: function (xhr) {

                        let message =
                            'Something went wrong.';


                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {

                            message =
                                xhr.responseJSON.message;

                        }


                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: message

                        });

                    }

                });

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RETRY PER COLLEGE
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.retry-single',
        function () {

            let id =
                $(this).data('id');


            Swal.fire({

                title: 'Retry?',

                text:
                    'Retry calling this college?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-calls.retryByCollege') }}",

                    type: 'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        college_id:
                            id

                    },

                    success: function (res) {

                        if (res.status) {

                            window.location.href =
                                res.redirect;

                        }

                    },

                    error: function (xhr) {

                        let message =
                            'Something went wrong.';


                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {

                            message =
                                xhr.responseJSON.message;

                        }


                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: message

                        });

                    }

                });

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SINGLE CALL
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.call-single',
        function () {

            let id =
                $(this).data('id');


            Swal.fire({

                title: 'Start Calling?',

                text:
                    'Proceed with this college?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-calls.storeSelection') }}",

                    type: 'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        ids: [id]

                    },

                    success: function (res) {

                        if (res.status) {

                            window.location.href =
                                "{{ route('admin.college-calls.create') }}";

                        }

                    },

                    error: function (xhr) {

                        let message =
                            'Something went wrong.';


                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {

                            message =
                                xhr.responseJSON.message;

                        }


                        Swal.fire({

                            icon: 'error',

                            title: 'Error',

                            text: message

                        });

                    }

                });

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | COLLEGE DATA
    |--------------------------------------------------------------------------
    */

    let colleges =
        @json($colleges);


    let districtsByState =
        @json($districtsGrouped);


    /*
    |--------------------------------------------------------------------------
    | LOAD FILTERED COLLEGES
    |--------------------------------------------------------------------------
    */

    function loadFilteredColleges() {

        let state =
            $('#filter-state').val();


        let district =
            $('#filter-district').val();


        let collegeDropdown =
            $('#filter-college');


        collegeDropdown.empty();


        collegeDropdown.append(
            '<option value="">College</option>'
        );


        colleges.forEach(
            function (c) {

                if (
                    state &&
                    c.state_id != state
                ) {

                    return;

                }


                if (
                    district &&
                    c.district_id != district
                ) {

                    return;

                }


                collegeDropdown.append(

                    `<option value="${c.id}">
                        ${c.full_name ?? c.college_name}
                    </option>`

                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | STATE CHANGE
    |--------------------------------------------------------------------------
    */

    $('#filter-state').on(
        'change',
        function () {

            let stateId =
                $(this).val();


            let districtDropdown =
                $('#filter-district');


            districtDropdown
                .empty()
                .append(
                    '<option value="">District</option>'
                );


            if (
                stateId &&
                districtsByState[stateId]
            ) {

                districtsByState[stateId].forEach(
                    function (d) {

                        districtDropdown.append(

                            `<option value="${d.id}">
                                ${d.name}
                            </option>`

                        );

                    }
                );

            }


            loadFilteredColleges();


            table.ajax.reload(
                null,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | DISTRICT CHANGE
    |--------------------------------------------------------------------------
    */

    $('#filter-district').on(
        'change',
        function () {

            loadFilteredColleges();


            table.ajax.reload(
                null,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | COLLEGE FILTER
    |--------------------------------------------------------------------------
    */

    $('#filter-college').on(
        'change',
        function () {

            table.ajax.reload(
                null,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | RANGE
    |--------------------------------------------------------------------------
    */

    $('#filter-range').on(
        'change',
        function () {

            if ($(this).val()) {

                $('#date_from').val('');

                $('#date_to').val('');

            }


            table.ajax.reload(
                null,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | DATE FILTER
    |--------------------------------------------------------------------------
    */

    $('#date_from, #date_to').on(
        'change',
        function () {

            $('#filter-range').val('');


            table.ajax.reload(
                null,
                false
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | MARK / EDIT CALL STATUS
    |--------------------------------------------------------------------------
    */

    $('#editCallStatus').on(
        'click',
        function () {

            if (
                selectedIds.size === 0
            ) {

                Swal.fire({

                    icon: 'warning',

                    title: 'No Selection',

                    text:
                        'Please select at least one college',

                    showConfirmButton: true

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Reset status
            |--------------------------------------------------------------------------
            */

            $('#callDoneValue').val('');


            /*
            |--------------------------------------------------------------------------
            | Open modal
            |--------------------------------------------------------------------------
            */

            $('#callStatusModal').modal(
                'show'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SAVE CALL STATUS
    |--------------------------------------------------------------------------
    */

    $('#saveCallStatus').on(
        'click',
        function () {

            if (
                selectedIds.size === 0
            ) {

                return;

            }


            let callDone =
                $('#callDoneValue').val();


            /*
            |--------------------------------------------------------------------------
            | STATUS REQUIRED
            |--------------------------------------------------------------------------
            */

            if (
                callDone === ''
            ) {

                Swal.fire({

                    icon: 'warning',

                    title: 'Status Required',

                    text:
                        'Please select Call Done status.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STATUS
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url:
                    "{{ route('admin.college-calls.updateCallStatus') }}",

                type: 'POST',

                data: {

                    _token:
                        "{{ csrf_token() }}",

                    ids:
                        Array.from(selectedIds),

                    call_done:
                        callDone

                },

                success: function (response) {

                    if (
                        response.status
                    ) {

                        $('#callStatusModal')
                            .modal('hide');


                        /*
                        |--------------------------------------------------------------------------
                        | Clear selection
                        |--------------------------------------------------------------------------
                        */

                        selectedIds.clear();


                        $('#checkAll').prop(
                            'checked',
                            false
                        );


                        Swal.fire({

                            icon: 'success',

                            title: 'Updated',

                            text:
                                response.message,

                            timer: 1500,

                            showConfirmButton: false

                        }).then(function () {


                            /*
                            |--------------------------------------------------------------------------
                            | Reload page
                            |--------------------------------------------------------------------------
                            */

                            window.location.reload();

                        });

                    }

                },

                error: function (xhr) {

                    let message =
                        'Something went wrong.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text: message

                    });

                }

            });

        }
    );


});

</script>

@endpush