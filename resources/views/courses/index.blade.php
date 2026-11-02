@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }

    /* Highlight clickable student count */
    .student-count {
        color: #0d6efd;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        transition: all 0.2s ease-in-out;
    }

    .student-count:hover {
        color: #084298;
        text-decoration: none;
        transform: scale(1.05);
    }

    /* Optional badge look */
    .student-count.badge-style {
        background-color: #e7f1ff;
        padding: 4px 10px;
        border-radius: 12px;
        text-decoration: none;
    }
</style>

<div class="container">

    <div class="row mb-2">

        <div class="col-md-2">
            <h1 class="page_heading">Technologies</h1>
        </div>

        <div class="col-md-10">

            <div class="col-md-3">
                <select id="student_filter" class="form-select">
                    <option value="">All Technologies</option>
                    <option value="asc">Low to High</option>
                    <option value="desc">High to Low</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2">

                {{-- DELETE SELECTED --}}
                <button type="button"
                        id="deleteSelected"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                {{-- ADD TECHNOLOGY --}}
                <a href="{{ route('courses.create') }}"
                   class="btn mb-3"
                   style="background-color: #6b51df; color: #fff;">
                    Add Technology
                </a>

            </div>

        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <div class="table-responsive">

        <table id="course_table"
               class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th>
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>ID</th>
                    <th>Technology Name</th>
                    <th>Students</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody></tbody>

        </table>

    </div>

</div>


{{-- ================= STUDENTS MODAL ================= --}}

<div class="modal fade" id="studentsModal" tabindex="-1">

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Students –
                    <span id="modalCourseName"></span>
                </h5>

                <a href="#"
                   id="downloadExcel"
                   class="btn btn-sm btn-success ms-3">
                    <i class="fa fa-file-excel"></i>
                    Download Excel
                </a>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="table-responsive">

                    <table class="table table-bordered">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>SNO</th>
                                <th>Session ID</th>
                                <th>Session Name</th>
                            </tr>

                        </thead>

                        <tbody id="studentsTableBody">

                            <tr>
                                <td colspan="5"
                                    class="text-center">
                                    Click student count to load data
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


@endsection


@push('scripts')

<script>
$(document).ready(function () {

    /*
    |--------------------------------------------------------------------------
    | Selected Technologies
    |--------------------------------------------------------------------------
    |
    | Keeps selected IDs while moving between DataTables pages,
    | searching and redrawing.
    |
    */

    let selectedTechnologies = {};


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#course_table').DataTable({

        processing: true,

        serverSide: true,

        ajax: {
            url: "{{ route('courses.data') }}",

            data: function (d) {

                d.student_filter = $('#student_filter').val();

            }
        },

        /*
        |--------------------------------------------------------------------------
        | Horizontal scrolling
        |--------------------------------------------------------------------------
        */

        scrollX: true,
        autoWidth: false,

        columns: [

            {
                data: 0,
                orderable: false,
                searchable: false
            },

            {
                data: 1,
                orderable: false,
                searchable: false
            },

            {
                data: 2
            },

            {
                data: 3
            },

            {
                data: 4,
                orderable: false,
                searchable: false
            }

        ],

        pageLength: 10,

        lengthMenu: [10, 15, 20, 25, 50, 100],

        /*
        |--------------------------------------------------------------------------
        | Restore selections after DataTables redraw
        |--------------------------------------------------------------------------
        */

        drawCallback: function () {

            $('#course_table tbody .technology-checkbox').each(function () {

                let id = $(this).val();

                $(this).prop(
                    'checked',
                    !!selectedTechnologies[id]
                );

            });

            updateCheckAll();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Student Filter
    |--------------------------------------------------------------------------
    */

    $('#student_filter').change(function () {

        table.ajax.reload();

    });


    /*
    |--------------------------------------------------------------------------
    | Individual Checkbox
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#course_table tbody .technology-checkbox',
        function () {

            let id = $(this).val();

            if ($(this).is(':checked')) {

                selectedTechnologies[id] = true;

            } else {

                delete selectedTechnologies[id];

            }

            updateCheckAll();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Select All - CURRENT PAGE ONLY
    |--------------------------------------------------------------------------
    */

    $('#checkAll').on('change', function () {

        let checked = $(this).is(':checked');

        $('#course_table tbody .technology-checkbox').each(function () {

            let id = $(this).val();

            $(this).prop('checked', checked);

            if (checked) {

                selectedTechnologies[id] = true;

            } else {

                delete selectedTechnologies[id];

            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Update Select All State
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        let checkboxes =
            $('#course_table tbody .technology-checkbox');

        let total =
            checkboxes.length;

        let checked =
            checkboxes.filter(':checked').length;


        /*
        |--------------------------------------------------------------------------
        | All current page selected
        |--------------------------------------------------------------------------
        */

        $('#checkAll').prop(
            'checked',
            total > 0 && total === checked
        );


        /*
        |--------------------------------------------------------------------------
        | Some but not all current page selected
        |--------------------------------------------------------------------------
        */

        $('#checkAll').prop(
            'indeterminate',
            checked > 0 && checked < total
        );

    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SELECTED
    |--------------------------------------------------------------------------
    */

    $('#deleteSelected').on('click', function () {

        let ids =
            Object.keys(selectedTechnologies);


        /*
        |--------------------------------------------------------------------------
        | No Selection
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
        | Confirmation
        |--------------------------------------------------------------------------
        */

        Swal.fire({

            icon: 'warning',

            title: 'Delete Selected Technologies?',

            text: 'Are you sure you want to delete the selected technologies?',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | AJAX Bulk Delete
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url: "{{ route('courses.bulkDelete') }}",

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

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | Clear selected IDs
                    |--------------------------------------------------------------------------
                    */

                    selectedTechnologies = {};


                    $('#checkAll')
                        .prop('checked', false)
                        .prop('indeterminate', false);


                    /*
                    |--------------------------------------------------------------------------
                    | Reload current DataTable page
                    |--------------------------------------------------------------------------
                    */

                    table.ajax.reload(null, false);

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
    | SINGLE DELETE - SWEETALERT
    |--------------------------------------------------------------------------
    |
    | Delegated because rows are created dynamically by server-side
    | DataTables.
    |
    */

    $(document).on(
        'submit',
        '.technology-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();

            const form = this;


            Swal.fire({

                icon: 'warning',

                title: 'Delete Technology?',

                text: 'Are you sure you want to delete this technology?',

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


{{-- ================= TOOLTIPS ================= --}}

<script>

var tooltipTriggerList =
    [].slice.call(
        document.querySelectorAll(
            '[data-bs-toggle="tooltip"]'
        )
    );

var tooltipList =
    tooltipTriggerList.map(function (tooltipTriggerEl) {

        return new bootstrap.Tooltip(
            tooltipTriggerEl
        );

    });

</script>


{{-- ================= STUDENTS MODAL ================= --}}

<script>

$(document).ready(function () {

    $(document).on(
        'click',
        '.view-students',
        function () {

            let courseId =
                $(this).data('course-id');

            let courseName =
                $(this).data('course-name');


            /*
            |--------------------------------------------------------------------------
            | Modal title
            |--------------------------------------------------------------------------
            */

            $('#modalCourseName')
                .text(courseName);


            /*
            |--------------------------------------------------------------------------
            | Loading
            |--------------------------------------------------------------------------
            */

            $('#studentsTableBody').html(

                '<tr>' +
                    '<td colspan="5" class="text-center">' +
                        'Loading...' +
                    '</td>' +
                '</tr>'

            );


            /*
            |--------------------------------------------------------------------------
            | Excel Download
            |--------------------------------------------------------------------------
            */

            $('#downloadExcel').attr(
                'href',
                `/courses/${courseId}/students/export-excel`
            );


            /*
            |--------------------------------------------------------------------------
            | Load Students
            |--------------------------------------------------------------------------
            */

            $.ajax({

                url:
                    `/courses/${courseId}/students`,

                type: 'GET',

                success: function (students) {

                    let rows = '';


                    if (students.length === 0) {

                        rows =
                            `<tr>
                                <td colspan="5"
                                    class="text-center">
                                    No students found
                                </td>
                            </tr>`;

                    } else {

                        students.forEach(
                            function (student, index) {

                                rows +=
                                    `<tr>

                                        <td>
                                            ${index + 1}
                                        </td>

                                        <td>
                                            ${student.student_name}
                                        </td>

                                        <td>
                                            ${student.sno ?? '-'}
                                        </td>

                                        <td>
                                            ${student.session_id ?? '-'}
                                        </td>

                                        <td>
                                            ${student.session_name ?? '-'}
                                        </td>

                                    </tr>`;

                            }
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Put students into modal
                    |--------------------------------------------------------------------------
                    */

                    $('#studentsTableBody')
                        .html(rows);


                    /*
                    |--------------------------------------------------------------------------
                    | Show modal
                    |--------------------------------------------------------------------------
                    */

                    $('#studentsModal')
                        .modal('show');

                }

            });

        }
    );

});

</script>

@endpush