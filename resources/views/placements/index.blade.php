@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
        vertical-align: middle;
    }

    #placementTable th.actions-column,
#placementTable td.actions-column {
    width: 150px !important;
    min-width: 150px !important;
    max-width: 150px !important;
    white-space: nowrap !important;
}
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2">

        <div class="col-md-6">
            <h1 class="page_heading">Student's Placements</h1>
        </div>

        <div class="col-md-6">

            <div class="d-flex justify-content-end gap-2">

                {{-- BULK DELETE --}}
                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                {{-- ADD PLACEMENT --}}
                <a href="{{ route('placements.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Placement
                </a>

            </div>

        </div>

    </div>


    {{-- ================= FRONTEND URL ================= --}}
    <div class="row mb-3">

        <div class="col-md-8">

            <label class="form-label fw-bold">
                Placed Students Page URL
            </label>

            <div class="input-group">

                <input
                    type="text"
                    id="placementFrontendUrl"
                    class="form-control"
                    style="color: blue;"
                    value="{{ route('front_placements.index') }}"
                    readonly
                >

                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    id="copyPlacementUrl"
                    title="Copy URL"
                >
                    <i class="fa fa-copy"></i>
                </button>

            </div>

            <small
                id="placementCopyMessage"
                class="text-success d-none"
            >
                Link copied successfully!
            </small>

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


    {{-- ================= FILTERS ================= --}}
    <form method="GET"
          id="filterForm"
          class="mb-3">

        <div class="row g-2">

            {{-- COLLEGE --}}
            <div class="col-md-2">

                <select name="college_id"
                        class="form-select filterchange select2">

                    <option value="">
                        All Colleges
                    </option>

                    @foreach($colleges as $college)

                        <option value="{{ $college->id }}">
                            {{ $college->full_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STATE --}}
            <div class="col-md-2">

                <select name="state_id"
                        class="form-select filterchange">

                    <option value="">
                        All States
                    </option>

                    @foreach($states as $state)

                        <option value="{{ $state->id }}">
                            {{ $state->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- LOCATION --}}
            <div class="col-md-2">

                <input type="text"
                       name="location"
                       class="form-control filterchangetext"
                       placeholder="Location">

            </div>


            {{-- TECHNOLOGY --}}
            <div class="col-md-2">

                <select name="tech"
                        class="form-select filterchange">

                    <option value="">
                        All Technology
                    </option>

                    @foreach($courses as $course)

                        <option value="{{ $course->id }}">
                            {{ $course->course_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- SESSION --}}
            <div class="col-md-2">

                <select name="session_id"
                        class="form-select filterchange">

                    <option value="">
                        All Sessions
                    </option>

                    @foreach($sessions as $session)

                        <option value="{{ $session->id }}">
                            {{ $session->session_name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- STUDENT --}}
            <div class="col-md-2">

                <input type="text"
                       name="student_name"
                       class="form-control filterchangetext"
                       placeholder="Student">

            </div>


            {{-- COMPANY --}}
            <div class="col-md-2">

                <select name="company"
                        class="form-select filterchange">

                    <option value="">
                        All Companies
                    </option>

                    @foreach($companies as $company)

                        <option value="{{ $company->id }}">
                            {{ $company->name }}
                        </option>

                    @endforeach

                </select>

            </div>


            {{-- MEDIA --}}
            <div class="col-md-2">

                <select name="media"
                        class="form-select filterchange">

                    <option value="">
                        All Media
                    </option>

                    <option value="with">
                        With Media
                    </option>

                    <option value="without">
                        No Media
                    </option>

                </select>

            </div>


            {{-- MISSING DATA --}}
            <div class="col-md-2">

                <select name="missing"
                        class="form-select filterchange">

                    <option value="">
                        All Data
                    </option>

                    <option value="yes">
                        Missing Info
                    </option>

                </select>

            </div>


            {{-- RESET --}}
            <div class="col-md-2 d-flex gap-2">

                <button type="button"
                        id="resetFilters"
                        class="btn btn-secondary w-100">

                    Reset

                </button>

            </div>

        </div>

    </form>


    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">

        <table class="table table-bordered table-striped"
               id="placementTable">

            <thead>

                <tr>

                    {{-- SELECT ALL --}}
                    <th style="width:40px;">

                        <input type="checkbox"
                               id="checkAll">

                    </th>

                    <th>#</th>

                    <th>Student</th>

                    <th>College</th>

                    <th>State</th>

                    <th>Location</th>

                    <th>Tech</th>

                    <th>Session</th>

                    <th>Media</th>

                    <th class="text-center actions-column" width="150">
                        Actions
                    </th>

                </tr>

            </thead>

        </table>

    </div>

</div>


{{-- ================= IMAGE MODAL ================= --}}
<div class="modal fade"
     id="imageModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-body text-center">

                <img id="modalMainImage"
                     style="width:100%;max-height:400px;object-fit:contain;">

                <div id="modalThumbnails"
                     class="mt-3 d-flex flex-wrap gap-2 justify-content-center">
                </div>

            </div>

        </div>

    </div>

</div>

@endsection


@push('scripts')

<script>

let selectedPlacements = {};

let table;


$(document).ready(function () {


    /*
    |--------------------------------------------------------------------------
    | DATATABLE
    |--------------------------------------------------------------------------
    */

    table = $('#placementTable').DataTable({

        processing: true,

        serverSide: true,

        scrollX: true,
        autoWidth: false,

        ajax: {

            url: "{{ route('placements.index') }}",

            data: function (d) {

                d.college_id =
                    $('[name=college_id]').val();

                d.state_id =
                    $('[name=state_id]').val();

                d.tech =
                    $('[name=tech]').val();

                d.session_id =
                    $('[name=session_id]')
                        .find(":selected")
                        .val();

                d.location =
                    $('[name=location]').val();

                d.student_name =
                    $('[name=student_name]').val();

                d.company =
                    $('[name=company]').val();

                d.media =
                    $('[name=media]').val();

                d.missing =
                    $('[name=missing]').val();

            }

        },

        columns: [

            {
                data: 'checkbox',
                orderable: false,
                searchable: false
            },

            {
                data: 'id'
            },

            {
                data: 'student'
            },

            {
                data: 'college'
            },

            {
                data: 'state'
            },

            {
                data: 'location'
            },

            {
                data: 'tech'
            },

            {
                data: 'session'
            },

            {
                data: 'media',
                orderable: false,
                searchable: false
            },

            {
                data: 'actions',
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

        ]

    });


    /*
    |--------------------------------------------------------------------------
    | DRAW
    |--------------------------------------------------------------------------
    */

    table.on('draw.dt', function () {

        $('.placement-checkbox').each(function () {

            const id = $(this).val();

            $(this).prop(
                'checked',
                !!selectedPlacements[id]
            );

        });

        updateCheckAll();

    });


    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL CHECKBOX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '.placement-checkbox',
        function () {

            const id = $(this).val();


            if ($(this).is(':checked')) {

                selectedPlacements[id] = true;

            } else {

                delete selectedPlacements[id];

            }


            updateCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT ALL - CURRENT PAGE
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#checkAll',
        function () {

            const checked =
                $(this).is(':checked');


            table
                .rows({ page: 'current' })
                .nodes()
                .to$()
                .find('.placement-checkbox')
                .each(function () {

                    const id = $(this).val();


                    $(this).prop(
                        'checked',
                        checked
                    );


                    if (checked) {

                        selectedPlacements[id] = true;

                    } else {

                        delete selectedPlacements[id];

                    }

                });

        }
    );


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
                .find('.placement-checkbox');


        const total =
            currentPageCheckboxes.length;


        const checked =
            currentPageCheckboxes
                .filter(':checked')
                .length;


        $('#checkAll').prop(
            'checked',
            total > 0 && total === checked
        );

    }


    /*
    |--------------------------------------------------------------------------
    | BULK DELETE
    |--------------------------------------------------------------------------
    */

    $('#bulkDeleteBtn').on('click', function () {

        const ids =
            Object.keys(selectedPlacements);


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

            title: 'Delete Selected Placements?',

            text: 'Are you sure you want to delete the selected placements?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('placements.bulkDelete') }}",

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

                    selectedPlacements = {};

                    $('#checkAll')
                        .prop('checked', false);


                    Swal.fire({

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        timer: 1500,

                        showConfirmButton: false

                    }).then(function () {

                        table.ajax.reload(null, false);

                        $('#bulkDeleteBtn')
                            .prop('disabled', false)
                            .text('Delete Selected');

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
    | SINGLE DELETE
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.placement-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();


            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete Placement?',

                text: 'Are you sure you want to delete this placement?',

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
    | FILTERS
    |--------------------------------------------------------------------------
    */

    let timer;


    $('.filterchange').on('change', function () {

        table.ajax.reload();

    });


    $('.filterchangetext').on('input', function () {

        clearTimeout(timer);


        timer = setTimeout(function () {

            table.ajax.reload();

        }, 500);

    });


    /*
    |--------------------------------------------------------------------------
    | RESET
    |--------------------------------------------------------------------------
    */

    $('#resetFilters').on('click', function () {

        $('#filterForm')
            .find('input, select')
            .val('');


        $('#filterForm select')
            .prop('selectedIndex', 0);


        table.ajax.reload();

    });


    /*
    |--------------------------------------------------------------------------
    | COPY FRONTEND URL
    |--------------------------------------------------------------------------
    */

    $('#copyPlacementUrl').click(function () {

        let url =
            $('#placementFrontendUrl').val();


        navigator.clipboard
            .writeText(url)
            .then(function () {

                $('#placementCopyMessage')
                    .removeClass('d-none')
                    .text('Link copied successfully!');


                setTimeout(function () {

                    $('#placementCopyMessage')
                        .addClass('d-none');

                }, 2000);

            })
            .catch(function () {

                let input =
                    document.getElementById(
                        'placementFrontendUrl'
                    );


                input.select();

                input.setSelectionRange(
                    0,
                    99999
                );


                document.execCommand('copy');


                $('#placementCopyMessage')
                    .removeClass('d-none')
                    .text('Link copied successfully!');


                setTimeout(function () {

                    $('#placementCopyMessage')
                        .addClass('d-none');

                }, 2000);

            });

    });


    /*
    |--------------------------------------------------------------------------
    | IMAGE POPUP
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.placement-img',
        function () {

            let images =
                $(this).data('images') || [];


            let baseUrl =
                "{{ asset('') }}";


            if (images.length === 0) {
                return;
            }


            $('#modalMainImage')
                .attr(
                    'src',
                    baseUrl + images[0]
                );


            let thumbs = '';


            images.forEach(function (img) {

                thumbs += `
                    <img src="${baseUrl + img}"
                         class="thumb-img"
                         style="width:70px;height:70px;object-fit:cover;cursor:pointer;border-radius:6px;">
                `;

            });


            $('#modalThumbnails')
                .html(thumbs);


            $('#imageModal').modal('show');

        }
    );


    /*
    |--------------------------------------------------------------------------
    | THUMBNAIL CLICK
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '.thumb-img',
        function () {

            $('#modalMainImage')
                .attr(
                    'src',
                    $(this).attr('src')
                );

        }
    );


});

</script>

@endpush