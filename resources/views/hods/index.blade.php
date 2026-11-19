@extends('layouts.app')

@section('content')

<div class="container">
<!-- 
    <h1 class="page_heading mb-3">Authority Management</h1>
     <div class="col-md-4 text-end">
            <button type="button"
                    id="sendHodWhatsApp"
                    class="btn btn-success">
                <i class="fab fa-whatsapp"></i> Send WhatsApp HOD
            </button>

            <button type="button"
                    id="sendTpoWhatsApp"
                    class="btn btn-success">
                <i class="fab fa-whatsapp"></i> Send WhatsApp TPO
            </button>
            <button type="button"
                    id="deleteSelected"
                    class="btn btn-danger">
                Delete Selected
            </button>

            <a href="{{ route('hods.create') }}"
               class="btn btn-primary">
                Add Authority
            </a>

        </div>
    {{-- FILTER FORM --}}
    <form method="GET" id="filterForm" class="row mb-3">

        <div class="col-md-4">
            <select name="state" id="stateFilter" class="form-control">

                <option value="">All States</option>

                @foreach($states as $state)

                    <option value="{{ $state->name }}"
                        {{ request('state') == $state->name ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>

                @endforeach

            </select>
        </div>


        <div class="col-md-4">

            <a href="{{ route('hods.index') }}"
               class="btn btn-secondary">
                Reset
            </a>

        </div>

    </form> -->

{{-- TOP ROW --}}
<div class="row mb-3 align-items-center">
    <div class="col-md-4">
        <h1 class="page_heading mb-0">Authority Management</h1>
    </div>

    <div class="col-md-8 text-end">
        <button type="button"
                id="sendHodWhatsApp"
                class="btn btn-success">
            <i class="fab fa-whatsapp"></i> Send WhatsApp HOD
        </button>

        <button type="button"
                id="sendTpoWhatsApp"
                class="btn btn-success">
            <i class="fab fa-whatsapp"></i> Send WhatsApp TPO
        </button>

        <button type="button"
                id="deleteSelected"
                class="btn btn-danger">
            Delete Selected
        </button>

        <a href="{{ route('hods.create') }}"
           class="btn btn-primary">
            Add Authority
        </a>
    </div>
</div>

{{-- SECOND ROW: FILTER --}}
<form method="GET" id="filterForm" class="row mb-3">

    <div class="col-md-4">
        <select name="state" id="stateFilter" class="form-control">
            <option value="">All States</option>

            @foreach($states as $state)
                <option value="{{ $state->name }}"
                    {{ request('state') == $state->name ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <a href="{{ route('hods.index') }}" class="btn btn-secondary">
            Reset
        </a>
    </div>

</form>


    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- TABLE --}}
    <div class="table-responsive">

        <table class="table table-bordered table-striped w-100"
               id="hodtable">

            <thead>

                <tr>

                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>

                    <th>#</th>

                    <th>College</th>

                    <th>State</th>

                    <th>District</th>

                    <th>TPO</th>

                    <th>Contact</th>

                    <th>HOD</th>

                    <th>Contact</th>

                    <th width="120">Action</th>

                </tr>

            </thead>


            <tbody>

                @foreach($hods as $hod)

                    <tr>

                        <td>
                            <input type="checkbox"
                                   class="hod-checkbox"
                                   value="{{ $hod->id }}">
                        </td>


                        <td>
                            {{ $loop->iteration }}
                        </td>


                        <td>
                            {{ $hod->college->college_name ?? '-' }}
                        </td>


                        <td>
                            {{ $hod->college->state->name ?? '-' }}
                        </td>


                        <td>
                            {{ $hod->college->district->name ?? '-' }}
                        </td>


                        <td>
                            <strong></strong>
                            {{ $hod->tpo_name ?? '-' }}

                            <br>

                            <small>
                                {{ $hod->tpo_gender ?? '' }}
                            </small>
                        </td>


                        <td>
                            {{ $hod->tpo_contact ?? '-' }}
                        </td>


                        <td>
                            <strong></strong>
                            {{ $hod->hod_name ?? '-' }}

                            <br>

                            <small>
                                {{ $hod->hod_gender ?? '' }}
                            </small>
                        </td>


                        <td>
                            {{ $hod->hod_contact ?? '-' }}
                        </td>


                        <td class="text-center">

                            <a href="{{ route('hods.edit', $hod->id) }}"
                               class="btn btn-sm"
                               title="Edit">

                                <i class="fas fa-edit"></i>

                            </a>


                            <form action="{{ route('hods.destroy', $hod->id) }}"
                                  method="POST"
                                  style="display:inline-block;"
                                  class="hod-delete-form">

                                @csrf

                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm"
                                        title="Delete">

                                    <i class="fas fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>

@endsection


@push('styles')

<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<style>

    #hodtable {
        width: 100% !important;
    }

    #hodtable_wrapper {
        width: 100%;
    }

</style>

@endpush


@push('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | Selected HODs
    |--------------------------------------------------------------------------
    */

    let selectedHods = {};


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#hodtable').DataTable({
    pageLength: 50,

    lengthMenu: [10, 25, 50, 100],

    order: [[1, 'asc']],

    autoWidth: true,

    scrollX: true,

    columnDefs: [
        {
            targets: 0,
            orderable: false,
            searchable: false
        }
    ],

    drawCallback: function () {

        $('#hodtable tbody .hod-checkbox').each(function () {

            let id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedHods[id]
            );
        });

        updateCheckAll();
    }
});

// GET HOD AND TPO DETAILS FROM DATATABLE ROW
function getAuthorityWhatsAppDetails(row) {

    function getName(value) {
        return $('<div>')
            .html(value || '')
            .clone()
            .children()
            .remove()
            .end()
            .text()
            .trim();
    }

    return {
        tpo: {
            name: getName(row[5]),
            mobile: $('<div>').html(row[6] || '').text().trim()
        },
        hod: {
            name: getName(row[7]),
            mobile: $('<div>').html(row[8] || '').text().trim()
        }
    };
}
    /*
    |--------------------------------------------------------------------------
    | Individual checkbox
    |--------------------------------------------------------------------------
    */

    // $(document).on(
    //     'change',
    //     '#hodtable tbody .hod-checkbox',
    //     function () {

    //         let id = $(this).val();

    //         if ($(this).is(':checked')) {

    //             selectedHods[id] = true;

    //         } else {

    //             delete selectedHods[id];

    //         }

    //         updateCheckAll();

    //     }
    // );
    // INDIVIDUAL CHECKBOX
$('#hodtable').on('change', '.hod-checkbox', function () {

    let id = String($(this).val());

    if ($(this).is(':checked')) {

        let row = table.row($(this).closest('tr')).data();

        selectedHods[id] = getAuthorityWhatsAppDetails(row);

    } else {

        delete selectedHods[id];

    }

    updateCheckAll();

});

    /*
    |--------------------------------------------------------------------------
    | Select All - CURRENT PAGE ONLY
    |--------------------------------------------------------------------------
    */

    // $('#checkAll').on('change', function () {

    //     let checked = $(this).is(':checked');


    //     $('#hodtable tbody .hod-checkbox').each(function () {

    //         let id = $(this).val();

    //         $(this).prop('checked', checked);


    //         if (checked) {

    //             selectedHods[id] = true;

    //         } else {

    //             delete selectedHods[id];

    //         }

    //     });

    // });
    // SELECT ALL - CURRENT PAGE ONLY
$('#checkAll').on('change', function () {

    let checked = $(this).is(':checked');

    $('#hodtable tbody .hod-checkbox').each(function () {

        let id = String($(this).val());

        let row = table.row($(this).closest('tr')).data();

        $(this).prop('checked', checked);

        if (checked) {

            selectedHods[id] = getAuthorityWhatsAppDetails(row);

        } else {

            delete selectedHods[id];

        }

    });

    updateCheckAll();

});

    /*
    |--------------------------------------------------------------------------
    | Update Select All
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        let checkboxes =
            $('#hodtable tbody .hod-checkbox');

        let total =
            checkboxes.length;

        let checked =
            checkboxes.filter(':checked').length;


        $('#checkAll').prop(
            'checked',
            total > 0 && total === checked
        );


        $('#checkAll').prop(
            'indeterminate',
            checked > 0 && checked < total
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SELECTED
    |--------------------------------------------------------------------------
    */

    $('#deleteSelected').on('click', function () {

        let ids =
            Object.keys(selectedHods);


        /*
        |--------------------------------------------------------------------------
        | No selection
        |--------------------------------------------------------------------------
        */

        if (ids.length === 0) {

            Swal.fire({

                icon: 'warning',

                title: 'No Record Selected',

                text: 'Please select at least 1 record.'

            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Confirmation
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'warning',

            title: 'Delete Selected Records?',

            text: 'Are you sure you want to delete the selected authorities?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AJAX
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: "{{ route('hods.bulkDelete') }}",

                type: 'POST',

                data: {

                    _token: "{{ csrf_token() }}",

                    ids: ids.join(',')

                },


                success: function (response) {

                    Swal.fire({

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        timer: 1500,

                        showConfirmButton: false

                    });


                    selectedHods = {};


                    $('#checkAll')
                        .prop('checked', false)
                        .prop('indeterminate', false);


                    /*
                    |--------------------------------------------------------------------------
                    | Full reload after delete
                    |--------------------------------------------------------------------------
                    */

                    window.location.reload();

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

    });


    /*
    |--------------------------------------------------------------------------
    | SINGLE DELETE - SWEETALERT
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.hod-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();

            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete Authority?',

                text: 'Are you sure you want to delete this record?',

                showCancelButton: true,

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {

                    HTMLFormElement.prototype.submit.call(form);

                }

            });


            return false;

        }
    );


// ========================================
// WHATSAPP MOBILE NUMBER VALIDATION
// ========================================

function formatAuthorityWhatsAppMobile(mobile) {

    let number = String(mobile || '').replace(/\D/g, '');

    if (number.length === 10) {

        number = '91' + number;

    } else if (number.length === 11 && number.startsWith('0')) {

        number = '91' + number.substring(1);

    }

    return /^91[6-9]\d{9}$/.test(number) ? number : null;
}


// ========================================
// OPEN WHATSAPP CHAT
// ========================================

function openAuthorityWhatsApp(record, message, appendName) {

    let mobile = formatAuthorityWhatsAppMobile(record.mobile);

    if (!mobile) return false;

    let finalMessage = message;

    if (appendName && record.name) {

        finalMessage = 'Hi ' + record.name + ',\n\n' + message;

    }

    let url = 'https://web.whatsapp.com/send?phone=' +
        mobile + '&text=' + encodeURIComponent(finalMessage);

    window.open(url, '_blank');

    return true;
}


// ========================================
// START WHATSAPP SENDING
// ========================================

function startAuthorityWhatsApp(type) {

    let selectedIds = Object.keys(selectedHods);

    if (selectedIds.length === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'No Records Selected',
            text: 'Please select at least 1 authority.'
        });

        return;
    }

    let contactRecords = [];

    selectedIds.forEach(function (id) {

        let authority = selectedHods[id];

        let contact = authority[type];

        contactRecords.push({
            name: contact.name,
            mobile: contact.mobile
        });

    });


    // VALIDATE NUMBERS

    let validRecords = [];

    let invalidCount = 0;

    contactRecords.forEach(function (record) {

        if (formatAuthorityWhatsAppMobile(record.mobile)) {

            validRecords.push(record);

        } else {

            invalidCount++;

        }

    });


    if (validRecords.length === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'No Valid Mobile Numbers',
            text: 'None of the selected records has a valid WhatsApp number.'
        });

        return;
    }


    let contactTitle = type === 'hod' ? 'HOD' : 'TPO';


    // MESSAGE POPUP

    Swal.fire({

        title: 'Send WhatsApp to ' + contactTitle,

        html: `
            <div class="text-start">

                <label class="form-label">Message</label>

                <textarea id="authorityWhatsAppMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message here..."></textarea>

                <div class="form-check mt-3">

                    <input type="checkbox"
                        class="form-check-input"
                        id="authorityAppendName"
                        checked>

                    <label class="form-check-label"
                        for="authorityAppendName">
                        Add ${contactTitle} name
                    </label>

                </div>

                <hr>

                <p>Selected: ${contactRecords.length}</p>
                <p>Valid: ${validRecords.length}</p>
                <p>Invalid: ${invalidCount}</p>

            </div>
        `,

        showCancelButton: true,

        confirmButtonText: 'Start Sending',

        cancelButtonText: 'Cancel',

        reverseButtons: false,

        focusConfirm: false,

        preConfirm: function () {

            let message = document.getElementById(
                'authorityWhatsAppMessage'
            ).value.trim();

            if (!message) {

                Swal.showValidationMessage('Please enter a message.');

                return false;
            }

            let appendName = document.getElementById(
                'authorityAppendName'
            ).checked;

            openAuthorityWhatsApp(
                validRecords[0],
                message,
                appendName
            );

            return {
                message: message,
                appendName: appendName
            };

        }

    }).then(function (result) {

        if (!result.isConfirmed) return;

        let message = result.value.message;

        let appendName = result.value.appendName;

        let currentIndex = 1;


        // OPEN NEXT CHAT SEQUENTIALLY

        function showNext() {

            if (currentIndex >= validRecords.length) {

                Swal.fire({
                    icon: 'success',
                    title: 'Completed',
                    text: 'All ' + contactTitle + ' WhatsApp chats have been processed.'
                });

                return;
            }

            let record = validRecords[currentIndex];

            Swal.fire({

                title: 'WhatsApp Sending',

                html: `
                    <p>Chat ${currentIndex + 1} of ${validRecords.length}</p>

                    <p><strong>${$('<div>').text(record.name || contactTitle).html()}</strong></p>

                    <p>${$('<div>').text(record.mobile).html()}</p>

                    <small>Click Open Next to open the next WhatsApp chat.</small>
                `,

                showCancelButton: true,

                confirmButtonText: 'Open Next',

                cancelButtonText: 'Finish',

                reverseButtons: false

            }).then(function (nextResult) {

                if (nextResult.isConfirmed) {

                    openAuthorityWhatsApp(
                        record,
                        message,
                        appendName
                    );

                    currentIndex++;

                    showNext();

                }

            });

        }

        showNext();

    });

}


// ========================================
// HOD WHATSAPP BUTTON
// ========================================

$('#sendHodWhatsApp').on('click', function () {

    startAuthorityWhatsApp('hod');

});


// ========================================
// TPO WHATSAPP BUTTON
// ========================================

$('#sendTpoWhatsApp').on('click', function () {

    startAuthorityWhatsApp('tpo');

});

    /*
    |--------------------------------------------------------------------------
    | State Filter
    |--------------------------------------------------------------------------
    */

    $('#stateFilter').on('change', function () {

        $('#filterForm').submit();

    });


});

</script>

@endpush