@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    .deleted-college-row td {
        background-color: #f8d7da !important;
        color: #842029 !important;
    }

    .deleted-college-row .badge {
        font-size: 11px;
    }

    .college-name-cell {
        min-width: 300px;
    }
</style>


<div class="container">

    {{-- PAGE HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="page_heading mb-1">
                Colleges -
                {{ $session->session_display_name ?? $session->session_name }}
            </h2>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ url()->previous() }}"
               class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i>
                Back
            </a>

            <a href="{{ route('sessions.exportColleges', [
                    'session' => $session->id,
                    'search' => $search
                ]) }}"
               class="btn"
               style="background-color:#6b51df;color:#fff;">

                <i class="fa fa-download"></i>
                Download Excel

            </a>

        </div>

    </div>


    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">

        {{-- SESSION --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h6 class="fw-bold">
                        Session
                    </h6>

                    <div class="fs-5">
                        {{ $session->session_display_name ?? $session->session_name }}
                    </div>

                </div>

            </div>

        </div>


        {{-- TOTAL COLLEGES --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h6 class="fw-bold">
                        Total Colleges
                    </h6>

                    <div class="fs-5">
                        {{ $colleges->count() }}
                    </div>

                </div>

            </div>

        </div>


        {{-- TOTAL STUDENTS --}}
        <div class="col-md-4">

            <div class="card h-100">

                <div class="card-body">

                    <h6 class="fw-bold">
                        Total Students
                    </h6>

                    <div class="fs-5">

                        {{ $colleges->sum('students_count') }}

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- SEARCH --}}
    <div class="card mb-4">

        <div class="card-body">

            <form method="GET"
      action="{{ route('sessions.college', $session->id) }}"
      id="collegeSearchForm">

    <div class="row align-items-end">

        <div class="col-md-8">

            <label class="form-label">
                Search College Name
            </label>

            <input type="text"
                   name="search"
                   id="collegeSearch"
                   value="{{ $search }}"
                   class="form-control"
                   placeholder="Search by college name...">

        </div>

        <div class="col-md-4">

            <button type="submit"
                    class="btn btn-primary">
                <i class="fa fa-search"></i>
                Search
            </button>

            <a href="{{ route('sessions.college', $session->id) }}"
               class="btn btn-secondary">
                Clear
            </a>

        </div>

    </div>

</form>

        </div>

    </div>


    {{-- COLLEGE TABLE --}}
    <div class="table-responsive">

        <table id="session-colleges-table"
               class="table table-bordered table-striped">

            <thead>

                <tr>

                    <th>ID</th>

                    <th>College ID</th>

                    <th>College Name/Place</th>

                    <th>State</th>

                    <th>District</th>

                    <th>Students</th>

                </tr>

            </thead>


            <tbody>

                @foreach($colleges as $college)

                    @php

                        $collegeMaster = $college->collegeData;

                        /*
                         * College can be:
                         * 1. Active
                         * 2. Soft deleted
                         * 3. Missing completely
                         */

                        $isDeleted = $collegeMaster
                            && method_exists($collegeMaster, 'trashed')
                            && $collegeMaster->trashed();

                        $isMissing = !$collegeMaster;

                    @endphp


                    <tr class="{{ ($isDeleted || $isMissing)
                        ? 'deleted-college-row'
                        : '' }}">


                        {{-- ID --}}
                        <td>
                            {{ $loop->iteration }}
                        </td>


                        {{-- COLLEGE ID --}}
                        <td>

                            {{ $college->college_name ?? '-' }}

                        </td>


                        {{-- COLLEGE NAME --}}
                        <td class="college-name-cell">

                            @if($collegeMaster)

                                {{ $collegeMaster->college_name }}

                                @if($isDeleted)

                                    <span class="badge bg-danger ms-1">
                                        Deleted
                                    </span>

                                @endif

                            @else

                                <span class="text-danger fw-bold">
                                    College record not found
                                </span>

                            @endif

                        </td>


                        {{-- STATE --}}
                        <td>

                            @if($collegeMaster)

                                {{ $collegeMaster->state->name ?? '-' }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- DISTRICT --}}
                        <td>

                            @if($collegeMaster)

                                {{ $collegeMaster->district->name ?? '-' }}

                            @else

                                -

                            @endif

                        </td>


                        {{-- STUDENTS --}}
                        <td>

                            <span class="badge bg-success"
                                  style="font-size:13px;">

                                {{ $college->students_count }}

                            </span>

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

    $('#session-colleges-table').DataTable({

        pageLength: 50,

        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, 'All']
        ],

        scrollX: true,

        autoWidth: false,

        order: [
            [5, 'desc']
        ],

        columnDefs: [

            {
                targets: 0,
                searchable: false,
                orderable: false
            }

        ]

    });

});

</script>
<script>
$(document).ready(function () {

    let searchTimer;

    $('#collegeSearch').on('keyup', function () {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function () {

            $('#collegeSearchForm').submit();

        }, 300);

    });

});
</script>
@endpush