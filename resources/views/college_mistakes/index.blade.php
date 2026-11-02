@extends('layouts.app')

@section('content')
<style>
    #mistakes-table {
    width: 100% !important;
}

#mistakes-table_wrapper {
    width: 100%;
}

#mistakes-table_wrapper .dataTables_scroll {
    width: 100%;
}

#mistakes-table_wrapper .dataTables_scrollHeadInner {
    width: 100% !important;
}

#mistakes-table_wrapper .dataTables_scrollHeadInner table {
    width: 100% !important;
}
</style>
<div class="container">

    <div class="row mb-3 align-items-end">

        <div class="col-md-6">
            <h1 class="page_heading">College Mistakes</h1>
        </div>

        <div class="col-md-6 text-end">

            <button type="button"
                    id="bulkDeleteBtn"
                    class="btn btn-danger">
                Delete Selected
            </button>

            <a href="{{ route('college-mistakes.create') }}"
               class="btn btn-primary">
                Add College Mistake
            </a>

        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-3">
            <input type="text"
                   name="college_name"
                   class="form-control"
                   placeholder="College Name">
        </div>

        <div class="col-md-3">
            <input type="text"
                   name="contact_person"
                   class="form-control"
                   placeholder="Contact Person">
        </div>

        <div class="col-md-2">
            <input type="text"
                   name="location"
                   class="form-control"
                   placeholder="Location">
        </div>

        <div class="col-md-2">
            <select name="website_status" class="form-select">
                <option value="">Website</option>
                <option value="with_website">With Website</option>
                <option value="without_website">Without Website</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="date"
                   name="date"
                   class="form-control">
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-md-2">
            <a href="{{ route('college-mistakes.index') }}"
               class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">

        <table class="table table-bordered table-striped"
               id="mistakes-table">

            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>

                    <th>#</th>
                    <th>College Name</th>
                    <th>Contact Person</th>
                    <th>Location</th>
                    <th>Website</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>

            <tbody></tbody>

        </table>

    </div>

</div>

@endsection

@push('scripts')

<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Selected College Mistakes
    |--------------------------------------------------------------------------
    */

    let selectedMistakes = {};


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#mistakes-table').DataTable({

        processing: true,

        serverSide: true,

        pageLength: 50,

        lengthMenu: [5, 10, 25, 50, 100],

        autoWidth: true,

        scrollX: true,

        ajax: {

            url: "{{ route('college-mistakes.data') }}",

            data: function (d) {

                d.college_name =
                    $('input[name=college_name]').val();

                d.contact_person =
                    $('input[name=contact_person]').val();

                d.location =
                    $('input[name=location]').val();

                d.website_status =
                    $('select[name=website_status]').val();

                d.date =
                    $('input[name=date]').val();

            }

        },

        columns: [

            {
                data: 0,
                orderable: false,
                searchable: false
            },

            {
                data: 1
            },

            {
                data: 2
            },

            {
                data: 3
            },

            {
                data: 4
            },

            {
                data: 5
            },

            {
                data: 6
            },

            {
                data: 7
            },

            {
                data: 8,
                orderable: false,
                searchable: false
            }

        ],

        columnDefs: [

            {
                targets: 0,
                orderable: false,
                searchable: false
            }

        ],

        drawCallback: function () {

            /*
            |--------------------------------------------------------------------------
            | Restore selected checkboxes after DataTable redraw
            |--------------------------------------------------------------------------
            */

            $('#mistakes-table tbody .mistake-checkbox').each(function () {

                let id = $(this).val();

                $(this).prop(
                    'checked',
                    !!selectedMistakes[id]
                );

            });

            updateCheckAll();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    $('input[name=college_name], input[name=contact_person], input[name=location], input[name=date]')
        .on('keyup change', function () {

            table.ajax.reload();

        });


    $('select[name=website_status]').on('change', function () {

        table.ajax.reload();

    });


    /*
    |--------------------------------------------------------------------------
    | Individual Checkbox
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#mistakes-table tbody .mistake-checkbox',
        function () {

            let id = $(this).val();

            if ($(this).is(':checked')) {

                selectedMistakes[id] = true;

            } else {

                delete selectedMistakes[id];

            }

            updateCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Select All - Current Page Only
    |--------------------------------------------------------------------------
    */

    $(document).on('change', '#checkAll', function () {

        let checked = $(this).is(':checked');

        $('#mistakes-table tbody .mistake-checkbox').each(function () {

            let id = $(this).val();

            $(this).prop('checked', checked);

            if (checked) {

                selectedMistakes[id] = true;

            } else {

                delete selectedMistakes[id];

            }

        });

        updateCheckAll();

    });


    /*
    |--------------------------------------------------------------------------
    | Update Select All Checkbox
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        let checkboxes =
            $('#mistakes-table tbody .mistake-checkbox');

        let total =
            checkboxes.length;

        let checked =
            checkboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]')
            .prop('checked', total > 0 && checked === total)
            .prop('indeterminate', checked > 0 && checked < total);

    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    $('#bulkDeleteBtn').on('click', function () {

        let ids = Object.keys(selectedMistakes);

        if (ids.length === 0) {

            Swal.fire({

                icon: 'warning',

                title: 'No Record Selected',

                text: 'Please select at least 1 record.'

            });

            return;

        }


        Swal.fire({

            icon: 'warning',

            title: 'Delete Selected Records?',

            text: 'Are you sure you want to delete the selected college mistakes?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('college-mistakes.bulkDelete') }}",

                type: 'POST',

                data: {

                    _token: "{{ csrf_token() }}",

                    ids: ids.join(',')

                },

                success: function (response) {

                    Swal.fire({

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        timer: 1500,

                        showConfirmButton: false

                    }).then(function () {

                        selectedMistakes = {};

                        window.location.reload();

                    });

                },

                error: function (xhr) {

                    let message =
                        'Something went wrong.';

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
    | Single Delete
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.mistake-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();

            const form = this;

            Swal.fire({

                icon: 'warning',

                title: 'Delete College Mistake?',

                text: 'Are you sure you want to delete this college mistake?',

                showCancelButton: true,

                confirmButtonText: 'Yes, Delete',

                cancelButtonText: 'Cancel',

                reverseButtons: false

            }).then(function (result) {

                if (result.isConfirmed) {

                    HTMLFormElement.prototype.submit.call(form);

                }

            });

            return false;

        }
    );

});

</script>

@endpush