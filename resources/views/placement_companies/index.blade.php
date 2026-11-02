@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">Placement Companies</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center gap-2 flex-wrap">

                {{-- BULK DELETE --}}
                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                {{-- ADD --}}
                <a href="{{ route('placement-companies.create') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Add Company
                </a>

                {{-- IMPORT --}}
                <a href="{{ route('placement-companies.import') }}"
                   class="btn mb-3"
                   style="background:#6b51df;color:#fff;">
                    Import Companies
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


    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">

        <table class="table table-bordered table-striped"
               id="companyTable">

            <thead class="table-light">

                <tr>

                    {{-- SELECT ALL --}}
                    <th style="width:40px;">
                        <input type="checkbox"
                               id="checkAll">
                    </th>

                    <th>#</th>

                    <th>Company Name</th>

                    {{-- <th>Contact Person</th> --}}

                    <th>Address</th>

                    <th>Phone</th>

                    <th>Status</th>

                    <th class="text-center" width="150">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

                @foreach($companies as $company)

                    <tr>

                        {{-- CHECKBOX --}}
                        <td>

                            <input type="checkbox"
                                   class="company-checkbox"
                                   value="{{ $company->id }}">

                        </td>


                        {{-- SERIAL NUMBER --}}
                        <td>{{ $loop->iteration }}</td>


                        {{-- COMPANY NAME --}}
                        <td>
                            {{ $company->name }}
                        </td>


                        {{-- ADDRESS --}}
                        <td>
                            {{ $company->address ?? '-' }}
                        </td>


                        {{-- PHONE --}}
                        <td>
                            {{ $company->phone ?? '-' }}
                        </td>


                        {{-- STATUS --}}
                        <td>

                            <span class="badge
                                {{ $company->status == 'active'
                                    ? 'bg-success'
                                    : 'bg-secondary' }}">

                                {{ ucfirst($company->status) }}

                            </span>

                        </td>


                        {{-- ACTIONS --}}
                        <td class="text-center">

                            {{-- VIEW --}}
                            <a href="{{ route('placement-companies.show', $company->id) }}"
                               class="btn btn-sm"
                               title="View">

                                <i class="fa fa-eye"></i>

                            </a>


                            {{-- EDIT --}}
                            <a href="{{ route('placement-companies.edit', $company->id) }}"
                               class="btn btn-sm"
                               title="Edit">

                                <i class="fa fa-edit"></i>

                            </a>


                            {{-- DELETE --}}
                            <form action="{{ route('placement-companies.destroy', $company->id) }}"
                                  method="POST"
                                  class="d-inline placement-company-delete-form">

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
    | SELECTED COMPANIES
    |--------------------------------------------------------------------------
    */

    let selectedCompanies = {};


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    var table = $('#companyTable').DataTable({

        pageLength: 25,

        scrollX: true,
        autoWidth: false,

        order: [[1, 'asc']],

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
        | RESTORE CHECKBOX SELECTION
        |--------------------------------------------------------------------------
        */

        $('.company-checkbox').each(function () {

            const id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedCompanies[id]
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

    $(document).on(
        'change',
        '.company-checkbox',
        function () {

            const id = $(this).val();


            if ($(this).is(':checked')) {

                selectedCompanies[id] = true;

            } else {

                delete selectedCompanies[id];

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
            .find('.company-checkbox')
            .each(function () {

                const id = $(this).val();


                $(this).prop(
                    'checked',
                    checked
                );


                if (checked) {

                    selectedCompanies[id] = true;

                } else {

                    delete selectedCompanies[id];

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
                .find('.company-checkbox');


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
            Object.keys(selectedCompanies);


        /*
        |--------------------------------------------------------------------------
        | NO RECORD SELECTED
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

            text: 'Are you sure you want to delete the selected placement companies?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('placement-companies.bulkDelete') }}",

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

                    selectedCompanies = {};

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
        '.placement-company-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete Placement Company?',

                text: 'Are you sure you want to delete this company?',

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


});

</script>

@endpush