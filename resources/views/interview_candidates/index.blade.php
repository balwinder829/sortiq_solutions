@extends('layouts.app')

@section('content')

<style>

    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }

    thead th {
        background-color: #f8f9fa !important;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6 !important;
    }

    table.table-bordered > :not(caption) > * > * {
        border-color: #dee2e6;
    }

    .badge {
        font-size: 12px;
        padding: 5px 8px;
    }

    .no-wrap {
        white-space: nowrap;
    }

</style>


<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                Interview Candidates
            </h1>

        </div>

    </div>


    {{-- INTERVIEW FORM URL --}}
    <div class="row mb-3">

        <div class="col-md-12">

            <label class="form-label fw-bold">
                Interview Form URL
            </label>

            <div class="input-group">

                <input
                    type="text"
                    id="interviewFormUrl"
                    class="form-control"
                    value="{{ route('interview.create') }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="copyInterviewUrl"
                    title="Copy Interview Form URL"
                >
                    <i class="fa fa-copy"></i>
                </button>

            </div>

            <small
                id="copyMessage"
                class="text-success d-none"
            >
                Link copied successfully!
            </small>

        </div>

    </div>


    {{-- FILTERS --}}
    <form id="filterForm" class="row mb-3">

        {{-- Candidate Name --}}
        <div class="col-md-3">

            <input
                type="text"
                name="candidate_name"
                value="{{ request('candidate_name') }}"
                class="form-control filter-input"
                placeholder="Candidate Name"
            >

        </div>


        {{-- Mobile --}}
        <div class="col-md-2">

            <input
                type="text"
                name="mobile"
                value="{{ request('mobile') }}"
                class="form-control filter-input"
                placeholder="Mobile"
            >

        </div>


        {{-- Position --}}
        <div class="col-md-2">

            <input
                type="text"
                name="position_applied"
                value="{{ request('position_applied') }}"
                class="form-control filter-input"
                placeholder="Position"
            >

        </div>


        {{-- Status --}}
        <div class="col-md-2">

            <select
                name="status"
                class="form-control filter-input"
            >

                <option value="">
                    All Status
                </option>

                <option value="pending"
                    {{ request('status') == 'pending' ? 'selected' : '' }}>
                    Pending
                </option>

                <option value="confirmed"
                    {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                    Confirmed
                </option>

                <option value="completed"
                    {{ request('status') == 'completed' ? 'selected' : '' }}>
                    Completed
                </option>

                <option value="cancelled"
                    {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>

            </select>

        </div>


        {{-- Preferred Date --}}
        <div class="col-md-2">

            <input
                type="date"
                name="preferred_date"
                value="{{ request('preferred_date') }}"
                class="form-control filter-input"
            >

        </div>


        {{-- Reset --}}
        <div class="col-md-1">

            <button
                type="button"
                id="resetFilters"
                class="btn btn-secondary w-100"
            >
                Reset
            </button>

        </div>

    </form>


    {{-- FLASH MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- TABLE --}}
    <div class="table-responsive">

        <table
            class="table table-bordered table-striped"
            id="interviewCandidatesTable"
        >

            <thead>

                <tr>

                    <th>#</th>

                    <th>Candidate Name</th>

                    <th>Mobile</th>

                    <th>Email</th>

                    <th>Position</th>

                    <th>Current Company</th>

                    <th>Location</th>

                    <th>Technology</th>

                    <th class="no-wrap">
                        Interview Date
                    </th>

                    <th class="no-wrap">
                        Interview Time
                    </th>

                    <th>Resume</th>

                    <th>Status</th>

                    <th class="no-wrap">
                        Submitted At
                    </th>

                    <th class="no-wrap text-center">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($candidates as $candidate)

                    <tr>

                        <td></td>


                        <td>
                            {{ $candidate->candidate_name }}
                        </td>


                        <td class="no-wrap">
                            {{ $candidate->mobile }}
                        </td>


                        <td>
                            {{ $candidate->email ?? '-' }}
                        </td>


                        <td>
                            {{ $candidate->position_applied }}
                        </td>


                        <td>
                            {{ $candidate->current_company ?? '-' }}
                        </td>


                        <td>
                            {{ $candidate->current_location ?? '-' }}
                        </td>


                        <td>
                            {{ $candidate->technology_known ?? '-' }}
                        </td>


                        <td class="no-wrap">

                            @if($candidate->preferred_date)

                                {{ \Carbon\Carbon::parse($candidate->preferred_date)->format('d M Y') }}

                            @else

                                -

                            @endif

                        </td>


                        <td class="no-wrap">

                            @if($candidate->preferred_time)

                                {{ \Carbon\Carbon::parse($candidate->preferred_time)->format('h:i A') }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- RESUME --}}
                        <td class="text-center">

                            @if($candidate->resume)

                                <a
                                    href="{{ asset($candidate->resume) }}"
                                    target="_blank"
                                    class="btn btn-sm"
                                    title="View Resume"
                                >
                                    <i class="fa fa-file-pdf"></i>
                                </a>

                            @else

                                -

                            @endif

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($candidate->status === 'pending')

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @elseif($candidate->status === 'confirmed')

                                <span class="badge bg-primary">
                                    Confirmed
                                </span>

                            @elseif($candidate->status === 'completed')

                                <span class="badge bg-success">
                                    Completed
                                </span>

                            @elseif($candidate->status === 'cancelled')

                                <span class="badge bg-danger">
                                    Cancelled
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ $candidate->status }}
                                </span>

                            @endif

                        </td>


                        {{-- CREATED --}}
                        <td class="no-wrap text-muted">

                            {{ $candidate->created_at
                                ? $candidate->created_at->format('d M Y h:i A')
                                : '-' }}

                        </td>


                        {{-- ACTIONS --}}
                        <td class="no-wrap text-center">

                            <a
                                href="{{ route('admin.interview_candidates.show', $candidate->id) }}"
                                class="btn btn-sm"
                                data-bs-toggle="tooltip"
                                title="View"
                            >
                                <i class="fa fa-eye"></i>
                            </a>

                            {{-- EDIT --}}
                            <a
                                href="{{ route('admin.interview_candidates.edit', $candidate->id) }}"
                                class="btn btn-sm"
                                data-bs-toggle="tooltip"
                                title="Edit"
                            >
                                <i class="fa fa-edit"></i>
                            </a>


                            {{-- DELETE --}}
                            <form
                                action="{{ route('admin.interview_candidates.destroy', $candidate->id) }}"
                                method="POST"
                                class="d-inline"
                                data-swal-confirm="Are you sure you want to delete this interview candidate?"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm"
                                    data-bs-toggle="tooltip"
                                    title="Delete"
                                >
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
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#interviewCandidatesTable').DataTable({

        paging: true,

        pageLength: 50,

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "All"]
        ],

        info: true,

        ordering: false,

        searching: true,

        columnDefs: [

            {
                targets: 0,
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


    /*
    |--------------------------------------------------------------------------
    | Number Column
    |--------------------------------------------------------------------------
    */

    table.on('draw.dt', function () {

        var pageInfo = table.page.info();

        table
            .column(0, { page: 'current' })
            .nodes()
            .each(function (cell, i) {

                cell.innerHTML =
                    pageInfo.start + i + 1;

            });

    }).draw();


    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    $('.filter-input').on('change keyup', function () {

        let query = $('#filterForm').serialize();

        window.location.href =
            "{{ route('admin.interview_candidates.index') }}?"
            + query;

    });


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    $('#resetFilters').click(function () {

        window.location.href =
            "{{ route('admin.interview_candidates.index') }}";

    });


    /*
    |--------------------------------------------------------------------------
    | Copy Interview Form URL
    |--------------------------------------------------------------------------
    */

    $('#copyInterviewUrl').click(function () {

        let url = $('#interviewFormUrl').val();

        navigator.clipboard.writeText(url).then(function () {

            $('#copyMessage')
                .removeClass('d-none')
                .text('Link copied successfully!');

            setTimeout(function () {

                $('#copyMessage').addClass('d-none');

            }, 2000);

        }).catch(function () {

            let input =
                document.getElementById('interviewFormUrl');

            input.select();

            input.setSelectionRange(0, 99999);

            document.execCommand('copy');

            $('#copyMessage')
                .removeClass('d-none')
                .text('Link copied successfully!');

            setTimeout(function () {

                $('#copyMessage').addClass('d-none');

            }, 2000);

        });

    });

});

</script>

@endpush