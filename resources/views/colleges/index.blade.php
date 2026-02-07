@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
        text-transform: capitalize;
     }
     .student-count {
        color: #0d6efd;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        transition: all 0.2s ease-in-out;
    }
    .student-count:hover {
        color: #084298;
        transform: scale(1.05);
    }
    .student-count.badge-style {
        background-color: #e7f1ff;
        padding: 4px 10px;
        border-radius: 12px;
        text-decoration: none;
    }

</style>

<div class="container">

    <!-- <div class="row mb-2">
        <div class="col-md-4">
            <h1 class="page_heading">Colleges/Places</h1>
        </div>
        <div class="col-md-2">
            
            <select id="filter-state" class="form-control">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            
            <select id="filter-district" class="form-control">
                <option value="">All Districts</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('colleges.export.excel') }}"
                   class="btn mb-3" style="background-color: #6b51df; color: #fff;">
                     Download Excel
                </a>
            </div>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('colleges.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add College/Place</a>
            </div>
        </div>
    </div> -->

    <div class="row mb-2 align-items-center">
    <div class="col-md-4">
        <h1 class="page_heading">Colleges / Places</h1>
    </div>

    <div class="col-md-2">
        <select id="filter-state" class="form-control">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->name }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-district" class="form-control">
            <option value="">All Districts</option>
        </select>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">

            {{-- IMPORT COLLEGES --}}
            <a href="{{ route('colleges.import.view') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Import
            </a>

            {{-- DOWNLOAD EXCEL --}}
            <a href="{{ route('colleges.export.excel') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Download
            </a>

            {{-- ADD COLLEGE --}}
            <a href="{{ route('colleges.create') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Add
            </a>
        </div>
    </div>
</div>

    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="colleges-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>College Name/Place</th>
                <th>State</th>
                <th>District</th>
                <th>Students</th>

                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colleges as $college)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $college->college_name }}</td>
                <td>{{ $college->state->name ?? '-' }}</td>
                <td>{{ $college->district->name ?? '-' }}</td>
                <!-- <td class="text-center">
                    <span class="student-count view-college-students badge-style"
                          data-college-id="{{ $college->id }}"
                          data-college-name="{{ $college->college_name }}">
                        {{ $college->students_count }}
                    </span>
                </td> -->

                <td>
                    <a href="{{ route('common_filtered_student', [
                        'college_name' => $college->id,
                    ]) }}"
                       class="text-decoration-none">
                        <span class="badge bg-success">
                            {{ $college->students_count }}
                        </span>
                    </a>
                </td>


                <td class="text-center">
                    <div class="mb-2">
                        <a href="{{ route('colleges.edit', $college->id) }}" class="btn btn-sm"
                           data-bs-toggle="tooltip" title="Edit">
                           <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('colleges.destroy', $college->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"
                                onclick="return confirm('Are you sure?')" data-bs-toggle="tooltip" title="Delete">
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

<div class="modal fade" id="collegeStudentsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Students – <span id="modalCollegeName"></span>
                </h5>

                <a href="#" id="downloadCollegeExcel"
                   class="btn btn-sm btn-success ms-3">
                    <i class="fa fa-file-excel"></i> Download Excel
                </a>

                <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>SNO</th>
                                <th>Session ID</th>
                                <th>Session Name</th>
                            </tr>
                        </thead>
                        <tbody id="collegeStudentsTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    Click student count to load data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
@push('scripts')

<script>
// districts grouped by state → coming from controller
let districtsByState = @json($districtsGrouped);

$(document).ready(function () {

    // INIT DATATABLE
    let table = $('#colleges-table').DataTable({
        "pageLength": 50,
        "lengthMenu": [5, 10, 25, 50, 100],
    });

    // ----------------------------------------
    // STATE FILTER → Updates District Dropdown
    // ----------------------------------------
    $('#filter-state').on('change', function () {

    let selectedState = this.value;

    let districtDropdown = $('#filter-district');

    // Reset district dropdown
    districtDropdown.empty().append('<option value="">All Districts</option>');

    if (selectedState === "") {
        // CLEAR state filter
        table.column(2).search("").draw();

        // CLEAR district filter
        table.column(3).search("").draw();

        return; // IMPORTANT
    }

    // APPLY state filter
    table.column(2).search(selectedState).draw();

    // Find state ID by matching name
    let stateId = Object.keys(districtsByState).find(id => {
        return districtsByState[id][0]?.state_name === selectedState;
    });

    // Populate district dropdown for selected state
    if (stateId && districtsByState[stateId]) {
        districtsByState[stateId].forEach(function (d) {
            districtDropdown.append(`<option value="${d.name}">${d.name}</option>`);
        });
    }

    // Reset district DataTable filter
    table.column(3).search("").draw();
});


    // ----------------------------------------
    // DISTRICT FILTER
    // ----------------------------------------
    $('#filter-district').on('change', function () {
        table.column(3).search(this.value).draw();
    });

});

// Bootstrap Tooltips Init
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});


$(document).on('click', '.view-college-students', function () {

    let collegeId   = $(this).data('college-id');
    let collegeName = $(this).data('college-name');

    $('#modalCollegeName').text(collegeName);
    $('#collegeStudentsTableBody').html(
        '<tr><td colspan="5" class="text-center">Loading...</td></tr>'
    );

    $('#downloadCollegeExcel').attr(
        'href',
        `/colleges/${collegeId}/students/export-excel`
    );

    $.ajax({
        url: `/colleges/${collegeId}/students`,
        type: 'GET',
        success: function (students) {

            let rows = '';

            if (students.length === 0) {
                rows = `<tr>
                            <td colspan="5" class="text-center">
                                No students found
                            </td>
                        </tr>`;
            } else {
                students.forEach((s, i) => {
                    rows += `<tr>
                        <td>${i + 1}</td>
                        <td>${s.student_name}</td>
                        <td>${s.sno ?? '-'}</td>
                        <td>${s.session_id ?? '-'}</td>
                        <td>${s.session_name ?? '-'}</td>
                    </tr>`;
                });
            }

            $('#collegeStudentsTableBody').html(rows);
            $('#collegeStudentsModal').modal('show');
        }
    });
});

</script>

@endpush
@endsection
