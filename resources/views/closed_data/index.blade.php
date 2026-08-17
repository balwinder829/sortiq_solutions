@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                Closed Data
            </h1>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- TABS --}}
    {{-- ========================================================= --}}

    <ul class="nav nav-tabs mb-3" id="closedDataTabs">

        <li class="nav-item">

            <button
                type="button"
                class="nav-link {{ $type == 1 ? 'active' : '' }}"
                data-type="1"
                id="enquiryTab">

                Enquiries

            </button>

        </li>

        <li class="nav-item">

            <button
                type="button"
                class="nav-link {{ $type == 2 ? 'active' : '' }}"
                data-type="2"
                id="manualTab">

                Manual Data

            </button>

        </li>

        <li class="nav-item">

            <button
                type="button"
                class="nav-link {{ $type == 3 ? 'active' : '' }}"
                data-type="3"
                id="hardTab">

                Hard Data

            </button>

        </li>

    </ul>


    {{-- ========================================================= --}}
    {{-- ENQUIRY TAB --}}
    {{-- ========================================================= --}}

    <div
        id="enquirySection"
        class="{{ $type == 1 ? '' : 'd-none' }}">

        <div class="row mb-3">

            <div class="col-md-3">

                <input
                    type="text"
                    id="enquiryMobile"
                    class="form-control"
                    placeholder="Mobile">

            </div>

            <div class="col-md-3">

                <input
                    type="text"
                    id="enquiryEmail"
                    class="form-control"
                    placeholder="Email">

            </div>

            <div class="col-md-3">

                <input
                    type="text"
                    id="enquiryStudy"
                    class="form-control"
                    placeholder="Course">

            </div>

        </div>


        <!-- <div class="mb-2">

            <button
                type="button"
                id="enquiryBulkAction"
                class="btn btn-warning">

                Bulk Action

            </button>

        </div>
 -->

        <div class="table-responsive">

            <table
                id="closed-enquiry-table"
                class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th>
                            <input
                                type="checkbox"
                                id="closedEnquiryCheckAll">
                        </th>

                        <th>ID</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>College</th>
                        <th>Course</th>
                        <th>Semester</th>
                        <th>Assigned To</th>
                        <th>Session</th>
                        <th>Type</th>
                        <th>Closed At</th>
                        <th>Reason</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MANUAL TAB --}}
    {{-- ========================================================= --}}

    <div
        id="manualSection"
        class="{{ $type == 2 ? '' : 'd-none' }}">

        <div class="row mb-3">

            <div class="col-md-3">

                <input
                    type="text"
                    id="manualMobile"
                    class="form-control"
                    placeholder="Mobile">

            </div>

            <div class="col-md-3">

                <input
                    type="text"
                    id="manualEmail"
                    class="form-control"
                    placeholder="Email">

            </div>

            <div class="col-md-3">

                <select
                    id="manualGender"
                    class="form-control">

                    <option value="">
                        All Gender
                    </option>

                    <option value="male">
                        Male
                    </option>

                    <option value="female">
                        Female
                    </option>

                </select>

            </div>

        </div>


       <!--  <div class="mb-2">

            <button
                type="button"
                id="manualBulkAction"
                class="btn btn-warning">

                Bulk Action

            </button>

        </div> -->


        <div class="table-responsive">

            <table
                id="closed-manual-table"
                class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th>
                            <input
                                type="checkbox"
                                id="closedManualCheckAll">
                        </th>

                        <th>ID</th>
                        <th>Name</th>
                        <th>College</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Class</th>
                        <th>Semester</th>
                        <th>Course Type</th>
                        <th>Gender</th>
                        <th>Session</th>
                        <th>Closed At</th>
                        <th>Reason</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- HARD TAB --}}
    {{-- ========================================================= --}}

    <div
        id="hardSection"
        class="{{ $type == 3 ? '' : 'd-none' }}">

        <div class="row mb-3">

            <div class="col-md-3">

                <input
                    type="text"
                    id="hardMobile"
                    class="form-control"
                    placeholder="Mobile">

            </div>

            <div class="col-md-3">

                <input
                    type="text"
                    id="hardEmail"
                    class="form-control"
                    placeholder="Email">

            </div>

            <div class="col-md-3">

                <select
                    id="hardGender"
                    class="form-control">

                    <option value="">
                        All Gender
                    </option>

                    <option value="male">
                        Male
                    </option>

                    <option value="female">
                        Female
                    </option>

                </select>

            </div>

        </div>


        <!-- <div class="mb-2">

            <button
                type="button"
                id="hardBulkAction"
                class="btn btn-warning">

                Bulk Action

            </button>

        </div> -->


        <div class="table-responsive">

            <table
                id="closed-hard-table"
                class="table table-bordered table-striped">

                <thead>

                    <tr>

                        <th>
                            <input
                                type="checkbox"
                                id="closedHardCheckAll">
                        </th>

                        <th>ID</th>
                        <th>Name</th>
                        <th>College</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Class</th>
                        <th>Semester</th>
                        <th>Course Type</th>
                        <th>Gender</th>
                        <th>Session</th>
                        <th>Closed At</th>
                        <th>Reason</th>

                    </tr>

                </thead>

                <tbody></tbody>

            </table>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | SEPARATE SELECTION SETS
    |--------------------------------------------------------------------------
    */

    let enquirySelectedIds = new Set();

    let manualSelectedIds = new Set();

    let hardSelectedIds = new Set();


    /*
    |--------------------------------------------------------------------------
    | DATATABLE VARIABLES
    |--------------------------------------------------------------------------
    */

    let enquiryTable = null;

    let manualTable = null;

    let hardTable = null;


    /*
    |--------------------------------------------------------------------------
    | ENQUIRY DATATABLE
    |--------------------------------------------------------------------------
    */

    function initEnquiryTable()
    {

        if (enquiryTable) {
            return;
        }


        enquiryTable = $('#closed-enquiry-table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: "{{ route('admin.closed_data.index') }}",

                data: function (d) {

                    d.type = 1;

                    d.mobile =
                        $('#enquiryMobile').val();

                    d.email =
                        $('#enquiryEmail').val();

                    d.study =
                        $('#enquiryStudy').val();

                }

            },

            columns: [

    // CHECKBOX
    {
        data: 'id',

        render: function (data, type, row) {

            let id = String(row.id);

            let checked =
                enquirySelectedIds.has(id)
                    ? 'checked'
                    : '';

            return `
                <input
                    type="checkbox"
                    class="closedEnquiryCheckbox"
                    value="${id}"
                    ${checked}>
            `;
        },

        orderable: false,
        searchable: false
    },

    // ID
    {
        data: 0
    },

    // NAME
    {
        data: 1
    },

    // MOBILE
    {
        data: 2
    },

    // EMAIL
    {
        data: 3
    },

    // COLLEGE
    {
        data: 4
    },

    // COURSE
    {
        data: 5
    },

    // SEMESTER
    {
        data: 6
    },

    // ASSIGNED TO
    {
        data: 7
    },

    // SESSION
    {
        data: 8
    },

    {
        data: 9,
        name: 'type'
    },

    // CLOSED AT
    {
        data: 10
    },

    // REASON
    {
        data: 11
    }

],

            drawCallback: function () {

                updateEnquiryCheckAll();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | MANUAL DATATABLE
    |--------------------------------------------------------------------------
    */

    function initManualTable()
    {

        if (manualTable) {
            return;
        }


        manualTable = $('#closed-manual-table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: "{{ route('admin.closed_data.index') }}",

                data: function (d) {

                    d.type = 2;

                    d.mobile =
                        $('#manualMobile').val();

                    d.email =
                        $('#manualEmail').val();

                    d.gender =
                        $('#manualGender').val();

                }

            },

            columns: [

                {
                    data: 'id',

                    render: function (
                        data,
                        type,
                        row
                    ) {

                        let id = String(row.id);

                        let checked =
                            manualSelectedIds.has(id)
                                ? 'checked'
                                : '';

                        return `
                            <input
                                type="checkbox"
                                class="closedManualCheckbox"
                                value="${id}"
                                ${checked}>
                        `;
                    },

                    orderable: false,

                    searchable: false
                },

                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7 },
                { data: 8 },
                { data: 9 },
                { data: 10 },
                { data: 11 }

            ],

            drawCallback: function () {

                updateManualCheckAll();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | HARD DATATABLE
    |--------------------------------------------------------------------------
    */

    function initHardTable()
    {

        if (hardTable) {
            return;
        }


        hardTable = $('#closed-hard-table').DataTable({

            processing: true,

            serverSide: true,

            ajax: {

                url: "{{ route('admin.closed_data.index') }}",

                data: function (d) {

                    d.type = 3;

                    d.mobile =
                        $('#hardMobile').val();

                    d.email =
                        $('#hardEmail').val();

                    d.gender =
                        $('#hardGender').val();

                }

            },

            columns: [

                {
                    data: 'id',

                    render: function (
                        data,
                        type,
                        row
                    ) {

                        let id = String(row.id);

                        let checked =
                            hardSelectedIds.has(id)
                                ? 'checked'
                                : '';

                        return `
                            <input
                                type="checkbox"
                                class="closedHardCheckbox"
                                value="${id}"
                                ${checked}>
                        `;
                    },

                    orderable: false,

                    searchable: false
                },

                { data: 0 },
                { data: 1 },
                { data: 2 },
                { data: 3 },
                { data: 4 },
                { data: 5 },
                { data: 6 },
                { data: 7 },
                { data: 8 },
                { data: 9 },
                { data: 10 },
                { data: 11 }

            ],

            drawCallback: function () {

                updateHardCheckAll();

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - ENQUIRY
    |--------------------------------------------------------------------------
    */

    $('#closedEnquiryCheckAll').on(
        'change',
        function () {

            let checked = this.checked;

            $('.closedEnquiryCheckbox').each(
                function () {

                    let id = String(
                        $(this).val()
                    );

                    if (checked) {

                        enquirySelectedIds.add(id);

                    } else {

                        enquirySelectedIds.delete(id);

                    }

                    $(this).prop(
                        'checked',
                        checked
                    );

                }
            );

            console.log(
                'Enquiry Select All:',
                checked,
                'Selected IDs:',
                Array.from(
                    enquirySelectedIds
                )
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SINGLE ENQUIRY CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.closedEnquiryCheckbox',
        function () {

            let id = String(
                $(this).val()
            );

            if ($(this).is(':checked')) {

                enquirySelectedIds.add(id);

            } else {

                enquirySelectedIds.delete(id);

            }

            updateEnquiryCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE ENQUIRY CHECK ALL
    |--------------------------------------------------------------------------
    */

    function updateEnquiryCheckAll()
    {

        let total =
            $('.closedEnquiryCheckbox').length;

        let checked =
            $('.closedEnquiryCheckbox:checked').length;

        $('#closedEnquiryCheckAll').prop(
            'checked',
            total > 0 &&
            total === checked
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - MANUAL
    |--------------------------------------------------------------------------
    */

    $('#closedManualCheckAll').on(
        'change',
        function () {

            let checked = this.checked;

            $('.closedManualCheckbox').each(
                function () {

                    let id = String(
                        $(this).val()
                    );

                    if (checked) {

                        manualSelectedIds.add(id);

                    } else {

                        manualSelectedIds.delete(id);

                    }

                    $(this).prop(
                        'checked',
                        checked
                    );

                }
            );

            console.log(
                'Manual Select All:',
                checked,
                'Selected IDs:',
                Array.from(
                    manualSelectedIds
                )
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SINGLE MANUAL CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.closedManualCheckbox',
        function () {

            let id = String(
                $(this).val()
            );

            if ($(this).is(':checked')) {

                manualSelectedIds.add(id);

            } else {

                manualSelectedIds.delete(id);

            }

            updateManualCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE MANUAL CHECK ALL
    |--------------------------------------------------------------------------
    */

    function updateManualCheckAll()
    {

        let total =
            $('.closedManualCheckbox').length;

        let checked =
            $('.closedManualCheckbox:checked').length;

        $('#closedManualCheckAll').prop(
            'checked',
            total > 0 &&
            total === checked
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - HARD
    |--------------------------------------------------------------------------
    */

    $('#closedHardCheckAll').on(
        'change',
        function () {

            let checked = this.checked;

            $('.closedHardCheckbox').each(
                function () {

                    let id = String(
                        $(this).val()
                    );

                    if (checked) {

                        hardSelectedIds.add(id);

                    } else {

                        hardSelectedIds.delete(id);

                    }

                    $(this).prop(
                        'checked',
                        checked
                    );

                }
            );

            console.log(
                'Hard Select All:',
                checked,
                'Selected IDs:',
                Array.from(
                    hardSelectedIds
                )
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SINGLE HARD CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.closedHardCheckbox',
        function () {

            let id = String(
                $(this).val()
            );

            if ($(this).is(':checked')) {

                hardSelectedIds.add(id);

            } else {

                hardSelectedIds.delete(id);

            }

            updateHardCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | UPDATE HARD CHECK ALL
    |--------------------------------------------------------------------------
    */

    function updateHardCheckAll()
    {

        let total =
            $('.closedHardCheckbox').length;

        let checked =
            $('.closedHardCheckbox:checked').length;

        $('#closedHardCheckAll').prop(
            'checked',
            total > 0 &&
            total === checked
        );

    }


    /*
    |--------------------------------------------------------------------------
    | TAB SWITCH
    |--------------------------------------------------------------------------
    */

    $('#closedDataTabs .nav-link').on(
        'click',
        function () {

            let type = parseInt(
                $(this).data('type')
            );


            /*
            |----------------------------------------------------------
            | ACTIVE TAB
            |----------------------------------------------------------
            */

            $('#closedDataTabs .nav-link')
                .removeClass('active');

            $(this).addClass('active');


            /*
            |----------------------------------------------------------
            | SHOW SECTION
            |----------------------------------------------------------
            */

            $('#enquirySection')
                .addClass('d-none');

            $('#manualSection')
                .addClass('d-none');

            $('#hardSection')
                .addClass('d-none');


            if (type === 1) {

                $('#enquirySection')
                    .removeClass('d-none');

                initEnquiryTable();

                setTimeout(function () {

                    enquiryTable
                        .columns.adjust();

                }, 100);

            }


            if (type === 2) {

                $('#manualSection')
                    .removeClass('d-none');

                initManualTable();

                setTimeout(function () {

                    manualTable
                        .columns.adjust();

                }, 100);

            }


            if (type === 3) {

                $('#hardSection')
                    .removeClass('d-none');

                initHardTable();

                setTimeout(function () {

                    hardTable
                        .columns.adjust();

                }, 100);

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | INITIAL TAB
    |--------------------------------------------------------------------------
    */

    let initialType = {{ $type }};


    if (initialType === 1) {

        initEnquiryTable();

    } else if (initialType === 2) {

        initManualTable();

    } else {

        initHardTable();

    }


    /*
    |--------------------------------------------------------------------------
    | FILTER RELOAD
    |--------------------------------------------------------------------------
    */

    $('#enquiryMobile, #enquiryEmail, #enquiryStudy')
        .on('keyup change', function () {

            if (enquiryTable) {

                enquiryTable.ajax.reload(
                    null,
                    false
                );

            }

        });


    $('#manualMobile, #manualEmail, #manualGender')
        .on('keyup change', function () {

            if (manualTable) {

                manualTable.ajax.reload(
                    null,
                    false
                );

            }

        });


    $('#hardMobile, #hardEmail, #hardGender')
        .on('keyup change', function () {

            if (hardTable) {

                hardTable.ajax.reload(
                    null,
                    false
                );

            }

        });


    /*
    |--------------------------------------------------------------------------
    | BULK ACTION BUTTONS
    |--------------------------------------------------------------------------
    */

    // ENQUIRY
    $('#enquiryBulkAction').on('click', function () {

        if (enquirySelectedIds.size === 0) {

            Swal.fire({
                icon: 'warning',
                title: 'No selection',
                text: 'Please select at least one enquiry.',
                confirmButtonText: 'OK'
            });

            return;
        }

        let ids = Array.from(enquirySelectedIds);

        console.log('Enquiry selected:', ids);

        showBulkActionPopup(
            1,
            Array.from(enquirySelectedIds)
        );

        // NEXT STEP WILL COME HERE
    });


    // MANUAL DATA
    $('#manualBulkAction').on('click', function () {

        if (manualSelectedIds.size === 0) {

            Swal.fire({
                icon: 'warning',
                title: 'No selection',
                text: 'Please select at least one Manual Data record.',
                confirmButtonText: 'OK'
            });

            return;
        }

        let ids = Array.from(manualSelectedIds);

        console.log('Manual selected:', ids);

        showBulkActionPopup(
            2,
            Array.from(manualSelectedIds)
        );

        // NEXT STEP WILL COME HERE
    });


    // HARD DATA
    $('#hardBulkAction').on('click', function () {

        if (hardSelectedIds.size === 0) {

            Swal.fire({
                icon: 'warning',
                title: 'No selection',
                text: 'Please select at least one Hard Data record.',
                confirmButtonText: 'OK'
            });

            return;
        }

        let ids = Array.from(hardSelectedIds);

        console.log('Hard selected:', ids);

        showBulkActionPopup(
            3,
            Array.from(hardSelectedIds)
        );

        // NEXT STEP WILL COME HERE
    });



    /*
|--------------------------------------------------------------------------
| SHOW BULK ACTION POPUP
|--------------------------------------------------------------------------
*/

function showBulkActionPopup(type, ids)
{
    let moduleName = '';

    if (type === 1) {
        moduleName = 'Enquiries';
    }

    if (type === 2) {
        moduleName = 'Manual Data';
    }

    if (type === 3) {
        moduleName = 'Hard Data';
    }


    Swal.fire({

        title: 'Bulk Action',

        html: `
            <div class="text-start">

                <p class="mb-3">
                    <strong>${ids.length}</strong>
                    ${moduleName} selected.
                </p>

                <button
                    type="button"
                    id="restoreClosedRecords"
                    class="btn btn-success w-100 mb-2">

                    <i class="fas fa-undo"></i>
                    Restore / Reopen

                </button>


                <button
                    type="button"
                    id="moveClosedRecords"
                    class="btn btn-warning w-100">

                    <i class="fas fa-exchange-alt"></i>
                    Move to Session

                </button>

            </div>
        `,

        showConfirmButton: false,

        showCancelButton: true,

        cancelButtonText: 'Cancel',

        didOpen: function () {

            /*
            |--------------------------------------------------------------------------
            | RESTORE
            |--------------------------------------------------------------------------
            */

            $('#restoreClosedRecords').on(
                'click',
                function () {

                    Swal.close();

                    confirmRestore(
                        type,
                        ids
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | MOVE
            |--------------------------------------------------------------------------
            */

            $('#moveClosedRecords').on(
                'click',
                function () {

                    Swal.close();

                    showMoveSessionPopup(
                        type,
                        ids
                    );

                }
            );
        }

    });
}

/*
|--------------------------------------------------------------------------
| RESTORE CONFIRMATION
|--------------------------------------------------------------------------
*/

function confirmRestore(type, ids)
{
    let moduleName = '';

    if (type === 1) {
        moduleName = 'enquiries';
    }

    if (type === 2) {
        moduleName = 'Manual Data records';
    }

    if (type === 3) {
        moduleName = 'Hard Data records';
    }


    Swal.fire({

        icon: 'question',

        title: 'Restore records?',

        text:
            `Restore ${ids.length} selected ${moduleName} and make them active again?`,

        showCancelButton: true,

        confirmButtonText: 'Yes, Restore',

        cancelButtonText: 'Cancel'

    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }


        performClosedAction(
            type,
            ids,
            'restore',
            null
        );

    });
}

/*
|--------------------------------------------------------------------------
| MOVE TO SESSION POPUP
|--------------------------------------------------------------------------
*/

function showMoveSessionPopup(type, ids)
{
    let moduleName = '';

    if (type === 1) {
        moduleName = 'Enquiries';
    }

    if (type === 2) {
        moduleName = 'Manual Data';
    }

    if (type === 3) {
        moduleName = 'Hard Data';
    }


    let optionsHtml = `
        <option value="">
            Select Session
        </option>
    `;


    @foreach($saleSessions as $session)

        optionsHtml += `
            <option value="{{ $session->id }}">
                {{ $session->session_name }}
            </option>
        `;

    @endforeach


    Swal.fire({

        title: 'Move to Session',

        html: `

            <p class="text-start mb-2">

                <strong>${ids.length}</strong>
                ${moduleName} selected.

            </p>

            <select
                id="closedTargetSession"
                class="form-control">

                ${optionsHtml}

            </select>

        `,

        showCancelButton: true,

        confirmButtonText: 'Move',

        cancelButtonText: 'Cancel',

        preConfirm: function () {

            let sessionId =
                $('#closedTargetSession').val();

            if (!sessionId) {

                Swal.showValidationMessage(
                    'Please select a session.'
                );

                return false;
            }

            return sessionId;
        }

    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }


        performClosedAction(
            type,
            ids,
            'move',
            result.value
        );

    });
}


/*
|--------------------------------------------------------------------------
| PERFORM CLOSED DATA ACTION
|--------------------------------------------------------------------------
*/

function performClosedAction(
    type,
    ids,
    action,
    sessionId
) {

    Swal.fire({

        title: 'Please wait...',

        text: 'Updating selected records.',

        allowOutsideClick: false,

        didOpen: function () {

            Swal.showLoading();

        }

    });


    $.ajax({

        url: "{{ route('admin.closed_data.bulk_action') }}",

        type: "POST",

        data: {

            _token: "{{ csrf_token() }}",

            type: type,

            ids: ids,

            action: action,

            session_id: sessionId

        },

        success: function (res) {

            Swal.fire({

                icon: 'success',

                title: 'Success',

                text: res.message

            });


            /*
            |--------------------------------------------------------------------------
            | CLEAR ONLY CURRENT TAB SELECTION
            |--------------------------------------------------------------------------
            */

            if (type === 1) {

                enquirySelectedIds.clear();

                $('#closedEnquiryCheckAll')
                    .prop('checked', false);

                if (enquiryTable) {
                    enquiryTable.ajax.reload(
                        null,
                        false
                    );
                }

            }


            if (type === 2) {

                manualSelectedIds.clear();

                $('#closedManualCheckAll')
                    .prop('checked', false);

                if (manualTable) {
                    manualTable.ajax.reload(
                        null,
                        false
                    );
                }

            }


            if (type === 3) {

                hardSelectedIds.clear();

                $('#closedHardCheckAll')
                    .prop('checked', false);

                if (hardTable) {
                    hardTable.ajax.reload(
                        null,
                        false
                    );
                }

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


});

</script>

@endpush