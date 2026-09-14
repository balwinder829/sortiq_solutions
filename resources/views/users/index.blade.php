@extends('layouts.app')

@section('content')

<style>

.permission-group{
    cursor:pointer;
    transition:0.2s ease;
    margin-right:3px;
}

.permission-group:hover{
    background:#6b51df !important;
    color:#fff !important;
}

/* Modern Popover Style */

.popover{
    border-radius:12px;
    border:none;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
    max-width:260px;
}

.popover-body{
    font-size:13px;
    line-height:1.6;
    padding:10px 14px;
}

.perm-popover{
    padding:2px 0;
}

.user-status-tab{
    cursor:pointer;
}

</style>


<div class="container">

    <!-- ========================================================= -->
    <!-- HEADER -->
    <!-- ========================================================= -->

    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading mb-0">
                Users
            </h1>

        </div>

        <div class="col-md-6">

            <div class="d-flex justify-content-end align-items-center gap-2">

                <!-- BULK ACTION BUTTON -->

                <button
                    type="button"
                    id="bulkStatusBtn"
                    class="btn btn-success">

                    Make Inactive

                </button>

                <!-- <span
                    id="selectedUserCount"
                    class="text-muted">
                </span> -->


                <!-- ADD USER -->

                <a
                    href="{{ route('users.create') }}"
                    class="btn"
                    style="background-color:#6b51df; color:#fff;">

                    Add User

                </a>

            </div>

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
    <!-- WARNING MESSAGE -->
    <!-- ========================================================= -->

    @if(session('warning'))

        <div
            class="alert alert-warning alert-dismissible fade show"
            role="alert">

            {{ session('warning') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close">
            </button>

        </div>

    @endif


    <!-- ========================================================= -->
    <!-- STATUS TABS -->
    <!-- ========================================================= -->

    <div class="mb-3">

        <ul class="nav nav-tabs">

            <!-- ACTIVE -->

            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link user-status-tab active"
                    data-status="active">

                    Active

                </a>

            </li>


            <!-- INACTIVE -->

            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link user-status-tab"
                    data-status="inactive">

                    Inactive

                </a>

            </li>


            <!-- DELETED -->

            <li class="nav-item">

                <a
                    href="javascript:void(0)"
                    class="nav-link user-status-tab"
                    data-status="deleted">

                    Deleted

                </a>

            </li>

        </ul>

    </div>


    <!-- ========================================================= -->
    <!-- USERS TABLE -->
    <!-- ========================================================= -->

    <table
        id="usersTable"
        class="table table-bordered table-striped">

        <thead>

            <tr>

                <th style="width:40px;">

                    <input
                        type="checkbox"
                        id="checkAll">

                </th>

                <th>ID</th>

                <th>Name</th>

                <th>Username</th>

                <th>Role</th>

                <th>Status</th>

                <th>Created At</th>

                <th>Actions</th>

            </tr>

        </thead>

        <tbody></tbody>

    </table>

</div>


<!-- ============================================================= -->
<!-- BULK STATUS FORM -->
<!-- ============================================================= -->

<form
    method="POST"
    action="{{ route('users.bulkStatus') }}"
    id="bulkUserStatusForm">

    @csrf

    <input
        type="hidden"
        name="ids"
        id="bulkUserIds">

    <input
        type="hidden"
        name="status"
        id="bulkUserStatus">

</form>


<!-- ============================================================= -->
<!-- BULK RESTORE FORM -->
<!-- ============================================================= -->

<form
    method="POST"
    action="{{ route('users.bulkRestore') }}"
    id="bulkUserRestoreForm">

    @csrf

    <input
        type="hidden"
        name="ids"
        id="bulkRestoreUserIds">

</form>


@endsection


@push('styles')

@endpush


@push('scripts')

<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | CURRENT TAB
    |--------------------------------------------------------------------------
    */

    let currentStatus = 'active';


    /*
    |--------------------------------------------------------------------------
    | SELECTED USERS
    |--------------------------------------------------------------------------
    |
    | Keeps selected IDs when DataTables:
    |
    | - Changes page
    | - Searches
    | - Sorts
    | - Redraws
    |
    |--------------------------------------------------------------------------
    */

    let selectedUsers = {};


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    var table = $('#usersTable').DataTable({

        processing: true,

        serverSide: true,

        ajax: {

            url: "{{ route('users.data') }}",

            data: function (d) {

                d.tab = currentStatus;

            }

        },

        columns: [

            {
                data: 0,
                orderable: false,
                searchable: false
            },

            {
                data: 1
            },

            {
                data: 2
            },

            {
                data: 3
            },

            {
                data: 4
            },

            {
                data: 5
            },

            {
                data: 6
            },

            {
                data: 7,
                orderable: false,
                searchable: false
            }

        ],

        pageLength: 10,

        lengthMenu: [
            5,
            10,
            25,
            50,
            100
        ],

        columnDefs: [

            {
                targets: 0,
                width: "40px",
                orderable: false,
                searchable: false
            },

            {
                targets: 7,
                width: "180px",
                orderable: false,
                searchable: false
            }

        ]

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE BULK BUTTON
    |--------------------------------------------------------------------------
    */

    function updateBulkButton() {

        const count = Object.keys(selectedUsers).length;


        /*
        |--------------------------------------------------------------------------
        | SELECTED COUNT
        |--------------------------------------------------------------------------
        */

        $('#selectedUserCount').text(

            count > 0
                ? count + ' selected'
                : ''

        );


        /*
        |--------------------------------------------------------------------------
        | DELETED TAB
        |--------------------------------------------------------------------------
        */

        if (currentStatus === 'deleted') {

            $('#bulkStatusBtn')
                .removeClass('btn-success')
                .addClass('btn-primary')
                .text('Restore Selected');

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | ACTIVE TAB
        |--------------------------------------------------------------------------
        */

        if (currentStatus === 'active') {

            $('#bulkStatusBtn')
                .removeClass('btn-primary')
                .addClass('btn-success')
                .text('Make Inactive');

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INACTIVE TAB
        |--------------------------------------------------------------------------
        */

        $('#bulkStatusBtn')
            .removeClass('btn-success')
            .addClass('btn-primary')
            .text('Make Active');

    }


    /*
    |--------------------------------------------------------------------------
    | RESTORE CHECKBOXES AFTER DATATABLE DRAW
    |--------------------------------------------------------------------------
    */

    function restoreCheckboxes() {

        $('#usersTable tbody .user-checkbox').each(function () {

            const id = String($(this).val());

            $(this).prop(
                'checked',
                !!selectedUsers[id]
            );

        });


        updateCheckAll();

        updateBulkButton();

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECT ALL
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        const checkboxes =
            $('#usersTable tbody .user-checkbox');


        if (!checkboxes.length) {

            $('#checkAll').prop(
                'checked',
                false
            );

            return;
        }


        let allChecked = true;


        checkboxes.each(function () {

            const id = String($(this).val());


            if (!selectedUsers[id]) {

                allChecked = false;

            }

        });


        $('#checkAll').prop(
            'checked',
            allChecked
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DATATABLE DRAW
    |--------------------------------------------------------------------------
    */

    $('#usersTable').on(
        'draw.dt',
        function () {

            restoreCheckboxes();


            /*
            |--------------------------------------------------------------------------
            | BOOTSTRAP POPOVERS
            |--------------------------------------------------------------------------
            */

            var popoverTriggerList =
                [].slice.call(
                    document.querySelectorAll(
                        '[data-bs-toggle="popover"]'
                    )
                );


            popoverTriggerList.map(function (el) {

                return new bootstrap.Popover(el);

            });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#usersTable tbody .user-checkbox',
        function () {

            const id = String(
                $(this).val()
            );


            if ($(this).is(':checked')) {

                selectedUsers[id] = true;

            } else {

                delete selectedUsers[id];

            }


            updateCheckAll();

            updateBulkButton();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - CURRENT PAGE
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on(
        'change',
        function () {

            const checked =
                $(this).is(':checked');


            $('#usersTable tbody .user-checkbox').each(
                function () {

                    const id =
                        String($(this).val());


                    $(this).prop(
                        'checked',
                        checked
                    );


                    if (checked) {

                        selectedUsers[id] = true;

                    } else {

                        delete selectedUsers[id];

                    }

                }
            );


            updateBulkButton();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | STATUS TABS
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.user-status-tab',
        function () {

            const status =
                $(this).data('status');


            if (currentStatus === status) {

                return;

            }


            currentStatus = status;


            /*
            |--------------------------------------------------------------------------
            | Clear selection when changing tab
            |--------------------------------------------------------------------------
            */

            selectedUsers = {};


            $('#checkAll').prop(
                'checked',
                false
            );


            /*
            |--------------------------------------------------------------------------
            | Active Tab
            |--------------------------------------------------------------------------
            */

            $('.user-status-tab')
                .removeClass('active');


            $(this)
                .addClass('active');


            updateBulkButton();


            /*
            |--------------------------------------------------------------------------
            | Reload DataTable
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
    | BULK ACTION BUTTON
    |--------------------------------------------------------------------------
    */

    $('#bulkStatusBtn').on(
        'click',
        function () {

            const ids =
                Object.keys(selectedUsers);


            /*
            |--------------------------------------------------------------------------
            | NO SELECTION
            |--------------------------------------------------------------------------
            */

            if (!ids.length) {

                Swal.fire({

                    icon: 'warning',

                title: 'No Record Selected',

                text: 'Please select at least 1 record.'

                });

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | DELETED TAB
            |--------------------------------------------------------------------------
            | Restore selected users
            |--------------------------------------------------------------------------
            */

            if (currentStatus === 'deleted') {

                Swal.fire({

                    icon: 'question',

                    title:
                        'Restore Selected Users?',

                    text:
                        'Are you sure you want to restore ' +
                        ids.length +
                        ' selected user' +
                        (ids.length === 1 ? '' : 's') +
                        '?',

                    showCancelButton: true,

                    confirmButtonText:
                        'Yes, Restore',

                    cancelButtonText:
                        'Cancel',

                    reverseButtons: false

                }).then(function (result) {

                    if (!result.isConfirmed) {

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SET RESTORE IDS
                    |--------------------------------------------------------------------------
                    */

                    $('#bulkRestoreUserIds').val(
                        ids.join(',')
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | AJAX RESTORE
                    |--------------------------------------------------------------------------
                    */

                    $.ajax({

                        url:
                            $('#bulkUserRestoreForm')
                                .attr('action'),

                        type: 'POST',

                        data:
                            $('#bulkUserRestoreForm')
                                .serialize(),

                        success: function (response) {

                            selectedUsers = {};


                            $('#checkAll').prop(
                                'checked',
                                false
                            );


                            Swal.fire({

                                icon: 'success',

                                title: 'Success',

                                text:
                                    response.message,

                                timer: 1500,

                                showConfirmButton: false

                            }).then(function () {

                                /*
                                |--------------------------------------------------------------------------
                                | FULL PAGE REFRESH
                                |--------------------------------------------------------------------------
                                */

                                window.location.reload();

                            });

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


                return;
            }


            /*
            |--------------------------------------------------------------------------
            | ACTIVE / INACTIVE
            |--------------------------------------------------------------------------
            */

            const newStatus =
                currentStatus === 'active'
                    ? 'inactive'
                    : 'active';


            const actionText =
                newStatus === 'inactive'
                    ? 'Make Inactive'
                    : 'Make Active';


            /*
            |--------------------------------------------------------------------------
            | CONFIRM STATUS CHANGE
            |--------------------------------------------------------------------------
            */

            Swal.fire({

                icon: 'question',

                title:
                    actionText + '?',

                text:
                    'Are you sure you want to ' +
                    actionText.toLowerCase() +
                    ' ' +
                    ids.length +
                    ' selected user' +
                    (ids.length === 1 ? '' : 's') +
                    '?',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, ' + actionText,

                cancelButtonText:
                    'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (!result.isConfirmed) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | SET FORM DATA
                |--------------------------------------------------------------------------
                */

                $('#bulkUserIds').val(
                    ids.join(',')
                );


                $('#bulkUserStatus').val(
                    newStatus
                );


                /*
                |--------------------------------------------------------------------------
                | AJAX STATUS UPDATE
                |--------------------------------------------------------------------------
                */

                $.ajax({

                    url:
                        $('#bulkUserStatusForm')
                            .attr('action'),

                    type: 'POST',

                    data:
                        $('#bulkUserStatusForm')
                            .serialize(),

                    success: function (response) {

                        selectedUsers = {};


                        $('#checkAll').prop(
                            'checked',
                            false
                        );


                        Swal.fire({

                            icon: 'success',

                            title: 'Success',

                            text:
                                response.message,

                            timer: 1500,

                            showConfirmButton: false

                        }).then(function () {

                            /*
                            |--------------------------------------------------------------------------
                            | FULL PAGE REFRESH
                            |--------------------------------------------------------------------------
                            */

                            window.location.reload();

                        });

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
    | SINGLE DELETE CONFIRMATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        'form.user-action-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete User?',

                text:
                    'Are you sure you want to delete this user?',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Delete',

                cancelButtonText:
                    'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {

                    HTMLFormElement.prototype.submit.call(
                        form
                    );

                }

            });


            return false;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SINGLE RESTORE CONFIRMATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        'form.user-restore-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'question',

                title: 'Restore User?',

                text:
                    'Are you sure you want to restore this user?',

                showCancelButton: true,

                confirmButtonText:
                    'Yes, Restore',

                cancelButtonText:
                    'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {

                    HTMLFormElement.prototype.submit.call(
                        form
                    );

                }

            });


            return false;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL BUTTON STATE
    |--------------------------------------------------------------------------
    */

    updateBulkButton();

});

</script>

@endpush