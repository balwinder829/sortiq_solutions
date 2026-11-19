@extends('layouts.app')

@section('content')

<style>
    table td {
        vertical-align: middle;
        text-transform: capitalize;
    }
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

    <div class="row mb-3 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading mb-0">Internship Registrations</h1>
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
     <!-- <div class="row mb-2">
        <div class="col-md-4">
            <h1 class="page_heading">Internship Registrations</h1>
        </div>
    </div> -->

    <div class="row mb-2">
        <div class="col-md-10">

            {{-- FILTERS (UNCHANGED) --}}
            <form method="GET" id="filterForm" class="mb-3">
                <div class="row g-2">

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
 

                <div class="col-md-3">
                    <select name="college" class="form-select filterchange select2">
                        <option value="">All Colleges</option>

                        @foreach($colleges as $college)
                            <option value="{{ $college }}"
                                {{ request('college') == $college ? 'selected' : '' }}>
                                {{ $college }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="technology" class="form-select filterchange">
                        <option value="">All Technologies</option>

                        @foreach($technologies as $tech)
                            <option value="{{ $tech }}"
                                {{ request('technology') == $tech ? 'selected' : '' }}>
                                {{ $tech }}
                            </option>
                        @endforeach
                    </select>
                </div>
                 

                    <!-- Quick Date Filter -->
                    <div class="col-md-3">
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

                    <!-- From Date -->
                    <div class="col-md-3">
                        <input type="date" id="from_date" class="form-control" disabled>
                    </div>

                    <!-- To Date -->
                    <div class="col-md-3">
                        <input type="date" id="to_date" class="form-control" disabled>
                    </div>


                    <div class="col-md-2">
                    <select name="download_status" id="download_status"
                            class="form-select filterchange">
                        <option value="">All Download Status</option>
                        <option value="downloaded">Downloaded</option>
                        <option value="not_downloaded">Not Downloaded</option>
                    </select>
                </div>
                 

                <div class="col-md-2">
                    <select name="limit" class="form-select filterchange">
                        <option value="">All</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
<div class="col-md-auto d-flex gap-2 flex-nowrap align-items-start">
    
    <a href="{{ route('internship-registrations.index') }}"
       class="btn btn-secondary text-nowrap">
        Reset
    </a>

   <!--  <select id="exportMode" class="form-select" style="width: auto; min-width: 190px;">
        <option value="all">All Entries</option>
        <option value="skip_downloaded">Skip Downloaded</option>
        <option value="downloaded_only">Downloaded Only</option>
    </select> -->

    <!-- <a href="javascript:void(0)"
       id="exportBtn"
       class="btn btn-success text-nowrap">
        Export
    </a>

    <a href="javascript:void(0)"
       id="exportSelectedBtn"
       class="btn btn-success text-nowrap">
        Export Selected
    </a> -->

    <a href="javascript:void(0)"
   id="exportBtn"
   class="btn btn-success text-nowrap">
    Export
</a>

    <button type="button"
            id="sendInternshipWhatsAppButton"
            class="btn btn-success text-nowrap">
        <i class="fab fa-whatsapp"></i> Send WhatsApp
    </button>

</div>
                </div>
            </form>

        </div>
    </div>

    {{-- SUCCESS --}}
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
                <th>College</th>
                <th>Technology</th>
                <th>Added At</th>
                <th>Last Downloaded</th>
                <th width="100">Actions</th>
            </tr>
            </thead>
        </table>
    </div>

</div>
@endsection

@push('scripts')

<script>
$(document).ready(function () {

    let table = $('#internshipTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('internship-registrations.index') }}",
            data: function (d) {
                d.slug = $('select[name=slug]').val();
                d.college = $('select[name=college]').val();
                d.technology = $('select[name=technology]').val();
                d.date_filter = $('#date_filter').val();
                d.from_date = $('#from_date').val();
                d.to_date = $('#to_date').val();
                d.download_status = $('#download_status').val();
            }
        },
        columnDefs: [
            {
                targets: 0,
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
            { data: 8, orderable:false, searchable:false },
            { data: 9, orderable:false, searchable:false }
        ],
        pageLength: 25
    });

    $('.filterchange').on('change', function(){
        table.ajax.reload();
    });


    // Enable/Disable Custom Date Range
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


// Reload table on custom date change
$('#from_date, #to_date').on('change', function () {

    if ($('#date_filter').val() === 'custom') {
        table.ajax.reload();
    }
});



// $(document).on('change', '#select-all', function () {

//     $('.row-checkbox').prop('checked', $(this).is(':checked'));

// });

// $(document).on('change', '.row-checkbox', function () {

//     if (!$(this).is(':checked')) {
//         $('#select-all').prop('checked', false);
//     }

// });

    // WhatsApp selection
    // const selectedInternshipIds = new Set();
    window.selectedInternshipIds = new Set();
    const selectedInternshipRecords = new Map();

    function updateInternshipSelectAll() {
        const visibleCheckboxes = $('#internshipTable .row-checkbox');
        const checkedCount = visibleCheckboxes.filter(':checked').length;

        $('#select-all').prop(
            'checked',
            visibleCheckboxes.length > 0 && checkedCount === visibleCheckboxes.length
        );
    }

    // Restore selected checkboxes after DataTable redraw
    table.on('draw', function () {
        $('#internshipTable .row-checkbox').each(function () {
            $(this).prop('checked', selectedInternshipIds.has(String($(this).val())));
        });

        updateInternshipSelectAll();
    });

    // Select all records on the current page
    $('#select-all').on('change', function () {
        const checked = $(this).is(':checked');

        $('#internshipTable .row-checkbox').each(function () {
            const id = String($(this).val());
            const row = table.row($(this).closest('tr')).data();

            $(this).prop('checked', checked);

            if (checked) {
                selectedInternshipIds.add(id);

                selectedInternshipRecords.set(id, {
                    name: $('<div>').html(row[2]).text().trim(),
                    mobile: $('<div>').html(row[4]).text().trim()
                });
            } else {
                selectedInternshipIds.delete(id);
                selectedInternshipRecords.delete(id);
            }
        });
    });

    // Individual checkbox selection
    $('#internshipTable').on('change', '.row-checkbox', function () {
        const id = String($(this).val());
        const row = table.row($(this).closest('tr')).data();

        if ($(this).is(':checked')) {
            selectedInternshipIds.add(id);

            selectedInternshipRecords.set(id, {
                name: $('<div>').html(row[2]).text().trim(),
                mobile: $('<div>').html(row[4]).text().trim()
            });
        } else {
            selectedInternshipIds.delete(id);
            selectedInternshipRecords.delete(id);
        }

        updateInternshipSelectAll();
    });

    // WhatsApp button
    $('#sendInternshipWhatsAppButton').on('click', function () {
        if (selectedInternshipIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Records Selected',
                text: 'Please select at least one record.',
                confirmButtonText: 'OK'
            });
            return;
        }

        startInternshipWhatsApp(Array.from(selectedInternshipRecords.values()));
    });
});


</script>


<script>
function formatInternshipWhatsAppMobile(mobile) {
    let number = String(mobile || '').replace(/\D/g, '');

    if (number.length === 10) {
        number = '91' + number;
    } else if (number.length === 11 && number.startsWith('0')) {
        number = '91' + number.substring(1);
    }

    return /^91[6-9]\d{9}$/.test(number) ? number : null;
}

function openInternshipWhatsApp(record, message, appendName) {
    const mobile = formatInternshipWhatsAppMobile(record.mobile);

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

function startInternshipWhatsApp(records) {

    const validRecords = [];
    let invalidCount = 0;

    records.forEach(function (record) {
        if (formatInternshipWhatsAppMobile(record.mobile)) {
            validRecords.push(record);
        } else {
            invalidCount++;
        }
    });

    if (validRecords.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Valid Mobile Numbers',
            text: 'None of the selected records has a valid WhatsApp number.',
            confirmButtonText: 'OK'
        });
        return;
    }

    let currentIndex = 0;

    Swal.fire({
        title: 'Send WhatsApp Message',
        html: `
            <div class="text-start">
                <label class="form-label">Message</label>
                <textarea id="internshipWhatsAppMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message here..."></textarea>

                <div class="form-check mt-3">
                    <input type="checkbox"
                        class="form-check-input"
                        id="internshipAppendName"
                        checked>
                    <label class="form-check-label" for="internshipAppendName">
                        Add student name
                    </label>
                </div>

                <hr>
                <p class="mb-1">Selected: ${records.length}</p>
                <p class="mb-1">Valid: ${validRecords.length}</p>
                <p class="mb-0">Invalid: ${invalidCount}</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Start Sending',
        cancelButtonText: 'Cancel',
        reverseButtons: false,
        focusConfirm: false,
        preConfirm: function () {
            const message = document.getElementById(
                'internshipWhatsAppMessage'
            ).value.trim();

            if (!message) {
                Swal.showValidationMessage('Please enter a message.');
                return false;
            }

            const appendName = document.getElementById(
                'internshipAppendName'
            ).checked;

            openInternshipWhatsApp(validRecords[0], message, appendName);

            return {
                message: message,
                appendName: appendName
            };
        }
    }).then(function (result) {

        if (!result.isConfirmed) return;

        const message = result.value.message;
        const appendName = result.value.appendName;

        currentIndex = 1;

        function showNext() {

            if (currentIndex >= validRecords.length) {
                Swal.fire({
                    icon: 'success',
                    title: 'Sending Completed',
                    text: 'All selected WhatsApp chats have been processed.',
                    confirmButtonText: 'OK'
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
                    openInternshipWhatsApp(record, message, appendName);
                    currentIndex++;
                    showNext();
                }
            });
        }

        showNext();
    });
}
</script>


{{-- EXPORT WITH FILTERS --}}
<script>
// $('#exportBtn').on('click', function () {

//     let params = new URLSearchParams();

//     let slug = $('select[name=slug]').val();
//     let college = $('select[name=college]').val();
//     let technology = $('select[name=technology]').val();

//     let date_filter = $('#date_filter').val();
//     let from_date = $('#from_date').val();
//     let to_date = $('#to_date').val();

//     // Existing Filters
//     if (slug) params.append('slug', slug);

//     if (college) params.append('college', college);

//     if (technology) params.append('technology', technology);

//     // Date Filters
//     if (date_filter) {
//         params.append('date_filter', date_filter);
//     }

//     // Custom Date Range
//     if (date_filter === 'custom') {

//         if (from_date) {
//             params.append('from_date', from_date);
//         }

//         if (to_date) {
//             params.append('to_date', to_date);
//         }
//     }

//     // Export mode and existing record limit
//     params.append('export_mode', $('#exportMode').val());

//     let limit = $('select[name=limit]').val();
//     if (limit) params.append('limit', limit);

//     let url = "{{ route('internship-registrations.export') }}?" + params.toString();

//     window.location.href = url;
// });

// $('#exportSelectedBtn').on('click', function () {

//     let selected = [];

//     $('.row-checkbox:checked').each(function () {
//         selected.push($(this).val());
//     });

//     if (selected.length === 0) {

//         Swal.fire({
//             icon: 'warning',
//             title: 'No Records Selected',
//             text: 'Please select at least one record to Export.',
//             confirmButtonText: 'OK'
//         });

//         return;
//     }

//     let params = new URLSearchParams();

//     selected.forEach(id => {
//         params.append('ids[]', id);
//     });

//     params.append('export_mode', $('#exportMode').val());

//     let url = "{{ route('internship-registrations.export') }}?" + params.toString();

//     window.location.href = url;
// });

    // Common function to build export filters
function getInternshipExportParams() {

    let params = new URLSearchParams();

    let slug = $('select[name=slug]').val();
    let college = $('select[name=college]').val();
    let technology = $('select[name=technology]').val();
    let downloadStatus = $('#download_status').val();

    let dateFilter = $('#date_filter').val();
    let fromDate = $('#from_date').val();
    let toDate = $('#to_date').val();
    let limit = $('select[name=limit]').val();

    if (slug) params.append('slug', slug);
    if (college) params.append('college', college);
    if (technology) params.append('technology', technology);

    if (downloadStatus) {
        params.append('download_status', downloadStatus);
    }

    if (dateFilter) {
        params.append('date_filter', dateFilter);
    }

    if (dateFilter === 'custom') {
        if (fromDate) params.append('from_date', fromDate);
        if (toDate) params.append('to_date', toDate);
    }

    if (limit) {
        params.append('limit', limit);
    }

    return params;
}


// Export All / Filtered Records
// $('#exportBtn').on('click', function () {

//     let params = getInternshipExportParams();

//     let url = "{{ route('internship-registrations.export') }}";

//     window.location.href = url + '?' + params.toString();
// });


// Export Selected Records
// $('#exportSelectedBtn').on('click', function () {

//     let selected = [];

//     // Use the persistent selection set from your DataTables code
//     if (typeof selectedInternshipIds !== 'undefined') {
//         selected = Array.from(selectedInternshipIds);
//     } else {
//         $('.row-checkbox:checked').each(function () {
//             selected.push($(this).val());
//         });
//     }

//     if (selected.length === 0) {

//         Swal.fire({
//             icon: 'warning',
//             title: 'No Records Selected',
//             text: 'Please select at least one record to export.',
//             confirmButtonText: 'OK',
//             reverseButtons: false
//         });

//         return;
//     }

//     let params = getInternshipExportParams();

//     selected.forEach(function (id) {
//         params.append('ids[]', id);
//     });

//     let url = "{{ route('internship-registrations.export') }}";

//     window.location.href = url + '?' + params.toString();
// });

// Common Export Button - All / Selected Records
$('#exportBtn').on('click', function () {

    let params = getInternshipExportParams();

    // Persistent selected IDs from DataTable
    let selected = Array.from(selectedInternshipIds);

    // If records are selected, export only selected records
    if (selected.length > 0) {

        selected.forEach(function (id) {
            params.append('ids[]', id);
        });

    }

    let url = "{{ route('internship-registrations.export') }}";

    window.location.href = url + '?' + params.toString();
});
</script>


@endpush