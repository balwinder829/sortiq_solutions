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
            <h1 class="page_heading">Visitor Records</h1>
        </div>

    </div>


    {{-- VISITOR FORM URL --}}
    <div class="row mb-3">

        <div class="col-md-12">

            <label class="form-label fw-bold">
                Visitor Form URL
            </label>

            <div class="input-group">

                <input
                    type="text"
                    id="visitorFormUrl"
                    class="form-control"
                    value="{{ route('visitor.create') }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="copyVisitorUrl"
                    title="Copy Visitor Form URL"
                >
                    <i class="fa fa-copy"></i>
                </button>

            </div>

            <small id="copyMessage" class="text-success d-none">
                Link copied successfully!
            </small>

        </div>

    </div>


    {{-- FILTERS --}}
    <form id="filterForm" class="row mb-3">

        {{-- Visitor Name --}}
        <div class="col-md-3">
            <input
                type="text"
                name="visitor_name"
                value="{{ request('visitor_name') }}"
                class="form-control filter-input"
                placeholder="Visitor Name"
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


        {{-- Status --}}
        <div class="col-md-2">
            <select
                name="status"
                class="form-control filter-input"
            >
                <option value="">All Status</option>

                <option value="pending"
                    {{ request('status') == 'pending' ? 'selected' : '' }}>
                    Pending
                </option>

                <option value="confirmed"
                    {{ request('status') == 'confirmed' ? 'selected' : '' }}>
                    Confirmed
                </option>

                <option value="visited"
                    {{ request('status') == 'visited' ? 'selected' : '' }}>
                    Visited
                </option>

                <option value="cancelled"
                    {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                    Cancelled
                </option>

            </select>
        </div>


        {{-- Visit Date --}}
        <div class="col-md-2">
            <input
                type="date"
                name="visit_date"
                value="{{ request('visit_date') }}"
                class="form-control filter-input"
            >
        </div>


        {{-- Reset --}}
        <div class="col-md-2">
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
                data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- TABLE --}}
    <div class="table-responsive">

        <table
            class="table table-bordered table-striped"
            id="visitorRecordsTable"
        >

            <thead>

                <tr>

                    <th>#</th>

                    <th>Visitor Name</th>

                    <th>Mobile</th>

                    <th>Email</th>

                    <th>Organization</th>

                    <th>Purpose</th>

                    <th class="no-wrap">
                        Visit Date
                    </th>

                    <th class="no-wrap">
                        Visit Time
                    </th>

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

                @foreach($visitors as $visitor)

                    <tr>

                        <td></td>

                        <td>
                            {{ $visitor->visitor_name }}
                        </td>

                        <td class="no-wrap">
                            {{ $visitor->mobile }}
                        </td>

                        <td>
                            {{ $visitor->email ?? '-' }}
                        </td>

                        <td>
                            {{ $visitor->organization ?? '-' }}
                        </td>

                        <td>
                            {{ $visitor->purpose }}
                        </td>

                        <td class="no-wrap">

                            @if($visitor->visit_date)
                                {{ \Carbon\Carbon::parse($visitor->visit_date)->format('d M Y') }}
                            @else
                                -
                            @endif

                        </td>

                        <td class="no-wrap">

                            @if($visitor->visit_time)
                                {{ \Carbon\Carbon::parse($visitor->visit_time)->format('h:i A') }}
                            @else
                                -
                            @endif

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($visitor->status === 'pending')

                                <span class="badge bg-warning text-dark">
                                    Pending
                                </span>

                            @elseif($visitor->status === 'confirmed')

                                <span class="badge bg-primary">
                                    Confirmed
                                </span>

                            @elseif($visitor->status === 'visited')

                                <span class="badge bg-success">
                                    Visited
                                </span>

                            @elseif($visitor->status === 'cancelled')

                                <span class="badge bg-danger">
                                    Cancelled
                                </span>

                            @else

                                <span class="badge bg-secondary">
                                    {{ $visitor->status }}
                                </span>

                            @endif

                        </td>


                        {{-- CREATED --}}
                        <td class="no-wrap text-muted">

                            {{ $visitor->created_at
                                ? $visitor->created_at->format('d M Y h:i A')
                                : '-' }}

                        </td>


                        {{-- ACTIONS --}}
                        <td class="no-wrap text-center">
                            <a
                                href="{{ route('admin.visitor_records.show', $visitor->id) }}"
                                class="btn btn-sm"
                                data-bs-toggle="tooltip"
                                title="View"
                            >
                                <i class="fa fa-eye"></i>
                            </a>
                            {{-- EDIT --}}
                            <a
                                href="{{ route('admin.visitor_records.edit', $visitor->id) }}"
                                class="btn btn-sm"
                                data-bs-toggle="tooltip"
                                title="Edit"
                            >
                                <i class="fa fa-edit"></i>
                            </a>


                            {{-- DELETE --}}
                            <form
                                action="{{ route('admin.visitor_records.destroy', $visitor->id) }}"
                                method="POST"
                                class="d-inline"
                                data-swal-confirm="Are you sure you want to delete this visitor record?"
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

    var table = $('#visitorRecordsTable').DataTable({

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
            "{{ route('admin.visitor_records.index') }}?" + query;

    });


    /*
    |--------------------------------------------------------------------------
    | Reset Filters
    |--------------------------------------------------------------------------
    */

    $('#resetFilters').click(function () {

        window.location.href =
            "{{ route('admin.visitor_records.index') }}";

    });


    /*
    |--------------------------------------------------------------------------
    | Copy Visitor Form URL
    |--------------------------------------------------------------------------
    */

    $('#copyVisitorUrl').click(function () {

        let url = $('#visitorFormUrl').val();

        navigator.clipboard.writeText(url).then(function () {

            $('#copyMessage')
                .removeClass('d-none')
                .text('Link copied successfully!');

            setTimeout(function () {

                $('#copyMessage').addClass('d-none');

            }, 2000);

        }).catch(function () {

            let input =
                document.getElementById('visitorFormUrl');

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