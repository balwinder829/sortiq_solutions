
@extends('layouts.app')

@section('title', 'Old Students')

@section('content')
<div class="container-fluid mt-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h1 class="page_heading mb-0">Old Students</h1>

        <a href="{{ route('old-students.export', request()->query()) }}"
           class="btn btn-primary">
            <i class="fa fa-download"></i> Export Excel
        </a>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- Filters --}}
    <div class="accordion mb-3" id="oldStudentFilterAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button {{ request()->hasAny(['college_name', 'technology', 'gender', 'is_online', 'from_session']) ? '' : 'collapsed' }}"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#oldStudentFilters">
                    <strong>Filters</strong>
                </button>
            </h2>

            <div id="oldStudentFilters"
                 class="accordion-collapse collapse {{ request()->hasAny(['college_name', 'technology', 'gender', 'is_online', 'from_session']) ? 'show' : '' }}"
                 data-bs-parent="#oldStudentFilterAccordion">

                <div class="accordion-body">
                    <form method="GET"
                          action="{{ route('old-students.index') }}"
                          id="oldStudentFilterForm">

                        <div class="row g-2">

                            {{-- College --}}
                            <div class="col-md-3 col-sm-6">
                                <select name="college_name" class="form-control old-filter">
    <option value="">-- Select College --</option>

    @foreach($colleges as $college)
        <option value="{{ $college->id }}"
            {{ (string) request('college_name') === (string) $college->id ? 'selected' : '' }}>
            {{ $college->FullName ?? $college->college_name ?? 'College '.$college->id }}
        </option>
    @endforeach
</select>
                            </div>

                            {{-- Technology --}}
                            <div class="col-md-3 col-sm-6">
                               <select name="technology" class="form-control old-filter">
    <option value="">-- Select Technology --</option>

    @foreach($courses as $course)
        <option value="{{ $course->id }}"
            {{ (string) request('technology') === (string) $course->id ? 'selected' : '' }}>
            {{ $course->course_name }}
        </option>
    @endforeach
</select>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-2 col-sm-6">
                                <select name="gender" class="form-control old-filter">
                                    <option value="">-- Gender --</option>
                                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            {{-- Study Mode --}}
                            <div class="col-md-2 col-sm-6">
                                <select name="is_online" class="form-control old-filter">
                                    <option value="">-- Study Mode --</option>
                                    <option value="1" {{ request('is_online') === '1' ? 'selected' : '' }}>Online</option>
                                    <option value="0" {{ request('is_online') === '0' ? 'selected' : '' }}>Offline</option>
                                </select>
                            </div>

                            {{-- From Session --}}
                            <div class="col-md-2 col-sm-6">
                                <select name="from_session" class="form-control old-filter">
                                    <option value="">-- From Session --</option>

                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}"
                                            {{ (string)request('from_session') === (string)$session->id ? 'selected' : '' }}>
                                            {{ $session->session_display_name ?? $session->session_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                Apply Filters
                            </button>

                            <a href="{{ route('old-students.index') }}"
                               class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Actions --}}
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <button type="button"
                id="bulkDeleteButton"
                class="btn btn-danger">
            <i class="fa fa-trash"></i> Delete Selected
        </button>
    </div>

    {{-- Bulk Delete Form --}}
    <form id="bulkDeleteForm"
          method="POST"
          action="{{ route('old-students.bulkDelete') }}"
          class="d-none">
        @csrf
        <div id="bulkDeleteInputs"></div>
    </form>

    {{-- Students Table --}}
     
            <div class="table-responsive">
                <table id="oldStudentsTable"
                       class="table table-bordered table-striped table-hover w-100">

                    <thead class="table-light">
                        <tr>
                            <th width="35">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th>#</th>
                            <th>Serial No.</th>
                            <th>Student Name</th>
                            <th>Father Name</th>
                            <th>Gender</th>
                            <th>From Session</th>
                            <th>College</th>
                            <th>Contact</th>
                            <th>Email</th>
                            <th>Technology</th>
                            <th>Department</th>
                            <th>Study Mode</th>
                            <th>Status</th>
                            <th>Joining Date</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Transferred At</th>
                            <th width="110">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)
                            @php
    $technologyNames = [];

    if (!empty($student->technology)) {
        foreach (explode(',', $student->technology) as $technologyId) {
            $technologyId = trim($technologyId);

            if ($technologyId === '' || !is_numeric($technologyId)) {
                continue;
            }

            $technology = $courseMap->get((int) $technologyId);

            // Show name only when the Course record exists.
            if ($technology) {
                $technologyNames[] = $technology->course_name;
            }
        }
    }

    $college = is_numeric($student->college_name)
        ? $collegeMap->get((int) $student->college_name)
        : null;

    $fromSession = $sessionMap->get($student->from_session);

    $formatDate = function ($date) {
        return $date
            ? \Carbon\Carbon::parse($date)->format('d M Y')
            : '-';
    };
@endphp

                            <tr>
                                <td>
                                    <input type="checkbox"
                                           class="old-student-checkbox"
                                           value="{{ $student->id }}">
                                </td>

                                <td></td>
                                <td>{{ $student->sno ?? '-' }}</td>
                                <td>{{ $student->student_name ?? '-' }}</td>
                                <td>{{ $student->f_name ?? '-' }}</td>
                                <td>{{ ucfirst($student->gender ?? '-') }}</td>

                                <td>
                                    {{ $fromSession->session_display_name ?? $fromSession->session_name ?? '-' }}
                                </td>

                                <td>{{ $college->FullName ?? $college->college_name ?? '-' }}</td>

                                <td>{{ $student->contact ?? '-' }}</td>
                                <td>{{ $student->email_id ?? '-' }}</td>

                                <td>{{ count($technologyNames) ? implode(', ', $technologyNames) : '-' }}</td>

                                <td>{{ $student->department ?? '-' }}</td>

                                <td>
                                    {{ ($student->is_online ?? 0) == 1 ? 'Online' : 'Offline' }}
                                </td>

                                <td>{{ $student->status ?? '-' }}</td>

                                <td>{{ $formatDate($student->join_date ?? null) }}</td>
                                <td>{{ $formatDate($student->start_date ?? null) }}</td>
                                <td>{{ $formatDate($student->end_date ?? null) }}</td>
                                <td>{{ $formatDate($student->transferred_at ?? null) }}</td>

                                <td class="text-nowrap">
                                    <a href="{{ route('old-students.edit', $student->id) }}"
                                       class="btn btn-sm btn-primary"
                                       title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form method="POST"
                                          action="{{ route('old-students.destroy', $student->id) }}"
                                          class="d-inline single-delete-form">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                title="Delete">
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
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // Apply filters automatically
    $('.old-filter').on('change', function () {
        $('#oldStudentFilterForm').submit();
    });

    // DataTable
    let table = $('#oldStudentsTable').DataTable({
        pageLength: 50,
        lengthMenu: [10, 25, 50, 100],
        scrollX: true,
        pagingType: 'full_numbers',
        columnDefs: [
            {
                targets: [0, 1, 18],
                orderable: false,
                searchable: false
            }
        ],
        order: []
    });

    // Persist selected IDs across DataTables pages/search/redraw
    let selectedIds = new Set();

    function syncCheckboxes() {
        table.rows({ page: 'current' }).nodes().to$()
            .find('.old-student-checkbox')
            .each(function () {
                this.checked = selectedIds.has(String(this.value));
            });

        let currentCheckboxes = table.rows({ page: 'current' }).nodes().to$()
            .find('.old-student-checkbox');

        let checkedCount = currentCheckboxes.filter(':checked').length;

        $('#checkAll').prop(
            'checked',
            currentCheckboxes.length > 0 &&
            checkedCount === currentCheckboxes.length
        );
    }

    table.on('draw.dt', function () {
        let pageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, index) {
            cell.innerHTML = pageInfo.start + index + 1;
        });

        syncCheckboxes();
    });

    // Individual checkbox
    $('#oldStudentsTable').on('change', '.old-student-checkbox', function () {
        let id = String(this.value);

        if (this.checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        syncCheckboxes();
    });

    // Select all on current page only
    $('#checkAll').on('change', function () {
        let checked = this.checked;

        table.rows({ page: 'current' }).nodes().to$()
            .find('.old-student-checkbox')
            .each(function () {
                this.checked = checked;

                let id = String(this.value);

                if (checked) {
                    selectedIds.add(id);
                } else {
                    selectedIds.delete(id);
                }
            });

        syncCheckboxes();
    });

    // Bulk delete
    $('#bulkDeleteButton').on('click', function () {

        if (selectedIds.size === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Records Selected',
                text: 'Please select at least 1 record.'
            });
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Selected old students will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then((result) => {

            if (!result.isConfirmed) {
                return;
            }

            let container = $('#bulkDeleteInputs');
            container.empty();

            selectedIds.forEach(function (id) {
                container.append(
                    $('<input>', {
                        type: 'hidden',
                        name: 'ids[]',
                        value: id
                    })
                );
            });

            $('#bulkDeleteForm').submit();
        });
    });

    // Single delete confirmation
    $(document).on('submit', '.single-delete-form', function (e) {
        e.preventDefault();

        let form = this;

        Swal.fire({
            title: 'Are you sure?',
            text: 'This old student will be deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    syncCheckboxes();
});
</script>
@endpush
