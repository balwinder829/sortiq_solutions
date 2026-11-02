@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2">

        <div class="col-md-6">
            <h1 class="page_heading">PGs</h1>
        </div>

        <div class="col-md-6">

            <div class="d-flex justify-content-end gap-2">

                {{-- BULK DELETE --}}
                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                {{-- ADD --}}
                <a href="{{ route('pgs.create') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Add PG
                </a>

                {{-- IMPORT --}}
                <a href="{{ route('pgs.import') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Import PGs
                </a>

                {{-- EXPORT --}}
                <a href="{{ route('pgs.export', request()->query()) }}"
                   class="btn mb-3"
                   style="background:#198754;color:#fff;">
                    Export Excel
                </a>

            </div>

        </div>

    </div>


    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}
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

            {{-- PG TYPE --}}
            <div class="col-md-2">

                <label class="fw-bold">
                    PG Type
                </label>

                <select name="pg_type"
                        class="form-control filterchange">

                    <option value="">
                        All
                    </option>

                    <option value="boys"
                        {{ request('pg_type') == 'boys' ? 'selected' : '' }}>
                        Boys
                    </option>

                    <option value="girls"
                        {{ request('pg_type') == 'girls' ? 'selected' : '' }}>
                        Girls
                    </option>

                </select>

            </div>


            {{-- FOOD TYPE --}}
            <div class="col-md-2">

                <label class="fw-bold">
                    Food Type
                </label>

                <select name="food_type"
                        class="form-control filterchange">

                    <option value="">
                        All
                    </option>

                    <option value="food"
                        {{ request('food_type') == 'food' ? 'selected' : '' }}>
                        Food
                    </option>

                    <option value="without_food"
                        {{ request('food_type') == 'without_food' ? 'selected' : '' }}>
                        Without Food
                    </option>

                </select>

            </div>


            {{-- STATUS --}}
            <div class="col-md-2">

                <label class="fw-bold">
                    Status
                </label>

                <select name="status"
                        class="form-control filterchange">

                    <option value="">
                        All
                    </option>

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


            {{-- LOCATION --}}
            <div class="col-md-3">

                <label class="fw-bold">
                    Location
                </label>

                <input type="text"
                       name="address"
                       value="{{ request('address') }}"
                       class="form-control filterchange"
                       placeholder="Search address">

            </div>


            {{-- RESET --}}
            <div class="col-md-2">

                <a href="{{ route('pgs.index') }}"
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
               id="pgTable">

            <thead class="table-light">

                <tr>

                    {{-- SELECT ALL --}}
                    <th style="width:40px;">

                        <input type="checkbox"
                               id="checkAll">

                    </th>

                    <th>#</th>

                    <th>Name</th>

                    <th>PG Type</th>

                    <th>Food Type</th>

                    <th>Rent</th>

                    <th>Status</th>

                    <th class="text-center">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($pgs as $pg)

                    <tr>

                        {{-- CHECKBOX --}}
                        <td>

                            <input type="checkbox"
                                   class="pg-checkbox"
                                   value="{{ $pg->id }}">

                        </td>


                        {{-- SERIAL --}}
                        <td></td>


                        {{-- NAME --}}
                        <td>
                            {{ $pg->name }}
                        </td>


                        {{-- PG TYPE --}}
                        <td>
                            {{ ucfirst($pg->pg_type) }}
                        </td>


                        {{-- FOOD TYPE --}}
                        <td>
                            {{ $pg->food_type == 'food'
                                ? 'Food'
                                : 'Without Food' }}
                        </td>


                        {{-- RENT --}}
                        <td>
                            {{ $pg->rent_estimate ?? '-' }}
                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span class="badge
                                {{ $pg->status == 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary' }}">

                                {{ ucfirst($pg->status) }}

                            </span>

                        </td>


                        {{-- ACTIONS --}}
                        <td class="text-center">

                            {{-- VIEW --}}
                            <a href="{{ route('pgs.show', $pg->id) }}"
                               class="btn btn-sm"
                               title="View">

                                <i class="fa fa-eye"></i>

                            </a>


                            {{-- EDIT --}}
                            <a href="{{ route('pgs.edit', $pg->id) }}"
                               class="btn btn-sm"
                               title="Edit">

                                <i class="fa fa-edit"></i>

                            </a>


                            {{-- DELETE --}}
                            <form action="{{ route('pgs.destroy', $pg->id) }}"
                                  method="POST"
                                  class="d-inline pg-delete-form">

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
    | SELECTED PGs
    |--------------------------------------------------------------------------
    */

    let selectedPgs = {};


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    var table = $('#pgTable').DataTable({

        pageLength: 25,

        scrollX: true,
        autoWidth: false,

        order: [[1, 'desc']],

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
    | DRAW
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

                cell.innerHTML =
                    pageInfo.start + i + 1;

            });


        /*
        |--------------------------------------------------------------------------
        | RESTORE SELECTED CHECKBOXES
        |--------------------------------------------------------------------------
        */

        $('.pg-checkbox').each(function () {

            const id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedPgs[id]
            );

        });


        updateCheckAll();

    }).draw();


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.pg-checkbox',
        function () {

            const id = $(this).val();


            if ($(this).is(':checked')) {

                selectedPgs[id] = true;

            } else {

                delete selectedPgs[id];

            }


            updateCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - CURRENT PAGE
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on('change', function () {

        const checked =
            $(this).is(':checked');


        table
            .rows({ page: 'current' })
            .nodes()
            .to$()
            .find('.pg-checkbox')
            .each(function () {

                const id = $(this).val();


                $(this).prop(
                    'checked',
                    checked
                );


                if (checked) {

                    selectedPgs[id] = true;

                } else {

                    delete selectedPgs[id];

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
                .find('.pg-checkbox');


        const total =
            currentPageCheckboxes.length;


        const checked =
            currentPageCheckboxes
                .filter(':checked')
                .length;


        if (total === 0) {

            $('#checkAll').prop(
                'checked',
                false
            );

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
            Object.keys(selectedPgs);


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
        | CONFIRM
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'warning',

            title: 'Delete Selected PGs?',

            text: 'Are you sure you want to delete the selected PGs?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('pgs.bulkDelete') }}",

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

                    selectedPgs = {};

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
        '.pg-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete PG?',

                text: 'Are you sure you want to delete this PG?',

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

        }, 300);

    });

});

</script>

@endpush