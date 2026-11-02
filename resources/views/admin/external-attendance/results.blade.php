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
                                            <td>{{ ucfirst($option->test_type ?? '-') }}</td>
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

        <div class="d-flex gap-2 mb-3">
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
                Export Excel
            </a>
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
                        <th>Submitted At</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($students as $i => $st)
                        <tr>
                            <td>
                                @if($st->is_moved_to_enquiry)
                                    <span class="badge bg-info">Moved</span>
                                @else
                                    <input
                                        type="checkbox"
                                        class="student-checkbox"
                                        value="{{ $st->id }}"
                                    >
                                @endif
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

            } else {

                selectedIds.delete(id);

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

            } else {

                selectedIds.delete(id);

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
</script>

@endsection