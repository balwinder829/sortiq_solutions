@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }

    /*
    |--------------------------------------------------------------------------
    | Horizontal DataTable Scroll
    |--------------------------------------------------------------------------
    */

    .table-responsive {
        overflow-x: auto;
        overflow-y: hidden;
        width: 100%;
    }

    #jobTable {
        min-width: 1000px;
    }
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-4">
            <h1 class="page_heading">Part-Time Jobs</h1>
        </div>

        <div class="col-md-8">
            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">

                {{-- BULK DELETE --}}
                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                {{-- ADD --}}
                <a href="{{ route('part-time-jobs.create') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Add Part-Time Job
                </a>

                {{-- IMPORT --}}
                <a href="{{ route('part-time-jobs.import') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Import Part-Time Jobs
                </a>

                {{-- EXPORT --}}
                <a href="{{ route('part-time-jobs.export', request()->query()) }}"
                   class="btn mb-3"
                   style="background:#198754;color:#fff;">
                    Export Excel
                </a>

            </div>
        </div>

    </div>


    {{-- ================= SUCCESS MESSAGE ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- ================= ERROR MESSAGE ================= --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">

            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    {{-- ================= FILTERS ================= --}}
    <form method="GET"
          id="filterForm"
          class="mb-4">

        <div class="row align-items-end">

            {{-- JOB TYPE --}}
            <div class="col-md-2">
                <label class="fw-bold">Job Type</label>

                <input type="text"
                       name="job_type"
                       value="{{ request('job_type') }}"
                       class="form-control filterchange"
                       placeholder="Job type">
            </div>


            {{-- SHIFT --}}
            <div class="col-md-2">
                <label class="fw-bold">Shift</label>

                <input type="text"
                       name="shift"
                       value="{{ request('shift') }}"
                       class="form-control filterchange"
                       placeholder="Shift">
            </div>


            {{-- LOCATION --}}
            <div class="col-md-2">
                <label class="fw-bold">Location</label>

                <input type="text"
                       name="location"
                       value="{{ request('location') }}"
                       class="form-control filterchange"
                       placeholder="Location">
            </div>


            {{-- STATUS --}}
            <div class="col-md-2">
                <label class="fw-bold">Status</label>

                <select name="status"
                        class="form-control filterchange">

                    <option value="">All</option>

                    <option value="active"
                        {{ request('status') == 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ request('status') == 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>
            </div>


            {{-- RESET --}}
            <div class="col-md-2">

                <a href="{{ route('part-time-jobs.index') }}"
                   class="btn btn-secondary">

                    <i class="fa fa-refresh"></i>
                    Reset

                </a>

            </div>

        </div>

    </form>


    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">

        <table class="table table-bordered table-striped"
               id="jobTable">

            <thead class="table-light">

                <tr>

                    {{-- SELECT ALL --}}
                    <th style="width:40px;">
                        <input type="checkbox"
                               id="checkAll">
                    </th>

                    <th>#</th>

                    <th>Name</th>

                    <th>Job Type</th>

                    <th>Shift</th>

                    <th>Location</th>

                    <th>Mobile</th>

                    <th>Status</th>

                    <th class="text-center" width="150">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody>

                @foreach($jobs as $job)

                    <tr>

                        {{-- CHECKBOX --}}
                        <td>

                            <input type="checkbox"
                                   class="job-checkbox"
                                   value="{{ $job->id }}">

                        </td>


                        {{-- SERIAL NUMBER --}}
                        <td></td>


                        {{-- NAME --}}
                        <td>
                            {{ $job->name }}
                        </td>


                        {{-- JOB TYPE --}}
                        <td>
                            {{ $job->job_type ?? '-' }}
                        </td>


                        {{-- SHIFT --}}
                        <td>
                            {{ $job->shift ?? '-' }}
                        </td>


                        {{-- LOCATION --}}
                        <td>
                            {{ $job->location ?? '-' }}
                        </td>


                        {{-- MOBILE --}}
                        <td>
                            {{ $job->mobile ?? '-' }}
                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span class="badge
                                {{ $job->status == 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary' }}">

                                {{ ucfirst($job->status) }}

                            </span>

                        </td>


                        {{-- ACTIONS --}}
                        <td class="text-center">

                            {{-- VIEW --}}
                            <a href="{{ route('part-time-jobs.show', $job->id) }}"
                               class="btn btn-sm"
                               title="View">

                                <i class="fa fa-eye"></i>

                            </a>


                            {{-- EDIT --}}
                            <a href="{{ route('part-time-jobs.edit', $job->id) }}"
                               class="btn btn-sm"
                               title="Edit">

                                <i class="fa fa-edit"></i>

                            </a>


                            {{-- DELETE --}}
                            <form action="{{ route('part-time-jobs.destroy', $job->id) }}"
                                  method="POST"
                                  class="d-inline part-time-delete-form">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm"
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

    /*
    |--------------------------------------------------------------------------
    | SELECTED JOBS
    |--------------------------------------------------------------------------
    */

    let selectedJobs = {};


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    var table = $('#jobTable').DataTable({

        pageLength: 25,

        order: [[1, 'desc']],

        scrollX: true,
        autoWidth: false,

        columnDefs: [

            {
                targets: 0,
                searchable: false,
                orderable: false
            },

            {
                targets: 1,
                searchable: false,
                orderable: false
            }

        ]

    });


    /*
    |--------------------------------------------------------------------------
    | RESTORE SELECTION AFTER DRAW
    |--------------------------------------------------------------------------
    */

    table.on('draw.dt', function () {

        var pageInfo = table.page.info();


        /*
        |--------------------------------------------------------------------------
        | SERIAL NUMBER
        |--------------------------------------------------------------------------
        */

        table
            .column(1, { page: 'current' })
            .nodes()
            .each(function (cell, i) {

                cell.innerHTML = pageInfo.start + i + 1;

            });


        /*
        |--------------------------------------------------------------------------
        | RESTORE CHECKBOXES
        |--------------------------------------------------------------------------
        */

        $('.job-checkbox').each(function () {

            const id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedJobs[id]
            );

        });


        /*
        |--------------------------------------------------------------------------
        | UPDATE SELECT ALL
        |--------------------------------------------------------------------------
        */

        updateCheckAll();

    });


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on('change', '.job-checkbox', function () {

        const id = $(this).val();

        if ($(this).is(':checked')) {

            selectedJobs[id] = true;

        } else {

            delete selectedJobs[id];

        }

        updateCheckAll();

    });


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - CURRENT PAGE
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on('change', function () {

        const checked = $(this).is(':checked');


        table
            .rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.job-checkbox')
            .each(function () {

                const id = $(this).val();

                $(this).prop('checked', checked);


                if (checked) {

                    selectedJobs[id] = true;

                } else {

                    delete selectedJobs[id];

                }

            });

    });


    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECT ALL
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        const currentPageCheckboxes =
            table
                .rows({ page: 'current' })
                .nodes()
                .to$()
                .find('.job-checkbox');


        const total = currentPageCheckboxes.length;

        const checked =
            currentPageCheckboxes.filter(':checked').length;


        if (total === 0) {

            $('#checkAll').prop('checked', false);

        } else {

            $('#checkAll').prop(
                'checked',
                total === checked
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    $('#bulkDeleteBtn').on('click', function () {

        const ids =
            Object.keys(selectedJobs);


        /*
        |--------------------------------------------------------------------------
        | NO SELECTION
        |--------------------------------------------------------------------------
        */

        if (ids.length === 0) {

            Swal.fire({

                icon: 'warning',

                title: 'No Record Selected',

                text: 'Please select at least 1 record.'

            });

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | CONFIRMATION
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'warning',

            title: 'Delete Selected Records?',

            text:
                'Are you sure you want to delete the selected part-time jobs?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('part-time-jobs.bulkDelete') }}",

                type: 'POST',

                data: {

                    _token: "{{ csrf_token() }}",

                    ids: ids.join(',')

                },

                beforeSend: function () {

                    $('#bulkDeleteBtn')
                        .prop('disabled', true)
                        .text('Deleting...');

                },

                success: function (response) {

                    selectedJobs = {};

                    $('#checkAll')
                        .prop('checked', false);


                    Swal.fire({

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        timer: 1500,

                        showConfirmButton: false

                    }).then(function () {

                        window.location.reload();

                    });

                },

                error: function (xhr) {

                    $('#bulkDeleteBtn')
                        .prop('disabled', false)
                        .text('Delete Selected');


                    let message =
                        'Something went wrong. Please try again.';


                    if (
                        xhr.responseJSON &&
                        xhr.responseJSON.message
                    ) {

                        message =
                            xhr.responseJSON.message;

                    }


                    Swal.fire({

                        icon: 'error',

                        title: 'Error',

                        text: message

                    });

                }

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | SINGLE DELETE CONFIRMATION
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.part-time-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete Part-Time Job?',

                text:
                    'Are you sure you want to delete this part-time job?',

                showCancelButton: true,

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {

                    HTMLFormElement
                        .prototype
                        .submit
                        .call(form);

                }

            });


            return false;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | FILTER AUTO SUBMIT
    |--------------------------------------------------------------------------
    */

    let timer;

    $('.filterchange').on('input change', function () {

        clearTimeout(timer);


        timer = setTimeout(function () {

            $('#filterForm').submit();

        }, 500);

    });


    /*
    |--------------------------------------------------------------------------
    | INITIAL DRAW
    |--------------------------------------------------------------------------
    */

    table.draw();

});

</script>

@endpush