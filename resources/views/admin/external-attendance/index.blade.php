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
            <h1 class="page_heading">College Forms</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
               <a href="{{ route('admin.external-attendance.create') }}" class="btn btn-primary mb-3">
                    Add
                </a>
            </div>
        </div>
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
            <a href="{{ route('admin.external-attendance.index') }}" class="btn btn-secondary w-100">
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
    <th>College</th>
    <th>Status</th>
    <th>Active</th>
    <th>Date</th>
    <th>Total Students</th>
    <th>Student Link</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
@foreach($tests as $test)
<tr>
     
    <td>{{ $loop->iteration }}</td>
    <td>{{ $test->title }}</td>
   
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
    <a href="{{ route('admin.external-attendance.results', $test->id) }}"
       class="text-decoration-none"
   title="View Submissions">
        <span class="badge bg-info">
            {{ $test->total_submissions ?? 0 }}
        </span>
    </a>
</td>

     

    

    

    <td class="text-center">

        <a href="{{ route('admin.external-attendance.links', $test->id) }}"
           class="btn btn-sm btn-outline-primary">
            View Links
        </a>

    </td>


  <td class="text-center">
    <div class="d-flex justify-content-center gap-2">

        
                    <a href="{{ route('admin.external-attendance.export.all', $test->id) }}"
                         class="btn btn-sm btn-outline-secondary"
                       title="Download">
                        <i class="fa fa-download"></i>
                       
                    </a>
               

        {{-- Edit --}}
        <a href="{{ route('admin.external-attendance.edit', $test->id) }}"
           class="btn btn-sm btn-outline-secondary"
           title="Edit">
            <i class="fa fa-edit"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('admin.external-attendance.destroy', $test->id) }}"
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
