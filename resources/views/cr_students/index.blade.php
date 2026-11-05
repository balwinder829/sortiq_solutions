@extends('layouts.app')

@section('title', 'CR Students')

@section('content')
<style>
    table.dataTable td { text-transform: capitalize; }
    #crStudentsTable th, #crStudentsTable td { white-space: nowrap; vertical-align: middle; }
    #crStudentsTable_wrapper .dataTables_scroll { margin-bottom: 8px; }
    .cr-source-badge { font-size: 11px; }
</style>

<div class="container mt-4">
    <div class="row mb-4 align-items-center">
        <div class="col-md-3"><h1 class="page_heading">CR Students</h1></div>
        <div class="col-md-9 d-flex justify-content-end gap-2 flex-wrap">
             <button type="button" id="deleteSelected" class="btn btn-danger">Delete Selected</button>
            <a href="{{ route('admin.cr-students.create') }}" class="btn" style="background-color:#6b51df;color:#fff;">+ Add CR Student</a>
            <a href="{{ route('admin.cr-students.importForm') }}" class="btn btn-primary" style="background-color:#6b51df;color:#fff;">Import CR Students</a>
            <a href="{{ route('admin.cr-students.exportExcel', request()->query()) }}" class="btn btn-success">Download Excel</a>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @php
        $filtersApplied = collect([request('college_name'), request('technology'), request('gender'), request('mobile')])->filter(fn($v) => $v !== null && $v !== '')->isNotEmpty();
    @endphp

    @if($filtersApplied)
        <div class="mb-3">
            <strong>Active Filters:</strong>
            <div class="d-flex flex-wrap gap-2 mt-2">
                @if(request('college_name'))
                    @php $collegeLabel = $colleges->firstWhere('id', request('college_name'))->FullName ?? request('college_name'); @endphp
                    <a href="{{ request()->fullUrlWithQuery(['college_name'=>null]) }}" class="badge bg-primary text-decoration-none">College: {{ $collegeLabel }} ✕</a>
                @endif
                @if(request('technology'))
                    @php $technologyLabel = $courses->firstWhere('id', request('technology'))->course_name ?? request('technology'); @endphp
                    <a href="{{ request()->fullUrlWithQuery(['technology'=>null]) }}" class="badge bg-primary text-decoration-none">Technology: {{ $technologyLabel }} ✕</a>
                @endif
                @if(request('gender'))
                    <a href="{{ request()->fullUrlWithQuery(['gender'=>null]) }}" class="badge bg-primary text-decoration-none">Gender: {{ ucfirst(request('gender')) }} ✕</a>
                @endif
                @if(request('mobile'))
                    <a href="{{ request()->fullUrlWithQuery(['mobile'=>null]) }}" class="badge bg-primary text-decoration-none">Mobile: {{ request('mobile') }} ✕</a>
                @endif
                <a href="{{ route('admin.cr-students.index') }}" class="badge bg-danger text-decoration-none">Clear All ✕</a>
            </div>
        </div>
    @endif

    <div class="accordion mb-3" id="filtersAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters"><b>Filters</b></button>
            </h2>
            <div id="collapseFilters" class="accordion-collapse collapse {{ $filtersApplied ? 'show' : '' }}" data-bs-parent="#filtersAccordion">
                <div class="accordion-body">
                    <form method="GET" action="{{ route('admin.cr-students.index') }}" id="filterForm" class="mb-2">
                        <div class="row g-2">
                            {{-- College --}}
                            <div class="col-md-3 col-6">
                                <select name="college_name" class="form-control filterchange select2">
                                    <option value="">--College--</option>
                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}" {{ (string)request('college_name') === (string)$college->id ? 'selected' : '' }}>{{ $college->FullName ?? $college->college_display_name ?? $college->college_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Technology --}}
                            <div class="col-md-3 col-6">
                                <select name="technology" class="form-control filterchange select2">
                                    <option value="">--Technology--</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ (string)request('technology') === (string)$course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            {{-- Gender --}}
                            <div class="col-md-2 col-6">
                                <select name="gender" class="form-control filterchange">
                                    <option value="">--Gender--</option>
                                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ request('gender') === 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            {{-- Mobile --}}
                            <div class="col-md-2 col-6">
                                <input type="text" name="mobile" class="form-control filterchangetext" placeholder="Mobile Number" value="{{ request('mobile') }}">
                            </div>

                            {{-- Future filters intentionally commented for later use:
                            <select name="status" class="form-control filterchange"><option value="">--Status--</option></select>
                            <select name="entry_type" class="form-control filterchange"><option value="">--Entry Source--</option></select>
                            <input type="date" name="start_date" class="form-control filterchange">
                            <input type="date" name="end_date" class="form-control filterchange">
                            <input type="text" name="student_name" class="form-control filterchangetext" placeholder="Student Name">
                            <input type="text" name="f_name" class="form-control filterchangetext" placeholder="Father Name">
                            <input type="text" name="email_id" class="form-control filterchangetext" placeholder="Email">
                            Fee, offer, referral, session and due-date filters can be restored here when required.
                            --}}

                            <div class="col-12 mt-2">
                                <a href="{{ route('admin.cr-students.index') }}" class="btn btn-secondary">Reset</a>
                                <a href="{{ route('admin.cr-students.exportExcel', request()->query()) }}" class="btn btn-primary">Download Excel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    

    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.cr-students.bulkDestroy') }}" class="d-none">
        @csrf
        <input type="hidden" name="ids" id="bulkDeleteIds">
    </form>

    <div class="desktop-view">
        <div class="table-responsive">
            <table id="crStudentsTable" class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th class="text-center" width="100px">#</th>
                        <!-- <th class="text-center" width="100px">Serial No.</th> -->
                        <th class="text-center">Name</th>
                        <th class="text-center">Father Name</th>
                        <th class="text-center">Gender</th>
                        <!-- <th class="text-center" width="100px">Session</th> -->
                        <th class="text-center" width="180px">College</th>
                        <th class="text-center">Contact</th>
                        <th class="text-center">Email</th>
                        <!-- <th class="text-center">Status</th> -->
                        <th class="text-center">Technology</th>
                        <!-- <th class="text-center">Total Fees(Rs.)</th>
                        <th class="text-center">Reg Fees(Rs.)</th>
                        <th class="text-center">Paid Fees(Rs.)</th>
                        <th class="text-center">Pending Fees(Rs.)</th> -->
                        <th class="text-center">Registered Date</th>
                        <th class="text-center">Duration</th>
                        <th class="text-center">Start Date</th>
                        <th class="text-center">End Date</th>
                        <th class="text-center">Entry Source</th>
                        <th class="text-center">Source Student ID</th>
                        <th width="140px" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td><input type="checkbox" class="record_checked" value="{{ $student->id }}"></td>
                            <td></td>
                            <!-- <td>{{ $student->sno }}</td> -->
                            <td>{{ $student->student_name }}</td>
                            <td>{{ $student->f_name }}</td>
                            <td>{{ $student->gender }}</td>
                            <!-- <td>{{ $student->sessionData->session_display_name ?? $student->sessionData->session_name ?? $student->session }}</td> -->
                            <td>{{ $student->collegeData->FullName ?? $student->collegeData->college_display_name ?? $student->college_name }}</td>
                            <td>{{ $student->contact }}</td>
                            <td>{{ $student->email_id }}</td>
                            <!-- <td><span class="badge bg-{{ strtolower($student->status ?? '') === 'joined' ? 'success' : 'secondary' }}">{{ ucwords(str_replace('_',' ', $student->status ?? '-')) }}</span></td> -->
                            <td style="width:150px;">{{ $student->course_name }}</td>
                           <!--  <td>{{ $student->total_fees }}</td>
                            <td>{{ $student->reg_fees }}</td>
                            <td>{{ $student->paid_fees }}</td>
                            <td>{{ $student->pending_fees }}</td> -->
                            <td>{{ $student->join_date ? \Carbon\Carbon::parse($student->join_date)->format('d M Y') : '-' }}</td>
                            <td>{{ $student->sessionData->session_display_name ?? '-' }}</td>
                            <td>{{ $student->start_date ? \Carbon\Carbon::parse($student->start_date)->format('d M Y') : '-' }}</td>
                            <td>{{ $student->end_date ? \Carbon\Carbon::parse($student->end_date)->format('d M Y') : '-' }}</td>
                            <td>
                                @if((int)$student->entry_type === 0)<span class="badge bg-info cr-source-badge">Copied from Student</span>
                                @elseif((int)$student->entry_type === 2)<span class="badge bg-warning text-dark cr-source-badge">Imported</span>
                                @else<span class="badge bg-secondary cr-source-badge">Manual Entry</span>@endif
                            </td>
                            <td>{{ $student->source_student_id ?? '-' }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.cr-students.edit', $student->id) }}" class="btn btn-sm edit_btn" data-bs-toggle="tooltip" title="Edit CR Student"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('admin.cr-students.destroy', $student->id) }}" method="POST" style="display:inline-block;" class="cr-single-delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" title="Delete CR Student"><i class="fa fa-trash"></i></button>
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
    let selectedCrStudents = [];
    const pageLength = 10;
    $.fn.dataTable.ext.pager.numbers_length = 12;

    const table = $('#crStudentsTable').DataTable({
        pageLength: pageLength,
        pagingType: 'full_numbers',
        lengthMenu: [10, 15, 20, 25, 50, 100],
        scrollX: true,
        columnDefs: [{ targets: [0, 1], searchable: false, orderable: false }]
    });

    function syncSelectedCheckboxes() {
        table.rows({ page: 'current' }).nodes().to$().find('.record_checked').each(function () {
            this.checked = selectedCrStudents.includes(String(this.value));
        });
        const current = table.rows({ page: 'current' }).nodes().to$().find('.record_checked:not(:disabled)');
        $('#checkAll').prop('checked', current.length > 0 && current.filter(':checked').length === current.length);
    }

    function getSelectedIds() {
        table.$('.record_checked:checked').each(function () {
            const id = String(this.value);
            if (!selectedCrStudents.includes(id)) selectedCrStudents.push(id);
        });
        return selectedCrStudents;
    }

    table.on('draw.dt', function () {
        const info = table.page.info();
        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = info.start + i + 1;
        });
        syncSelectedCheckboxes();
    }).draw();

    $('#checkAll').on('change', function () {
        const checked = this.checked;
        table.rows({ page: 'current' }).nodes().to$().find('.record_checked').each(function () {
            const id = String(this.value);
            this.checked = checked;
            if (checked && !selectedCrStudents.includes(id)) selectedCrStudents.push(id);
            if (!checked) selectedCrStudents = selectedCrStudents.filter(item => item !== id);
        });
        syncSelectedCheckboxes();
    });

    $('#crStudentsTable').on('change', '.record_checked', function () {
        const id = String(this.value);
        if (this.checked && !selectedCrStudents.includes(id)) selectedCrStudents.push(id);
        if (!this.checked) selectedCrStudents = selectedCrStudents.filter(item => item !== id);
        syncSelectedCheckboxes();
    });

    $('#deleteSelected').on('click', function () {
        const ids = getSelectedIds();
        if (!ids.length) {
            Swal.fire({ icon: 'warning', text: 'Please select at least 1 record.' });
            return;
        }
        Swal.fire({
            title: 'Delete selected CR students?',
            text: 'Only the selected CR records will be deleted. Original Student records will remain unchanged.',
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

    $(document).on('submit', '.cr-single-delete-form', function (e) {
        const form = this;
        e.preventDefault();
        Swal.fire({
            title: 'Delete this CR student?',
            text: 'The original Student record will remain unchanged.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {
            if (result.isConfirmed) form.submit();
        });
    });

    let filterTimer;
    $('.filterchange').on('change', function () { $('#filterForm').trigger('submit'); });
    $('.filterchangetext').on('input', function () {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(function () { $('#filterForm').trigger('submit'); }, 500);
    });
});
</script>
@endpush
