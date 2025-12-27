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
</style>

<div class="container">
<div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Online Tests</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
               <a href="{{ route('admin.tests.create') }}" class="btn btn-primary mb-3">
                    Add Test
                </a>
            </div>
        </div>
    </div>


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



<form method="GET" class="p-3 rounded mb-3" style="background:#f1f3f8">
    <div class="row">

        <div class="col-md-2 mb-2">
            <label>College</label>
            <select name="college_id" class="form-select">
                <option value="">All</option>
                @foreach($colleges as $col)
                    <option value="{{ $col->id }}"
                        {{ request('college_id') == $col->id ? 'selected' : '' }}>
                        {{ $col->FullName }}
                    </option>
                @endforeach
            </select>
        </div>

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
        <div class="col-md-2">
            <label>Gender</label>
            <select name="gender" class="form-select">
                <option value="">All</option>
                <option value="male" {{ request('gender')=='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender')=='female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

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

        <div class="col-md-2 mb-2">
            <label>Status</label>
            <select name="status" class="form-select">
                <option value="">All</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="published" {{ request('status')=='published'?'selected':'' }}>Published</option>
                <option value="unpublished" {{ request('status')=='unpublished'?'selected':'' }}>Unpublished</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <label>Active</label>
            <select name="is_active" class="form-select">
                <option value="">All</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

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

        <div class="col-md-2 mb-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Apply</button>
        </div>

        <div class="col-md-2 mb-2 d-flex align-items-end">
            <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>
</form>
<div class="table-responsive">
<table class="table table-bordered table-striped" id="testTable">
<thead>
<tr>
    <th>#</th>
    <th>Title</th>
    <th>Category</th>
    <th>College</th>
    <th>Course</th>
    <th>Semester</th>
    <th>Status</th>
    <th>Active</th>
    <th>Date</th>
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
    <td>{{ $loop->iteration }}</td>
    <td>{{ $test->title }}</td>
    <td>{{ $test->category->name }}</td>
    <td>{{ $test->college_full_name }}</td>
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

    <td>
        @if($test->is_active)
            <span class="badge bg-success">Active</span>
        @else
            <span class="badge bg-dark">Inactive</span>
        @endif
    </td>

    <td>{{ $test->test_date ?? '-' }}</td>

    <td>
        <span class="badge bg-info">
            {{ $test->total_registered ?? 0 }}
        </span>
    </td>

    <td>
        <span class="badge bg-success">
            {{ $test->selected_count ?? 0 }}
        </span>
    </td>

    <td>
        @if(($test->selected_count ?? 0) > 0)
        <button class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#marksModal"
                data-test-id="{{ $test->id }}">
            View
        </button>
        @else
            <span class="text-muted">—</span>
        @endif
    </td>

    <td class="text-center">
    <a href="{{ route('admin.tests.results', $test->id) }}"
       class="btn btn-sm btn-info text-nowrap">
        View Results
    </a>
</td>

    <td>
        @if($test->slug)
            @php
                $testUrl = route('student.test.slug', $test->slug);
            @endphp

            <a href="{{ $testUrl }}"
               target="_blank"
               class="btn btn-sm btn-outline-primary text-nowrap">
                Open Test Link
            </a>

            <button type="button"
                    class="btn btn-sm btn-outline-secondary text-nowrap"
                    onclick="copyTestLink('{{ $testUrl }}')">
                Copy Link
            </button>
        @else
            <span class="text-danger">No Link</span>
        @endif
    </td>


  <td class="text-center">
    <div class="d-flex justify-content-center gap-1">
         
            <a href="{{ route('admin.tests.export.all', $test->id) }}"
               class="btn btn-sm btn-outline-primary"
               data-bs-toggle="tooltip"
            title="Download All">
            <i class="fa fa-download"></i>
                <!-- Download All -->
            </a>

            <a href="{{ route('admin.tests.export.finalized', $test->id) }}"
               class="btn btn-sm btn-outline-success"
               data-bs-toggle="tooltip"
                title="Download Selected">
                <i class="fa fa-download"></i>
                <!-- Download Finalized -->
            </a>
         
        <a href="{{ route('admin.tests.show', $test->id) }}"
           class="btn btn-sm btn-outline-primary"
           data-bs-toggle="tooltip"
           title="View Questions">
            <i class="fa fa-eye"></i>
        </a>

        <a href="{{ route('admin.questions.create', $test->id) }}"
           class="btn btn-sm btn-outline-success"
           data-bs-toggle="tooltip"
           title="Add Questions">
            <i class="fa fa-plus"></i>
        </a>

        <a href="{{ route('admin.tests.edit', $test->id) }}"
           class="btn btn-sm btn-outline-warning"
           data-bs-toggle="tooltip"
           title="Edit">
            <i class="fa fa-edit"></i>
        </a>

        <form action="{{ route('admin.tests.destroy', $test->id) }}"
              method="POST"
              class="d-inline"
              onsubmit="return confirm('Are you sure you want to delete this test?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    class="btn btn-sm btn-outline-danger"
                    data-bs-toggle="tooltip"
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

{{-- MARKS MODAL --}}
<div class="modal fade" id="marksModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Selected Students</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="marksContent" class="text-center">
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
    $('#testTable').DataTable({
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
    });
});
</script>

<script>
$('#marksModal').on('show.bs.modal', function (e) {
    let testId = e.relatedTarget.getAttribute('data-test-id');
    $('#marksContent').load(`/admin/tests/${testId}/selected-students`);
});
</script>

<script>
function copyTestLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        alert('✅ Test link copied to clipboard!');
    }).catch(() => {
        alert('❌ Failed to copy link');
    });
}
</script>

@endpush
