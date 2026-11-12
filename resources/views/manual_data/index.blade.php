@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Manual Data</h1>
    </div>

    <div class="col-md-8 text-end">
        <button type="button" id="sendManualWhatsApp" class="btn btn-success mb-3">
            <i class="fab fa-whatsapp"></i> Send WhatsApp
        </button>
        <button id="moveToEnquiry" class="btn btn-warning mb-3">
            Move to Enquiries
        </button>

        <button type="button"
                id="bulkManualActionBtn"
                class="btn btn-warning mb-3">
            Move / Close Selected
        </button>
        
        <button type="button"
                id="bulkDeleteManualData"
                class="btn btn-danger mb-3">
            <i class="fa fa-trash"></i> Bulk Delete
        </button>

        <a href="{{ route('admin.manual_data.create') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Add
        </a>
        <a href="{{ route('admin.manual_data.import.form') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Import
        </a>
        <a href="#" id="exportExcel"
   class="btn btn-success mb-3">
    Export Excel
</a>
    </div>
<form id="filterForm" class="row mb-3 gap-2">
   

    {{-- COLLEGE FILTER --}}
    <div class="col-md-2">
        <select name="college_name" class="form-control select2">
            <option value="">All College</option>
            @foreach($collegeNames as $collegeName)
                <option value="{{ $collegeName }}">{{ $collegeName }}</option>
            @endforeach
        </select>
    </div>

    {{-- COURSE TYPE FILTER --}}
    <div class="col-md-2">
        <select name="course_type" class="form-control">
            <option value="">All Course</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
        </select>
    </div>

    {{-- CLASS FILTER --}}
    <div class="col-md-2">
        <select name="class" class="form-control">
            <option value="">All Classes</option>
            @foreach($classNames ?? collect() as $className)
                <option value="{{ $className }}">{{ $className }}</option>
            @endforeach
        </select>
    </div>

    {{-- SEMESTER FILTER --}}
    <div class="col-md-2">
        <select name="semester" class="form-control">
            <option value="">All Semesters</option>
            @for($semester = 1; $semester <= 8; $semester++)
                <option value="{{ $semester }}">Semester {{ $semester }}</option>
            @endfor
        </select>
    </div>

    {{-- DATE RANGE FILTER --}}
    <div class="col-md-2">
        <select name="range" id="manualDateRange" class="form-control">
            <option value="">All Dates</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="this_week">This Week</option>
            <option value="last_week">Last Week</option>
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="this_month">This Month</option>
            <option value="custom">Custom</option>
        </select>
    </div>

    {{-- CUSTOM DATE FILTERS --}}
    <div class="col-md-2 manual-custom-date" style="display:none;">
        <input type="date" name="from_date" class="form-control" placeholder="From Date">
    </div>
    <div class="col-md-2 manual-custom-date" style="display:none;">
        <input type="date" name="to_date" class="form-control" placeholder="To Date">
    </div>

     <div class="col-md-2">
        <select name="gender" class="form-control">
            <option value="">All Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="is_moved" class="form-control">
            <option value="">All Status</option>
            <option value="1">Moved</option>
            <option value="0">Not Moved</option>
        </select>
    </div>

     <div class="col-md-3">
        <input type="text" name="email" class="form-control" placeholder="Email">
    </div>

    <div class="col-md-3">
        <input type="text" name="mobile" class="form-control" placeholder="Mobile">
    </div>
<div class="col-md-2">
    <button type="button" id="resetFilters" class="btn btn-secondary w-100 mt-2">
        Reset
    </button>
</div>
</form>
</div>


{{-- SUCCESS --}}
@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- TABLE --}}
<div class="table-responsive">
<table id="trainers-table" class="table table-bordered table-striped">

<thead>
<tr>
    <th>
        <input type="checkbox" id="checkAll">
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
    <th>Created</th>
    <th width="150">Actions</th>
</tr>
</thead>

<tbody></tbody>

</table>
</div>

</div>


<!-- Move / Close Manual Data Modal -->
<div class="modal fade"
     id="moveManualDataModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog">

        <form id="moveManualDataForm">

            @csrf

            <input type="hidden"
                   name="ids"
                   id="manualBulkIds">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Move / Close Records
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <!-- ACTION -->

                    <div class="mb-3">

                        <label class="form-label">
                            Action
                        </label>

                        <select class="form-control"
                                name="action"
                                id="manualBulkAction"
                                required>

                            <option value="">
                                Select Action
                            </option>

                            <option value="move">
                                Move To Another Session
                            </option>

                            <option value="close">
                                Close Record
                            </option>

                        </select>

                    </div>


                    <!-- MOVE SECTION -->

                    <div id="manualMoveSection"
                         style="display:none;">

                        <div class="mb-3">

                            <label class="form-label">
                                Session
                            </label>

                            <select class="form-control"
                                    name="session_id"
                                    id="manualSessionId">

                                <option value="">
                                    Select Session
                                </option>

                                @foreach($saleSessions as $id => $name)

                                    <option value="{{ $id }}">
                                        {{ $name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <!-- CLOSE SECTION -->

                    <div id="manualCloseSection"
                         style="display:block;">

                        <div class="mb-3">

                            <label class="form-label">
                                Reason
                            </label>

                            <textarea class="form-control"
                                      name="reason"
                                      id="manualCloseReason"
                                      rows="3"
                                      placeholder="Enter reason"></textarea>

                        </div>

                    </div>


                    <!-- SELECTED COUNT -->

                    <!-- <div class="alert alert-light">

                        Selected Records:

                        <strong id="manualSelectedCount">
                            0
                        </strong>

                    </div> -->

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="manualBulkSubmit">

                        Save

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
@endsection


@push('scripts')

<script>

let selectedIds = new Set();
let selectedRecordIds = new Set();
let selectedManualWhatsAppRecords = new Map();

function escapeManualWhatsAppValue(value) {
    return String(value ?? '').replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[char];
    });
}

// ✅ DATATABLE
var table = $('#trainers-table').DataTable({
    processing: true,
    serverSide: true,
    // ajax: "{{ route('admin.manual_data.index') }}",
    ajax: {
    url: "{{ route('admin.manual_data.index') }}",
    data: function (d) {
        d.email = $('input[name=email]').val();
        d.mobile = $('input[name=mobile]').val();
        d.gender = $('select[name=gender]').val();
        d.college_name = $('select[name=college_name]').val();
        d.course_type = $('select[name=course_type]').val();
        d.class = $('select[name=class]').val();
        d.semester = $('select[name=semester]').val();
        d.range = $('select[name=range]').val();
        d.from_date = $('input[name=from_date]').val();
        d.to_date = $('input[name=to_date]').val();
        d.is_moved = $('select[name=is_moved]').val();
    }
},

    columns: [

        // ✅ CHECKBOX COLUMN
        {
    data: 0,

    render: function (data, type, row) {

        let checked = selectedIds.has(String(row.row_id))
            ? 'checked'
            : '';

        return `
            <div class="d-inline-flex align-items-center gap-1">

                <input type="checkbox"
                       id="${row.row_id}"
                       class="record_checkbox"
                       value="${row.row_id}"
                       data-name="${escapeManualWhatsAppValue(row[1])}"
                       data-contact="${escapeManualWhatsAppValue(row[4])}"
                       ${checked}>

                ${
                    row.is_moved_to_enquiry == 1
                    ? `
                          <i class="fas fa-arrow-right-arrow-left text-warning"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           title="Moved">
                        </i>
                      `
                    : ''
                }

            </div>
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
        { data: 10 }

    ]
});

// ======================================
// RESTORE SELECTION AFTER DATATABLE DRAW
// ======================================
table.on('draw.dt', function () {

    $('.record_checkbox').each(function () {

        let id = String($(this).val());

        $(this).prop(
            'checked',
            selectedIds.has(id)
        );

    });

    syncCheckAll();

});
$('#filterForm input, #filterForm select').on('keyup change', function () {
    table.ajax.reload();
});

// Show custom date inputs only when Custom is selected.
function toggleManualCustomDates() {
    const isCustom = $('#manualDateRange').val() === 'custom';
    $('.manual-custom-date').toggle(isCustom);

    if (!isCustom) {
        $('input[name=from_date], input[name=to_date]').val('');
    }
}

$('#manualDateRange').on('change', toggleManualCustomDates);
toggleManualCustomDates();


// ======================================
// SELECT / UNSELECT SINGLE RECORD
// ======================================
$(document).on('change', '.record_checkbox', function () {

    let id = String($(this).val());

    if ($(this).is(':checked')) {

        selectedIds.add(id);
        selectedRecordIds.add(id);
        selectedManualWhatsAppRecords.set(id, {
            id: id,
            name: $(this).attr('data-name') || '',
            contact: $(this).attr('data-contact') || ''
        });

    } else {

        selectedIds.delete(id);
        selectedRecordIds.delete(id);
        selectedManualWhatsAppRecords.delete(id);
    }

    syncCheckAll();

});


// ======================================
// SYNC SELECT ALL FOR CURRENT PAGE
// ======================================
function syncCheckAll() {

    let currentPageCheckboxes = $('.record_checkbox');

    if (currentPageCheckboxes.length === 0) {

        $('#checkAll').prop('checked', false);

        return;
    }

    let checkedCount =
        currentPageCheckboxes.filter(':checked').length;

    $('#checkAll').prop(
        'checked',
        checkedCount === currentPageCheckboxes.length
    );
    console.log(checkedCount);
}

 
// ======================================
// SELECT ALL - CURRENT PAGE ONLY
// ======================================
$('#checkAll').on('change', function () {

    let checked = this.checked;

    $('.record_checkbox').each(function () {

        let id = String($(this).val());

        $(this).prop('checked', checked);

        if (checked) {

            selectedIds.add(id);
            selectedRecordIds.add(id);
            selectedManualWhatsAppRecords.set(id, {
                id: id,
                name: $(this).attr('data-name') || '',
                contact: $(this).attr('data-contact') || ''
            });

        } else {

            selectedIds.delete(id);
            selectedRecordIds.delete(id);
             selectedIds.delete(id);
            selectedRecordIds.delete(id);
            selectedManualWhatsAppRecords.delete(id);

        }

    });

    console.log(
        'Select All:',
        checked,
        'Selected IDs:',
        Array.from(selectedIds)
    );

    syncCheckAll();

});
$('#exportExcel').click(function () {

    let query = $('#filterForm').serialize();

    window.location.href = "{{ route('admin.manual_data.export') }}?" + query;
});

// ✅ MOVE TO ENQUIRY
$('#moveToEnquiry').click(function () {

    if (selectedIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }

    let sessionOptions = @json($sessionsList ?? []);
    let optionsHtml = '';

    Object.keys(sessionOptions).forEach(function(key) {
        optionsHtml += `<option value="${key}">${sessionOptions[key]}</option>`;
    });

    Swal.fire({
        title: 'Select Session',
        html: `<select id="session_id" class="form-control">${optionsHtml}</select>`,
        showCancelButton: true,
        confirmButtonText: 'Move'
    }).then((result) => {

        if (!result.isConfirmed) return;

        let session_id = $('#session_id').val();

        if (!session_id) {
            Swal.fire('Error', 'Session is required', 'error');
            return;
        }

        $.ajax({
            url: "{{ route('admin.manual_data.move.enquiries') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: Array.from(selectedIds),
                session_id: session_id
            },
            success: function (res) {
                Swal.fire('Success', res.message, 'success');
                location.reload();
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });

    });

});

$('#resetFilters').click(function () {

    // Reset all inputs
    $('#filterForm')[0].reset();

    // If using select2 (optional)
    $('#filterForm select').val('').trigger('change');

    // Reload table
    table.ajax.reload();
});

 // ======================================
// BULK WHATSAPP - MANUAL DATA
// ======================================

function formatManualWhatsAppMobile(mobile) {

    let number = String(mobile || '').replace(/\D/g, '');

    if (number.length === 10) {
        number = '91' + number;
    } else if (number.length === 11 && number.startsWith('0')) {
        number = '91' + number.substring(1);
    }

    return /^91[6-9]\d{9}$/.test(number) ? number : null;
}


function openManualWhatsAppChat(record, message, appendName) {

    const mobile = formatManualWhatsAppMobile(record.contact);

    if (!mobile) {
        return false;
    }

    let finalMessage = message;

    if (appendName && record.name) {
        finalMessage = 'Hi ' + record.name + ',\n\n' + message;
    }

    const url = 'https://web.whatsapp.com/send?phone='
        + mobile
        + '&text='
        + encodeURIComponent(finalMessage);

    const chatWindow = window.open(url, '_blank');

    return !!chatWindow;
}


function startManualWhatsApp(records) {

    if (!records || records.length === 0) {
        Swal.fire(
            'No Selection',
            'Please select at least one record.',
            'warning'
        );
        return;
    }

    const validRecords = records.filter(record =>
        formatManualWhatsAppMobile(record.contact)
    );

    const invalidCount = records.length - validRecords.length;

    if (validRecords.length === 0) {
        Swal.fire(
            'Invalid Mobile Numbers',
            'No selected record has a valid Indian mobile number.',
            'warning'
        );
        return;
    }

    Swal.fire({

        title: 'Send WhatsApp Message',

        html: `
            <div class="text-start">

                <label class="form-label">
                    Message
                </label>

                <textarea
                    id="manualWhatsappMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message..."
                ></textarea>

                <div class="form-check mt-3">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="manualWhatsappAppendName"
                        checked
                    >

                    <label
                        class="form-check-label"
                        for="manualWhatsappAppendName"
                    >
                        Add student name
                    </label>

                </div>

                <p class="text-muted mt-3 mb-0">
                    Selected: ${records.length}<br>
                    Valid: ${validRecords.length}<br>
                    Invalid: ${invalidCount}
                </p>

            </div>
        `,

        showCancelButton: true,
        confirmButtonText: 'Start Sending',
        cancelButtonText: 'Cancel',
        reverseButtons: false,

        preConfirm: () => {

            const message = document
                .getElementById('manualWhatsappMessage')
                .value.trim();

            const appendName = document
                .getElementById('manualWhatsappAppendName')
                .checked;

            if (!message) {
                Swal.showValidationMessage(
                    'Please enter a message.'
                );
                return false;
            }

            const opened = openManualWhatsAppChat(
                validRecords[0],
                message,
                appendName
            );

            if (!opened) {
                Swal.showValidationMessage(
                    'Unable to open WhatsApp chat.'
                );
                return false;
            }

            return {
                message: message,
                appendName: appendName
            };
        }

    }).then(result => {

        if (!result.isConfirmed) {
            return;
        }

        let currentIndex = 1;

        function openNextStudent() {

            if (currentIndex >= validRecords.length) {

                Swal.fire({
                    icon: 'success',
                    title: 'All Chats Opened',
                    text:
                        `Opened ${validRecords.length} valid WhatsApp chats. ` +
                        `Invalid numbers skipped: ${invalidCount}.`
                });

                return;
            }

            const currentRecord = validRecords[currentIndex];

            Swal.fire({

                title: 'WhatsApp Sending',

                html: `
                    <p>
                        Chat ${currentIndex + 1}
                        of ${validRecords.length}
                    </p>

                    <p>
                        <strong>
                            ${$('<div>').text(currentRecord.name || 'Student').html()}
                        </strong>
                    </p>

                    <p class="text-muted">
                        ${$('<div>').text(currentRecord.contact || '').html()}
                    </p>

                    <p>
                        Send the message in WhatsApp, then click
                        <strong>Open Next Student</strong>.
                    </p>
                `,

                showCancelButton: true,
                confirmButtonText: 'Open Next Student',
                cancelButtonText: 'Finish',
                reverseButtons: false,

                preConfirm: () => {

                    const opened = openManualWhatsAppChat(
                        currentRecord,
                        result.value.message,
                        result.value.appendName
                    );

                    if (!opened) {
                        Swal.showValidationMessage(
                            'Unable to open this WhatsApp chat.'
                        );
                        return false;
                    }

                    currentIndex++;
                }

            }).then(nextResult => {

                if (nextResult.isConfirmed) {
                    openNextStudent();
                }

            });
        }

        if (validRecords.length > 1) {
            openNextStudent();
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Chat Opened',
                text: 'The WhatsApp chat has been opened.'
            });
        }

    });
}


// SEND WHATSAPP BUTTON

$('#sendManualWhatsApp').on('click', function () {

    const selectedRecords = Array.from(
        selectedManualWhatsAppRecords.values()
    );

    if (selectedRecords.length === 0) {

        Swal.fire({
            icon: 'warning',
            text: 'Please select at least 1 record.'
        });

        return;
    }

    startManualWhatsApp(selectedRecords);

});

 

$('#bulkManualActionBtn').on('click', function () {

    if (selectedIds.size === 0) {

        showPopup('Please select at least one record.');

        return;
    }

    // Show selected count
    $('#manualSelectedCount')
        .text(selectedIds.size);

    // Reset modal
    $('#manualBulkAction').val('');

    $('#manualMoveSection').hide();

    // $('#manualCloseSection').hide();

    $('#manualSessionId').val('');

    // $('#manualCloseReason').val('');

    // Open modal
    let modal = new bootstrap.Modal(
        document.getElementById('moveManualDataModal')
    );

    modal.show();

});

$('#manualBulkAction').on('change', function () {

    let action = $(this).val();

    $('#manualMoveSection').hide();

    // $('#manualCloseSection').hide();


    if (action === 'move') {

        $('#manualMoveSection').show();

    }


    if (action === 'close') {

        // $('#manualCloseSection').show();

    }

});

$('#moveManualDataForm').on('submit', function (e) {

    e.preventDefault();


    let action = $('#manualBulkAction').val();

    let ids = Array.from(selectedIds);


    // --------------------------------
    // CHECK IDS
    // --------------------------------

    if (ids.length === 0) {

        showPopup('Please select at least one record.');

        return;
    }


    // --------------------------------
    // CHECK ACTION
    // --------------------------------

    if (!action) {

        showPopup('Please select an action.');

        return;
    }


    // --------------------------------
    // MOVE VALIDATION
    // --------------------------------

    if (action === 'move') {

        let sessionId =
            $('#manualSessionId').val();

        if (!sessionId) {

            showPopup('Please select a session.');

            return;
        }

    }


    // --------------------------------
    // CLOSE VALIDATION
    // --------------------------------

    // if (action === 'close') {

    //     let reason =
            $('#manualCloseReason').val().trim();

    //     if (!reason) {

    //         showPopup('Please enter close reason.');

    //         return;
    //     }

    // }


    // --------------------------------
    // PREPARE DATA
    // --------------------------------

    let formData = {

        _token: "{{ csrf_token() }}",

        ids: ids,

        action: action

    };


    if (action === 'move') {

        formData.session_id =
            $('#manualSessionId').val();

    }


    // if (action === 'close') {

        formData.reason =
            $('#manualCloseReason').val().trim();

    // }


    // --------------------------------
    // SUBMIT
    // --------------------------------

    $('#manualBulkSubmit')
        .prop('disabled', true)
        .text('Processing...');


    $.ajax({

        url: "{{ route('admin.manual_data.bulk.action') }}",

        type: "POST",

        data: formData,


        success: function (response) {

            // Clear selections
            selectedIds.clear();
            selectedRecordIds.clear();

            $('#checkAll')
                .prop('checked', false);

            $('.record_checkbox')
                .prop('checked', false);


            // Close modal
            let modalElement =
                document.getElementById(
                    'moveManualDataModal'
                );

            let modal =
                bootstrap.Modal.getInstance(
                    modalElement
                );

            if (modal) {
                modal.hide();
            }


            // Reload table
            table.ajax.reload(null, false);


            showPopup(
                response.message ||
                'Operation completed successfully.'
            );

        },


        error: function (xhr) {

            let message =
                xhr.responseJSON?.message ||
                'Something went wrong.';


            /*
            |--------------------------------------------------------------------------
            | Clear selection on error
            |--------------------------------------------------------------------------
            */

            selectedIds.clear();
            selectedRecordIds.clear();

            $('#checkAll')
                .prop('checked', false);

            $('.record_checkbox')
                .prop('checked', false);


            showPopup(message);

        },


        complete: function () {

            $('#manualBulkSubmit')
                .prop('disabled', false)
                .text('Save');

        }

    });

});

// ======================================
 // BULK DELETE MANUAL DATA (SOFT DELETE)
 // ======================================
$('#bulkDeleteManualData').on('click', function () {

    let ids = Array.from(selectedIds);

    if (ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            text: 'Please select at least 1 record.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'The selected records will be soft-deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: false
    }).then((result) => {

        if (!result.isConfirmed) {
            return;
        }

        $.ajax({
            url: "{{ route('admin.manual_data.bulkDelete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                ids: ids
            },
            success: function (response) {

                selectedIds.clear();
                selectedRecordIds.clear();

                $('#checkAll').prop('checked', false);

                Swal.fire({
                    icon: 'success',
                    title: 'Deleted Successfully',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    table.ajax.reload(null, false);
                });
            },
            error: function (xhr) {
                let message = xhr.responseJSON?.message
                    || 'Unable to delete the selected records.';

                Swal.fire({
                    icon: 'error',
                    title: 'Delete Failed',
                    text: message
                });
            }
        });
    });
});

function showPopup($msg){
    Swal.fire({
            icon: 'warning',
            text: $msg
        });

        return;

}

// ======================================
// INDIVIDUAL DELETE - SWEETALERT
// ======================================
$(document).on('submit', '.manual-data-delete-form', function (e) {
    e.preventDefault();

    const form = this;

    Swal.fire({
        title: 'Are you sure?',
        text: 'This record will be deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: false
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});
</script>

@endpush