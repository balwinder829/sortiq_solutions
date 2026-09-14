@extends('layouts.app')

@section('content')

<style>
table.dataTable td {
    text-transform: capitalize;
}
</style>


<div class="container">

    {{-- ================================================================
         PAGE HEADER
    ================================================================= --}}

    <div class="row mb-2 align-items-end">

        {{-- LEFT: PAGE TITLE --}}
        <div class="col-md-8">

            <h1 class="page_heading">
                Sales Users
            </h1>

        </div>


        {{-- RIGHT: BUTTONS --}}
        <div class="col-md-4">

            <div class="d-flex justify-content-end gap-2">

                {{-- BULK STATUS BUTTON --}}
                <button type="button"
                        id="bulkStatusBtn"
                        class="btn btn-success mb-3">

                    Make Inactive

                </button>


                {{-- EXISTING INACTIVE ALL BUTTON --}}
                <!-- <form action="{{ route('sales_staff.inactiveAll') }}"
                      method="POST"
                      class="d-inline">

                    @csrf

                    <button type="submit"
                            class="btn btn-danger mb-3"
                            data-swal-delete
                            data-swal-confirm="Do you want to inactive all sales users?">

                        Inactive All

                    </button>

                </form> -->


                {{-- ADD SALES USER --}}
                <a href="{{ route('sales_staff.create') }}"
                   style="background-color: #6b51df; color: #fff;"
                   class="btn btn-primary mb-3">

                    Add Sales User

                </a>

            </div>


            {{-- SELECTED COUNT --}}
           <!--  <div class="d-flex justify-content-end">

                <span id="selectedSalesCount"
                      class="ms-2 text-muted">
                </span>

            </div> -->

        </div>

    </div>



    {{-- ================================================================
         SALES STAFF LOGIN URL
    ================================================================= --}}

    <div class="col-md-8 mb-4">

        <p class="mb-1 fw-bold">
            Sales Staff Login URL
        </p>


        <div class="input-group">

            <a href="{{ route('sale_staff.login') }}"
               target="_blank"
               id="loginUrl"
               class="form-control text-primary text-decoration-none">

                {{ route('sale_staff.login') }}

            </a>


            <button class="btn btn-outline-secondary"
                    type="button"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Copy Sales Login URL"
                    onclick="copyLoginUrl()">

                <i class="fa fa-copy"></i>

            </button>

        </div>


        <small id="copyMessage"
               class="text-success d-none">

            Copied to clipboard!

        </small>

    </div>



    {{-- ================================================================
         SUCCESS MESSAGE
    ================================================================= --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif



    {{-- ================================================================
         ERROR MESSAGE
    ================================================================= --}}

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif



    {{-- ================================================================
         ACTIVE / INACTIVE TABS
    ================================================================= --}}

    <ul class="nav nav-tabs mb-3">

        <li class="nav-item">

            <a href="javascript:void(0)"
               class="nav-link sales-status-tab active"
               data-status="active">

                Active

            </a>

        </li>


        <li class="nav-item">

            <a href="javascript:void(0)"
               class="nav-link sales-status-tab"
               data-status="inactive">

                Inactive

            </a>

        </li>

    </ul>



    {{-- ================================================================
         TABLE
    ================================================================= --}}

    <div class="table-responsive">

        <table id="trainers-table"
               class="table table-bordered table-striped">

            <thead>

                <tr>

                    {{-- SELECT ALL --}}
                    <th style="width:40px;">

                        <input type="checkbox"
                               id="checkAll">

                    </th>


                    <th>ID</th>

                    <th>UserName</th>

                    <th>Name</th>

                    <th>Gender</th>

                    <th>Phone</th>

                    <th>Email</th>

                    <th>Status</th>

                    <th>Actions</th>

                </tr>

            </thead>


            {{-- ========================================================
                 IMPORTANT:
                 Server-side DataTable fills tbody through AJAX.
            ========================================================= --}}

            <tbody>

            </tbody>

        </table>

    </div>

</div>



{{-- ================================================================
     BULK STATUS FORM
================================================================= --}}

<form method="POST"
      action="{{ route('sales_staff.bulkStatus') }}"
      id="bulkSalesStatusForm">

    @csrf


    {{-- SELECTED IDS --}}
    <input type="hidden"
           name="ids"
           id="bulkSalesIds">


    {{-- NEW STATUS --}}
    <input type="hidden"
           name="status"
           id="bulkSalesStatus">


    {{-- CURRENT OPEN TAB --}}
    <input type="hidden"
           name="return_status"
           id="bulkSalesReturnStatus"
           value="active">

</form>



@endsection



@push('scripts')

<script>

$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | CURRENT STATUS
    |--------------------------------------------------------------------------
    | First load = active
    |--------------------------------------------------------------------------
    */
    let currentStatus = 'active';


    /*
    |--------------------------------------------------------------------------
    | SELECTED SALES STAFF
    |--------------------------------------------------------------------------
    | Object keeps selected IDs across:
    |
    | - Pagination
    | - Search
    | - Sorting
    | - DataTable redraw
    |--------------------------------------------------------------------------
    */
    let selectedSales = {};


    /*
    |--------------------------------------------------------------------------
    | DATA TABLE
    |--------------------------------------------------------------------------
    */
    let table = $('#trainers-table').DataTable({

        processing: true,

        serverSide: true,

        pageLength: 50,

        lengthMenu: [5, 10, 25, 50, 100],


        /*
        |--------------------------------------------------------------------------
        | AJAX
        |--------------------------------------------------------------------------
        */
        ajax: {

            url: "{{ route('sales_staff.index') }}",

            type: "GET",

            data: function (d) {

                /*
                |--------------------------------------------------------------------------
                | Send current Active / Inactive status to controller
                |--------------------------------------------------------------------------
                */
                d.status = currentStatus;

            }

        },


        /*
        |--------------------------------------------------------------------------
        | COLUMNS
        |--------------------------------------------------------------------------
        */
        columns: [

            /*
            | 0 - Checkbox
            */
            {
                data: 0,
                name: 'checkbox',
                orderable: false,
                searchable: false
            },


            /*
            | 1 - ID
            */
            {
                data: 1,
                name: 'id'
            },


            /*
            | 2 - Username
            */
            {
                data: 2,
                name: 'username'
            },


            /*
            | 3 - Name
            */
            {
                data: 3,
                name: 'name'
            },


            /*
            | 4 - Gender
            */
            {
                data: 4,
                name: 'gender'
            },


            /*
            | 5 - Phone
            */
            {
                data: 5,
                name: 'phone'
            },


            /*
            | 6 - Email
            */
            {
                data: 6,
                name: 'email'
            },


            /*
            | 7 - Status
            */
            {
                data: 7,
                name: 'status'
            },


            /*
            | 8 - Actions
            */
            {
                data: 8,
                name: 'actions',
                orderable: false,
                searchable: false
            }

        ]

    });



    /*
    |--------------------------------------------------------------------------
    | STATUS TAB
    |--------------------------------------------------------------------------
    */
    $('.sales-status-tab').on('click', function () {


        /*
        | Remove active class
        */
        $('.sales-status-tab').removeClass('active');


        /*
        | Add active class
        */
        $(this).addClass('active');


        /*
        | Get selected status
        */
        currentStatus = $(this).data('status');


        /*
        |--------------------------------------------------------------------------
        | Clear previous tab selections
        |--------------------------------------------------------------------------
        */
        selectedSales = {};

        $('#checkAll').prop('checked', false);

        updateBulkButtons();


        /*
        |--------------------------------------------------------------------------
        | Update hidden return status
        |--------------------------------------------------------------------------
        */
        $('#bulkSalesReturnStatus').val(
            currentStatus
        );


        /*
        |--------------------------------------------------------------------------
        | Reload DataTable from server
        |--------------------------------------------------------------------------
        */
        table.ajax.reload(function () {

            updateCheckAll();

            updateBulkButtons();

        }, true);


        /*
        |--------------------------------------------------------------------------
        | Update Bulk Button
        |--------------------------------------------------------------------------
        */
        updateBulkStatusButton();

    });



    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL CHECKBOX
    |--------------------------------------------------------------------------
    */
    $('#trainers-table tbody').on(
        'change',
        '.sales-checkbox',
        function () {


            let id = $(this).val();


            if ($(this).is(':checked')) {

                selectedSales[id] = true;

            } else {

                delete selectedSales[id];

            }


            updateBulkButtons();

            updateCheckAll();

        }

    );



    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - CURRENT PAGE ONLY
    |--------------------------------------------------------------------------
    */
    $('#checkAll').on('change', function () {


        let checked = $(this).is(':checked');


        table.rows({
            page: 'current'
        }).nodes().to$().find('.sales-checkbox').each(function () {


            let id = $(this).val();


            $(this).prop(
                'checked',
                checked
            );


            if (checked) {

                selectedSales[id] = true;

            } else {

                delete selectedSales[id];

            }

        });


        updateBulkButtons();

    });



    /*
    |--------------------------------------------------------------------------
    | DRAW
    |--------------------------------------------------------------------------
    | Restore selected checkboxes after:
    |
    | - Pagination
    | - Search
    | - Sorting
    | - AJAX reload
    |--------------------------------------------------------------------------
    */
    table.on('draw', function () {


        table.rows({
            page: 'current'
        }).nodes().to$().find('.sales-checkbox').each(function () {


            let id = $(this).val();


            $(this).prop(
                'checked',
                !!selectedSales[id]
            );

        });


        updateCheckAll();

        updateBulkButtons();

    });



    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECT ALL
    |--------------------------------------------------------------------------
    */
    function updateCheckAll() {


        let currentPageCheckboxes = table.rows({
            page: 'current'
        }).nodes().to$().find('.sales-checkbox');


        let total =
            currentPageCheckboxes.length;


        let checked =
            currentPageCheckboxes.filter(':checked').length;


        $('#checkAll').prop(
            'checked',
            total > 0 && total === checked
        );

    }



    /*
    |--------------------------------------------------------------------------
    | UPDATE SELECTED COUNT
    |--------------------------------------------------------------------------
    */
    function updateBulkButtons() {


        let count =
            Object.keys(selectedSales).length;


        if (count > 0) {

            $('#selectedSalesCount').text(
                count + ' selected'
            );

        } else {

            $('#selectedSalesCount').text('');

        }

    }



    /*
    |--------------------------------------------------------------------------
    | DYNAMIC BULK STATUS BUTTON
    |--------------------------------------------------------------------------
    */
    function updateBulkStatusButton() {


        if (currentStatus === 'active') {


            $('#bulkStatusBtn')
                .html('Make Inactive')
                .removeClass('btn-warning')
                .addClass('btn-success');


        } else {


            $('#bulkStatusBtn')
                .html('Make Active')
                .removeClass('btn-warning')
                .addClass('btn-success');

        }

    }



    /*
    |--------------------------------------------------------------------------
    | BULK STATUS
    |--------------------------------------------------------------------------
    */
    function bulkStatus(status) {


        let ids =
            Object.keys(selectedSales);


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
        let actionText =
            status === 'active'
                ? 'activate'
                : 'deactivate';



        Swal.fire({

            title:
                status === 'active'
                    ? 'Activate Sales Staff?'
                    : 'Deactivate Sales Staff?',


            text:
                'Are you sure you want to ' +
                actionText +
                ' the selected sales staff?',


            icon: 'warning',


            showCancelButton: true,


            confirmButtonText:
                status === 'active'
                    ? 'Yes, Activate'
                    : 'Yes, Deactivate',


            cancelButtonText: 'Cancel'

        }).then((result) => {


            if (result.isConfirmed) {


                /*
                |--------------------------------------------------------------------------
                | SELECTED IDS
                |--------------------------------------------------------------------------
                */
                $('#bulkSalesIds').val(
                    ids.join(',')
                );


                /*
                |--------------------------------------------------------------------------
                | NEW STATUS
                |--------------------------------------------------------------------------
                */
                $('#bulkSalesStatus').val(
                    status
                );


                /*
                |--------------------------------------------------------------------------
                | CURRENT TAB
                |--------------------------------------------------------------------------
                */
                $('#bulkSalesReturnStatus').val(
                    currentStatus
                );


                /*
                |--------------------------------------------------------------------------
                | FULL PAGE RELOAD
                |--------------------------------------------------------------------------
                | Form submits normally.
                | Controller updates DB and redirects back
                | to the same tab.
                |--------------------------------------------------------------------------
                */
                // $('#bulkSalesStatusForm').submit();
                $.ajax({

                    url: $('#bulkSalesStatusForm').attr('action'),

                    type: 'POST',

                    data: $('#bulkSalesStatusForm').serialize(),

                    success: function (response) {

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        /*
                        |--------------------------------------------------------------------------
                        | Clear selected records
                        |--------------------------------------------------------------------------
                        */
                        selectedSales = {};

                        $('#checkAll').prop('checked', false);

                        updateBulkButtons();

                        /*
                        |--------------------------------------------------------------------------
                        | Reload ONLY DataTable
                        |--------------------------------------------------------------------------
                        | No complete page refresh.
                        |--------------------------------------------------------------------------
                        */
                        table.ajax.reload(null, false);

                    },

                    error: function (xhr) {

                        let message = 'Something went wrong.';

                        if (
                            xhr.responseJSON &&
                            xhr.responseJSON.message
                        ) {
                            message = xhr.responseJSON.message;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message
                        });

                    }

                });

            }

        });

    }



    /*
    |--------------------------------------------------------------------------
    | SINGLE DYNAMIC BULK BUTTON
    |--------------------------------------------------------------------------
    */
    $('#bulkStatusBtn').on('click', function () {


        let newStatus =
            currentStatus === 'active'
                ? 'inactive'
                : 'active';


        bulkStatus(newStatus);

    });



    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */
    updateBulkStatusButton();

    updateBulkButtons();


    $(document).on('submit', 'form.sales-action-form', function (e) {

    e.preventDefault();
    e.stopImmediatePropagation();

    const form = this;

    Swal.fire({
        icon: 'warning',
        title: 'Delete Sales Staff?',
        text: 'Do you want to delete this?',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel',
        reverseButtons: false
    }).then(function (result) {

        if (result.isConfirmed) {

            // Remove our interception marker
            $(form).data('confirmed', true);

            // Native submit bypasses submit event handlers
            HTMLFormElement.prototype.submit.call(form);
        }

    });

    return false;
});
});



/*
|--------------------------------------------------------------------------
| COPY LOGIN URL
|--------------------------------------------------------------------------
*/
function copyLoginUrl() {


    const url =
        document
            .getElementById('loginUrl')
            .textContent
            .trim();


    navigator.clipboard.writeText(url).then(function () {


        const msg =
            document.getElementById('copyMessage');


        msg.classList.remove('d-none');


        setTimeout(() => {

            msg.classList.add('d-none');

        }, 2000);

    });

}



</script>

@endpush