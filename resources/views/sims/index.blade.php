@extends('layouts.app')

@section('title', 'SIMs')

@section('content')

<style>
    /* =========================
       TABLE
    ========================== */

    table.dataTable {
        width: 100% !important;
    }

    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    table.dataTable th {
        vertical-align: middle;
        white-space: nowrap;
    }

    #simsTable {
        width: 100% !important;
    }

    #simsTable th,
    #simsTable td {
        padding: 12px 14px;
    }

    /* Checkbox column */
    #simsTable th:first-child,
    #simsTable td:first-child {
        width: 45px !important;
        text-align: center;
    }

    /* S.No. */
    #simsTable th:nth-child(2),
    #simsTable td:nth-child(2) {
        width: 70px;
        text-align: center;
    }

    /* Action column */
    #simsTable th:last-child,
    #simsTable td:last-child {
        min-width: 140px !important;
        width: 140px !important;
        white-space: nowrap !important;
        text-align: center;
    }

    /* Action buttons */
    #simsTable td:last-child .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        padding: 0;
        margin: 0 2px;
        vertical-align: middle;
    }

    #simsTable td:last-child form {
        display: inline-block !important;
        margin: 0;
        padding: 0;
        vertical-align: middle;
    }

    /* Bulk buttons */
    .tble-bts {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 15px;
        margin-bottom: 15px;
    }

    .tble-bts .btn {
        min-width: 140px;
    }

    /* DataTables search / length */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
    }

    .dataTables_wrapper .dataTables_filter input {
        margin-left: 5px;
    }

    /* Responsive */
    @media (max-width: 767px) {

        .page_heading {
            margin-bottom: 15px;
        }

        .page-header-button {
            justify-content: flex-start !important;
        }

        #simsTable th,
        #simsTable td {
            white-space: nowrap;
        }

        .tble-bts {
            margin-top: 15px;
        }

    }
</style>


<div class="container-fluid mt-4">

    {{-- =========================
         PAGE HEADER
    ========================== --}}

    <div class="row mb-4 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                SIMs
            </h1>

        </div>

        <div class="col-md-6 d-flex justify-content-end page-header-button">
            <button type="button"
                id="deleteSelected"
                class="btn btn-danger mr-2">

            <i class="fa fa-trash me-1"></i>
            Delete Selected

        </button>
            <a href="{{ route('sims.create') }}"
               class="btn"
               style="background-color:#6b51df;color:#fff;">

                <i class="fa fa-plus"></i>
                Add SIM

            </a>

        </div>

    </div>


    {{-- =========================
         SUCCESS MESSAGE
    ========================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================
         ERROR MESSAGE
    ========================== --}}

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================
         VALIDATION ERROR
    ========================== --}}

    @if($errors->has('error'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            {{ $errors->first('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close">
            </button>

        </div>

    @endif


    {{-- =========================
         TABLE
    ========================== --}}

    <div class="desktop-view">

        <div class="table-responsive">

            <table id="simsTable"
                   class="table table-bordered table-striped">

                <thead class="table-light">

                    <tr>

                        {{-- SELECT ALL --}}
                        <th>
                            <input type="checkbox"
                                   id="checkAll">
                        </th>

                        {{-- S.NO --}}
                        <th>
                            #
                        </th>

                        {{-- MOBILE NUMBER --}}
                        <th>
                            Mobile Number
                        </th>

                        {{-- OPERATOR --}}
                        <th>
                            Operator
                        </th>

                        {{-- NAME --}}
                        <th>
                            Name
                        </th>

                        {{-- STATUS --}}
                        <th class="text-center">
                            Status
                        </th>

                        {{-- ISSUED DATE --}}
                        <th>
                            Issued Date
                        </th>

                        {{-- ACTION --}}
                        <th class="text-center">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($sims as $sim)

                        <tr>

                            {{-- CHECKBOX --}}

                            <td class="text-center">

                                <input type="checkbox"
                                       class="record_checked"
                                       value="{{ $sim->id }}">

                            </td>


                            {{-- SERIAL NUMBER --}}

                            <td class="text-center">
                            </td>


                            {{-- MOBILE NUMBER --}}

                            <td>
                                {{ $sim->mobile_number }}
                            </td>


                            {{-- OPERATOR --}}

                            <td>
                                {{ $sim->operator }}
                            </td>


                            {{-- NAME --}}

                            <td>
                                {{ $sim->name ?: '-' }}
                            </td>


                            {{-- STATUS --}}

                            <td class="text-center">

                                @if($sim->status === 'active')

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge bg-secondary">
                                        Deactive
                                    </span>

                                @endif

                            </td>


                            {{-- ISSUED DATE --}}

                            <td>

                                {{ $sim->issued_at
                                    ? $sim->issued_at->format('d M Y')
                                    : '-' }}

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-center">

                                {{-- VIEW --}}

                                <a href="{{ route('sims.show', $sim) }}"
                                   class="btn btn-sm"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="View SIM">

                                    <i class="fas fa-eye"></i>

                                </a>


                                {{-- EDIT --}}

                                <a href="{{ route('sims.edit', $sim) }}"
                                   class="btn btn-sm"
                                   data-bs-toggle="tooltip"
                                   data-bs-placement="top"
                                   title="Edit SIM">

                                    <i class="fas fa-edit"></i>

                                </a>


                                {{-- DELETE --}}

                                <form action="{{ route('sims.destroy', $sim) }}"
                                      method="POST"
                                      class="delete-sim-form">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm delete-sim-btn"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            title="Delete SIM">

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


    {{-- =========================
         BULK ACTIONS
    ========================== --}}

    <!-- <div class="mt-3 tble-bts">

        

    </div> -->


    {{-- =========================
         BULK DELETE FORM
    ========================== --}}

    <form id="bulkDeleteForm"
          method="POST"
          action="{{ route('sims.bulkDelete') }}"
          style="display:none;">

        @csrf
        @method('DELETE')
        <input type="hidden"
               name="ids"
               id="deleteIds"
               value="">

    </form>


</div>

@endsection


{{-- =========================================================
     DATATABLE STYLES
========================================================= --}}

@push('styles')

<link rel="stylesheet"
      href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

@endpush


{{-- =========================================================
     SCRIPTS
========================================================= --}}

@push('scripts')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

$(document).ready(function () {


    /* =====================================================
       SELECTED SIM IDS
    ===================================================== */

    let selectedSims = [];


    /* =====================================================
       DATATABLE
    ===================================================== */

    var table = $('#simsTable').DataTable({

        pageLength: 10,

        // pagingType: "full_numbers",

        lengthMenu: [
            [10, 15, 20, 25, 50, 100],
            [10, 15, 20, 25, 50, 100]
        ],
        autoWidth: true,
        scrollX: true,

        order: [],

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
            },

            {
                targets: 7,
                searchable: false,
                orderable: false
            }

        ]

    });


    /* =====================================================
       GET SELECTED IDS
    ===================================================== */

    function getSelectedIds() {

        $('.record_checked:checked').each(function () {

            let id = $(this).val();

            if (!selectedSims.includes(id)) {

                selectedSims.push(id);

            }

        });

        return selectedSims;

    }


    /* =====================================================
       CHECK ALL STATE
    ===================================================== */

    function syncCheckAll() {

        var currentPageCheckboxes =
            $(table.rows({ page: 'current' }).nodes())
                .find('.record_checked:not(:disabled)');

        var checkedCount =
            currentPageCheckboxes.filter(':checked').length;


        $('#checkAll').prop(
            'checked',
            currentPageCheckboxes.length > 0 &&
            checkedCount === currentPageCheckboxes.length
        );

    }


    /* =====================================================
       DATATABLE DRAW
    ===================================================== */

    table.on('draw.dt', function () {

        var PageInfo = table.page.info();


        /* -----------------------------------------------
           DYNAMIC SERIAL NUMBER
        ------------------------------------------------ */

        table
            .column(1, { page: 'current' })
            .nodes()
            .each(function (cell, i) {

                cell.innerHTML =
                    PageInfo.start + i + 1;

            });


        /* -----------------------------------------------
           RESTORE SELECTED CHECKBOXES
        ------------------------------------------------ */

        $(table.rows({ page: 'current' }).nodes())
            .find('.record_checked')
            .each(function () {

                let id = $(this).val();

                $(this).prop(
                    'checked',
                    selectedSims.includes(id)
                );

            });


        syncCheckAll();

    }).draw();


    /* =====================================================
       SELECT ALL
    ===================================================== */

    $('#checkAll').on('click', function (e) {

        e.stopPropagation();

    });


    $('#checkAll').on('change', function () {

        const checked = this.checked;


        $(table.rows({ page: 'current' }).nodes())
            .find('.record_checked')
            .each(function () {

                if (this.disabled) {

                    this.checked = false;

                    return;

                }


                this.checked = checked;


                let id = $(this).val();


                if (checked) {

                    if (!selectedSims.includes(id)) {

                        selectedSims.push(id);

                    }

                } else {

                    selectedSims =
                        selectedSims.filter(
                            x => x !== id
                        );

                }

            });


        syncCheckAll();

    });


    /* =====================================================
       INDIVIDUAL CHECKBOX
    ===================================================== */

    $('#simsTable').on(
        'change',
        '.record_checked',
        function () {

            let id = $(this).val();


            if ($(this).is(':checked')) {

                if (!selectedSims.includes(id)) {

                    selectedSims.push(id);

                }

            } else {

                selectedSims =
                    selectedSims.filter(
                        x => x !== id
                    );

            }


            syncCheckAll();

        }
    );


    /* =====================================================
       BULK DELETE
    ===================================================== */

    $('#deleteSelected').on('click', function () {

        var ids = getSelectedIds();


        /* -----------------------------------------------
           NOTHING SELECTED
        ------------------------------------------------ */

        if (ids.length === 0) {

            Swal.fire({

                icon: 'warning',

                title: 'No SIM Selected',

                text: 'Please select at least one SIM.',

                confirmButtonText: 'OK'

            });

            return;

        }


        /* -----------------------------------------------
           CONFIRMATION
        ------------------------------------------------ */

        Swal.fire({

            title: 'Are you sure?',

            text: 'Delete selected SIMs?',

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, delete!',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then((result) => {


            if (!result.isConfirmed) {

                return;

            }


            /* -------------------------------------------
               SEND IDS AS JSON
            -------------------------------------------- */

            $('#deleteIds')
                .val(JSON.stringify(ids));


            /* -------------------------------------------
               SUBMIT FORM
            -------------------------------------------- */

            $('#bulkDeleteForm').submit();

        });

    });


    /* =====================================================
       INDIVIDUAL DELETE
    ===================================================== */

    $(document).on(
        'submit',
        '.delete-sim-form',
        function (e) {

            e.preventDefault();


            let form = this;


            Swal.fire({

                title: 'Are you sure?',

                text: 'This SIM will be deleted.',

                icon: 'warning',

                showCancelButton: true,

                confirmButtonText: 'Yes, delete it!',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then((result) => {


                if (result.isConfirmed) {

                    form.submit();

                }

            });

        }
    );


    /* =====================================================
       BOOTSTRAP TOOLTIPS
    ===================================================== */

    document
        .querySelectorAll('[data-bs-toggle="tooltip"]')
        .forEach(function (element) {

            new bootstrap.Tooltip(element);

        });


});

</script>

@endpush