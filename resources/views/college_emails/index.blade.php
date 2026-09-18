@extends('layouts.app')

@section('content')

<style>

.email-status-tab {
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
                Colleges Email Panel
            </h3>

        </div>


        <div class="col-md-6 text-end">

            <button
                id="markEmailStatus"
                type="button"
                class="btn btn-success">

                Edit Email Status

            </button>


            <button
                id="sendSelected"
                type="button"
                class="btn btn-primary">

                Send Email (Selected)

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
    <!-- EMAIL STATUS TABS -->
    <!-- ========================================================= -->

    <div class="mb-3">

        <ul class="nav nav-tabs">

            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link email-status-tab"
                    data-status="sent">

                    Sent

                </a>

            </li>


            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link email-status-tab active"
                    data-status="not_sent">

                    Not Sent

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
                    href="{{ route('admin.college-emails.index') }}"
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
            id="collegeTable"
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
                        Email Count
                    </th>

                    <th>
                        Sent To
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
<!-- EMAIL STATUS MODAL -->
<!-- ============================================================= -->

<div
    class="modal fade"
    id="emailStatusModal"
    tabindex="-1"
    aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Email Status
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

                        Email Sent
                        <span class="text-danger">*</span>

                    </label>


                    <select
                        id="emailSentValue"
                        class="form-select"
                        required>

                        <option value="">
                            Select Status
                        </option>

                        <option value="1">
                            Sent
                        </option>

                        <option value="0">
                            Not Sent
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
                    id="saveEmailStatus"
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
    | CURRENT EMAIL STATUS TAB
    |--------------------------------------------------------------------------
    */

    let currentEmailStatus = 'not_sent';


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

    let table = $('#collegeTable').DataTable({

        processing: true,

        serverSide: true,

        ajax: {

            url: "{{ route('admin.college-emails.index') }}",

            data: function (d) {

                /*
                |--------------------------------------------------------------------------
                | EMAIL STATUS TAB
                |--------------------------------------------------------------------------
                */

                d.email_status =
                    currentEmailStatus;


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

                let id = String(
                    $(this).val()
                );

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


            updateSelectionCount();

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


            updateSelectionCount();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECTION COUNT
    |--------------------------------------------------------------------------
    */

    function updateSelectionCount() {

        /*
        |--------------------------------------------------------------------------
        | No visual count required currently.
        | Selection is still maintained internally.
        |--------------------------------------------------------------------------
        */

    }


    /*
    |--------------------------------------------------------------------------
    | EMAIL STATUS TABS
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.email-status-tab',
        function () {

            let status =
                $(this).data('status');


            if (
                currentEmailStatus === status
            ) {

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | Change tab
            |--------------------------------------------------------------------------
            */

            currentEmailStatus =
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

            $('.email-status-tab')
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
    | BULK SEND
    |--------------------------------------------------------------------------
    */

    $('#sendSelected').click(
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
                    'Send email to selected colleges?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: true

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-emails.storeSelection') }}",

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
                                "{{ route('admin.college-emails.create') }}";

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
                    'Retry failed emails for this college?',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: true

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-emails.retryByCollege') }}",

                    type: 'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        college_id:
                            id

                    },

                    success: function (res) {

                        Swal.fire({

                            icon: 'success',

                            title: 'Done',

                            text:
                                res.message,

                            showConfirmButton: true

                        });


                        table.ajax.reload(
                            null,
                            false
                        );

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
    | SINGLE SEND
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.send-single',
        function () {

            let id =
                $(this).data('id');


            Swal.fire({

                title: 'Send Email?',

                text:
                    'Proceed with this college?',

                icon: 'question',

                showCancelButton: true,

                confirmButtonText: 'Yes',

                cancelButtonText: 'Cancel',

                reverseButtons: true

            }).then(function (result) {

                if (
                    !result.isConfirmed
                ) {

                    return;

                }


                $.ajax({

                    url:
                        "{{ route('admin.college-emails.storeSelection') }}",

                    type: 'POST',

                    data: {

                        _token:
                            '{{ csrf_token() }}',

                        ids: [id]

                    },

                    success: function (res) {

                        if (res.status) {

                            window.location.href =
                                "{{ route('admin.college-emails.create') }}";

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
    | MARK / EDIT EMAIL STATUS
    |--------------------------------------------------------------------------
    */

    $('#markEmailStatus').on(
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

            $('#emailSentValue').val('');


            /*
            |--------------------------------------------------------------------------
            | Open modal
            |--------------------------------------------------------------------------
            */

            $('#emailStatusModal').modal(
                'show'
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SAVE EMAIL STATUS
    |--------------------------------------------------------------------------
    */

    $('#saveEmailStatus').on(
        'click',
        function () {

            if (
                selectedIds.size === 0
            ) {

                return;

            }


            let emailSent =
                $('#emailSentValue').val();


            /*
            |--------------------------------------------------------------------------
            | STATUS REQUIRED
            |--------------------------------------------------------------------------
            */

            if (
                emailSent === ''
            ) {

                Swal.fire({

                    icon: 'warning',

                    title: 'Status Required',

                    text:
                        'Please select Email Sent status.'

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
                    "{{ route('admin.college-emails.updateEmailStatus') }}",

                type: 'POST',

                data: {

                    _token:
                        "{{ csrf_token() }}",

                    ids:
                        Array.from(selectedIds),

                    email_sent:
                        emailSent

                },

                success: function (response) {

                    if (
                        response.status
                    ) {

                        $('#emailStatusModal')
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