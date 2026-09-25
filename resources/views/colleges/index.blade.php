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

    .switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
    }

    .switch input {
        display: none;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        background-color: #ccc;
        transition: .4s;
        border-radius: 22px;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider {
        background-color: #0d6efd;
    }

    input:checked + .slider:before {
        transform: translateX(18px);
    }

    /* Department Filter Dropdown */
.department-dropdown-menu {
    min-width: 400px !important;
    max-width: 450px !important;
    max-height: 450px;
    overflow-y: auto;
    overflow-x: hidden;
}

/* Each department row */
.department-dropdown-menu .department-option {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 10px;
    padding: 0 !important;
}

/* Checkbox */
.department-dropdown-menu .department-option .form-check-input {
    position: static !important;
    flex: 0 0 20px;
    width: 20px;
    height: 20px;
    margin: 3px 0 0 0 !important;
}

/* Department text */
.department-dropdown-menu .department-option .form-check-label {
    display: block;
    flex: 1;
    margin: 0 !important;
    padding: 0 !important;
    white-space: normal;
    line-height: 1.5;
    cursor: pointer;
}
</style>

<div class="container">

    {{-- ================================
        PAGE HEADER
    ================================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">Colleges / Places</h1>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="col-md-6">
            <div class="d-flex justify-content-end gap-2">

                {{-- IMPORT COLLEGES --}}
                <a href="{{ route('colleges.import.view') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df; color:#fff;">
                    Import
                </a>

                {{-- EXPORT --}}
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

                <a href="{{ route('colleges.shift') }}"
                   class="btn mb-3" style="background-color:#6b51df; color:#fff;">
                    Shift / Merge College
                </a>

            </div>
        </div>

    </div>


    {{-- ================================
        COLLEGE STATUS TABS
    ================================= --}}
    <div class="mb-3">

        <ul class="nav nav-tabs">

            <li class="nav-item">
                <a href="javascript:void(0)"
                   class="nav-link college-status-tab active"
                   data-status="active">
                    Active
                </a>
            </li>

            <li class="nav-item">
                <a href="javascript:void(0)"
                   class="nav-link college-status-tab"
                   data-status="closed">
                    Closed
                </a>
            </li>

            <li class="nav-item">
                <a href="javascript:void(0)"
                   class="nav-link college-status-tab"
                   data-status="blocked">
                    Blocked
                </a>
            </li>

        </ul>

    </div>


  {{-- ================================
    FILTERS
================================ --}}

{{-- ROW 1 --}}
<div class="row mb-2 align-items-center">

    {{-- Filters Title --}}
    <div class="col-md-1">
        <h1 class="page_heading">Filters</h1>
    </div>

    {{-- Student Count --}}
    <div class="col-md-2">
        <select id="student_filter" class="form-select">
            <option value="">Student Count</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
        </select>
    </div>

    {{-- State --}}
    <div class="col-md-2">
        <select id="filter-state" class="form-control">
            <option value="">All States</option>

            @foreach($states as $state)
                <option value="{{ $state->name }}">
                    {{ $state->name }}
                </option>
            @endforeach

        </select>
    </div>

    {{-- District --}}
    <div class="col-md-2">
        <select id="filter-district" class="form-control">
            <option value="">All Districts</option>
        </select>
    </div>

    {{-- College Type --}}
    <div class="col-md-2">
        <select id="filter_college_type" class="form-control">
            <option value="">College Type</option>

            @foreach(\App\Models\College::TYPES as $key => $value)
                <option value="{{ $key }}">
                    {{ $value }}
                </option>
            @endforeach

        </select>
    </div>

    {{-- Training --}}
    <div class="col-md-2">
        <select id="filter_training" class="form-control">
            <option value="">Training</option>
            <option value="1">Providing Training</option>
            <option value="0">Not Providing</option>
        </select>
    </div>

</div>


{{-- ROW 2 --}}
<div class="row mb-2 align-items-center">

    {{-- Important --}}
    <div class="col-md-2 offset-md-1">
        <select id="filter_important" class="form-control">
            <option value="">Important</option>
            <option value="1">Important</option>
            <option value="0">Normal</option>
        </select>
    </div>

    {{-- Ownership --}}
    <div class="col-md-2">
        <select id="filter_ownership" class="form-control">
            <option value="">Ownership</option>
            <option value="1">Government</option>
            <option value="0">Private</option>
        </select>
    </div>

    {{-- Connection --}}
    <div class="col-md-2">
        <select id="filter_connection" class="form-control">
            <option value="">Connection</option>
            <option value="1">Old Connection</option>
            <option value="0">New Connection</option>
        </select>
    </div>

    {{-- Department --}}
<div class="col-md-2">
    <div class="dropdown">

        <button class="btn btn-outline-secondary dropdown-toggle w-100"
                type="button"
                id="departmentFilterButton"
                data-bs-toggle="dropdown"
                aria-expanded="false">
            Department
        </button>

        <div class="dropdown-menu p-3 department-dropdown-menu"
             aria-labelledby="departmentFilterButton">

            {{-- Degree Departments --}}
            <div class="department-group">

                <div class="mb-2">
                    <strong>Degree</strong>
                </div>

                @foreach($collegeDepartments->where('type', 'degree') as $department)

                    <div class="form-check department-option">

                        <input class="form-check-input department-filter"
                               type="checkbox"
                               value="{{ $department->name }}"
                               id="dept_{{ $department->id }}">

                        <label class="form-check-label"
                               for="dept_{{ $department->id }}">
                            {{ $department->name }}
                        </label>

                    </div>

                @endforeach

            </div>


            {{-- Diploma Departments --}}
            <div class="department-group mt-3">

                <div class="mb-2">
                    <strong>Diploma</strong>
                </div>

                @foreach($collegeDepartments->where('type', 'diploma') as $department)

                    <div class="form-check department-option">

                        <input class="form-check-input department-filter"
                               type="checkbox"
                               value="{{ $department->name }}"
                               id="dept_{{ $department->id }}">

                        <label class="form-check-label"
                               for="dept_{{ $department->id }}">
                            {{ $department->name }}
                        </label>

                    </div>

                @endforeach

            </div>

        </div>
    </div>
</div>

    {{-- Training In --}}
    <div class="col-md-2">
        <select id="filter_training_in" class="form-control">
            <option value="">Training In</option>
            <option value="Degree">Degree</option>
            <option value="Diploma">Diploma</option>
            <option value="Both">Both</option>
        </select>
    </div>

</div>


{{-- ROW 3 - TRAINING DURATION --}}
<div class="row mb-2 align-items-center">

    {{-- 21 Days --}}
    <div class="col-md-2 offset-md-1">
        <select id="filter_training_21_days" class="form-control">
            <option value="">21 Days</option>

            @for($i = 0; $i <= 8; $i++)
                <option value="{{ $i }}">
                    {{ $i }} Times / Year
                </option>
            @endfor

        </select>
    </div>

    {{-- 45 Days --}}
    <div class="col-md-2">
        <select id="filter_training_45_days" class="form-control">
            <option value="">45 Days</option>

            @for($i = 0; $i <= 8; $i++)
                <option value="{{ $i }}">
                    {{ $i }} Times / Year
                </option>
            @endfor

        </select>
    </div>

    {{-- 6 Months --}}
    <div class="col-md-2">
        <select id="filter_training_6_months" class="form-control">
            <option value="">6 Months</option>

            @for($i = 0; $i <= 8; $i++)
                <option value="{{ $i }}">
                    {{ $i }} Times / Year
                </option>
            @endfor

        </select>
    </div>

</div>


{{-- ROW 4 --}}
<div class="row mb-2 align-items-center">

    {{-- Whom to Connect --}}
    <div class="col-md-2 offset-md-1">
        <select id="filter_connected_to" class="form-control">
            <option value="">Whom to Connect</option>
            <option value="HOD">HOD</option>
            <option value="TPO">TPO</option>
            <option value="Principal">Principal</option>
            <option value="other">Some Other</option>
            <option value="Both">Both (HOD + TPO)</option>
        </select>
    </div>

    {{-- Reference By --}}
    <div class="col-md-2">
        <input type="text"
               id="filter_reference_by"
               class="form-control"
               placeholder="Reference By">
    </div>

    {{-- Reset --}}
    <div class="col-md-1">
        <a href="{{ route('colleges.index') }}"
           class="btn btn-secondary w-100">
            Reset
        </a>
    </div>

</div>

@if(session('delete_error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>College cannot be deleted!</strong>

        <div class="mt-2">
            <strong>{{ session('delete_error.college') }}</strong>
            has existing records in:
        </div>

        <ul class="mb-0 mt-2">
            @foreach(session('delete_error.records') as $table => $count)
                <li>
                    <strong>{{ $table }}</strong>
                    — {{ $count }} record{{ $count > 1 ? 's' : '' }}
                </li>
            @endforeach
        </ul>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert"
                aria-label="Close"></button>
    </div>
@endif
    {{-- ================================
        SUCCESS MESSAGE
    ================================= --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ================================
        COLLEGE TABLE
    ================================= --}}
    <table id="colleges-table"
           class="table table-bordered table-striped">

        <thead>

            <tr>

                <th width="30">
                    <input type="checkbox" id="checkAll">
                </th>

                <th>ID</th>

                <th>College ID</th>

                <th>College Name/Place</th>

                <th>State</th>

                <th>District</th>

                <th>Students</th>

                <th>Confirmation</th>

                <th>Certificate</th>

                <th>College Type</th>

                <th>Offer Training</th>

                <th>No of times in year</th>

                <th style="width:250px!important;">
                    Actions
                </th>

            </tr>

        </thead>

        <tbody>
            {{-- Data loaded via server-side Ajax --}}
        </tbody>

    </table>


    {{-- ================================
        BULK EDIT BUTTON
    ================================= --}}
    <button
        id="editBulkCollege"
        class="btn btn-primary">

        Edit Selected Colleges

    </button>

</div>


{{-- ==========================================
    BULK EDIT MODAL
========================================== --}}
<div class="modal fade"
     id="bulkEditModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl">

        <form method="POST"
              action="{{ route('colleges.bulkUpdate') }}"
              id="bulkCollegeForm">

            @csrf

            <input type="hidden"
                   name="ids"
                   id="bulkCollegeIds">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Bulk Edit Colleges
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>


                <div class="modal-body">

                    <div class="row">

                        


                        {{-- Offer Training --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>Offer Training</strong>
                            </label>

                            <select name="offer_training"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                <option value="0">
                                    No
                                </option>

                                <option value="1">
                                    Yes
                                </option>

                            </select>

                        </div>


                        {{-- Training Times --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>Training Times in Year</strong>
                            </label>

                            <select name="training_in_year"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                @foreach(range(0,5) as $year)

                                    <option value="{{ $year }}">
                                        {{ $year }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Important --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>Important College</strong>
                            </label>

                            <select name="is_important"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                <option value="1">
                                    Yes
                                </option>

                                <option value="0">
                                    No
                                </option>

                            </select>

                        </div>


                        {{-- Ownership --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>Ownership</strong>
                            </label>

                            <select name="ownership_type"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                <option value="1">
                                    Government
                                </option>

                                <option value="0">
                                    Private
                                </option>

                            </select>

                        </div>


                        {{-- Connection --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>Connection Type</strong>
                            </label>

                            <select name="connection_type"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                <option value="1">
                                    Old Connection
                                </option>

                                <option value="0">
                                    New Connection
                                </option>

                            </select>

                        </div>

                        {{-- Status --}}
        <div class="form-group col-md-6 mb-3">

            <label>
                <strong>Status</strong>
            </label>

            <select name="status" class="form-control">

                <option value="">
                    Keep Existing
                </option>

                <option value="active">
                    Active
                </option>

                <option value="closed">
                    Closed
                </option>

                <option value="blocked">
                    Blocked
                </option>

            </select>

        </div>

        {{-- Training In --}}
<div class="form-group col-md-6 mb-3">
    <label>
        <strong>Training In</strong>
    </label>

    <select name="training_in" class="form-control">
        <option value="">
            Keep Existing
        </option>

        <option value="Degree">
            Degree
        </option>

        <option value="Diploma">
            Diploma
        </option>

        <option value="Both">
            Both
        </option>
    </select>
</div>
{{-- Training Months --}}
<div class="form-group col-md-6 mb-3">

    <label>
        <strong>Training Months</strong>
    </label>

    <input type="text"
           name="training_months"
           class="form-control"
           placeholder="Keep Existing if blank">

     

</div>
{{-- Whom to Connect --}}
<div class="form-group col-md-6 mb-3">
    <label>
        <strong>Whom to Connect</strong>
    </label>

    <select name="connected_to" class="form-control">
        <option value="">
            Keep Existing
        </option>

        <option value="HOD">
            HOD
        </option>

        <option value="TPO">
            TPO
        </option>

        <option value="Principal">
            Principal
        </option>

        <option value="other">
            Some Other
        </option>

        <option value="Both">
            Both (HOD + TPO)
        </option>
    </select>
</div>
{{-- Reference By --}}
<div class="form-group col-md-6 mb-3">
    <label>
        <strong>Reference By</strong>
    </label>

    <input type="text"
           name="reference_by"
           class="form-control"
           placeholder="Keep Existing if blank">
</div>
                       {{-- College Type --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>College Type</strong>
                            </label>

                            <select name="college_type"
                                id="bulkCollegeType"
                                    class="form-control">

                                <option value="">
                                    Keep Existing
                                </option>

                                @foreach(\App\Models\College::TYPES as $key => $value)

                                    <option value="{{ $key }}">
                                        {{ $value }}
                                    </option>

                                @endforeach

                            </select>

                        </div>
{{-- Departments --}}
<div class="form-group col-md-12 mb-3">

    <label>
        <strong>Departments</strong>
    </label>

    <select
        name="departments[]"
        id="bulkDepartments"
        class="form-control"
        multiple>

        @foreach($collegeDepartments->where('type', 'degree') as $department)

            <option
                value="{{ $department->name }}"
                data-type="degree">

                {{ $department->name }}

            </option>

        @endforeach

        @foreach($collegeDepartments->where('type', 'diploma') as $department)

            <option
                value="{{ $department->name }}"
                data-type="diploma">

                {{ $department->name }}

            </option>

        @endforeach

    </select>

    <small class="text-muted">
        Select departments to apply to all selected colleges.
    </small>

</div>

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-primary">

                        Update Selected Colleges

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


@endsection


@section('scripts')

@push('scripts')

<script>

    // ==========================================
    // CURRENT COLLEGE STATUS TAB
    // ==========================================

    let currentStatus = 'active';


    // ==========================================
    // DISTRICTS
    // ==========================================

    let districtsByState = @json($districtsGrouped);


    // ==========================================
    // DATATABLE
    // ==========================================

    $(document).ready(function () {

        let selectedColleges = {};


        let table = $('#colleges-table').DataTable({

            processing: true,

            serverSide: true,

            scrollX: true,

            ajax: {

                url: "{{ route('colleges.data') }}",

                type: 'GET',

                data: function (d) {

                    // STATUS
                    d.status = currentStatus;

                    // EXISTING FILTERS
                    d.state_name = $('#filter-state').val();

                    d.district_name = $('#filter-district').val();

                    d.student_filter = $('#student_filter').val();

                    d.college_type = $('#filter_college_type').val();

                    d.offer_training = $('#filter_training').val();

                    d.is_important = $('#filter_important').val();

                    d.ownership_type = $('#filter_ownership').val();

                    d.connection_type = $('#filter_connection').val();

                    d.departments = $('.department-filter:checked')
                        .map(function () {
                            return $(this).val();
                        })
                        .get();

                    d.training_in = $('#filter_training_in').val();

                    d.training_21_days = $('#filter_training_21_days').val();
                    d.training_45_days = $('#filter_training_45_days').val();
                    d.training_6_months = $('#filter_training_6_months').val();

                    d.connected_to = $('#filter_connected_to').val();

                    d.reference_by = $('#filter_reference_by').val();

                    // d.contact_person = $('#filter_contact_person').val();

                }

            },


            columns: [

                {
                    data: 0,
                    orderable: false,
                    searchable: false
                },

                {
                    data: 1,
                    name: 'id'
                },

                {
                    data: 2,
                    name: 'college_id'
                },

                {
                    data: 3,
                    name: 'college_name'
                },

                {
                    data: 4,
                    name: 'state'
                },

                {
                    data: 5,
                    name: 'district'
                },

                {
                    data: 6,
                    name: 'students_count',
                    orderable: true,
                    searchable: false
                },

                {
                    data: 7,
                    name: 'confirmation_students_count',
                    orderable: true,
                    searchable: false
                },

                {
                    data: 8,
                    name: 'certificate_students_count',
                    orderable: true,
                    searchable: false
                },

                {
                    data: 9,
                    name: 'college_type'
                },

                {
                    data: 10,
                    name: 'offer_training'
                },

                {
                    data: 11,
                    name: 'training_in_year'
                },

                {
                    data: 12,
                    name: 'actions',
                    orderable: false,
                    searchable: false
                }

            ],


            pageLength: 50,

            lengthMenu: [5, 10, 25, 50, 100],

            order: []

        });


        // ==========================================
        // STATUS TAB CLICK
        // ==========================================

        $(document).on('click', '.college-status-tab', function (e) {

            e.preventDefault();

            let status = $(this).data('status');

            currentStatus = status;


            // Active tab styling
            $('.college-status-tab').removeClass('active');

            $(this).addClass('active');


            // Reset Select All checkbox
            $('#checkAll').prop('checked', false);


            // Reload DataTable
            table.ajax.reload(null, true);

        });


        // ==========================================
        // FILTER CHANGE
        // ==========================================

        // $('#student_filter, #filter_college_type, #filter_training, #filter_important, #filter_ownership, #filter_connection, #filter_department')
        //     .change(function () {

        //         table.ajax.reload();

        //     });

            // $('#student_filter, #filter_college_type, #filter_training, #filter_important, #filter_ownership, #filter_connection, #filter_training_in, #filter_training_year, #filter_connected_to')
            //     .change(function () {

            //         table.ajax.reload();

            //     });


            $('#student_filter, #filter_training, #filter_important, #filter_ownership, #filter_connection, #filter_training_in, #filter_training_21_days, #filter_training_45_days, #filter_training_6_months, #filter_connected_to')
                .change(function () {

                    table.ajax.reload();

                });
                $('#filter_college_type').on('change', function () {

                    filterDepartmentOptionsByCollegeType();

                    table.ajax.reload();
                });

            $('.department-filter').on('change', function () {
                table.ajax.reload();
            });


            let filterTimer;

            $('#filter_reference_by, #filter_contact_person').on('keyup', function () {

                clearTimeout(filterTimer);

                filterTimer = setTimeout(function () {
                    table.ajax.reload();
                }, 400);

            });
        // ==========================================
        // STATE FILTER
        // ==========================================

        $('#filter-state').on('change', function () {

            let selectedState = this.value;

            let districtDropdown = $('#filter-district');

            districtDropdown
                .empty()
                .append('<option value="">All Districts</option>');


            if (selectedState && districtsByState) {

                let stateId = Object.keys(districtsByState).find(id => {

                    return districtsByState[id][0]?.state_name === selectedState;

                });


                if (stateId && districtsByState[stateId]) {

                    districtsByState[stateId].forEach(function (d) {

                        districtDropdown.append(
                            '<option value="' +
                            d.name +
                            '">' +
                            d.name +
                            '</option>'
                        );

                    });

                }

            }


            table.ajax.reload();

        });


        // ==========================================
        // DISTRICT FILTER
        // ==========================================

        $('#filter-district').on('change', function () {

            table.ajax.reload();

        });


        // ==========================================
        // INDIVIDUAL CHECKBOX SELECTION
        // ==========================================

        $(document).on('change', '.record_checked', function () {

            let id = $(this).val();


            if ($(this).is(':checked')) {

                selectedColleges[id] = true;

            } else {

                delete selectedColleges[id];

            }

        });


        // ==========================================
        // RESTORE CHECKED ROWS AFTER DATATABLE DRAW
        // ==========================================

        table.on('draw.dt', function () {

            let allChecked = true;


            $('.record_checked').each(function () {

                let id = $(this).val();


                if (selectedColleges[id]) {

                    $(this).prop('checked', true);

                } else {

                    $(this).prop('checked', false);

                    allChecked = false;

                }

            });


            $('#checkAll').prop(
                'checked',
                $('.record_checked').length > 0 && allChecked
            );

        });


        // ==========================================
        // CHECK ALL - CURRENT PAGE
        // ==========================================

        $(document).on('change', '#checkAll', function () {

            let isChecked = $(this).is(':checked');


            $('.record_checked').each(function () {

                let id = $(this).val();


                $(this).prop(
                    'checked',
                    isChecked
                );


                if (isChecked) {

                    selectedColleges[id] = true;

                } else {

                    delete selectedColleges[id];

                }

            });

        });


        // ==========================================
        // EDIT SELECTED COLLEGES
        // ==========================================

        $('#editBulkCollege').click(function () {

            if (Object.keys(selectedColleges).length === 0) {

                Swal.fire({

                    icon: 'warning',

                    title: 'No College Selected',

                    text: 'Please choose at least 1 college.'

                });

                return;

            }


            $('#bulkCollegeIds').val(
                Object.keys(selectedColleges).join(',')
            );


            $('#bulkEditModal').modal('show');

        });


    });


    // ==========================================
    // BOOTSTRAP TOOLTIPS
    // ==========================================

    var tooltipTriggerList =
        [].slice.call(
            document.querySelectorAll(
                '[data-bs-toggle="tooltip"]'
            )
        );

    var tooltipList =
        tooltipTriggerList.map(function (tooltipTriggerEl) {

            return new bootstrap.Tooltip(
                tooltipTriggerEl
            );

        });


    // ==========================================
    // EXPORT EXCEL
    // ==========================================

    $('#exportExcel').on('click', function () {

    let $btn = $(this);

    if ($btn.prop('disabled')) {
        return false;
    }

    $btn.prop('disabled', true)
        .text('Exporting...');

    let state =
        $('#filter-state').val() ?? '';

    let district =
        $('#filter-district').val() ?? '';

    let student =
        $('#student_filter').val() ?? '';

    let college_type =
        $('#filter_college_type').val() ?? '';

    let offer_training =
        $('#filter_training').val() ?? '';

    let call_status =
        $('#filter_status').val() ?? '';

    let is_important =
        $('#filter_important').val() ?? '';

    let ownership_type =
        $('#filter_ownership').val() ?? '';

    let connection_type =
        $('#filter_connection').val() ?? '';

    // NEW FILTERS

    let training_in =
        $('#filter_training_in').val() ?? '';

    let training_in_year =
        $('#filter_training_year').val() ?? '';

    let connected_to =
        $('#filter_connected_to').val() ?? '';

    let reference_by =
        $('#filter_reference_by').val() ?? '';

    let contact_person =
        $('#filter_contact_person').val() ?? '';

    let training_21_days =
    $('#filter_training_21_days').val() ?? '';

    let training_45_days =
        $('#filter_training_45_days').val() ?? '';

    let training_6_months =
        $('#filter_training_6_months').val() ?? '';


    // Build URL
    let url =
        "{{ route('colleges.export.excel') }}?" +

        "status=" +
        encodeURIComponent(currentStatus) +

        "&state_name=" +
        encodeURIComponent(state) +

        "&district_name=" +
        encodeURIComponent(district) +

        "&student_filter=" +
        encodeURIComponent(student) +

        "&college_type=" +
        encodeURIComponent(college_type) +

        "&call_status=" +
        encodeURIComponent(call_status) +

        "&offer_training=" +
        encodeURIComponent(offer_training) +

        "&is_important=" +
        encodeURIComponent(is_important) +

        "&ownership_type=" +
        encodeURIComponent(ownership_type) +

        "&connection_type=" +
        encodeURIComponent(connection_type) +

        "&training_in=" +
        encodeURIComponent(training_in) +

        "&training_in_year=" +
        encodeURIComponent(training_in_year) +

        "&connected_to=" +
        encodeURIComponent(connected_to) +

        "&reference_by=" +
        encodeURIComponent(reference_by) +

        "&training_21_days=" +
        encodeURIComponent(training_21_days) +

        "&training_45_days=" +
        encodeURIComponent(training_45_days) +

        "&training_6_months=" +
        encodeURIComponent(training_6_months) +

        "&contact_person=" +
        encodeURIComponent(contact_person);


    // Multiple departments
    $('.department-filter:checked').each(function () {

        url +=
            "&departments[]=" +
            encodeURIComponent($(this).val());

    });


    window.location.href = url;


    setTimeout(function () {

        $btn.prop('disabled', false)
            .text('Export');

    }, 3000);

});


    // ==========================================
    // EXISTING CALL STATUS TOGGLE
    // ==========================================

    $(document).on('change', '.toggle-status', function () {

        let checkbox = $(this);

        let id = checkbox.data('id');

        let status =
            checkbox.is(':checked')
                ? 1
                : 0;


        $.ajax({

            url: "{{ route('colleges.toggle.status', ':id') }}"
                .replace(':id', id),

            type: 'POST',

            data: {

                _token: '{{ csrf_token() }}',

                status: status

            },


            success: function (res) {

                console.log('Updated');

            },


            error: function () {

                alert('Something went wrong');

                checkbox.prop(
                    'checked',
                    !status
                );

            }

        });

    });
function filterDepartmentOptionsByCollegeType() {

    const collegeType = $('#filter_college_type').val();

    const $degreeGroup = $('.department-group').eq(0);
    const $diplomaGroup = $('.department-group').eq(1);

    if (collegeType == '0') {

        // Degree
        $degreeGroup.show();
        $diplomaGroup.hide();

    } else if (collegeType == '1') {

        // Diploma
        $degreeGroup.hide();
        $diplomaGroup.show();

    } else {

        // Both / Unknown / Nothing selected
        $degreeGroup.show();
        $diplomaGroup.show();
    }
}

filterDepartmentOptionsByCollegeType();

// ==========================================
// BULK EDIT - DEPARTMENT FILTER
// ==========================================

 $(document).ready(function () {

    // ==========================================
// DELETE COLLEGE - SWEETALERT CONFIRMATION
// ==========================================

$(document).on('submit', '.college-delete-form', function (e) {

    e.preventDefault();

    const form = this;

    Swal.fire({

        icon: 'warning',

        title: 'Delete College?',

        text: 'Are you sure you want to delete this college?',

        showCancelButton: true,

        confirmButtonText: 'Yes, Delete',

        cancelButtonText: 'Cancel',

        confirmButtonColor: '#d33',

        cancelButtonColor: '#6c757d',

        reverseButtons: false

    }).then(function (result) {

        if (result.isConfirmed) {

            form.submit();

        }

    });

});

    // ==========================================
    // BULK EDIT - DEPARTMENT FILTER
    // ==========================================

    const $bulkCollegeType = $('#bulkCollegeType');
    const $bulkDepartments = $('#bulkDepartments');

    // Store all departments once
    const allBulkDepartments = [];

    $bulkDepartments.find('option').each(function () {

        allBulkDepartments.push({
            value: $(this).val(),
            text: $(this).text().trim(),
            type: $(this).data('type')
        });

    });


    function filterBulkDepartments() {

        const collegeType = $bulkCollegeType.val();

        let allowedTypes = [];

        if (collegeType == '0') {

            // Degree
            allowedTypes = ['degree'];

        } else if (collegeType == '1') {

            // Diploma
            allowedTypes = ['diploma'];

        } else if (collegeType == '2' || collegeType == '3') {

            // Both / Unknown
            allowedTypes = ['degree', 'diploma'];

        } else {

            // Keep Existing / blank
            allowedTypes = ['degree', 'diploma'];
        }


        // Remove current options
        $bulkDepartments.empty();


        // Add Degree first, then Diploma
        allowedTypes.forEach(function (type) {

            allBulkDepartments.forEach(function (department) {

                if (department.type === type) {

                    const option = new Option(
                        department.text,
                        department.value,
                        false,
                        false
                    );

                    $(option).attr('data-type', department.type);

                    $bulkDepartments.append(option);
                }

            });

        });


        // Refresh Select2 if it is being used
        if ($bulkDepartments.hasClass('select2-hidden-accessible')) {
            $bulkDepartments.trigger('change.select2');
        }

    }


    // Use delegated event so it also works when modal is opened
    $(document).on('change', '#bulkCollegeType', function () {

        filterBulkDepartments();

    });


    // Initial state
    filterBulkDepartments();

});
</script>


@endpush

@endsection