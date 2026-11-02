@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Final Students</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if(session('letter_missing_fields'))
        <div class="alert alert-warning">
            <strong>Complete these details before generating letters:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('letter_missing_fields') as $missingField)
                    <li>{{ $missingField }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="GET" action="{{ route('admin.final-students.index') }}" id="filterForm" class="row g-2 align-items-center mb-4">
        <input type="hidden" name="tab" value="{{ $activeTab }}">
        <div class="col-lg-4 col-md-12">
            <input type="text" name="search" class="form-control filter-search"
                   value="{{ request('search') }}" placeholder="Search name, email, mobile or college">
        </div>
        <div class="col-lg-3 col-md-4">
            <select name="college_id" class="form-select filter-change">
                <option value="">All Colleges</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}" @selected((string)request('college_id') === (string)$college->id)>
                        {{ $college->college_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="class" class="form-select filter-change">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class }}" @selected(request('class') === $class)>
                        {{ $class }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-2 col-md-4">
            <select name="final_status" class="form-select filter-change">
                <option value="">All Statuses</option>
                @foreach($statusOptions as $status)
                    <option value="{{ $status }}" @selected(request('final_status') === $status)>
                        {{ ucwords(str_replace('_', ' ', $status)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-md-4 d-flex gap-2">
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter"></i> Filter</button>
            <a class="btn btn-secondary" href="{{ route('admin.final-students.index', ['tab' => $activeTab]) }}">Reset</a>
            <a class="btn btn-outline-success" href="{{ route('admin.final-students.export', request()->query()) }}">
                <i class="fas fa-download"></i> Excel
            </a>
        </div>
    </form>

    <ul class="nav nav-tabs mb-3" id="finalStudentTabs">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab === 'all' ? 'active' : '' }}"
               href="{{ route('admin.final-students.index', array_merge(request()->except('page'), ['tab' => 'all'])) }}">
                All <span class="badge bg-secondary">{{ $tabCounts['all'] }}</span>
            </a>
        </li>
        @foreach($benefitTypes as $key => $label)
            <li class="nav-item">
                <a class="nav-link {{ $activeTab === $key ? 'active' : '' }}"
                   href="{{ route('admin.final-students.index', array_merge(request()->except('page'), ['tab' => $key])) }}">
                    {{ $label }} <span class="badge bg-secondary">{{ $tabCounts[$key] ?? 0 }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="{{ route('admin.final-students.certificates') }}" id="finalStudentsForm">
        @csrf
        <input type="hidden" name="certificate_type" id="certificateTypeInput">
        <input type="hidden" name="course_name" id="certificateCourseInput">
        <input type="hidden" name="certificate_date" id="certificateDateInput">
        <input type="hidden" name="custom_details" id="certificateDetailsInput">

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
            <div>
                <h5 class="mb-1">Final Students — {{ $activeTab === 'all' ? 'All Categories' : ($benefitTypes[$activeTab] ?? $activeTab) }}</h5>
                <span class="badge bg-primary">Total: {{ $students->count() }}</span>
                <span class="badge bg-secondary" id="selectedCountBadge">Selected: 0</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-warning" id="openBulkCategoryModal">
                    <i class="fas fa-exchange-alt"></i> Change Category
                </button>
                <!-- <button type="button" class="btn btn-primary" id="openCertificateModal">
                    <i class="fas fa-certificate"></i> Download Certificates
                </button> -->
                <button type="button" class="btn btn-success" id="openLetterModal">
                    <i class="fas fa-file-pdf"></i> Bulk Download Letters
                </button>
                <button type="button" class="btn btn-outline-secondary" id="clearSelection">Clear Selection</button>
            </div>
        </div>

        <div class="final-students-table-wrap">
            <table class="table table-bordered table-striped align-middle w-100" id="final-students-table">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="selectCurrentPage" aria-label="Select current page"></th>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>College</th>
                        <th>Course / Technology</th>
                        <th>Previous Round</th>
                        <th>Internship Category</th>
                        <th>Final Status</th>
                        <!-- <th>Benefit Amount</th> -->
                        <th>Offer Date</th>
                        <th>Notes</th>
                        <th>Added On</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><input type="checkbox" class="final-student-checkbox" value="{{ $student->id }}"></td>
                        <td>{{ $student->id }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->student_email }}</td>
                        <td>{{ $student->student_mobile }}</td>
                        <td>{{ $student->college_name }}</td>
                        <td>{{ $student->course_name ?: '—' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $student->previous_round_type ?? '-')) }}</td>
                        <td>{{ $benefitTypes[$student->benefit_type] ?? $student->benefit_type ?? '—' }}</td>
                        <td><span class="badge bg-{{ $student->final_status === 'selected' ? 'success' : 'secondary' }}">
                            {{ ucwords(str_replace('_', ' ', $student->final_status)) }}
                        </span></td>
                        <!-- <td>{{ $student->benefit_amount !== null ? number_format((float)$student->benefit_amount, 2) : '—' }}</td> -->
                        <td>{{ $student->offer_date ? $student->offer_date->format('d M Y') : '—' }}</td>
                        <td>{{ $student->notes }}</td>
                        <td>{{ $student->created_at ? $student->created_at->format('d M Y') : '—' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary edit-final-student"
                                data-id="{{ $student->id }}"
                                data-url="{{ route('admin.final-students.update', $student->id) }}"
                                data-status="{{ $student->final_status }}"
                                data-benefit="{{ $student->benefit_type }}"
                                data-amount="{{ $student->benefit_amount }}"
                                data-details="{{ $student->benefit_details }}"
                                data-offer-date="{{ $student->offer_date ? $student->offer_date->format('Y-m-d') : '' }}"
                                data-course-name="{{ $student->course_name }}"
                                data-notes="{{ $student->notes }}">Edit</button>
                        </td>
                    </tr>
@endforeach
                </tbody>
            </table>
        </div>
        <div id="selectedFinalInputs"></div>
    </form>

    <form method="POST" action="{{ route('admin.final-students.letters') }}" id="finalLettersForm" class="d-none">
        @csrf
        <input type="hidden" name="letter_type" id="finalLetterTypeInput">
        <input type="hidden" name="letter_date" id="finalLetterDateInput">
        <input type="hidden" name="course_name" id="finalLetterCourseInput">
        <div id="selectedLetterInputs"></div>
    </form>
</div>

<div class="modal fade" id="bulkCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" action="{{ route('admin.final-students.bulk-category') }}" id="bulkCategoryForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Change Internship Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">This updates the selected students' existing category. It does not create or move student records.</p>
                <label class="form-label" for="bulkBenefitType">New Category</label>
                <select name="benefit_type" id="bulkBenefitType" class="form-select" required>
                    <option value="">Select category</option>
                    @foreach($benefitTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <div id="bulkCategorySelectedInputs"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Update Selected</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="certificateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Download Certificates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="certificateType">Certificate Type</label>
                    <select class="form-select" id="certificateType" required>
                        <option value="">Select certificate</option>
                        @foreach($certificateTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="certificateCourse">Course / Internship Name</label>
                    <input type="text" id="certificateCourse" class="form-control" value="Internship / Professional Training">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="certificateDate">Certificate Date</label>
                    <input type="date" id="certificateDate" class="form-control" value="{{ now()->toDateString() }}">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="certificateDetails">Additional Details (optional)</label>
                    <textarea id="certificateDetails" class="form-control" rows="3" placeholder="Placement company, offer details, duration, etc."></textarea>
                </div>
                <small class="text-muted">A single PDF will be generated with one page per selected student.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmCertificateDownload" class="btn btn-primary">Download PDF</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="letterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Download Letters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="finalLetterType" class="form-label">Letter Type</label>
                    <select id="finalLetterType" class="form-select" required>
                        <option value="offer">Offer Letter</option>
                        <option value="placement">Placement Letter</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="finalLetterCourse" class="form-label">Course / Technology <span class="text-danger">*</span></label>
                    <input type="text" id="finalLetterCourse" class="form-control" maxlength="255" placeholder="e.g. B.Tech Computer Science, BCA, MBA" required>
                    <small class="text-muted">This course will be used for every selected student's letter in this download. You do not need to update each student's record.</small>
                </div>
                <div class="mb-3">
                    <label for="finalLetterDate" class="form-label">Letter Date <span class="text-danger">*</span></label>
                    <input type="date" id="finalLetterDate" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
                <small class="text-muted">A ZIP file will contain one PDF per selected student.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmLetterDownload" class="btn btn-success">Generate &amp; Download</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editFinalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editFinalForm" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Final Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Final Status</label>
                        <select name="final_status" id="editFinalStatus" class="form-select" required>
                            <option value="selected">Selected</option>
                            <option value="offered">Offered</option>
                            <option value="placed">Placed</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                            <option value="withdrawn">Withdrawn</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Internship Category</label>
                        <select name="benefit_type" id="editBenefitType" class="form-select">
                            <option value="">Not specified</option>
                            @foreach($benefitTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- <div class="col-md-6">
                        <label class="form-label">Benefit Amount</label>
                        <input type="number" min="0" step="0.01" name="benefit_amount" id="editBenefitAmount" class="form-control">
                    </div> -->
                    <div class="col-md-6">
                        <label class="form-label">Offer Date</label>
                        <input type="date" name="offer_date" id="editOfferDate" class="form-control">
                    </div>
                    <!-- <div class="col-12">
                        <label class="form-label">Benefit Details</label>
                        <textarea name="benefit_details" id="editBenefitDetails" class="form-control" rows="2"></textarea>
                    </div> -->

                    <div class="col-md-6">
                        <label for="editFinalCourseName" class="form-label">Course / Technology</label>
                        <input
                            type="text"
                            name="course_name"
                            id="editFinalCourseName"
                            class="form-control"
                            maxlength="255"
                            placeholder="e.g. Web Development, Python, Digital Marketing"
                        >
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" id="editFinalNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    #final-students-table th, #final-students-table td { white-space: nowrap; vertical-align: middle; }
    #final-students-table td:nth-child(4), #final-students-table td:nth-child(12) { white-space: normal; min-width: 170px; }
    .final-students-table-wrap, #final-students-table_wrapper { width: 100%; }
    #final-students-table_wrapper .dataTables_scroll { margin-bottom: .75rem; }
    #final-students-table_wrapper .dataTables_scrollBody { overflow-x: auto !important; }
    #final-students-table_wrapper .dataTables_length, #final-students-table_wrapper .dataTables_filter { margin-bottom: .75rem; }
    #final-students-table_wrapper .dataTables_info { padding-top: .75rem; }
    #final-students-table_wrapper .dataTables_paginate { padding-top: .5rem; }
</style>
@endpush

@push('scripts')
<script>
$(function () {
    const selectedIds = new Set();
    let finalStudentsTable = null;

    // DataTables owns pagination and the horizontal scroll bar.
    if ($.fn.DataTable) {
        finalStudentsTable = $('#final-students-table').DataTable({
            paging: true,
            pageLength: 50,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
            // pagingType: 'full_numbers',
            scrollX: true,
            autoWidth: false,
            ordering: true,
            order: [[1, 'desc']],
            columnDefs: [{ targets: [0, 13], orderable: false, searchable: false }],
            language: { emptyTable: 'No Final Students found for this tab.' },
            drawCallback: function () { syncPageCheckboxes(); }
        });
    }

    function currentPageCheckboxes() {
        if (finalStudentsTable) return $(finalStudentsTable.rows({ page: 'current' }).nodes()).find('.final-student-checkbox');
        return $('.final-student-checkbox:visible');
    }

    function syncPageCheckboxes() {
        $('.final-student-checkbox').each(function () { this.checked = selectedIds.has(String(this.value)); });
        const visible = currentPageCheckboxes();
        const checked = visible.filter(':checked');
        $('#selectCurrentPage').prop('checked', visible.length > 0 && visible.length === checked.length)
            .prop('indeterminate', checked.length > 0 && checked.length < visible.length);
        $('#selectedCountBadge').text('Selected: ' + selectedIds.size);
        renderSelectedInputs();
    }

    function renderSelectedInputs() {
        const container = $('#selectedFinalInputs').empty();
        selectedIds.forEach(id => $('<input>', {type: 'hidden', name: 'student_final_ids[]', value: id}).appendTo(container));
    }

    $(document).on('change', '.final-student-checkbox', function () {
        const id = String(this.value);
        if (this.checked) selectedIds.add(id); else selectedIds.delete(id);
        syncPageCheckboxes();
    });

    $('#selectCurrentPage').on('change', function () {
        const checked = this.checked;
        currentPageCheckboxes().each(function () {
            if (checked) selectedIds.add(String(this.value)); else selectedIds.delete(String(this.value));
            this.checked = checked;
        });
        syncPageCheckboxes();
    });

    $('#clearSelection').on('click', function () { selectedIds.clear(); syncPageCheckboxes(); });

    $('#openBulkCategoryModal').on('click', function () {
        if (!selectedIds.size) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'No students selected', text: 'Please select at least one student.'});
            else alert('Please select at least one student.');
            return;
        }
        const inputs = $('#bulkCategorySelectedInputs').empty();
        selectedIds.forEach(id => $('<input>', {type: 'hidden', name: 'student_final_ids[]', value: id}).appendTo(inputs));
        bootstrap.Modal.getOrCreateInstance(document.getElementById('bulkCategoryModal')).show();
    });

    $('#bulkCategoryForm').on('submit', function (e) {
        if (!selectedIds.size || !$('#bulkBenefitType').val()) {
            e.preventDefault();
            if (window.Swal) Swal.fire({icon: 'warning', title: 'Select students and a category'}); else alert('Select students and a category.');
            return;
        }
        const inputs = $('#bulkCategorySelectedInputs').empty();
        selectedIds.forEach(id => $('<input>', {type: 'hidden', name: 'student_final_ids[]', value: id}).appendTo(inputs));
        if (window.Swal) {
            e.preventDefault();
            Swal.fire({icon: 'warning', title: 'Change category for selected students?', text: 'This will update ' + selectedIds.size + ' selected student(s).', showCancelButton: true, confirmButtonText: 'Update Category', cancelButtonText: 'Cancel', reverseButtons: false})
                .then(result => { if (result.isConfirmed) this.submit(); });
        }
    });

    $('#openCertificateModal').on('click', function () {
        if (!selectedIds.size) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'No students selected', text: 'Please select at least one student.'});
            else alert('Please select at least one student.');
            return;
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('certificateModal')).show();
    });

    $('#confirmCertificateDownload').on('click', function () {
        const type = $('#certificateType').val();
        if (!type) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'Select certificate type'}); else alert('Please select a certificate type.');
            return;
        }
        $('#certificateTypeInput').val(type);
        $('#certificateCourseInput').val($('#certificateCourse').val());
        $('#certificateDateInput').val($('#certificateDate').val());
        $('#certificateDetailsInput').val($('#certificateDetails').val());
        renderSelectedInputs();
        const submit = () => document.getElementById('finalStudentsForm').submit();
        if (window.Swal) Swal.fire({icon: 'question', title: 'Download certificates?', text: selectedIds.size + ' certificate(s) will be included in one PDF.', showCancelButton: true, confirmButtonText: 'Download', cancelButtonText: 'Cancel', reverseButtons: false}).then(result => { if (result.isConfirmed) submit(); });
        else if (confirm('Download certificates for ' + selectedIds.size + ' selected student(s)?')) submit();
    });

    $('#openLetterModal').on('click', function () {
        if (!selectedIds.size) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'No students selected', text: 'Please select at least one student.'});
            else alert('Please select at least one student.');
            return;
        }
        bootstrap.Modal.getOrCreateInstance(document.getElementById('letterModal')).show();
    });

    $('#confirmLetterDownload').on('click', function () {
        if (!selectedIds.size) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'No students selected', text: 'Please select at least one student.'});
            else alert('Please select at least one student.');
            return;
        }

        const courseName = $('#finalLetterCourse').val().trim();
        const letterDate = $('#finalLetterDate').val();
        const letterType = $('#finalLetterType').val();

        if (!letterType || !courseName || !letterDate) {
            if (window.Swal) Swal.fire({icon: 'warning', title: 'Required fields missing', text: 'Please select a letter type, enter the course / technology, and choose the letter date.'});
            else alert('Please select a letter type, enter the course / technology, and choose the letter date.');
            return;
        }

        $('#finalLetterTypeInput').val(letterType);
        $('#finalLetterDateInput').val(letterDate);
        $('#finalLetterCourseInput').val(courseName);

        const inputs = $('#selectedLetterInputs').empty();
        selectedIds.forEach(id => $('<input>', {
            type: 'hidden', name: 'student_final_ids[]', value: id
        }).appendTo(inputs));

        const submit = () => document.getElementById('finalLettersForm').submit();
        const typeLabel = $('#finalLetterType option:selected').text();

        if (window.Swal) {
            Swal.fire({
                icon: 'question',
                title: 'Generate selected letters?',
                text: typeLabel + ' will be generated for ' + selectedIds.size + ' selected student(s) and downloaded as a ZIP.',
                showCancelButton: true,
                confirmButtonText: 'Generate & Download',
                cancelButtonText: 'Cancel',
                reverseButtons: false
            }).then(result => { if (result.isConfirmed) submit(); });
        } else if (confirm('Generate ' + typeLabel + ' for ' + selectedIds.size + ' selected student(s)?')) {
            submit();
        }
    });

    $('#final-students-table').on('click', '.edit-final-student', function () {
        const b = $(this);
        $('#editFinalForm').attr('action', b.data('url'));
        $('#editFinalStatus').val(b.data('status') || 'selected');
        $('#editBenefitType').val(b.data('benefit') || '');
        $('#editBenefitAmount').val(b.data('amount') || '');
        $('#editBenefitDetails').val(b.attr('data-details') || '');
        $('#editOfferDate').val(b.data('offer-date') || '');
        $('#editFinalCourseName').val(b.attr('data-course-name') || '');
        $('#editFinalNotes').val(b.attr('data-notes') || '');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('editFinalModal')).show();
    });

    // Restore IDs carried through filter submits, then sync the visible page.
    @if(request()->has('selected_ids'))
        @foreach((array) request('selected_ids') as $selectedId)
            selectedIds.add(String(@json((string) $selectedId)));
        @endforeach
    @endif
    syncPageCheckboxes();

    // Auto-submit dropdown filters when College, Class, or Final Status changes.
    $('#filterForm').on('change', 'select.filter-change', function () {
        $('#filterForm').trigger('submit');
    });

    // Auto-submit search after the user pauses typing.
    let finalSearchTimer;
    $('#filterForm').on('input', 'input.filter-search', function () {
        clearTimeout(finalSearchTimer);
        finalSearchTimer = setTimeout(function () {
            $('#filterForm').trigger('submit');
        }, 400);
    });

    // Preserve selected IDs if a server-side filter is submitted.
    $('#filterForm').on('submit', function () {
        $(this).find('input[name="selected_ids[]"]').remove();
        selectedIds.forEach(id => $('<input>', {type: 'hidden', name: 'selected_ids[]', value: id}).appendTo(this));
    });
});
</script>
@endpush
