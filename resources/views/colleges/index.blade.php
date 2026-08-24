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
</style>

<div class="container">

    {{-- ================================
        PAGE HEADER
    ================================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-8">
            <h1 class="page_heading">Colleges / Places</h1>
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
    ================================= --}}
    <div class="row mb-2 align-items-center">

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

        {{-- Important --}}
        <div class="col-md-2">
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
            <select id="filter_department" class="form-control">

                <option value="">Department</option>

                <option value="CSE">CSE</option>
                <option value="MBA">MBA</option>
                <option value="BBA">BBA</option>
                <option value="Civil">Civil</option>
                <option value="EC">EC</option>
                <option value="Mechanical">Mechanical</option>

            </select>
        </div>

        {{-- Reset --}}
        <div class="col-md-1 mt-2">
            <a href="{{ route('colleges.index') }}"
               class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>


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

                        {{-- College Type --}}
                        <div class="form-group col-md-6 mb-3">

                            <label>
                                <strong>College Type</strong>
                            </label>

                            <select name="college_type"
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

                        {{-- Departments --}}
                        <div class="form-group col-md-12 mb-3">

                            <label>
                                <strong>Departments</strong>
                            </label>

                            @php

                                $departmentList = [
                                    'CSE',
                                    'MBA',
                                    'BBA',
                                    'Civil',
                                    'EC',
                                    'Mechanical',
                                ];

                            @endphp

                            <select
                                name="departments[]"
                                id="bulkDepartments"
                                class="form-control"
                                multiple>

                                @foreach($departmentList as $department)

                                    <option value="{{ $department }}">
                                        {{ $department }}
                                    </option>

                                @endforeach

                            </select>

                            <small class="text-muted">
                                Hold Ctrl to select multiple departments.
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

                    d.department = $('#filter_department').val();

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

        $('#student_filter, #filter_college_type, #filter_training, #filter_important, #filter_ownership, #filter_connection, #filter_department')
            .change(function () {

                table.ajax.reload();

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
            $('#filter_college_type').val();

        let offer_training =
            $('#filter_training').val();

        let call_status =
            $('#filter_status').val();

        let is_important =
            $('#filter_important').val();

        let ownership_type =
            $('#filter_ownership').val();

        let connection_type =
            $('#filter_connection').val();

        let department =
            $('#filter_department').val();


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

            "&department=" +
            encodeURIComponent(department);


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

</script>

@endpush

@endsection