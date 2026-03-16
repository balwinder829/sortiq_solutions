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
    <div class="col-md-8">
        <h1 class="page_heading">Colleges / Places</h1>
    </div>

    <!-- <div class="col-md-2">
        <select id="student_filter" class="form-select">
            <option value="">All Colleges</option>
            <option value="zero">0 Students</option>
            <option value="more">More than 0 Students</option>
        </select>
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
    </div> -->

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
           <!--  <a href="{{ route('colleges.export.excel') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Export
            </a> -->

            <a href="javascript:void(0)"
               id="exportExcel"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
               Export
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
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <h1 class="page_heading">FIlters</h1>
    </div>

    <div class="col-md-2">
        <select id="student_filter" class="form-select">
            <option value="">Student Count</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
        </select>
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
        <select id="filter_college_type" class="form-control">
            <option value="">College Type</option>
            <option value="0">Degree</option>
            <option value="1">Diploma</option>
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter_training" class="form-control">
            <option value="">Training</option>
            <option value="1">Providing Training</option>
            <option value="0">Not Providing</option>
        </select>
    </div>

    <div class="col-md-1">
        <a href="{{ route('colleges.index') }}" class="btn btn-secondary w-100">
            Reset
        </a>
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
                <th>College Type</th>
                <th>Offer Training</th>
                <th>No of times in year</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Data loaded via server-side Ajax (no full dataset in page) --}}
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

    // SERVER-SIDE DATATABLE — only current page is loaded from server
    let table = $('#colleges-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('colleges.data') }}",
            type: 'GET',
            data: function (d) {
                d.state_name   = $('#filter-state').val();
                d.district_name = $('#filter-district').val();
                d.student_filter = $('#student_filter').val();
                d.college_type = $('#filter_college_type').val();
                d.offer_training = $('#filter_training').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'college_name' },
            { data: 2, name: 'state' },
            { data: 3, name: 'district' },
            { data: 4, name: 'students_count', orderable: true, searchable: false },
            { data: 5, name: 'college_type' },
            { data: 6, name: 'offer_training' },
            { data: 7, name: 'training_in_year,' },
            { data: 8, name: 'actions', orderable: false, searchable: false }
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order:[]
    });

    $('#student_filter, #filter_college_type, #filter_training').change(function () {
        table.ajax.reload();
    });

    // STATE FILTER → Updates District Dropdown and reloads table
    $('#filter-state').on('change', function () {
        let selectedState = this.value;
        let districtDropdown = $('#filter-district');
        districtDropdown.empty().append('<option value="">All Districts</option>');

        if (selectedState && districtsByState) {
            let stateId = Object.keys(districtsByState).find(id => {
                return districtsByState[id][0]?.state_name === selectedState;
            });
            if (stateId && districtsByState[stateId]) {
                districtsByState[stateId].forEach(function (d) {
                    districtDropdown.append('<option value="' + d.name + '">' + d.name + '</option>');
                });
            }
        }
        table.ajax.reload();
    });

    $('#filter-district').on('change', function () {
        table.ajax.reload();
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
// $('#exportExcel').click(function () {

//     let state      = $('#filter-state').val();
//     let district   = $('#filter-district').val();
//     let student    = $('#student_filter').val();

//     let url = "{{ route('colleges.export.excel') }}?" +
//         "state_name=" + state +
//         "&district_name=" + district +
//         "&student_filter=" + student;

//     window.location.href = url;
// });

$('#exportExcel').on('click', function () {

    let $btn = $(this);

    // 🚫 Stop if already processing
    if ($btn.prop('disabled')) {
        return false;
    }

    // ✅ Disable button
    $btn.prop('disabled', true).text('Exporting...');

    let state    = $('#filter-state').val() ?? '';
    let district = $('#filter-district').val() ?? '';
    let student  = $('#student_filter').val() ?? '';

    let college_type = $('#filter_college_type').val();
    let offer_training = $('#filter_training').val();

    let url = "{{ route('colleges.export.excel') }}?" +
        "state_name=" + encodeURIComponent(state) +
        "&district_name=" + encodeURIComponent(district) +
        "&student_filter=" + encodeURIComponent(student) +
        "&college_type=" + encodeURIComponent(college_type) +
        "&offer_training=" + encodeURIComponent(offer_training);

    // Trigger download
    window.location.href = url;

    // ⏳ Re-enable after 3 seconds (adjust if needed)
    setTimeout(function () {
        $btn.prop('disabled', false).text('Export');
    }, 3000);
});
</script>

@endpush
@endsection
