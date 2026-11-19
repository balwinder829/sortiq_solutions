
@extends('layouts.app')

@section('title', 'Seminar Registrations')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    #seminarRegistrationsTable th,
    #seminarRegistrationsTable td {
        white-space: nowrap;
        vertical-align: middle;
    }

    #seminarRegistrationsTable_wrapper .dataTables_scroll {
        margin-bottom: 8px;
    }
</style>

<div class="container mt-4">

    {{-- Page Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-4">
            <h1 class="page_heading">Seminar Registrations</h1>
        </div>

        <div class="col-md-8 d-flex justify-content-end gap-2 flex-wrap">
            <button type="button"
                    class="btn btn-primary"
                    id="copySeminarLink">
                <i class="fa fa-copy"></i> Copy Registration Link
            </button>
            <button type="button" id="deleteSelected" class="btn btn-danger">
                Delete Selected
            </button>

           <button type="button"
                    class="btn btn-success exportExcelBtn">
                <i class="fa fa-file-excel"></i> Download Excel
            </button>

        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $filterFields = [
            'college_name_input' => 'College',
            'course_name_input'  => 'Course',
            'student_name'       => 'Student Name',
            'semester'           => 'Semester',
            'gender'             => 'Gender',
            'contact'            => 'Contact',
        ];

        $activeFilters = collect($filterFields)
            ->filter(fn($label, $field) => request()->filled($field));

        $filtersApplied = $activeFilters->isNotEmpty();
    @endphp

    {{-- Active Filters --}}
    @if($filtersApplied)
        <div class="mb-3">

            <strong>Active Filters:</strong>

            <div class="d-flex flex-wrap gap-2 mt-2">

                @foreach($activeFilters as $field => $label)

                    <a href="{{ request()->fullUrlWithQuery([$field => null]) }}"
                       class="badge bg-primary text-decoration-none">

                        {{ $label }}:
                        {{ $field === 'gender' ? ucfirst(request($field)) : request($field) }}
                        ✕

                    </a>

                @endforeach

                <a href="{{ route('admin.seminar-registration.index') }}"
                   class="badge bg-danger text-decoration-none">
                    Clear All ✕
                </a>

            </div>
        </div>
    @endif

    {{-- Filters Accordion --}}
    <div class="accordion mb-3" id="filtersAccordion">

        <div class="accordion-item">

            <h2 class="accordion-header">
                <button class="accordion-button collapsed"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#collapseFilters">
                    <b>Filters</b>
                </button>
            </h2>

            <div id="collapseFilters"
                 class="accordion-collapse collapse {{ $filtersApplied ? 'show' : '' }}"
                 data-bs-parent="#filtersAccordion">

                <div class="accordion-body">

                    <form method="GET"
                          action="{{ route('admin.seminar-registration.index') }}"
                          id="filterForm"
                          class="mb-2">

                        <div class="row g-2">

                            {{-- College --}}
                            <div class="col-md-3 col-6">

                                <select name="college_name_input"
                                        class="form-control filterchange select2">

                                    <option value="">--College--</option>

                                    @foreach($colleges as $college)

                                        @php
                                            $collegeName = $college;
                                        @endphp

                                        @continue(empty($collegeName))

                                        <option value="{{ $collegeName }}"
                                            {{ (string)request('college_name_input') === (string)$collegeName ? 'selected' : '' }}>
                                            {{ $collegeName }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            {{-- Course --}}
                            <div class="col-md-3 col-6">

                                <select name="course_name_input"
                                        class="form-control filterchange select2">

                                    <option value="">--Course / Technology--</option>

                                    @foreach($technologies as $course)

                                        @php
                                            $courseName = $course;
                                        @endphp

                                        @continue(empty($courseName))

                                        <option value="{{ $courseName }}"
                                            {{ (string)request('course_name_input') === (string)$courseName ? 'selected' : '' }}>
                                            {{ $courseName }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            {{-- Student Name --}}
                            <div class="col-md-2 col-6">

                                <input type="text"
                                       name="student_name"
                                       class="form-control filterchangetext"
                                       placeholder="Student Name"
                                       value="{{ request('student_name') }}">

                            </div>

                            {{-- Semester --}}
                            <div class="col-md-2 col-6">

                                <input type="text"
                                       name="semester"
                                       class="form-control filterchangetext"
                                       placeholder="Semester"
                                       value="{{ request('semester') }}">

                            </div>

                            {{-- Gender --}}
                            <div class="col-md-2 col-6">

                                <select name="gender" class="form-control filterchange">

                                    <option value="">--Gender--</option>

                                    <option value="male"
                                        {{ request('gender') === 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>

                                    <option value="female"
                                        {{ request('gender') === 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>

                                    <option value="other"
                                        {{ request('gender') === 'other' ? 'selected' : '' }}>
                                        Other
                                    </option>

                                </select>

                            </div>

                            {{-- Contact --}}
                            <div class="col-md-3 col-6">

                                <input type="text"
                                       name="contact"
                                       class="form-control filterchangetext"
                                       placeholder="Mobile Number"
                                       value="{{ request('contact') }}">

                            </div>

                            <div class="col-12 mt-2">

                                <a href="{{ route('admin.seminar-registration.index') }}"
                                   class="btn btn-secondary">
                                    Reset
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Apply Filters
                                </button>

                                <a href="{{ route('admin.seminar-registration.exportExcel', request()->query()) }}"
                                   class="btn btn-success">
                                    <i class="fa fa-file-excel"></i> Download Excel
                                </a>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

    {{-- Bulk Delete Form --}}
    <form id="bulkDeleteForm"
          method="POST"
          action="{{ route('admin.seminar-registration.bulkDestroy') }}"
          class="d-none">

        @csrf

        <input type="hidden" name="ids" id="bulkDeleteIds">

    </form>

    {{-- Registration Table --}}
    <div class="desktop-view">

        <div class="table-responsive">

            <table id="seminarRegistrationsTable"
                   class="table table-bordered table-striped">

                <thead class="table-light">

                    <tr>

                        <th>
                            <input type="checkbox" id="checkAll">
                        </th>

                        <th class="text-center" width="100px">#</th>

                        <th class="text-center">Name</th>

                        <th class="text-center">Father Name</th>

                        <th class="text-center">Gender</th>

                        <th class="text-center">College</th>

                        <th class="text-center">Contact</th>

                        <th class="text-center">Email</th>

                        <th class="text-center">Technology</th>

                        <th class="text-center">Semester</th>

                        <th class="text-center">Registered Date</th>

                        <th width="100px" class="text-center">Action</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($students as $student)

                        <tr>

                            <td>
                                <input type="checkbox"
                                       class="record_checked"
                                       value="{{ $student->id }}">
                            </td>

                            <td></td>

                            <td>{{ $student->student_name }}</td>

                            <td>{{ $student->father_name }}</td>

                            <td>{{ $student->gender }}</td>

                            <td>
                                {{ $student->college_name_input }}
                            </td>

                            <td>{{ $student->contact }}</td>

                            <td>{{ $student->email_id }}</td>

                            <td>{{ $student->course_name_input }}</td>

                            <td>{{ $student->semester ?? '-' }}</td>

                            <td>
                                {{ $student->created_at
                                    ? \Carbon\Carbon::parse($student->created_at)->format('d M Y')
                                    : '-' }}
                            </td>

                            <td class="text-center">

                                <form action="{{ route('admin.seminar-registration.destroy', $student->id) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      class="seminar-single-delete-form">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm"
                                            data-bs-toggle="tooltip"
                                            title="Delete Registration">

                                        <i class="fa fa-trash"></i>

                                    </button>

                                </form>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
$(document).ready(function () {

    let selectedRegistrations = [];

    $.fn.dataTable.ext.pager.numbers_length = 12;

    const table = $('#seminarRegistrationsTable').DataTable({

        pageLength: 10,

        pagingType: 'full_numbers',

        lengthMenu: [10, 15, 20, 25, 50, 100],

        scrollX: true,

        autoWidth: false,

        columnDefs: [
            {
                targets: [0, 1],
                searchable: false,
                orderable: false
            },
            {
                targets: -1,
                searchable: false,
                orderable: false
            }
        ]

    });

    // Synchronize selected checkboxes.
    function syncSelectedCheckboxes() {

        table.rows({ page: 'current' }).nodes().to$()
            .find('.record_checked')
            .each(function () {

                this.checked = selectedRegistrations.includes(String(this.value));

            });

        const current = table.rows({ page: 'current' })
            .nodes().to$()
            .find('.record_checked:not(:disabled)');

        $('#checkAll').prop(
            'checked',
            current.length > 0 &&
            current.filter(':checked').length === current.length
        );

    }

    // Get selected IDs.
    function getSelectedIds() {

        table.$('.record_checked:checked').each(function () {

            const id = String(this.value);

            if (!selectedRegistrations.includes(id)) {
                selectedRegistrations.push(id);
            }

        });

        return selectedRegistrations;

    }

    // Serial number and pagination.
    table.on('draw.dt', function () {

        const info = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {

            cell.innerHTML = info.start + i + 1;

        });

        syncSelectedCheckboxes();

    }).draw();

    // Select all current page.
    $('#checkAll').on('change', function () {

        const checked = this.checked;

        table.rows({ page: 'current' }).nodes().to$()
            .find('.record_checked')
            .each(function () {

                const id = String(this.value);

                this.checked = checked;

                if (checked && !selectedRegistrations.includes(id)) {
                    selectedRegistrations.push(id);
                }

                if (!checked) {
                    selectedRegistrations = selectedRegistrations.filter(
                        item => item !== id
                    );
                }

            });

        syncSelectedCheckboxes();

    });

    // Individual selection.
    $('#seminarRegistrationsTable').on(
        'change',
        '.record_checked',
        function () {

            const id = String(this.value);

            if (this.checked && !selectedRegistrations.includes(id)) {
                selectedRegistrations.push(id);
            }

            if (!this.checked) {
                selectedRegistrations = selectedRegistrations.filter(
                    item => item !== id
                );
            }

            syncSelectedCheckboxes();

        }
    );

    // Bulk delete.
    $('#deleteSelected').on('click', function () {

        const ids = getSelectedIds();

        if (!ids.length) {

            Swal.fire({
                icon: 'warning',
                text: 'Please select at least 1 record.'
            });

            return;

        }

        Swal.fire({

            title: 'Delete selected registrations?',

            text: 'Only the selected seminar registration records will be deleted.',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) return;

            $('#bulkDeleteIds').val(JSON.stringify(ids));

            $('#bulkDeleteForm').trigger('submit');

        });

    });

    // Individual delete.
    $(document).on(
        'submit',
        '.seminar-single-delete-form',
        function (e) {

            const form = this;

            e.preventDefault();

            Swal.fire({

                title: 'Delete this registration?',

                text: 'This seminar registration record will be deleted.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {
                    form.submit();
                }

            });

        }
    );

    // Automatic dropdown filtering.
    $('.filterchange').on('change', function () {

        $('#filterForm').trigger('submit');

    });

    // Debounced text filtering.
    let filterTimer;

    $('.filterchangetext').on('input', function () {

        clearTimeout(filterTimer);

        filterTimer = setTimeout(function () {

            $('#filterForm').trigger('submit');

        }, 500);

    });

    // Excel export: selected records first, otherwise applied filters.
$('.exportExcelBtn').on('click', function () {

    const ids = getSelectedIds();

    const params = new URLSearchParams(window.location.search);

    // Remove any old IDs.
    params.delete('ids[]');
    params.delete('ids');

    // Selected records take priority.
    if (ids.length > 0) {

        ids.forEach(function (id) {
            params.append('ids[]', id);
        });

    }

    const exportUrl = @json(route('admin.seminar-registration.exportExcel'));

    const queryString = params.toString();

    window.location.href = exportUrl +
        (queryString ? '?' + queryString : '');

});

// Copy Seminar Registration Link
$('#copySeminarLink').on('click', function () {

    const link = @json(route('seminar.register.form'));

    navigator.clipboard.writeText(link).then(function () {

        Swal.fire({
            icon: 'success',
            title: 'Copied!',
            text: 'Seminar registration link copied successfully.',
            timer: 1500,
            showConfirmButton: false
        });

    }).catch(function () {

        Swal.fire({
            icon: 'error',
            text: 'Unable to copy link.'
        });

    });

});

});
</script>

@endpush
