@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Blocked Numbers</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end align-items-center gap-2">

                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger">
                    Unblock Selected
                </button>

                <!-- <span id="selectedBlockedCount"
                      class="text-muted"></span> -->

                <a href="{{ route('admin.blocked-numbers.create') }}"
                   class="btn btn-primary">
                    Block New Number
                </a>

            </div>
        </div>
    </div>


    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <table class="table table-bordered" id="pagesTable">

        <thead>
        <tr>

            <th>
                <input type="checkbox"
                       id="checkAll">
            </th>

            <th>#</th>

            <th>Number</th>

            <th>Occurrences</th>

            <th>Blocked At</th>

            <th>Actions</th>

        </tr>
        </thead>


        <tbody>

        @foreach($blockedNumbers as $blocked)

            <tr>

                <td>
                    <input type="checkbox"
                           class="blocked-checkbox"
                           value="{{ $blocked->id }}">
                </td>

                <td></td>

                <td>
                    {{ $blocked->number }}
                </td>

                <td>
                    {{ $blocked->occurrence_count }}
                </td>

                <td>
                    {{ \Carbon\Carbon::parse($blocked->blocked_at)->format('d M Y h:i A') }}
                </td>

                <td>

                    <a href="{{ route('admin.blocked-numbers.show', $blocked) }}"
                       class="btn btn-sm btn-info">
                        View
                    </a>

                    <a href="{{ route('admin.blocked-numbers.edit', $blocked) }}"
                       class="btn btn-sm btn-warning">
                        Edit
                    </a>

                    <form method="POST"
                          action="{{ route('admin.blocked-numbers.destroy', $blocked) }}"
                          class="d-inline">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="btn btn-sm btn-danger"
                                data-swal-delete
                                data-swal-confirm="Unblock this number?">
                            Unblock
                        </button>

                    </form>

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>

</div>


<script>

$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Selected Blocked Numbers
    |--------------------------------------------------------------------------
    |
    | Object is used so selected IDs remain selected when:
    | - changing DataTable pages
    | - searching
    | - sorting
    | - redrawing
    |
    |--------------------------------------------------------------------------
    */

    let selectedBlockedNumbers = {};


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#pagesTable').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

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
    | Update Selected Count
    |--------------------------------------------------------------------------
    */

    function updateSelectedCount() {

        let count = Object.keys(selectedBlockedNumbers).length;

        if (count > 0) {

            $('#selectedBlockedCount').text(
                count + ' selected'
            );

        } else {

            $('#selectedBlockedCount').text('');

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Update Current Page Select All
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        let pageCheckboxes = $('.blocked-checkbox');

        let total = pageCheckboxes.length;

        if (total === 0) {

            $('#checkAll').prop('checked', false);

            return;
        }


        let checked = 0;


        pageCheckboxes.each(function () {

            let id = $(this).val();

            if (selectedBlockedNumbers[id]) {
                checked++;
            }

        });


        $('#checkAll').prop(
            'checked',
            checked === total
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DataTable Draw
    |--------------------------------------------------------------------------
    */

    table.on('draw.dt', function () {

        var PageInfo = table.page.info();


        /*
        |--------------------------------------------------------------------------
        | Serial Number
        |--------------------------------------------------------------------------
        */

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {

            cell.innerHTML =
                PageInfo.start + i + 1;

        });


        /*
        |--------------------------------------------------------------------------
        | Restore Selected Checkboxes
        |--------------------------------------------------------------------------
        */

        $('.blocked-checkbox').each(function () {

            let id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedBlockedNumbers[id]
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Update Select All + Count
        |--------------------------------------------------------------------------
        */

        updateCheckAll();

        updateSelectedCount();

    });


    /*
    |--------------------------------------------------------------------------
    | Individual Checkbox
    |--------------------------------------------------------------------------
    */

    $('#pagesTable tbody').on(
        'change',
        '.blocked-checkbox',
        function () {

            let id = $(this).val();


            if ($(this).is(':checked')) {

                selectedBlockedNumbers[id] = true;

            } else {

                delete selectedBlockedNumbers[id];

            }


            updateCheckAll();

            updateSelectedCount();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Select All - CURRENT PAGE ONLY
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on('change', function () {

        let checked = $(this).is(':checked');


        $('.blocked-checkbox').each(function () {

            let id = $(this).val();


            $(this).prop(
                'checked',
                checked
            );


            if (checked) {

                selectedBlockedNumbers[id] = true;

            } else {

                delete selectedBlockedNumbers[id];

            }

        });


        updateSelectedCount();

    });


    /*
    |--------------------------------------------------------------------------
    | Bulk Unblock
    |--------------------------------------------------------------------------
    */

    $('#bulkDeleteBtn').on('click', function () {

        let ids = Object.keys(
            selectedBlockedNumbers
        );


        /*
        |--------------------------------------------------------------------------
        | No Selection
        |--------------------------------------------------------------------------
        */

        if (ids.length === 0) {

            Swal.fire({

                icon: 'warning',

                title: 'No Selection',

                text: 'Please select at least 1 record.'

            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Confirmation
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'warning',

            title: 'Unblock Selected Numbers?',

            text: 'Selected blocked numbers will be unblocked and their records will be restored.',

            showCancelButton: true,

            confirmButtonText: 'Yes, Unblock',

            cancelButtonText: 'Cancel'

        }).then(function (result) {


            if (!result.isConfirmed) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AJAX Bulk Unblock
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: "{{ route('admin.blocked-numbers.bulkDelete') }}",

                type: "POST",

                data: {

                    _token: "{{ csrf_token() }}",

                    ids: ids.join(',')

                },


                /*
                |--------------------------------------------------------------------------
                | Success
                |--------------------------------------------------------------------------
                */

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
                    | Clear Selection
                    |--------------------------------------------------------------------------
                    */

                    selectedBlockedNumbers = {};


                    $('#checkAll').prop(
                        'checked',
                        false
                    );


                    updateSelectedCount();


                    /*
                    |--------------------------------------------------------------------------
                    | Refresh DataTable
                    |--------------------------------------------------------------------------
                    |
                    | This is a normal client-side DataTable,
                    | so draw(false) is used instead of ajax.reload().
                    |
                    |--------------------------------------------------------------------------
                    */

                    // table.draw(false);
                    window.location.reload();

                },


                /*
                |--------------------------------------------------------------------------
                | Error
                |--------------------------------------------------------------------------
                */

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
    | Initial Draw
    |--------------------------------------------------------------------------
    */

    table.draw();

});

</script>

@endsection