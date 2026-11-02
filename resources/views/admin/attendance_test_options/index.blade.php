@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <h1 class="page_heading mb-0">Test Link Settings</h1>
        <!-- <a href="{{ route('admin.attendance-test-options.create') }}"
           class="btn text-white"
           style="background-color:#6b51df;">
            Add Link
        </a> -->
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @php
        $codingOptions = $options->where('test_type', 'technical');
        $nonCodingOptions = $options->where('test_type', 'non_technical');
    @endphp

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="testOptionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active"
                            id="assign-noncoding-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#assign-noncoding-content"
                            type="button"
                            role="tab"
                            aria-controls="assign-noncoding-content"
                            aria-selected="true">
                        Non-Coding
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="coding-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#coding-content"
                            type="button"
                            role="tab"
                            aria-controls="coding-content"
                            aria-selected="false">
                        Coding
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link"
                            id="noncoding-tab"
                            data-bs-toggle="tab"
                            data-bs-target="#noncoding-content"
                            type="button"
                            role="tab"
                            aria-controls="noncoding-content"
                            aria-selected="false">
                        Categories
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                {{-- CODING LIST --}}
                <div class="tab-pane fade" id="coding-content" role="tabpanel">
                    <div class="d-flex justify-content-end mb-3">
                        <a href="{{ route('admin.attendance-test-options.create') }}"
                           class="btn btn-sm text-white"
                           style="background-color:#6b51df;">
                            Add Coding Test
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle w-100" id="codingTable">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Attendance Form</th>
                                    <th>Online Test</th>
                                    <th>Status</th>
                                    <th style="width:110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($codingOptions as $option)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $option->attendanceTest->title ?? 'Attendance Form #' . $option->external_attendance_test_id }}</td>
                                        <td>{{ $option->test->title ?? 'Test #' . $option->test_id }}</td>
                                        <td>
                                            <span class="badge {{ $option->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $option->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.attendance-test-options.edit', $option) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.attendance-test-options.destroy', $option) }}"
                                                      method="POST"
                                                      data-swal-confirm="Delete this Coding test option?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
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

                {{-- NON-CODING LIST --}}
                <div class="tab-pane fade" id="noncoding-content" role="tabpanel">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="mb-0">Non-Coding Categories</h5>
                        <a href="#addCategoryForm" class="btn btn-sm btn-outline-primary">
                            Add Category
                        </a>
                    </div>

                    <form id="addCategoryForm"
                          method="POST"
                          action="{{ route('admin.attendance-test-options.categories.store') }}"
                          class="row g-2 align-items-end border rounded p-3 mb-4">
                        @csrf
                        <div class="col-md-4">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input type="text" name="name" id="category_name"
                                   class="form-control" maxlength="100"
                                   placeholder="e.g. HR, Finance" required>
                        </div>
                        <div class="col-md-5">
                            <label for="category_description" class="form-label">Description (optional)</label>
                            <input type="text" name="description" id="category_description"
                                   class="form-control" maxlength="255"
                                   placeholder="Description">
                        </div>
                        <input type="hidden" name="sort_order" value="0">
                        <input type="hidden" name="is_active" value="1">
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Save Category</button>
                        </div>
                    </form>

                    @if($categories->isNotEmpty())
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-striped align-middle w-100" id="categoriesTable">
                                <thead>
                                    <tr>
                                        <th style="width:60px;">#</th>
                                        <th>Category</th>
                                        <th>Description</th>
                                        <th>Status</th>
                                        <th style="width:110px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categories as $category)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->description ?: '—' }}</td>
                                            <td>
                                                <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    <a href="{{ route('admin.attendance-test-options.categories.edit', $category) }}"
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.attendance-test-options.categories.destroy', $category) }}"
                                                          method="POST"
                                                          data-swal-confirm="Delete this category?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                title="Delete"
                                                                {{ $category->options_count > 0 ? 'disabled' : '' }}>
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
                <div class="tab-pane fade show active" id="assign-noncoding-content" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1">Non-Coding Tests</h5>
                            <small class="text-muted">Manage non-coding tests assigned to attendance forms and categories.</small>
                        </div>
                        <a href="{{ route('admin.attendance-test-options.create') }}"
                           class="btn btn-sm text-white"
                           style="background-color:#6b51df;">
                            <i class="fas fa-plus me-1"></i> Assign Non-Coding Test
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle w-100" id="nonCodingTable">
                            <thead>
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>Attendance Form</th>
                                    <th>Category</th>
                                    <th>Online Test</th>
                                    <th>Status</th>
                                    <th style="width:110px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nonCodingOptions as $option)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $option->attendanceTest->title ?? 'Attendance Form #' . $option->external_attendance_test_id }}</td>
                                        <td>
                                            {{ $option->category->name ?? '—' }}
                                            @if($option->is_default)
                                                <span class="badge bg-primary ms-1">Default</span>
                                            @endif
                                        </td>
                                        <td>{{ $option->test->title ?? 'Test #' . $option->test_id }}</td>
                                        <td>
                                            <span class="badge {{ $option->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $option->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.attendance-test-options.edit', $option) }}"
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.attendance-test-options.destroy', $option) }}"
                                                      method="POST"
                                                      data-swal-confirm="Delete this Non-Coding test assignment?">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
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

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    #testOptionTabs .nav-link { padding: .75rem 1.1rem; }
    #testOptionTabs .nav-link.active { color: #6b51df; }
    table.dataTable thead th { white-space: nowrap; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$(function () {
    const config = {
        pageLength: 50,
        lengthMenu: [10, 25, 50, 100],
        searching: true,
        ordering: true,
        autoWidth: false
    };

    if ($('#codingTable').length) {
        $('#codingTable').DataTable(config);
    }
    if ($('#nonCodingTable').length) {
        $('#nonCodingTable').DataTable(config);
    }
    if ($('#categoriesTable').length) {
        $('#categoriesTable').DataTable(config);
    }

    // Adjust table widths when switching to a tab that was initially hidden.
    document.querySelectorAll('#testOptionTabs [data-bs-toggle="tab"]').forEach(function (tab) {
        tab.addEventListener('shown.bs.tab', function () {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
});
</script>
@endpush
