@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">

    <div class="col-md-4">
        <h1 class="page_heading">Hard Data</h1>
    </div>

    <div class="col-md-8 text-end">
        <button id="moveToEnquiry" class="btn btn-warning mb-3">
            Move to Enquiries
        </button>

        <button type="button"
                id="bulkHardActionBtn"
                class="btn btn-warning mb-3">
            Move / Close Selected
        </button>

        <button type="button"
                id="bulkDeleteHardDataBtn"
                class="btn btn-danger mb-3">
            Bulk Delete
        </button>

        <a href="{{ route('admin.hard_data.create') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Add
        </a>
        <a href="{{ route('admin.hard_data.import.form') }}"
           class="btn btn-primary mb-3"
           style="background:#6b51df;">
            Import
        </a>
               <a href="#" id="exportExcel"
   class="btn btn-success mb-3">
    Export Excel
</a>
    </div>
<form id="filterForm" class="row mb-3 gap-1">
   

    {{-- College names stored in Hard Data --}}
    <div class="col-md-2">
        <select name="college_name" class="form-control select2">
            <option value="">All Colleges</option>
            @foreach($collegeNames as $collegeName)
                <option value="{{ $collegeName }}">{{ $collegeName }}</option>
            @endforeach
        </select>
    </div>

    {{-- COURSE TYPE --}}
    <div class="col-md-2">
        <select name="course_type" class="form-control">
            <option value="">All Course</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
        </select>
    </div>

    {{-- SEMESTER FILTER --}}
    <div class="col-md-2">
        <select name="semester" class="form-control">
            <option value="">All Semesters</option>
            @for($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}">Semester {{ $i }}</option>
            @endfor
        </select>
    </div>

    {{-- CLASS FILTER --}}
    <div class="col-md-2">
        <select name="class" class="form-control">
            <option value="">All Classes</option>
            @foreach(($classNames ?? collect()) as $className)
                <option value="{{ $className }}">{{ $className }}</option>
            @endforeach
        </select>
    </div>

    {{-- SPECIFIC DATE FILTER --}}
    <!-- <div class="col-md-2">
        <input type="date"
               name="date"
               class="form-control"
               title="Select Specific Date">
    </div> -->

    {{-- DATE RANGE FILTER --}}
    <div class="col-md-2">
        <select name="range" class="form-control">
            <option value="">All Dates</option>
            <option value="today">Today</option>
            <option value="yesterday">Yesterday</option>
            <option value="this_week">This Week</option>
            <option value="last_week">Last Week</option>
            <option value="last_7_days">Last 7 Days</option>
            <option value="last_30_days">Last 30 Days</option>
            <option value="this_month">This Month</option>
            <option value="custom">Custom Date Range</option>
        </select>
    </div>

    {{-- CUSTOM DATE FILTER --}}
    <div class="col-md-2 custom-date-filter" style="display:none;">
        <input type="date"
               name="from_date"
               class="form-control"
               title="From Date">
    </div>

    <div class="col-md-2 custom-date-filter" style="display:none;">
        <input type="date"
               name="to_date"
               class="form-control"
               title="To Date">
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
        <input type="text" name="mobile" class="form-control mt-2" placeholder="Mobile">
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
<!-- Move / Close Hard Data Modal -->

<div class="modal fade"
     id="moveHardDataModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog">

        <form id="moveHardDataForm">

            @csrf

            <input type="hidden"
                   name="ids"
                   id="hardBulkIds">

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

                    <div class="mb-3">

                        <label class="form-label">
                            Action
                        </label>

                        <select class="form-control"
                                name="action"
                                id="hardBulkAction"
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

                    <div id="hardMoveSection"
                         style="display:none;">

                        <div class="mb-3">

                            <label class="form-label">
                                Session
                            </label>

                            <select class="form-control"
                                    name="session_id"
                                    id="hardSessionId">

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

                    <div id="hardCloseSection"
                         style="display:block;">

                        <div class="mb-3">

                            <label class="form-label">
                                Reason
                            </label>

                            <textarea class="form-control"
                                      name="reason"
                                      id="hardCloseReason"
                                      rows="3"
                                      placeholder="Enter reason"></textarea>

                        </div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success"
                            id="hardBulkSubmit">

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
    // ajax: "{{ route('admin.hard_data.index') }}",
    ajax: {
    url: "{{ route('admin.hard_data.index') }}",
    data: function (d) {
        // EXISTING FILTERS
        d.email = $('input[name=email]').val();
        d.mobile = $('input[name=mobile]').val();
        d.gender = $('select[name=gender]').val();
        d.college_name = $('select[name=college_name]').val();
        d.course_type = $('select[name=course_type]').val();
        d.is_moved = $('select[name=is_moved]').val();

        // NEW FILTERS
        d.semester = $('select[name=semester]').val();
        d.class = $('select[name=class]').val();

        // DATE FILTERS
        d.date = $('input[name=date]').val();
        d.range = $('select[name=range]').val();
        d.from_date = $('input[name=from_date]').val();
        d.to_date = $('input[name=to_date]').val();
    }
},
    columns: [

        // ✅ CHECKBOX COLUMN
        {
    data: 0,

    render: function (data, type, row) {

        let id = String(row.id);

        let checked = selectedIds.has(id)
            ? 'checked'
            : '';

        return `
            <div class="d-inline-flex align-items-center gap-1">

                <input type="checkbox"
                       id="${id}"
                       class="record_checkbox"
                       value="${id}"
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
// ======================================
// FILTER CHANGE HANDLING
// ======================================

// Show custom date fields only when Custom Date Range is selected.
function toggleCustomDateFields() {
    let range = $('select[name=range]').val();

    if (range === 'custom') {
        $('.custom-date-filter').show();
    } else {
        $('.custom-date-filter').hide();
        $('input[name=from_date]').val('');
        $('input[name=to_date]').val('');
    }
}

// Keep specific date, predefined range and custom range mutually exclusive.
$('#filterForm').on('change', 'select[name=range]', function () {
    $('input[name=date]').val('');
    toggleCustomDateFields();
    table.ajax.reload();
});

$('#filterForm').on('change', 'input[name=date]', function () {
    if ($(this).val()) {
        $('select[name=range]').val('');
        $('input[name=from_date]').val('');
        $('input[name=to_date]').val('');
        toggleCustomDateFields();
    }
    table.ajax.reload();
});

$('#filterForm').on('change', 'input[name=from_date], input[name=to_date]', function () {
    $('input[name=date]').val('');
    $('select[name=range]').val('custom');
    toggleCustomDateFields();
    table.ajax.reload();
});

// Existing text/select filters continue to reload automatically.
$('#filterForm').on('keyup change', 'input:not([name=date]):not([name=from_date]):not([name=to_date]), select:not([name=range])', function () {
    table.ajax.reload();
});

// Set correct custom-date visibility on page load.
toggleCustomDateFields();

// ✅ SELECT SINGLE
// $(document).on('change', '.record_checkbox', function () {
//     let id = $(this).val();

//     if ($(this).is(':checked')) {
//         selectedIds.add(id);
//     } else {
//         selectedIds.delete(id);
//     }
// });

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

function syncCheckAll() {

    let currentPageCheckboxes =
        $('.record_checkbox');

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

}


// ✅ SELECT ALL
// $('#checkAll').on('change', function () {
//     let checked = this.checked;

//     $('.record_checkbox').each(function () {
//         let id = $(this).val();

//         if (checked) {
//             selectedIds.add(id);
//         } else {
//             selectedIds.delete(id);
//         }

//         $(this).prop('checked', checked);
//     });
// });

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
            url: "{{ route('admin.hard_data.move.enquiries') }}",
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
$('#exportExcel').click(function () {

    let query = $('#filterForm').serialize();

    window.location.href = "{{ route('admin.hard_data.export') }}?" + query;
});

$('#resetFilters').click(function () {

    // Reset all filters without changing existing form or DataTable variables.
    $('#filterForm')[0].reset();

    // Refresh Select2 if it is used, without triggering multiple AJAX reloads.
    $('#filterForm select').val('').trigger('change.select2');

    // Clear and hide custom date fields.
    $('input[name=from_date]').val('');
    $('input[name=to_date]').val('');
    $('.custom-date-filter').hide();

    // Reload table once with all filters cleared.
    table.ajax.reload();
});

$('#bulkHardActionBtn').on('click', function () {

    if (selectedIds.size === 0) {

        showPopup(
            'Please select at least one record.'
        );

        return;
    }

    $('#hardBulkAction').val('');

    $('#hardMoveSection').hide();

    // Same behavior as your final Manual Data
    $('#hardCloseSection').show();

    $('#hardSessionId').val('');

    $('#hardCloseReason').val('');

    let modal = new bootstrap.Modal(
        document.getElementById('moveHardDataModal')
    );

    modal.show();

});

$('#hardBulkAction').on('change', function () {

    let action = $(this).val();

    $('#hardMoveSection').hide();

    if (action === 'move') {

        $('#hardMoveSection').show();

    }

    if (action === 'close') {

        // Reason is optional
        $('#hardCloseSection').show();

    }

});

$('#moveHardDataForm').on('submit', function (e) {

    e.preventDefault();

    let action = $('#hardBulkAction').val();

    let ids = Array.from(selectedIds);


    if (ids.length === 0) {

        showPopup(
            'Please select at least one record.'
        );

        return;
    }


    if (!action) {

        showPopup(
            'Please select an action.'
        );

        return;
    }


    // MOVE

    if (action === 'move') {

        let sessionId =
            $('#hardSessionId').val();

        if (!sessionId) {

            showPopup(
                'Please select a session.'
            );

            return;
        }

    }


    let formData = {

        _token: "{{ csrf_token() }}",

        ids: ids,

        action: action

    };


    if (action === 'move') {

        formData.session_id =
            $('#hardSessionId').val();

    }


    if (action === 'close') {

        // OPTIONAL
        formData.reason =
            $('#hardCloseReason').val().trim();

    }


    $('#hardBulkSubmit')
        .prop('disabled', true)
        .text('Processing...');


    $.ajax({

        url: "{{ route('admin.hard_data.bulk.action') }}",

        type: "POST",

        data: formData,


        success: function (response) {

            selectedIds.clear();
            selectedRecordIds.clear();

            $('#checkAll')
                .prop('checked', false);

            $('.record_checkbox')
                .prop('checked', false);


            let modalElement =
                document.getElementById(
                    'moveHardDataModal'
                );

            let modal =
                bootstrap.Modal.getInstance(
                    modalElement
                );

            if (modal) {

                modal.hide();

            }


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

            showPopup(message);

        },


        complete: function () {

            $('#hardBulkSubmit')
                .prop('disabled', false)
                .text('Save');

        }

    });

});

function showPopup($msg) {

    Swal.fire({

        icon: 'warning',

        text: $msg

    });

}
// Individual delete confirmation
$(document).on('submit', '.hard-data-delete-form', function (e) {
    e.preventDefault();

    const form = this;

    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this record?',
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


// Bulk delete
$('#bulkDeleteHardDataBtn').on('click', function () {

    if (selectedIds.size === 0) {
        Swal.fire({
            icon: 'warning',
            text: 'Please select at least 1 record.'
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: 'The selected records will be deleted.',
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
            url: "{{ route('admin.hard_data.bulkDelete') }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                ids: Array.from(selectedIds)
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
                });

                table.ajax.reload(null, false);
            },

            error: function (xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Unable to Delete',
                    text: xhr.responseJSON?.message
                        || 'Something went wrong.'
                });
            }
        });
    });
});
</script>

@endpush