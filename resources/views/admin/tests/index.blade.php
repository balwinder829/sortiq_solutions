@extends('layouts.app')

@section('content')

<style>
    table.dataTable {
        /*overflow: visible !important;*/
    }

    table.dataTable tbody td {
        /*overflow: visible !important;*/
    }

    .dropdown-menu {
        position: absolute !important;
        z-index: 20000 !important;
    }

    #testTable {
        width: 100% !important;
    }

    #testTable_wrapper {
        width: 100%;
    }

    #testTable_wrapper .dataTables_scroll {
        width: 100%;
    }

    
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="row mb-2">

        <div class="col-md-6">
            <h1 class="page_heading">Online Exams</h1>
        </div>

        <div class="col-md-6">

            <div class="d-flex justify-content-end gap-2">

                <button type="button"
                        id="bulkDeleteBtn"
                        class="btn btn-danger mb-3">
                    Delete Selected
                </button>

                <a href="{{ route('admin.tests.create') }}"
                   class="btn btn-primary mb-3">
                    Add Test
                </a>

            </div>

        </div>

    </div>


    {{-- ================= EXPORT BUTTONS ================= --}}
    <div class="mb-3 d-flex flex-wrap gap-2">

        {{-- OVERALL (ONLINE + OFFLINE) --}}
        <a href="{{ route('admin.tests.export.overall.attempted') }}"
           class="btn btn-outline-primary">
            <i class="fa fa-download"></i> Overall Students
        </a>

        <a href="{{ route('admin.tests.export.overall.finalized') }}"
           class="btn btn-outline-success">
            <i class="fa fa-download"></i> Overall Finalized
        </a>


        {{-- ONLINE ONLY --}}
        <a href="{{ route('admin.tests.export.online.attempted') }}"
           class="btn btn-outline-info">
            <i class="fa fa-download"></i> Online All Students
        </a>

        <a href="{{ route('admin.tests.export.online.finalized') }}"
           class="btn btn-outline-warning">
            <i class="fa fa-download"></i> Online Finalized
        </a>

    </div>


    {{-- ================= SUCCESS ================= --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            {{ session('success') }}

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif


    {{-- ================= ERROR ================= --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif


    {{-- ================= VALIDATION ERRORS ================= --}}
    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <ul class="mb-0">

                @foreach($errors->all() as $err)

                    <li>{{ $err }}</li>

                @endforeach

            </ul>

            <button class="btn-close"
                    data-bs-dismiss="alert"></button>

        </div>

    @endif


    {{-- ================= FILTER FORM ================= --}}
    <form method="GET"
          id="filterForm"
          class="p-3 rounded mb-3"
          style="background:#f1f3f8">

        <div class="row">

            {{-- College --}}
            <div class="col-md-2 mb-2">

                <label>College</label>

                <select name="college_id"
                        class="form-select filterchange select2">

                    <option value="">All</option>

                    @foreach($colleges as $col)

                        <option value="{{ $col->id }}"
                            {{ request('college_id') == $col->id ? 'selected' : '' }}>

                            {{ $col->FullName }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Gender --}}
            <div class="col-md-2">

                <label>Gender</label>

                <select name="gender"
                        class="form-select filterchange">

                    <option value="">All</option>

                    <option value="male"
                        {{ request('gender') == 'male' ? 'selected' : '' }}>
                        Male
                    </option>

                    <option value="female"
                        {{ request('gender') == 'female' ? 'selected' : '' }}>
                        Female
                    </option>

                </select>

            </div>


            {{-- Category --}}
            <div class="col-md-2 mb-2">

                <label>Category</label>

                <select name="test_category_id"
                        class="form-select filterchange">

                    <option value="">All</option>

                    @foreach($categories as $cat)

                        <option value="{{ $cat->id }}"
                            {{ request('test_category_id') == $cat->id ? 'selected' : '' }}>

                            {{ $cat->name }}

                        </option>

                    @endforeach

                </select>

            </div>


            {{-- Status --}}
            <div class="col-md-2 mb-2">

                <label>Status</label>

                <select name="status"
                        class="form-select filterchange">

                    <option value="">All</option>

                    <option value="draft"
                        {{ request('status') == 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>

                    <option value="published"
                        {{ request('status') == 'published' ? 'selected' : '' }}>
                        Published
                    </option>

                    <option value="unpublished"
                        {{ request('status') == 'unpublished' ? 'selected' : '' }}>
                        Unpublished
                    </option>

                </select>

            </div>


            {{-- Active --}}
            <div class="col-md-2 mb-2">

                <label>Active</label>

                <select name="is_active"
                        class="form-select filterchange">

                    <option value="">All</option>

                    <option value="1"
                        {{ request('is_active') === '1' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ request('is_active') === '0' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>


            {{-- From Date --}}
             


            {{-- To Date --}}
             


            {{-- Reset --}}
            <div class="col-md-2 mb-2 d-flex align-items-end">

                <a href="{{ route('admin.tests.index') }}"
                   class="btn btn-secondary w-100">

                    Reset

                </a>

            </div>

        </div>

    </form>


    {{-- ================= TEST TABLE ================= --}}
    <div class="table-container">

        <table class="table table-bordered table-striped"
               id="testTable"
               style="width:100%;">

            <thead>

                <tr>

                    {{-- Select All --}}
                    <th>
                        <input type="checkbox"
                               id="checkAll">
                    </th>

                    <th>#</th>

                    <th>Title</th>

                    <th>Category</th>

                    <th>College</th>

                    <th>Status</th>

                    <th>Active</th>

                    <th>Total Questions</th>

                    <th>Total Students</th>

                    <th>Selected</th>

                    <th>Marks</th>

                    <th>Results</th>

                    <th>Student Link</th>

                    <th>Action</th>

                </tr>

            </thead>


            <tbody>

                @foreach($tests as $test)

                    <tr>

                        {{-- Checkbox --}}
                        <td>

                            <input type="checkbox"
                                   class="test-checkbox"
                                   value="{{ $test->id }}">

                        </td>


                        {{-- Number --}}
                        <td>
                            {{ $loop->iteration }}
                        </td>


                        {{-- Title --}}
                        <td>
                            {{ $test->title }}
                        </td>


                        {{-- Category --}}
                        <td>
                            {{ $test->category?->name ?? '-' }}
                        </td>


                        {{-- College --}}
                        <td>

                            @if($test->links && $test->links->count())

                                @foreach($test->links as $link)

                                    <span class="badge bg-secondary">

                                        {{ $link->college->full_name ?? '-' }}

                                    </span>

                                @endforeach

                            @else

                                <span class="badge bg-secondary">

                                    {{ $test->college_full_name ?? '-' }}

                                </span>

                            @endif

                        </td>


                        {{-- Status --}}
                        <td>

                            @if($test->status == 'published')

                                <span class="badge bg-success">
                                    Published
                                </span>

                            @elseif($test->status == 'draft')

                                <span class="badge bg-secondary">
                                    Draft
                                </span>

                            @else

                                <span class="badge bg-danger">
                                    Unpublished
                                </span>

                            @endif

                        </td>


                        {{-- Active --}}
                        <td>

                            @if($test->is_active)

                                <span class="badge bg-success">
                                    Active
                                </span>

                            @else

                                <span class="badge bg-dark">
                                    Inactive
                                </span>

                            @endif

                        </td>


                        {{-- Questions --}}
                        <td>

                            @if($test->questions_count > 0)

                                <a href="{{ route('admin.tests.show', $test->id) }}"
                                   class="badge bg-success text-decoration-none">

                                    {{ $test->questions_count }} Questions

                                </a>

                            @else

                                <span class="badge bg-danger">
                                    No Questions
                                </span>

                            @endif

                        </td>


                        {{-- Total Students --}}
                        <td>

                            <span class="badge bg-info">

                                {{ $test->total_registered ?? 0 }}

                            </span>

                        </td>


                        {{-- Selected --}}
                        <td>

                            <span class="badge bg-success">

                                {{ $test->selected_count ?? 0 }}

                            </span>

                        </td>


                        {{-- Marks --}}
                        <td>

                            @if(($test->selected_count ?? 0) > 0)

                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#marksModal"
                                        data-test-id="{{ $test->id }}">

                                    View

                                </button>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- Results --}}
                        <td class="text-center">

                            <a href="{{ route('admin.tests.results', $test->id) }}"
                               class="btn btn-sm btn-info text-nowrap">

                                View Results

                            </a>

                        </td>


                        {{-- Student Link --}}
                        <td class="text-center">

                            <a href="{{ route('admin.tests.links', $test->id) }}"
                               class="btn btn-sm btn-outline-primary">

                                View Links

                            </a>

                        </td>


                        {{-- Actions --}}
                        <td class="text-center">

                            <div class="d-flex justify-content-center gap-2">


                                {{-- Download Dropdown --}}
                                <div class="dropdown">

                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown">

                                        <i class="fa fa-download me-1"></i>
                                        Download

                                    </button>

                                    <ul class="dropdown-menu">

                                        <li>

                                            <a class="dropdown-item"
                                               href="{{ route('admin.tests.export.all', $test->id) }}">

                                                All Students

                                            </a>

                                        </li>

                                        <li>

                                            <a class="dropdown-item"
                                               href="{{ route('admin.tests.export.finalized', $test->id) }}">

                                                Selected Students

                                            </a>

                                        </li>

                                        <li>

                                            <a class="dropdown-item"
                                               href="{{ route('admin.online.tests.download.mcq.paper', $test->id) }}">

                                                Question Paper

                                            </a>

                                        </li>

                                    </ul>

                                </div>


                                {{-- View --}}
                                <a href="{{ route('admin.tests.show', $test->id) }}"
                                   class="btn btn-sm btn-outline-info"
                                   title="View Questions">

                                    <i class="fa fa-eye"></i>

                                </a>


                                {{-- Add Question --}}
                                <a href="{{ route('admin.questions.create', $test->id) }}"
                                   class="btn btn-sm btn-outline-success"
                                   title="Add Question">

                                    <i class="fa fa-plus"></i>

                                </a>


                                {{-- Edit --}}
                                <a href="{{ route('admin.tests.edit', $test->id) }}"
                                   class="btn btn-sm btn-outline-secondary"
                                   title="Edit">

                                    <i class="fa fa-edit"></i>

                                </a>


                                {{-- Delete --}}
                                <form action="{{ route('admin.tests.destroy', $test->id) }}"
                                      method="POST"
                                      class="d-inline test-delete-form">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete">

                                        <i class="fa fa-trash"></i>

                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

</div>


{{-- ================= MARKS MODAL ================= --}}
<div class="modal fade"
     id="marksModal"
     tabindex="-1">

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Selected Students
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div id="marksContent"
                     class="text-center">

                    <div class="spinner-border"></div>

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
    | Selected Tests
    |--------------------------------------------------------------------------
    */

    let selectedTests = {};


    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    var table = $('#testTable').DataTable({

        pageLength: 25,

        lengthMenu: [10, 25, 50, 100],

        autoWidth: true,

        scrollX: true,

        columnDefs: [

            {
                targets: 0,
                orderable: false,
                searchable: false
            },

            {
                targets: 13,
                orderable: false,
                searchable: false
            }

        ],

        drawCallback: function () {

            /*
            |--------------------------------------------------------------------------
            | Restore selections after pagination/search
            |--------------------------------------------------------------------------
            */

            $('#testTable tbody .test-checkbox').each(function () {

                let id = $(this).val();

                $(this).prop(
                    'checked',
                    !!selectedTests[id]
                );

            });

            updateCheckAll();

        }

    });


    /*
    |--------------------------------------------------------------------------
    | Individual Checkbox
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'change',
        '#testTable tbody .test-checkbox',
        function () {

            let id = $(this).val();

            if ($(this).is(':checked')) {

                selectedTests[id] = true;

            } else {

                delete selectedTests[id];

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

        $('#testTable tbody .test-checkbox').each(function () {

            let id = $(this).val();

            $(this).prop('checked', checked);

            if (checked) {

                selectedTests[id] = true;

            } else {

                delete selectedTests[id];

            }

        });

        updateCheckAll();

    });


    /*
    |--------------------------------------------------------------------------
    | Update Select All
    |--------------------------------------------------------------------------
    */

    function updateCheckAll() {

        let checkboxes =
            $('#testTable tbody .test-checkbox');

        let total =
            checkboxes.length;

        let checked =
            checkboxes.filter(':checked').length;

        $('input[type="checkbox"][id="checkAll"]')
            .prop(
                'checked',
                total > 0 && checked === total
            )
            .prop(
                'indeterminate',
                checked > 0 && checked < total
            );

    }


    /*
    |--------------------------------------------------------------------------
    | Bulk Delete
    |--------------------------------------------------------------------------
    */

    $('#bulkDeleteBtn').on('click', function () {

        let ids = Object.keys(selectedTests);

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

            title: 'Delete Selected Tests?',

            text: 'Tests having student records will not be deleted.',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel',

            reverseButtons: false

        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }


            $.ajax({

                url: "{{ route('admin.tests.bulkDelete') }}",

                type: 'POST',

                data: {

                    _token: "{{ csrf_token() }}",

                    ids: ids.join(',')

                },

                success: function (response) {

                    selectedTests = {};

                    Swal.fire({

                        icon: 'success',

                        title: 'Success',

                        text: response.message,

                        timer: 1800,

                        showConfirmButton: false

                    }).then(function () {

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

                        title: 'Cannot Delete',

                        text: message

                    });

                }

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | Single Delete SweetAlert
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'submit',
        '.test-delete-form',
        function (e) {

            e.preventDefault();

            e.stopImmediatePropagation();

            const form = this;

            Swal.fire({

                icon: 'warning',

                title: 'Delete Test?',

                text: 'Are you sure you want to delete this test?',

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


/*
|--------------------------------------------------------------------------
| Marks Modal
|--------------------------------------------------------------------------
*/

$('#marksModal').on('show.bs.modal', function (e) {

    let testId =
        e.relatedTarget.getAttribute('data-test-id');

    let url =
        "{{ route('admin.tests.selected.students', ':id') }}";

    url =
        url.replace(':id', testId);

    $('#marksContent').load(url);

});


/*
|--------------------------------------------------------------------------
| Copy Test Link
|--------------------------------------------------------------------------
*/

function copyTestLink(url) {

    navigator.clipboard.writeText(url).then(() => {

        Swal.fire({

            icon: 'success',

            title: 'Copied!',

            text: 'Test link copied to clipboard!',

            confirmButtonText: 'OK'

        });

    }).catch(() => {

        Swal.fire({

            icon: 'error',

            title: 'Failed',

            text: 'Failed to copy link',

            confirmButtonText: 'OK'

        });

    });

}


/*
|--------------------------------------------------------------------------
| Regenerate Test Link
|--------------------------------------------------------------------------
*/

function confirmRegenerate(e) {

    e.preventDefault();

    Swal.fire({

        title: 'Regenerate Test Link?',

        text: 'Old link will stop working.',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#d33',

        cancelButtonColor: '#6c757d',

        confirmButtonText: 'Yes, regenerate',

        reverseButtons: false

    }).then((result) => {

        if (result.isConfirmed) {

            e.target.submit();

        }

    });

    return false;

}


/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){

        $('#filterForm').submit();

    });

    $('.filterchangetext').on('input', function(){

        clearTimeout(timer);

        timer = setTimeout(function(){

            $('#filterForm').submit();

        }, 500);

    });

});

</script>

@endpush