@extends('layouts.app')

@section('title', 'Intern Students')

@section('content')
<style>
    table.dataTable td,
    #internStudentsTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    #internStudentsTable th,
    #internStudentsTable td {
        white-space: nowrap;
    }

    #internStudentsTable_wrapper .dataTables_length,
    #internStudentsTable_wrapper .dataTables_filter {
        margin-bottom: 12px;
    }

    #internStudentsTable_wrapper .dataTables_paginate {
        margin-top: 12px;
    }

    .intern-filter-chip {
        display: inline-block;
        padding: .35rem .65rem;
        border-radius: .35rem;
        background: #6b51df;
        color: #fff;
        text-decoration: none;
        font-size: .8rem;
    }

    .intern-filter-chip:hover {
        color: #fff;
        opacity: .9;
    }

    .intern-action-btn {
        background-color: #6b51df;
        color: #fff;
    }

    .intern-action-btn:hover {
        background-color: #5940c6;
        color: #fff;
    }
</style>

<div class="container mt-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-4">
            <h1 class="page_heading">Intern Students</h1>
        </div>

        <div class="col-md-8 d-flex justify-content-md-end flex-wrap gap-2">
            <a href="{{ route('intern-students.create') }}" class="btn"
               style="background-color:#6b51df;color:#fff;">+ Add Intern Student</a>

            <!-- <a href="{{ route('intern-students.importForm') }}" class="btn"
               style="background-color:#6b51df;color:#fff;">Import Students</a> -->

            <a href="{{ route('intern-students.export', request()->query()) }}" class="btn"
               style="background-color:#6b51df;color:#fff;">Download Excel</a>

            <button type="button" id="deleteSelectedInterns" class="btn btn-danger">
                Delete Selected
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $filtersApplied = collect(request()->except(['page', 'limit']))
            ->filter(fn ($value) => $value !== null && $value !== '')
            ->isNotEmpty();
    @endphp

    @if($filtersApplied)
        <div class="mb-3">
            <strong>Active Filters:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @foreach(request()->except(['page']) as $key => $value)
    @if(is_scalar($value) && $value !== '')
        @php
            $displayValue = $value;

            if ($key === 'college_name') {
                $selectedCollege = $colleges->firstWhere('id', $value);

                if ($selectedCollege) {
                    $displayValue = $selectedCollege->college_display_name
                        ?? $selectedCollege->college_name
                        ?? $value;
                }
            }

            if ($key === 'technology') {
                $selectedTechnology = $technologies->firstWhere('id', $value);

                if ($selectedTechnology) {
                    $displayValue = $selectedTechnology->course_name;
                }
            }
        @endphp

        <a href="{{ request()->fullUrlWithQuery([$key => null, 'page' => null]) }}"
           class="intern-filter-chip">
            {{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $displayValue }} ✕
        </a>
    @endif
@endforeach

                <a href="{{ route('intern-students.index') }}"
                   class="badge bg-danger text-decoration-none p-2">Clear All ✕</a>
            </div>
        </div>
    @endif

    {{-- Same accordion-style filter panel as the Student module --}}
    <div class="accordion mb-3" id="internFiltersAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button"
                        data-bs-toggle="collapse" data-bs-target="#internCollapseFilters"
                        aria-expanded="false" aria-controls="internCollapseFilters">
                    <b>Filters</b>
                </button>
            </h2>

            <div id="internCollapseFilters" class="accordion-collapse collapse"
                 data-bs-parent="#internFiltersAccordion">
                <div class="accordion-body">
                    <form method="GET" action="{{ route('intern-students.index') }}" id="internFilterForm">
                        <div class="row g-2">
                            
                             
                             <div class="col-md-3">
                                <select name="college_name" class="form-control">
                                    <option value="">--College--</option>
                                    @foreach($colleges as $college)
									    <option value="{{ $college->id }}"
									        {{ (string) request('college_name') === (string) $college->id ? 'selected' : '' }}>
									        {{ $college->college_display_name ?? $college->college_name }}
									    </option>
									@endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="technology" class="form-control">
                                    <option value="">--Technology--</option>

                                    @foreach($technologies as $technology)
                                        <option value="{{ $technology->id }}"
                                            {{ (string) request('technology') === (string) $technology->id ? 'selected' : '' }}>
                                            {{ $technology->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <select name="gender" class="form-control">
                                    <option value="">--Gender--</option>
                                    @foreach(['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}" {{ request('gender') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            

                            @php
                                $flagFilters = [
                                    
                                    'is_online' => 'Study Mode',
                                    
                                ];
                            @endphp
                            @foreach($flagFilters as $field => $label)
                                <div class="col-md-3">
                                    <select name="{{ $field }}" class="form-control">
                                        <option value="">--{{ $label }}--</option>
                                        <option value="1" {{ request($field) === '1' ? 'selected' : '' }}>Yes</option>
                                        <option value="0" {{ request($field) === '0' ? 'selected' : '' }}>No</option>
                                    </select>
                                </div>
                            @endforeach

                             
                            
                            <div class="col-md-2">
                                <input type="number" name="limit" min="1" max="5000" class="form-control"
                                       placeholder="Limit" value="{{ request('limit') }}">
                            </div>
                        </div>

                        <div class="mt-3 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn" style="background-color:#6b51df;color:#fff;">Apply Filters</button>
                            <a href="{{ route('intern-students.index') }}" class="btn btn-secondary">Reset</a>
                            <a href="{{ route('intern-students.export', request()->query()) }}" class="btn btn-primary">Download Excel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk action form: selected IDs are populated by JavaScript, including IDs selected on other DataTable pages --}}
    <form id="bulkDeleteInternsForm" method="POST" action="{{ route('intern-students.bulk-delete') }}" class="d-none">
        @csrf
    </form>

    <div class="desktop-view">
        <div class="table-responsive">
            <table id="internStudentsTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAllInterns"></th>
                        <th class="text-center" width="100px">#</th>
                        
                        <th class="text-center">Name</th>
                        <th class="text-center">Father Name</th>
                        <th class="text-center">Gender</th>
                        
                        <th class="text-center" width="180px">College</th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">Email</th>
                        
                        <th class="text-center">Technology</th>
                        
                        <th class="text-center">Study Mode</th>
                        
                        <th class="text-center">Created At</th>
                        <th class="text-center">Updated At</th>
                        <th width="150px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($internStudents as $internStudent)
                        <tr>
                            <td>
                                <input type="checkbox" class="record_checked" value="{{ $internStudent->id }}">
                            </td>
                            <td></td>
                            
                            <td>{{ $internStudent->student_name }}</td>
                            <td>{{ $internStudent->f_name ?? '-' }}</td>
                            <td>{{ $internStudent->gender ?? '-' }}</td>
                            
                            <td>{{ $internStudent->collegeData->FullName ?? '-' }}</td>
                            <td>{{ $internStudent->contact ?? '-' }}</td>
                            <td style="text-transform:none;">{{ $internStudent->email_id ?? '-' }}</td>
                            
                             {{-- Technology / Course --}}
							<td>
							    {{ $internStudent->course_name ?: '-' }}
							</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $internStudent->is_online ? 'success' : 'secondary' }}">
                                    {{ $internStudent->is_online ? 'Online' : 'Offline' }}
                                </span>
                            </td>
                             
                            <td>{{ $internStudent->created_at ? \Carbon\Carbon::parse($internStudent->created_at)->format('d M Y') : '-' }}</td>
                            <td>{{ $internStudent->updated_at ? \Carbon\Carbon::parse($internStudent->updated_at)->format('d M Y') : '-' }}</td>
                            <td class="text-center">
                                <div class="mb-2">
                                    <a href="{{ route('intern-students.edit', $internStudent->id) }}"
                                       class="btn btn-sm edit_btn" data-bs-toggle="tooltip"
                                       data-bs-placement="top" title="Edit Intern Student">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    <form action="{{ route('intern-students.destroy', $internStudent->id) }}"
                                          method="POST" style="display:inline-block;" class="delete-intern-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Delete Intern Student"
                                                data-swal-confirm="Delete this intern student?">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Selection persists while moving between DataTables pages.
    const selectedInternIds = new Set();
    const savedPageKey = 'intern_students_datatable_page';
    const savedLengthKey = 'intern_students_datatable_length';
    const savedPage = parseInt(sessionStorage.getItem(savedPageKey) || '0', 10);
    const savedLength = parseInt(sessionStorage.getItem(savedLengthKey) || '10', 10);
    const allowedLengths = [10, 15, 20, 25, 50, 100];
    const pageLength = allowedLengths.includes(savedLength) ? savedLength : 10;

    $.fn.dataTable.ext.pager.numbers_length = 12;

    const table = $('#internStudentsTable').DataTable({
        pageLength: pageLength,
        displayStart: savedPage * pageLength,
        // pagingType: 'full_numbers',
        lengthMenu: [10, 15, 20, 25, 50, 100],
        scrollX: true,
        autoWidth: false,
        columnDefs: [
            {
                targets: [0, 1, 12],
                searchable: false,
                orderable: false
            }
        ],
        order: []
    });

    function updateCurrentPageCheckboxes() {
        $(table.rows({ page: 'current' }).nodes()).find('.record_checked').each(function () {
            this.checked = selectedInternIds.has(String(this.value));
        });

        const currentCheckboxes = $(table.rows({ page: 'current' }).nodes())
            .find('.record_checked:not(:disabled)');
        const checkedCount = currentCheckboxes.filter(':checked').length;

        $('#checkAllInterns').prop(
            'checked',
            currentCheckboxes.length > 0 && checkedCount === currentCheckboxes.length
        );
    }

    function updateRowNumbers() {
        const pageInfo = table.page.info();
        table.column(1, { page: 'current' }).nodes().each(function (cell, index) {
            cell.innerHTML = pageInfo.start + index + 1;
        });
    }

    table.on('draw.dt', function () {
        updateRowNumbers();
        updateCurrentPageCheckboxes();
        sessionStorage.setItem(savedPageKey, table.page());
        sessionStorage.setItem(savedLengthKey, table.page.len());
    });

    table.on('length.dt', function (event, settings, len) {
        sessionStorage.setItem(savedLengthKey, len);
    });

    $('#checkAllInterns').on('click', function (event) {
        event.stopPropagation();
    });

    $('#checkAllInterns').on('change', function () {
        const shouldCheck = this.checked;

        $(table.rows({ page: 'current' }).nodes()).find('.record_checked:not(:disabled)').each(function () {
            const id = String(this.value);
            this.checked = shouldCheck;

            if (shouldCheck) {
                selectedInternIds.add(id);
            } else {
                selectedInternIds.delete(id);
            }
        });

        updateCurrentPageCheckboxes();
    });

    $('#internStudentsTable').on('change', '.record_checked', function () {
        const id = String(this.value);

        if (this.checked) {
            selectedInternIds.add(id);
        } else {
            selectedInternIds.delete(id);
        }

        updateCurrentPageCheckboxes();
    });

    // Apply dropdown filters immediately when the selection changes.
    // Keep text/number inputs manual so the form does not submit while typing.
    $('#internFilterForm select').on('change', function () {
        sessionStorage.removeItem(savedPageKey);
        sessionStorage.removeItem(savedLengthKey);
        selectedInternIds.clear();
        $('#internFilterForm').trigger('submit');
    });

    // Reset saved pagination and cross-page selections when applying filters.
    $('#internFilterForm').on('submit', function () {
        sessionStorage.removeItem(savedPageKey);
        sessionStorage.removeItem(savedLengthKey);
        selectedInternIds.clear();
    });

    $('a[href="{{ route('intern-students.index') }}"]').on('click', function () {
        sessionStorage.removeItem(savedPageKey);
        sessionStorage.removeItem(savedLengthKey);
    });

    // SweetAlert confirmation for individual delete.
    $(document).on('submit', '.delete-intern-form', function (event) {
        const form = this;
        if (form.dataset.confirmed === '1') return;

        event.preventDefault();

        if (window.Swal) {
            Swal.fire({
                title: 'Delete intern student?',
                text: 'This record will be moved to trash.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        } else if (window.confirm('Delete this intern student?')) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    });

    $('#deleteSelectedInterns').on('click', function () {
        const ids = Array.from(selectedInternIds);

        if (!ids.length) {
            if (window.Swal) {
                Swal.fire({ icon: 'warning', text: 'Select at least one intern student.' });
            } else {
                window.alert('Select at least one intern student.');
            }
            return;
        }

        const submitBulkDelete = function () {
            const form = document.getElementById('bulkDeleteInternsForm');
            form.querySelectorAll('input[name="ids[]"]').forEach(input => input.remove());

            ids.forEach(function (id) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
                form.appendChild(input);
            });

            form.submit();
        };

        if (window.Swal) {
            Swal.fire({
                title: 'Delete selected intern students?',
                text: ids.length + ' record(s) selected, including records selected on other table pages.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete selected',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (result.isConfirmed) submitBulkDelete();
            });
        } else if (window.confirm('Delete ' + ids.length + ' selected intern student(s)?')) {
            submitBulkDelete();
        }
    });

    updateRowNumbers();
    updateCurrentPageCheckboxes();
});
</script>
@endsection
