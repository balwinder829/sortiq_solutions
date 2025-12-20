@extends('layouts.app')

@section('content')
<style>
    table.dataTable {
    overflow: visible !important;
}

table.dataTable tbody td {
    overflow: visible !important;
}

.dropdown-menu {
    position: absolute !important;
    z-index: 9999 !important;
}

.dropdown-menu {
    z-index: 20000 !important;
}


</style>
<div class="container">

<h3 class="mb-3">Tests</h3>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
        @endforeach
    </ul>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif


<a href="{{ route('admin.tests.create') }}" 
   class="btn btn-primary mb-3">Add Test</a>

<form method="GET" class="p-3 rounded mb-3" style="background:#f1f3f8">
    <div class="row">

        {{-- College --}}
        <div class="col-md-2 mb-2">
            <label>College</label>
            <select name="college_id" class="form-select">
                <option value="">All</option>
                @foreach($colleges as $col)
                    <option value="{{ $col->id }}"
                        {{ request('college_id') == $col->id ? 'selected' : '' }}>
                        {{ $col->college_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Course --}}
        <div class="col-md-2 mb-2">
            <label>Course</label>
            <select name="student_course_id" class="form-select">
                <option value="">All</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}"
                        {{ request('student_course_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Semester --}}
        <div class="col-md-2 mb-2">
            <label>Semester</label>
            <select name="semester_id" class="form-select">
                <option value="">All</option>
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}"
                        {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                        {{ $sem->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Branch --}}
        <div class="col-md-2 mb-2">
            <label>Branch</label>
            <select name="branch_id" class="form-select">
                <option value="">All</option>
                @foreach($branches as $b)
                    <option value="{{ $b->id }}"
                        {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                        {{ $b->branch_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Category --}}
        <div class="col-md-2 mb-2">
            <label>Category</label>
            <select name="test_category_id" class="form-select">
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
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="draft"       {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="published"   {{ request('status')=='published'?'selected':'' }}>Published</option>
                <option value="unpublished" {{ request('status')=='unpublished'?'selected':'' }}>Unpublished</option>
            </select>
        </div>

        {{-- Date Filters --}}
        <div class="col-md-2 mb-2">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control"
                   value="{{ request('from_date') }}">
        </div>

        <div class="col-md-2 mb-2">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control"
                   value="{{ request('to_date') }}">
        </div>

        {{-- Apply --}}
        <div class="col-md-2 mb-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Apply</button>
        </div>

        {{-- Reset --}}
        <div class="col-md-2 mb-2 d-flex align-items-end">
            <a href="{{ route('admin.tests.index') }}"
               class="btn btn-secondary w-100">Reset</a>
        </div>

    </div>
</form>

<table class="table table-bordered table-striped" id="testTable">
<thead>
<tr>
    <th>#</th>
    <th>Title</th>
    <th>Category</th>
    <th>Course</th>
    <th>Semester</th>
    <th>Status</th>
    <th>Date</th>
    <th>Results</th>
    <th>Student Link</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@foreach($tests as $test)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $test->title }}</td>
    <td>{{ $test->category->name }}</td>
    <td>{{ $test->course->course_name }}</td>
    <td>{{ $test->semester->name }}</td>
        <td>
        @if($test->status == 'published')
            <span class="badge bg-success">Published</span>
        @elseif($test->status == 'draft')
            <span class="badge bg-secondary">Draft</span>
        @else
            <span class="badge bg-danger">Unpublished</span>
        @endif
    </td>

    <td>{{ $test->test_date ?? '-' }}</td>

   <!--  <td>
        <a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-sm btn-info">
            Edit
        </a>

        <form method="POST" action="{{ route('admin.tests.destroy',$test->id) }}" 
              class="d-inline">
            @csrf @method('DELETE')
            <button onclick="return confirm('Delete Test?')"
                    class="btn btn-sm btn-danger">
                Delete
            </button>
        </form>
    </td> -->
   
    <td>
        <a href="{{ route('admin.tests.results', $test->id) }}" class="btn btn-info btn-sm">View Results</a>
        </td>
        <td>
        @if($test->slug)
            <a href="{{ route('student.test.slug', $test->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                Open Test Link
            </a>
        @else
            <span class="text-danger">No Link</span>
        @endif
    </td>

     <td>
    
         <div class="dropdown">
        <button class="btn btn-sm btn-primary dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        data-bs-display="static"
        data-bs-boundary="window">
    Actions
</button>

        <ul class="dropdown-menu">

            <li>
                <a class="dropdown-item" href="{{ route('admin.tests.show', $test->id) }}">
                    View Questions
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ route('admin.questions.create', $test->id) }}">
                    Add Questions
                </a>
            </li>

            <li>
                <a class="dropdown-item" href="{{ route('admin.tests.edit', $test->id) }}">
                    Edit
                </a>
            </li>

            <li>
                <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST"
                      onsubmit="return confirm('Delete?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="dropdown-item text-danger">
                        Delete
                    </button>
                </form>
            </li>

        </ul>
    </div>
</td>

</tr>
@endforeach
</tbody>
</table>

</div>
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#testTable').DataTable({
            "pageLength": 25,
            "lengthMenu": [ 10, 25, 50, 100],
             
        });
    });
</script>
<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
<script>
document.querySelectorAll('.dropdown-toggle').forEach(function (dd) {
    new bootstrap.Dropdown(dd, {
        popperConfig: function (defaultBsPopperConfig) {
            return {
                ...defaultBsPopperConfig,
                placement: 'bottom-start',
                strategy: 'fixed'  // ← IMPORTANT — renders in BODY, not table
            }
        }
    })
})
</script>

@endpush