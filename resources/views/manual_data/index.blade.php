@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Manual Data</h1>
    </div>

    <div class="col-md-8 text-end">
        @include('partials.whatsapp-popover', [
                'add_margin' => 'mb-3'
            ])
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




// ✅ SELECT SINGLE
// $(document).on('change', '.record_checkbox', function () {
//     let id = $(this).val();
//     let record_id = $(this).attr('id');
//     // console.log('Checkbox changed:', { id, record_id, checked: $(this).is(':checked') });
//     if ($(this).is(':checked')) {
//         selectedIds.add(id);
//         selectedRecordIds.add(record_id);
//     } else {
//         selectedIds.delete(id);
//         selectedRecordIds.delete(record_id);
//     }
// });


// ======================================
// SELECT / UNSELECT SINGLE RECORD
// ======================================
$(document).on('change', '.record_checkbox', function () {

    let id = String($(this).val());

    if ($(this).is(':checked')) {

        selectedIds.add(id);
        selectedRecordIds.add(id);

    } else {

        selectedIds.delete(id);
        selectedRecordIds.delete(id);
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


// ✅ SELECT ALL
// $('#checkAll').on('change', function () {
//     let checked = this.checked;

//     $('.record_checkbox').each(function () {
//         let id = $(this).val();
//         let record_id = $(this).attr('id');
//         if (checked) {
//             selectedIds.add(id);
//             selectedRecordIds.add(record_id);
//         } else {
//             selectedIds.delete(id);
//             selectedRecordIds.delete(record_id);
//         }

//         $(this).prop('checked', checked);
//     });
// });

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

        } else {

            selectedIds.delete(id);
            selectedRecordIds.delete(id);

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

new bootstrap.Popover(document.getElementById('whatsappBtn'), {
    html: true,
    sanitize: false,
    customClass: 'whatsapp-popover',
    content: function () {
        return $('#whatsappPopoverContent').html();
    }
});

$(document).on('click', '#sendWhatsappNotification', function () {
    
    let popover = $(this).closest('.popover');
    let custom_message = popover.find('textarea[name="customMessage"]').val();
    let append_name = $('input[name="append_name"]:checked').val();

    if (selectedRecordIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }
    
    if (custom_message?.length > 0) {
        custom_message = custom_message;
    } else {
        Swal.fire('Message Required', 'Please enter a custom message', 'warning');
        return;
    }

    let formData = new FormData();
    formData.append('append_name',append_name);
    formData.append('message',custom_message);
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('message_type', append_name ? 'with_name' : 'same_message');
    Array.from(selectedRecordIds).forEach(function(id) {
        formData.append('ids[]', id);
    });
    formData.append('existing_file_path', popover.find('#existingFile').val());
    let fileInput = popover.find('input[type="file"]')[0];
    // alert(fileInput.files.length);
    if (fileInput && fileInput.files.length > 0) {
        formData.append('whatsappFile', fileInput.files[0]);
        console.log(fileInput.files[0].name);
    }

    $.ajax({
        url: "{{ route('admin.message.send_whatsapp') }}",
        type: "POST",
        processData: false,
        contentType: false,
        data: formData,
        beforeSend: function () {
            popover.find('#message_loader').show();
            popover.find('#sendWhatsappNotification').prop('disabled', true);
        },
        success: function (res) {
            if(res.status === 'error' || res.status === false) {
                Swal.fire('Error', res.message, 'error');
                return;
            }
            Swal.fire('Success', res.message, 'success');
        },
        error: function () {
            Swal.fire('Error', 'Something went wrong', 'error');
        },
        complete: function () {
            popover.find('#message_loader').hide();
            popover.find('#sendWhatsappNotification').prop('disabled', false);
        }
    });
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