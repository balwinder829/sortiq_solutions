@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>

<div class="container">

    {{-- Page Header --}}
    <div class="row mb-2">

        <div class="col-md-6">
            <h1 class="page_heading">Project Categories</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end">

                <a href="{{ route('project-categories.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Category
                </a>

            </div>
        </div>

    </div>


    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Error Message --}}
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


    {{-- Status Filter --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-4">

            <form method="GET"
                  action="{{ route('project-categories.index') }}"
                  id="filterForm">

                <select name="status"
                        class="form-control filterchange">

                    <option value="">
                        All Categories
                    </option>

                    <option value="active"
                        {{ request('status') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ request('status') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </form>

        </div>

        <div class="col-md-2">

            <a href="{{ route('project-categories.index') }}"
               class="btn btn-secondary">
                Reset
            </a>

        </div>

    </div>


    {{-- Table --}}
    <table id="projectCategoriesTable"
           class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>#</th>
                <th>Category Name</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Updated Date</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @foreach($categories as $category)

                <tr>

                    <td></td>

                    <td>
                        {{ $category->name }}
                    </td>

                    <td>

                        @if($category->status === 'active')

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>
                        {{ $category->created_at?->format('d M Y') }}
                    </td>

                    <td>
                        {{ $category->updated_at?->format('d M Y') }}
                    </td>

                    <td>

    {{-- Edit --}}
    @can('project-categories.edit')
        <a href="{{ route('project-categories.edit', $category) }}"
           class="btn btn-sm"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="Edit">

            <i class="fas fa-edit"></i>

        </a>
    @endcan


    {{-- Delete --}}
    @can('project-categories.delete')
        <form action="{{ route('project-categories.destroy', $category) }}"
              method="POST"
               class="delete-category-form"
              style="display:inline;">

            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-sm"
                    data-swal-confirm="Do you want to delete this category?"
                    data-bs-toggle="tooltip"
                    data-bs-placement="top"
                    title="Delete">

                <i class="fas fa-trash"></i>

            </button>

        </form>
    @endcan

</td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection


@push('scripts')

<script>

$(document).ready(function() {

    /*
     * Status Filter
     */
    $('.filterchange').on('change', function() {

        $('#filterForm').submit();

    });


    /*
     * DataTable
     */
    var table = $('#projectCategoriesTable').DataTable({

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        columnDefs: [
            {
                targets: 0,
                searchable: false,
                orderable: false
            }
        ]

    });


    /*
     * Dynamic Serial Number
     */
    table.on('draw.dt', function() {

        var PageInfo = table.page.info();

        table.column(0, { page: 'current' })
            .nodes()
            .each(function(cell, i) {

                cell.innerHTML = PageInfo.start + i + 1;

            });

    }).draw();


    /*
     * Bootstrap Tooltip
     */
    new bootstrap.Tooltip(document.body, {

        selector: '[data-bs-toggle="tooltip"]'

    });

});

</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    $('.delete-category-form').on('submit', function(e) {

        e.preventDefault();

        const form = this;

        Swal.fire({
            title: 'Delete Category?',
            text: 'Do you want to delete this category?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then((result) => {

            if (result.isConfirmed) {
                form.submit();
            }

        });

    });

});
</script>
@endpush