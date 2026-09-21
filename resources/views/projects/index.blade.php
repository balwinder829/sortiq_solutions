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
            <h1 class="page_heading">Projects</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end">

                <a href="{{ route('projects.create') }}"
                   class="btn mb-3"
                   style="background-color:#6b51df;color:#fff;">
                    Add Project
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


    {{-- Category Filter --}}
    <div class="row mb-3">

        <div class="col-md-4">

            <form method="GET"
                  action="{{ route('projects.index') }}"
                  id="filterForm">

                <select name="category_id"
                        class="form-control filterchange">

                    <option value="">
                        All Categories
                    </option>

                    @foreach($categories as $category)

                        <option value="{{ $category->id }}"
                            {{ request('category_id') == $category->id ? 'selected' : '' }}>

                            {{ $category->name }}

                        </option>

                    @endforeach

                </select>

            </form>

        </div>

        <div class="col-md-2">

            <a href="{{ route('projects.index') }}"
               class="btn btn-secondary">

                Reset

            </a>

        </div>

    </div>


    {{-- Projects Table --}}
    <table id="projectsTable"
           class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>#</th>
                <th>Project Name</th>
                <th>Category</th>
                <th>Tech Stack</th>
                <th>Guthub Link</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>

            @foreach($projects as $project)

                <tr>

                    <td></td>

                    <td>
                        {{ $project->name }}
                    </td>

                    <td>

                        @if($project->category)

                            <span class="badge bg-success">
                                {{ $project->category->name }}
                            </span>

                        @else

                            -

                        @endif

                    </td>

                    <td>
                        {{ $project->tech_stack }}
                    </td>

                    <td>

                        @if($project->github_link)

                            <a href="{{ $project->github_link }}"
                               target="_blank"
                               rel="noopener noreferrer">

                                {{ $project->github_link }}

                            </a>

                        @else

                            -

                        @endif

                    </td>

                    <td>

                        {{-- Edit --}}
                        <a href="{{ route('projects.edit', $project) }}"
                           class="btn btn-sm"
                           data-bs-toggle="tooltip"
                           title="Edit">

                            <i class="fas fa-edit"></i>

                        </a>


                        {{-- Delete --}}
                        <form action="{{ route('projects.destroy', $project) }}"
                              method="POST"
                              style="display:inline;">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm"
                                    data-swal-confirm="Delete Project?"
                                    data-bs-toggle="tooltip"
                                    title="Delete">

                                <i class="fas fa-trash"></i>

                            </button>

                        </form>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>

@endsection


@push('styles')

{{-- DataTables CSS is already available in your layout --}}

@endpush


@push('scripts')

<script>

$(document).ready(function() {

    /*
     * Category Filter
     */
    $('.filterchange').on('change', function() {

        $('#filterForm').submit();

    });


    /*
     * DataTable
     */
    var table = $('#projectsTable').DataTable({

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
     * Bootstrap Tooltips
     */
    new bootstrap.Tooltip(document.body, {

        selector: '[data-bs-toggle="tooltip"]'

    });

});

</script>

@endpush