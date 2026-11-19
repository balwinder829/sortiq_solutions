
@extends('layouts.app')

@section('content')

<style>
    table td {
        vertical-align: middle;
        text-transform: capitalize;
    }

    /*.last-download-info {
        font-size: 13px;
        color: #6c757d;
        text-align: right;
    }*/

     table th:last-child,
        table td:last-child {
            min-width: 120px;
            width: 120px;
            white-space: nowrap;
        }

        table td:last-child .btn {
            margin-right: 4px;
        }
</style>

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading mb-0">Services Registrations</h1>
        </div>

        <div class="col-md-6  text-md-end">
            <div class="last-download-info">
                <strong>Last Downloaded:</strong>

                {{ !empty($lastDownloadedAt)
                    ? \Carbon\Carbon::parse($lastDownloadedAt)->format('d M Y, h:i A')
                    : 'Never' }}
            </div>
        </div>

    </div>

    {{-- FILTERS --}}
    <div class="row mb-2">
        <div class="col-md-12">

            <form method="GET" id="filterForm" class="mb-3">

                <div class="row g-2">

                    {{-- SLUG --}}
                    <div class="col-md-3">

                        <select name="slug" class="form-select filterchange">

                            <option value="">All Slugs</option>

                            @foreach($slugs as $slug)

                                <option value="{{ $slug }}"
                                    {{ request('slug') == $slug ? 'selected' : '' }}>

                                    {{ ucfirst($slug) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    {{-- TECHNOLOGY --}}
                    <div class="col-md-3">

                        <select name="technology" class="form-select filterchange">

                            <option value="">All Technologies</option>

                            <option value="Website Development">Website Development</option>
                            <option value="E-commerce Development">E-commerce Development</option>
                            <option value="SEO Services">SEO Services</option>
                            <option value="Google Ads">Google Ads</option>
                            <option value="Social Media Marketing">Social Media Marketing</option>
                            <option value="App Development">App Development</option>
                            <option value="Web Designing">Web Designing</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Digital Marketing">Digital Marketing</option>
                            <option value="MERN Stack Development">MERN Stack Development</option>
                            <option value="PHP Development">PHP Development</option>
                            <option value="Graphic Designing">Graphic Designing</option>
                            <option value="Shopify & WooCommerce Stores">Shopify & WooCommerce Stores</option>
                            <option value="Business Websites & Custom Web Apps">Business Websites & Custom Web Apps</option>
                            <option value="CRM Implementation">CRM Implementation</option>
                            <option value="API Integration">API Integration</option>
                            <option value="Custom Software Development">Custom Software Development</option>
                            <option value="Database Management">Database Management</option>

                        </select>

                    </div>

                    {{-- DATE FILTER --}}
                    <div class="col-md-2">

                        <select id="date_filter" class="form-control">

                            <option value="">Select Date Filter</option>
                            <option value="today">Today</option>
                            <option value="yesterday">Yesterday</option>
                            <option value="this_week">This Week</option>
                            <option value="last_week">Last Week</option>
                            <option value="this_month">This Month</option>
                            <option value="custom">Custom Range</option>

                        </select>

                    </div>

                    {{-- FROM DATE --}}
                    <div class="col-md-2">

                        <input type="date"
                               id="from_date"
                               class="form-control"
                               disabled>

                    </div>

                    {{-- TO DATE --}}
                    <div class="col-md-2">

                        <input type="date"
                               id="to_date"
                               class="form-control"
                               disabled>

                    </div>

                    {{-- DOWNLOAD STATUS --}}
                    <div class="col-md-3">

                        <select name="download_status"
                                id="download_status"
                                class="form-select filterchange">

                            <option value="">All Download Status</option>
                            <option value="downloaded">Downloaded</option>
                            <option value="not_downloaded">Not Downloaded</option>

                        </select>

                    </div>

                    {{-- LIMIT --}}
                    <div class="col-md-2">

                        <select name="limit" class="form-select filterchange">

                            <option value="">All</option>
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="50">50</option>
                            <option value="100">100</option>

                        </select>

                    </div>

                    {{-- BUTTONS --}}
                    <div class="col-md-auto d-flex gap-2 flex-wrap align-items-start">

                        <a href="{{ route('services-registrations.index') }}"
                           class="btn btn-secondary text-nowrap">

                            Reset

                        </a>

                       <!--  <a href="javascript:void(0)"
                           id="exportBtn"
                           class="btn btn-success text-nowrap">

                            <i class="fa fa-download"></i> Export

                        </a>

                        <a href="javascript:void(0)"
                           id="exportSelectedBtn"
                           class="btn btn-success text-nowrap">

                            <i class="fa fa-download"></i> Export Selected

                        </a> -->

                        <a href="javascript:void(0)"
                           id="exportBtn"
                           class="btn btn-success text-nowrap">

                            <i class="fa fa-download"></i> Export

                        </a>
                        
                        <button type="button"
                                id="sendServicesWhatsAppButton"
                                class="btn btn-success text-nowrap">

                            <i class="fab fa-whatsapp"></i> Send WhatsApp

                        </button>

                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button class="btn-close" data-bs-dismiss="alert"></button>

        </div>

    @endif

    {{-- TABLE --}}
    <div class="table-responsive">

        <table class="table table-bordered table-striped" id="internshipTable">

            <thead>

                <tr>

                    <th>
                        <input type="checkbox" id="select-all">
                    </th>

                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Technology</th>
                    <th>Added Date</th>
                    <th>Last Downloaded</th>
                    <th width="100">Actions</th>

                </tr>

            </thead>

        </table>

    </div>

</div>

@endsection


@push('scripts')

{{-- DATATABLE, FILTERS AND SELECTION --}}
<script>

$(document).ready(function () {

    // Maintain selections across DataTable pages.
    const selectedServicesIds = new Set();
    const selectedServicesRecords = new Map();

    // DATATABLE
    let table = $('#internshipTable').DataTable({

        processing: true,
        serverSide: true,

        ajax: {

            url: "{{ route('services-registrations.index') }}",

            data: function (d) {

                d.slug = $('select[name=slug]').val();

                d.technology = $('select[name=technology]').val();

                d.limit = $('select[name=limit]').val();

                d.date_filter = $('#date_filter').val();

                d.from_date = $('#from_date').val();

                d.to_date = $('#to_date').val();

                // DOWNLOAD STATUS FILTER
                d.download_status = $('#download_status').val();

            }

        },

        columnDefs: [

            {
                targets: 0,
                orderable: false,
                searchable: false
            },

            {
                targets: [8, 9],
                orderable: false,
                searchable: false
            }

        ],

        columns: [

            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4 },
            { data: 5 },
            { data: 6 },
            { data: 7 },
            { data: 8 },
            { data: 9, orderable: false, searchable: false }

        ],

        pageLength: 25

    });


    // FILTER CHANGE
    $('.filterchange').on('change', function () {

        table.ajax.reload();

    });


    // DATE FILTER
    $('#date_filter').on('change', function () {

        if ($(this).val() === 'custom') {

            $('#from_date').prop('disabled', false);

            $('#to_date').prop('disabled', false);

        } else {

            $('#from_date').prop('disabled', true).val('');

            $('#to_date').prop('disabled', true).val('');

        }

        table.ajax.reload();

    });


    // CUSTOM DATE CHANGE
    $('#from_date, #to_date').on('change', function () {

        if ($('#date_filter').val() === 'custom') {

            table.ajax.reload();

        }

    });


    // UPDATE SELECT ALL STATUS
    function updateServicesSelectAll() {

        const checkboxes = $('#internshipTable .row-checkbox');

        const checked = checkboxes.filter(':checked').length;

        $('#select-all').prop(
            'checked',
            checkboxes.length > 0 && checked === checkboxes.length
        );

    }


    // RESTORE SELECTION AFTER DATATABLE REDRAW
    table.on('draw', function () {

        $('#internshipTable .row-checkbox').each(function () {

            $(this).prop(
                'checked',
                selectedServicesIds.has(String($(this).val()))
            );

        });

        updateServicesSelectAll();

    });


    // SELECT ALL CURRENT PAGE
    $('#select-all').on('change', function () {

        const checked = $(this).is(':checked');

        $('#internshipTable .row-checkbox').each(function () {

            const id = String($(this).val());

            const row = table.row($(this).closest('tr')).data();

            $(this).prop('checked', checked);

            if (checked) {

                selectedServicesIds.add(id);

                selectedServicesRecords.set(id, {

                    name: $('<div>').html(row[2]).text().trim(),

                    mobile: $('<div>').html(row[4]).text().trim()

                });

            } else {

                selectedServicesIds.delete(id);

                selectedServicesRecords.delete(id);

            }

        });

    });


    // INDIVIDUAL CHECKBOX
    $('#internshipTable').on('change', '.row-checkbox', function () {

        const id = String($(this).val());

        const row = table.row($(this).closest('tr')).data();

        if ($(this).is(':checked')) {

            selectedServicesIds.add(id);

            selectedServicesRecords.set(id, {

                name: $('<div>').html(row[2]).text().trim(),

                mobile: $('<div>').html(row[4]).text().trim()

            });

        } else {

            selectedServicesIds.delete(id);

            selectedServicesRecords.delete(id);

        }

        updateServicesSelectAll();

    });


    // WHATSAPP BUTTON
    $('#sendServicesWhatsAppButton').on('click', function () {

        if (selectedServicesIds.size === 0) {

            Swal.fire({

                icon: 'warning',
                title: 'No Records Selected',
                text: 'Please select at least one record.',
                confirmButtonText: 'OK'

            });

            return;

        }

        startServicesWhatsApp(
            Array.from(selectedServicesRecords.values())
        );

    });


    // MAKE SELECTED IDS AVAILABLE TO EXPORT SCRIPT
    window.selectedServicesIds = selectedServicesIds;

});

</script>


{{-- WHATSAPP FUNCTIONS --}}
<script>

function formatServicesWhatsAppMobile(mobile) {

    let number = String(mobile || '').replace(/\D/g, '');

    if (number.length === 10) {

        number = '91' + number;

    } else if (number.length === 11 && number.startsWith('0')) {

        number = '91' + number.substring(1);

    }

    return /^91[6-9]\d{9}$/.test(number) ? number : null;

}


function openServicesWhatsApp(record, message, appendName) {

    const mobile = formatServicesWhatsAppMobile(record.mobile);

    if (!mobile) return false;

    let finalMessage = message;

    if (appendName && record.name) {

        finalMessage = 'Hi ' + record.name + ',\n\n' + message;

    }

    const url = 'https://web.whatsapp.com/send?phone=' +
        mobile + '&text=' + encodeURIComponent(finalMessage);

    window.open(url, '_blank');

    return true;

}


function startServicesWhatsApp(records) {

    const validRecords = [];

    let invalidCount = 0;

    records.forEach(function (record) {

        if (formatServicesWhatsAppMobile(record.mobile)) {

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


    Swal.fire({

        title: 'Send WhatsApp Message',

        html: `
            <div class="text-start">

                <label class="form-label">Message</label>

                <textarea id="servicesWhatsAppMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message here..."></textarea>

                <div class="form-check mt-3">

                    <input type="checkbox"
                        class="form-check-input"
                        id="servicesAppendName"
                        checked>

                    <label class="form-check-label" for="servicesAppendName">
                        Add student name
                    </label>

                </div>

                <hr>

                <p>Selected: ${records.length}</p>

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

            const message = document.getElementById(
                'servicesWhatsAppMessage'
            ).value.trim();

            if (!message) {

                Swal.showValidationMessage('Please enter a message.');

                return false;

            }

            const appendName = document.getElementById(
                'servicesAppendName'
            ).checked;

            openServicesWhatsApp(validRecords[0], message, appendName);

            return { message, appendName };

        }

    }).then(function (result) {

        if (!result.isConfirmed) return;

        const message = result.value.message;

        const appendName = result.value.appendName;

        let currentIndex = 1;


        function showNext() {

            if (currentIndex >= validRecords.length) {

                Swal.fire({

                    icon: 'success',

                    title: 'Sending Completed',

                    text: 'All selected WhatsApp chats have been processed.'

                });

                return;

            }

            const record = validRecords[currentIndex];

            Swal.fire({

                title: 'WhatsApp Sending',

                html: `
                    <p>Chat ${currentIndex + 1} of ${validRecords.length}</p>

                    <p><strong>${record.name || 'Student'}</strong></p>

                    <p>${record.mobile}</p>

                    <small>Click Open Next to open the next WhatsApp chat.</small>
                `,

                showCancelButton: true,

                confirmButtonText: 'Open Next',

                cancelButtonText: 'Finish',

                reverseButtons: false

            }).then(function (nextResult) {

                if (nextResult.isConfirmed) {

                    openServicesWhatsApp(record, message, appendName);

                    currentIndex++;

                    showNext();

                }

            });

        }

        showNext();

    });

}

</script>


{{-- DELETE WITH SWEETALERT --}}
<script>

$(document).ready(function () {

    $('body').on('click', '.delete_btn', function () {

        let id = $(this).data('id');

        Swal.fire({

            title: 'Are you sure?',

            text: 'This record will be permanently deleted!',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonColor: '#d33',

            confirmButtonText: 'Yes, delete it!',

            reverseButtons: false

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: "{{ route('services-registrations.destroy', ':id') }}"
                        .replace(':id', id),

                    type: 'POST',

                    data: {

                        _token: "{{ csrf_token() }}",

                        _method: 'DELETE'

                    },

                    success: function () {

                        Swal.fire({

                            icon: 'success',

                            title: 'Deleted!',

                            text: 'Record deleted successfully.'

                        });

                        $('#internshipTable').DataTable().ajax.reload();

                    },

                    error: function (xhr) {

                        Swal.fire({

                            icon: 'error',

                            title: 'Error!',

                            text: 'Something went wrong.'

                        });

                        console.log(xhr.responseText);

                    }

                });

            }

        });

    });

});

</script>


{{-- EXPORT FILTERED AND SELECTED --}}
<script>


// GET ALL ACTIVE FILTERS
function getServicesExportParams() {

    let params = new URLSearchParams();

    let slug = $('select[name=slug]').val();

    let technology = $('select[name=technology]').val();

    let limit = $('select[name=limit]').val();

    let date_filter = $('#date_filter').val();

    let from_date = $('#from_date').val();

    let to_date = $('#to_date').val();

    let download_status = $('#download_status').val();


    if (slug) {

        params.append('slug', slug);

    }

    if (technology) {

        params.append('technology', technology);

    }

    if (limit) {

        params.append('limit', limit);

    }

    if (date_filter) {

        params.append('date_filter', date_filter);

    }

    if (date_filter === 'custom') {

        if (from_date) {

            params.append('from_date', from_date);

        }

        if (to_date) {

            params.append('to_date', to_date);

        }

    }

    // DOWNLOAD STATUS
    if (download_status) {

        params.append('download_status', download_status);

    }

    return params;

}


// EXPORT FILTERED RECORDS
// $('#exportBtn').on('click', function () {

//     let params = getServicesExportParams();

//     let url = "{{ route('services-registrations.export') }}?" +
//         params.toString();

//     window.location.href = url;

// });


// // EXPORT SELECTED RECORDS
// $('#exportSelectedBtn').on('click', function () {

//     let selected = Array.from(window.selectedServicesIds || []);

//     if (selected.length === 0) {

//         Swal.fire({

//             icon: 'warning',

//             title: 'No Records Selected',

//             text: 'Please select at least one record to Export.',

//             confirmButtonText: 'OK'

//         });

//         return;

//     }

//     // Include active filters as well as selected IDs.
//     let params = getServicesExportParams();

//     selected.forEach(function (id) {

//         params.append('ids[]', id);

//     });

//     let url = "{{ route('services-registrations.export') }}?" +
//         params.toString();

//     window.location.href = url;

// });
// SINGLE EXPORT BUTTON - FILTERED / SELECTED RECORDS
$('#exportBtn').on('click', function () {

    let params = getServicesExportParams();

    // Get persistent selected IDs across DataTable pages
    let selected = Array.from(window.selectedServicesIds || []);

    // If records are selected, export only selected records
    if (selected.length > 0) {

        selected.forEach(function (id) {

            params.append('ids[]', id);

        });

    }

    let url = "{{ route('services-registrations.export') }}?" +
        params.toString();

    window.location.href = url;

});
</script>

@endpush
