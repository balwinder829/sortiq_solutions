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
            <h1 class="page_heading">Online Exams</h1>
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



<form method="GET" id="filterForm" class="p-3 rounded mb-3" style="background:#f1f3f8">
    <div class="row">

        <div class="col-md-2 mb-2">
            <label>College</label>
            <select name="college_id" class="form-select filterchange">
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
            <select name="student_course_id" class="form-select filterchange">
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
            <select name="semester_id" class="form-select filterchange">
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
            <select name="gender" class="form-select filterchange">
                <option value="">All</option>
                <option value="male" {{ request('gender')=='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ request('gender')=='female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <label>Category</label>
            <select name="test_category_id" class="form-select filterchange">
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
            <select name="status" class="form-select filterchange">
                <option value="">All</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
                <option value="published" {{ request('status')=='published'?'selected':'' }}>Published</option>
                <option value="unpublished" {{ request('status')=='unpublished'?'selected':'' }}>Unpublished</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <label>Active</label>
            <select name="is_active" class="form-select filterchange">
                <option value="">All</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <div class="col-md-2 mb-2">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control filterchange"
                   value="{{ request('from_date') }}">
        </div>

        <div class="col-md-2 mb-2">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control filterchange"
                   value="{{ request('to_date') }}">
        </div>

        <!-- <div class="col-md-2 mb-2 d-flex align-items-end">
            <button class="btn btn-primary w-100">Apply</button>
        </div> -->

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
     
    <td>{{ $loop->iteration }}</td>
    <td>{{ $test->title }}</td>
    <td>{{ $test->category?->name ?? '-' }}</td>
    <!-- <td>{{ $test->college_full_name ?? '-' }}</td> -->
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
    <td>{{ $test->course?->course_name ?? '-' }}</td>
    <td>{{ $test->semester?->name ?? '-' }}</td>

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

    <td>
        {{ $test->test_date 
            ? \Carbon\Carbon::parse($test->test_date)->format('d M Y') 
            : '-' 
        }}
    </td>

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

    <td class="text-center">

        <a href="{{ route('admin.tests.links', $test->id) }}"
           class="btn btn-sm btn-outline-primary">
            View Links
        </a>

    </td>


  <td class="text-center">
    <div class="d-flex justify-content-center gap-2">

        {{-- Download Dropdown --}}
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                    data-bs-toggle="dropdown">
                <i class="fa fa-download me-1"></i> Download
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
              class="d-inline"
              data-swal-confirm="Are you sure you want to delete this test?">
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
    let url = "{{ route('admin.tests.selected.students', ':id') }}";
    url = url.replace(':id', testId);

    $('#marksContent').load(url);
    // $('#marksContent').load(`/admin/tests/${testId}/selected-students`);
});
</script>

<!-- <script>
function copyTestLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            toast: true,
            icon: 'success',
            title: 'Test link copied!',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    }).catch(() => {
        Swal.fire({
            toast: true,
            icon: 'error',
            title: 'Failed to copy link',
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
}
</script> -->

<script>
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
</script>
<script>
function confirmRegenerate(e) {
    e.preventDefault();

    Swal.fire({
        title: 'Regenerate Test Link?',
        text: 'Old link will stop working.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, regenerate'
    }).then((result) => {
        if (result.isConfirmed) {
            e.target.submit();
        }
    });

    return false;
}
</script>
<script>
$(document).ready(function(){

    let timer;

    $('.filterchange').on('change', function(){
        $('#filterForm').submit();
        
    });
    $('.filterchangetext').on('input', function(){
        clearTimeout(timer);

        timer = setTimeout(function(){
            $('#filterForm').submit();
        }, 500); // waits 500ms after typing stops
    });

});
</script>
@endpush
