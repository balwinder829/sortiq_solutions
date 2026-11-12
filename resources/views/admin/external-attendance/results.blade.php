@extends('layouts.app')

@section('content')
<div class="container">

     <div class="row mb-2 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">Attendance Submissions Records</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.external-attendance.index') }}"
                   class="btn mb-2" style="background-color:#6b51df; color:#fff;">
                    Back
                </a>
            </div>
        </div>
    </div>
    
 
 

    {{-- SUCCESS / ERROR MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- LINKED CATEGORIES AND TESTS: COLLAPSED BY DEFAULT --}}
    @if(isset($linkedOptions))
        <div class="card mb-3">
            <div class="card-header p-0">
                <button
                    class="btn btn-light w-100 text-start d-flex justify-content-between align-items-center"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#linkedTestsSection"
                    aria-expanded="false"
                    aria-controls="linkedTestsSection"
                    style="font-weight: 600;"
                >
                    <span>
                        <i class="fa fa-link me-1"></i>
                        Linked Categories & Tests

                        <span class="badge bg-secondary ms-1">
                            {{ $linkedOptions->count() }}
                        </span>
                    </span>

                    <i class="fa fa-chevron-down"></i>
                </button>
            </div>

            <div class="collapse" id="linkedTestsSection">
                <div class="card-body p-2">

                    @if($linkedOptions->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category</th>
                                        <th>Test</th>
                                        <th>Type</th>
                                        <th>Display Name</th>
                                        <th>Students</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($linkedOptions as $option)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $option->category_name ?? '-' }}</td>
                                            <td>{{ $option->test_title ?? '-' }}</td>
                                            <td>{{ strtolower($option->test_type ?? '') === 'technical' ? 'Coding' : 'Non Coding' }}</td>
                                            <td>{{ $option->display_name ?? '-' }}</td>
                                            <td>{{ $option->student_count }}</td>
                                            <td>
                                                @if(!empty($option->test_id))
                                                    <a
                                                        href="{{ route('admin.tests.results', [
                                                            'test' => $option->test_id,
                                                            'college_id' => request('college_id'),
                                                        ]) }}"
                                                        class="btn btn-sm btn-primary"
                                                        target="_blank"
                                                        rel="noopener"
                                                    >
                                                        View Results
                                                    </a>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-muted text-center py-2">
                            No linked categories or tests found.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    @endif

    {{-- FILTERS --}}
    <form method="GET" id="filterForm" class="row g-2 mb-4">

        <div class="col-md-2">
            <select name="college_id" class="form-select filterchange">
                <option value="">All Colleges</option>

                @foreach($colleges as $college)
                    <option
                        value="{{ $college->id }}"
                        {{ request('college_id') == $college->id ? 'selected' : '' }}
                    >
                        {{ $college->full_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Category Filter --}}
        <!-- <div class="col-md-2">
            <select name="category_id" class="form-select filterchange">
                <option value="">All Categories</option>

                @foreach($categories as $category)
                    <option value="{{ $category->category_id }}"
                        {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </select>
        </div> -->

        {{-- Test Filter --}}
        <!-- <div class="col-md-2">
            <select name="linked_test_id" class="form-select filterchange">
                <option value="">All Tests</option>

                @foreach($linkedOptions->unique('test_id') as $option)
                    <option value="{{ $option->test_id }}"
                        {{ request('linked_test_id') == $option->test_id ? 'selected' : '' }}>
                        {{ $option->test_title }}
                    </option>
                @endforeach
            </select>
        </div> -->

        <div class="col-md-2">
            <input
                type="text"
                name="name"
                value="{{ request('name') }}"
                class="form-control filterchangetext"
                placeholder="Student Name"
            >
        </div>

        <div class="col-md-2">
            <input
                type="text"
                name="email"
                value="{{ request('email') }}"
                class="form-control filterchangetext"
                placeholder="Email"
            >
        </div>

        <div class="col-md-2">
            <input
                type="text"
                name="mobile"
                value="{{ request('mobile') }}"
                class="form-control filterchangetext"
                placeholder="Mobile"
            >
        </div>

        {{-- Course Type Filter --}}
        <div class="col-md-2">
            <select name="course_type" class="form-select filterchange">
                <option value="">All Course Types</option>

                <option value="Degree"
                    {{ request('course_type') == 'Degree' ? 'selected' : '' }}>
                    Degree
                </option>

                <option value="Diploma"
                    {{ request('course_type') == 'Diploma' ? 'selected' : '' }}>
                    Diploma
                </option>
            </select>
        </div>

        {{-- Branch Filter --}}
        <div class="col-md-2">
            <select name="student_branch" class="form-select filterchange">
                <option value="">All Branches</option>

                
                    <option value="Computer Science"
                        {{ request('student_branch') == 'Computer Science' ? 'selected' : '' }}>
                        Computer Science
                    </option>
                    <option value="IT"
                        {{ request('student_branch') == 'IT' ? 'selected' : '' }}>
                        IT
                    </option>
                    <option value="Mechanical"
                        {{ request('student_branch') == 'Mechanical' ? 'selected' : '' }}>
                        Mechanical
                    </option>
                    <option value="Civil"
                        {{ request('student_branch') == 'Civil' ? 'selected' : '' }}>
                        Civil
                    </option>
                    <option value="Electrical"
                        {{ request('student_branch') == 'Electrical' ? 'selected' : '' }}>
                        Electrical
                    </option>
                    <option value="Management"
                        {{ request('student_branch') == 'Management' ? 'selected' : '' }}>
                        Management
                    </option>
                
            </select>
        </div>

        <div class="col-md-2">
            <select name="semester" class="form-select filterchange">
                <option value="">All Semester</option>

                @for($i = 1; $i <= 8; $i++)
                    <option
                        value="{{ $i }}"
                        {{ request('semester') == $i ? 'selected' : '' }}
                    >
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>

        <div class="col-md-2">
            <select name="gender" class="form-select filterchange">
                <option value="">All Gender</option>
                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="status" class="form-select filterchange">
                <option value="">Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Moved</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Not Moved</option>
            </select>
        </div>

        <div class="col-md-2">
            <a
                href="{{ route('admin.external-attendance.results', $test->id) }}"
                class="btn btn-secondary w-100"
            >
                Reset
            </a>
        </div>

    </form>

    {{-- BULK ACTION FORM --}}
    <form method="POST" id="bulkForm">
        @csrf

        {{-- Selected IDs are inserted here by JavaScript --}}
        <div id="selectedAttendanceInputs"></div>
        <input type="hidden" name="action" id="attendanceAction">
        <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

        <div class="d-flex gap-2 mb-3 flex-wrap">
            <button type="button" class="btn btn-success" id="sendAttendanceWhatsAppButton">
                <i class="fab fa-whatsapp"></i> Send WhatsApp
            </button>
            <button
                type="submit"
                class="btn btn-warning"
                formaction="{{ route('admin.attendance.move.enquiries', $test->id) }}"
                id="moveSelectedButton"
            >
                Move to Enquiries
            </button>

            <a
                href="{{ route('admin.external-attendance.export.results', [$test->id] + request()->query()) }}"
                class="btn btn-success"
            >
                Export All Excel
            </a>

            <button type="button" class="btn btn-primary" id="updateSelectedScoresButton">
                Update Selected Scores
            </button>

            <button type="button" class="btn btn-info text-white" id="downloadSelectedButton">
                Download Selected
            </button>
        </div>

        {{-- ORIGINAL TABLE STYLE PRESERVED --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="attendanceResultsTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll">
                        </th>
                        <th>#</th>
                        <th>College</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Gender</th>
                        <th>Class</th>
                        <th>Semester</th>
                        <th>Course</th>
                        <th>Test Name</th>
                        <th>Test Type</th>
                        <th>Test Status</th>
                        <th>Marks</th>
                        <th>Update Score</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($students as $i => $st)
                        <tr>
                            <td>
                                
                                    <input
                                        type="checkbox"
                                        class="student-checkbox"
                                        value="{{ $st->id }}"
                                         data-name="{{ $st->student_name }}"
                                        data-mobile="{{ $st->student_mobile }}"
                                    >
                                
                            </td>

                            <td>{{ $i + 1 }}</td>
                            <td>{{ $st->college->full_name ?? '-' }}</td>
                            <td>{{ $st->student_name }}</td>
                            <td>{{ $st->student_email }}</td>
                            <td>{{ $st->student_mobile }}</td>
                            <td>{{ ucfirst($st->gender) }}</td>
                            <td>{{ $st->class ?? '-' }}</td>
                            <td>{{ $st->semester ?? '-' }}</td>
                            <td>{{ $st->course->course_name ?? '-' }}</td>
                            <td>{{ $st->latest_category_name ?? '-' }}</td>
                            <td>
                                {{ $st->latest_test_type
                                    ? (strtolower($st->latest_test_type) === 'technical' ? 'Coding' : 'Non Coding')
                                    : '-' }}
                            </td>
                            
                            <td>
                                @if($st->latest_student_test_id)
                                    <span class="badge bg-success">Given</span>
                                @else
                                    <span class="badge bg-secondary">Not Given</span>
                                @endif
                            </td>
                            <td>
                                @if($st->latest_student_test_id)
                                    {{ (int) ($st->latest_score ?? 0) }} / {{ (int) ($st->latest_max_score ?? 0) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($st->latest_student_test_id)
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-primary edit-score-button"
                                        data-attendance-id="{{ $st->id }}"
                                        data-student-name="{{ $st->student_name }}"
                                        data-score="{{ (int) ($st->latest_score ?? 0) }}"
                                        data-max-score="{{ (int) ($st->latest_max_score ?? 0) }}"
                                    >
                                        Edit
                                    </button>
                                @else
                                    -
                                @endif
                            </td>

                            <td>
                                {{ $st->exam_submitted_at
                                    ? \Carbon\Carbon::parse($st->exam_submitted_at)->format('d M Y h:i A')
                                    : '-' }}
                            </td>
                        </tr>
                    
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    {{-- UPDATE INDIVIDUAL SCORE MODAL --}}
    <div class="modal fade" id="updateScoreModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.external-attendance.results.action', $test->id) }}" id="updateSingleScoreForm">
                @csrf
                <input type="hidden" name="action" value="update_one_score">
                <input type="hidden" name="attendance_id" id="scoreAttendanceId">
                <input type="hidden" name="return_url" value="{{ request()->fullUrl() }}">

                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Update Student Score</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">Student: <strong id="scoreStudentName"></strong></p>
                        <p class="text-muted mb-2">Maximum Marks: <strong id="scoreMaxMarks"></strong></p>
                        <label for="singleScoreInput" class="form-label">Marks Obtained</label>
                        <input type="number" name="score" id="singleScoreInput" class="form-control" min="0" required>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Score</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</div>

{{-- FILTER AUTO-SUBMIT --}}
{{-- DATATABLES: CLIENT-SIDE, NO AJAX --}}
<script>
$(document).ready(function () {

    $('#attendanceResultsTable').DataTable({
        processing: true,
        serverSide: false,
        autoWidth: false,
        width: '100%',
        scrollX: true,
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order: [],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });

});
</script>
<script>
$(document).ready(function () {
    let timer;

    $('.filterchange').on('change', function () {
        $('#filterForm').submit();
    });

    $('.filterchangetext').on('input', function () {
        clearTimeout(timer);

        timer = setTimeout(function () {
            $('#filterForm').submit();
        }, 500);
    });
});
</script>

{{-- SELECT ALL + KEEP SELECTION DURING DATATABLE PAGINATION --}}
<script>
$(document).ready(function () {

    const bulkForm = document.getElementById('bulkForm');
    const selectedInputs = document.getElementById('selectedAttendanceInputs');

    // Get already initialized DataTable
    const table = $('#attendanceResultsTable').DataTable();

    // Keep selected IDs across all pages
    const selectedIds = new Set();

    // Store selected student WhatsApp details
const selectedAttendanceWhatsAppRecords = new Map();


    // =====================================================
    // CURRENT DATATABLE PAGE CHECKBOXES ONLY
    // =====================================================
    function getCurrentPageCheckboxes() {

        const nodes = table
            .rows({ page: 'current', search: 'applied' })
            .nodes();

        return $(nodes)
            .find('.student-checkbox')
            .toArray();
    }


    // =====================================================
    // HEADER SELECT ALL
    // =====================================================
    function getSelectAll() {

        // Use DataTables visible header
        return $(table.table().header())
            .find('#selectAll');

    }


    // =====================================================
    // CREATE HIDDEN attendance_ids[]
    // =====================================================
    function syncHiddenInputs() {

        selectedInputs.innerHTML = '';

        selectedIds.forEach(function (id) {

            const input = document.createElement('input');

            input.type = 'hidden';
            input.name = 'attendance_ids[]';
            input.value = id;

            selectedInputs.appendChild(input);

        });

    }


    // =====================================================
    // UPDATE HEADER CHECKBOX
    // =====================================================
    function updateSelectAllState() {

        const checkboxes = getCurrentPageCheckboxes();

        let total = checkboxes.length;
        let checked = 0;


        checkboxes.forEach(function (checkbox) {

            if (selectedIds.has(String(checkbox.value))) {
                checked++;
            }

        });


        const $selectAll = getSelectAll();


        if (total === 0) {

            $selectAll
                .prop('checked', false)
                .prop('indeterminate', false);

            return;

        }


        if (checked === total) {

            $selectAll
                .prop('checked', true)
                .prop('indeterminate', false);

        } else if (checked > 0) {

            $selectAll
                .prop('checked', false)
                .prop('indeterminate', true);

        } else {

            $selectAll
                .prop('checked', false)
                .prop('indeterminate', false);

        }

    }


    // =====================================================
    // RESTORE CURRENT PAGE
    // =====================================================
    function restoreCurrentPage() {

        const checkboxes = getCurrentPageCheckboxes();


        checkboxes.forEach(function (checkbox) {

            checkbox.checked =
                selectedIds.has(String(checkbox.value));

        });


        updateSelectAllState();

        syncHiddenInputs();

    }


    // =====================================================
    // INDIVIDUAL CHECKBOX
    // =====================================================
    $('#attendanceResultsTable tbody').on(
        'change',
        '.student-checkbox',
        function () {

            const id = String(this.value);


            if (this.checked) {

                selectedIds.add(id);
                
                selectedAttendanceWhatsAppRecords.set(id, {
                    id: id,
                    name: $(this).data('name') || '',
                    mobile: $(this).data('mobile') || ''
                });

            } else {

                selectedIds.delete(id);
                selectedAttendanceWhatsAppRecords.delete(id);

            }


            updateSelectAllState();

            syncHiddenInputs();

        }
    );


    // =====================================================
    // SELECT ALL CURRENT PAGE
    // =====================================================
    $(document).on('change', '#selectAll', function () {

        const shouldCheck = this.checked;

        const checkboxes = getCurrentPageCheckboxes();


        checkboxes.forEach(function (checkbox) {

            const id = String(checkbox.value);

            checkbox.checked = shouldCheck;


            if (shouldCheck) {

                selectedIds.add(id);

                selectedAttendanceWhatsAppRecords.set(id, {
                    id: id,
                    name: $(checkbox).data('name') || '',
                    mobile: $(checkbox).data('mobile') || ''
                });

            } else {

                selectedIds.delete(id);
                selectedAttendanceWhatsAppRecords.delete(id);

            }

        });


        updateSelectAllState();

        syncHiddenInputs();

    });


    // =====================================================
    // DATATABLE PAGE / SEARCH / LENGTH / DRAW
    // =====================================================
    table.on('draw', function () {

        restoreCurrentPage();

    });

    // =====================================================
// WHATSAPP BUTTON
// =====================================================
$(document).on('click', '#sendAttendanceWhatsAppButton', function () {

    if (selectedIds.size === 0) {
        Swal.fire(
            'No Selection',
            'Please select at least one student.',
            'warning'
        );
        return;
    }

    const selectedRecords = Array.from(selectedIds)
        .map(id => selectedAttendanceWhatsAppRecords.get(String(id)))
        .filter(Boolean);

    if (selectedRecords.length !== selectedIds.size) {
        Swal.fire(
            'Selection Details Missing',
            'Some selected students do not have saved contact details. Please unselect and select those students again.',
            'warning'
        );
        return;
    }

    startAttendanceWhatsApp(selectedRecords);
});
    // =====================================================
    // MOVE TO ENQUIRIES
    // =====================================================
    const sessionOptions = @json($sessionsList);


    $('#moveSelectedButton').on('click', function (event) {

        event.preventDefault();

        const button = this;

        syncHiddenInputs();


        if (selectedIds.size === 0) {

            Swal.fire(
                'No Students Selected',
                'Please select at least one student.',
                'warning'
            );

            return;

        }


        let optionsHtml =
            '<option value="">Select Session</option>';


        Object.keys(sessionOptions).forEach(function (key) {

            optionsHtml +=
                '<option value="' +
                key +
                '">' +
                sessionOptions[key] +
                '</option>';

        });


        Swal.fire({

            title: 'Select Session',

            html:
                '<select id="session_id" class="form-control">' +
                optionsHtml +
                '</select>',

            showCancelButton: true,

            confirmButtonText: 'Move'

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            const sessionId =
                $('#session_id').val();


            if (!sessionId) {

                Swal.fire(
                    'Error',
                    'Session is required.',
                    'error'
                );

                return;

            }


            let sessionInput =
                bulkForm.querySelector(
                    'input[name="session_id"]'
                );


            if (!sessionInput) {

                sessionInput =
                    document.createElement('input');

                sessionInput.type = 'hidden';

                sessionInput.name = 'session_id';

                bulkForm.appendChild(sessionInput);

            }


            sessionInput.value = sessionId;


            syncHiddenInputs();


            bulkForm.action =
                button.getAttribute('formaction');


            bulkForm.submit();

        });

    });


    // =====================================================
    // INITIAL
    // =====================================================
    restoreCurrentPage();

});


/* =========================================================
   ATTENDANCE BULK WHATSAPP
   Same popup and sequential workflow as Old Students
========================================================= */

// Format Indian mobile number
function formatAttendanceWhatsAppMobile(mobile) {
    mobile = String(mobile || '').replace(/\D/g, '');

    if (mobile.length === 10) {
        mobile = '91' + mobile;
    } else if (mobile.length === 11 && mobile.startsWith('0')) {
        mobile = '91' + mobile.substring(1);
    }

    if (!/^91[6-9]\d{9}$/.test(mobile)) return null;

    return mobile;
}


// Open WhatsApp chat
function openAttendanceWhatsApp(record, message, appendName) {
    const mobile = formatAttendanceWhatsAppMobile(record.mobile);

    if (!mobile) return false;

    let finalMessage = message;

    if (appendName && record.name) {
        finalMessage = 'Hi ' + record.name + ',\n\n' + message;
    }

    const url =
        'https://web.whatsapp.com/send?phone=' +
        mobile +
        '&text=' +
        encodeURIComponent(finalMessage);

    window.open(url, '_blank');

    return true;
}


// Start bulk WhatsApp sending
function startAttendanceWhatsApp(records) {

    if (!records || records.length === 0) {
        Swal.fire(
            'No Selection',
            'Please select at least one student.',
            'warning'
        );
        return;
    }

    const validRecords = records.filter(record =>
        formatAttendanceWhatsAppMobile(record.mobile)
    );

    const invalidCount = records.length - validRecords.length;

    if (validRecords.length === 0) {
        Swal.fire(
            'Invalid Mobile Numbers',
            'No selected student has a valid Indian mobile number.',
            'warning'
        );
        return;
    }

    Swal.fire({
        title: 'Send WhatsApp Message',

        html: `
            <div class="text-start">
                <label class="form-label">Message</label>

                <textarea
                    id="attendanceWhatsappMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message..."
                ></textarea>

                <div class="form-check mt-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="attendanceWhatsappAppendName"
                        checked
                    >

                    <label
                        class="form-check-label"
                        for="attendanceWhatsappAppendName"
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
                .getElementById('attendanceWhatsappMessage')
                .value.trim();

            const appendName = document
                .getElementById('attendanceWhatsappAppendName')
                .checked;

            if (!message) {
                Swal.showValidationMessage('Please enter a message.');
                return false;
            }

            const opened = openAttendanceWhatsApp(
                validRecords[0],
                message,
                appendName
            );

            if (!opened) {
                Swal.showValidationMessage('Unable to open WhatsApp chat.');
                return false;
            }

            return {
                message: message,
                appendName: appendName
            };
        }

    }).then(result => {

        if (!result.isConfirmed) return;

        let currentIndex = 1;

        function openNextAttendanceWhatsApp() {

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

            const safeName = $('<div>')
                .text(currentRecord.name || 'Student')
                .html();

            const safeMobile = $('<div>')
                .text(currentRecord.mobile || '')
                .html();

            Swal.fire({

                title: 'WhatsApp Sending',

                html: `
                    <p>
                        Chat ${currentIndex + 1}
                        of ${validRecords.length}
                    </p>

                    <p><strong>${safeName}</strong></p>

                    <p class="text-muted">${safeMobile}</p>

                    <p>
                        Send the message in WhatsApp, then click
                        <strong>Open Next</strong>.
                    </p>
                `,

                showCancelButton: true,
                confirmButtonText: 'Open Next',
                cancelButtonText: 'Finish',
                reverseButtons: false,

                preConfirm: () => {

                    const opened = openAttendanceWhatsApp(
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
                    openNextAttendanceWhatsApp();
                }

            });
        }

        if (validRecords.length > 1) {
            openNextAttendanceWhatsApp();
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Chat Opened',
                text: 'The WhatsApp chat has been opened.'
            });
        }

    });
}


// WhatsApp button
 


</script>


<script>
$(document).ready(function () {
    const actionUrl = @json(route('admin.external-attendance.results.action', $test->id));

    function selectedCount() {
        return $('#selectedAttendanceInputs input[name="attendance_ids[]"]').length;
    }

    function submitSelectedAction(action, score = null) {
        const count = selectedCount();

        if (!count) {
            Swal.fire('No Students Selected', 'Please select at least one student.', 'warning');
            return;
        }

        $('#attendanceAction').val(action);

        if (score !== null) {
            let scoreInput = $('#bulkForm input[name="score"]');
            if (!scoreInput.length) {
                scoreInput = $('<input>', { type: 'hidden', name: 'score' }).appendTo('#bulkForm');
            }
            scoreInput.val(score);
        }

        $('#bulkForm').attr('action', actionUrl).trigger('submit');
    }

    $('#downloadSelectedButton').on('click', function () {
        submitSelectedAction('download_selected');
    });

    $('#updateSelectedScoresButton').on('click', function () {
        if (!selectedCount()) {
            Swal.fire('No Students Selected', 'Please select at least one student.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Update Selected Scores',
            text: 'Enter the marks to apply to the latest test attempt of each selected student.',
            input: 'number',
            inputAttributes: { min: 0, step: 1 },
            inputValidator: value => {
                if (value === '' || !Number.isInteger(Number(value)) || Number(value) < 0) {
                    return 'Please enter a valid non-negative whole number.';
                }
            },
            showCancelButton: true,
            confirmButtonText: 'Update',
            reverseButtons: false
        }).then(function (result) {
            if (result.isConfirmed) {
                submitSelectedAction('update_selected_scores', result.value);
            }
        });
    });

    $(document).on('click', '.edit-score-button', function () {
        const button = $(this);
        $('#scoreAttendanceId').val(button.data('attendance-id'));
        $('#scoreStudentName').text(button.data('student-name'));
        $('#scoreMaxMarks').text(button.data('max-score'));
        $('#singleScoreInput').attr('max', button.data('max-score')).val(button.data('score'));

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('updateScoreModal'));
        modal.show();
    });

    $('#updateSingleScoreForm').on('submit', function (event) {
        const score = Number($('#singleScoreInput').val());
        const max = Number($('#singleScoreInput').attr('max'));

        if (!Number.isInteger(score) || score < 0 || score > max) {
            event.preventDefault();
            Swal.fire('Invalid Score', 'Score must be between 0 and ' + max + '.', 'warning');
        }
    });

    // Keep the current page's selected attendance IDs in the common action form.
    $('#bulkForm').on('submit', function () {
        if ($('#attendanceAction').val() === 'download_selected' ||
            $('#attendanceAction').val() === 'update_selected_scores') {
            // Hidden inputs are already synchronized by the existing selection script.
        }
    });
});
</script>

@endsection