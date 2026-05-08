@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2">
    <div class="col-md-6">
        <h1 class="page_heading">Office Exams</h1>
    </div>

    <div class="col-md-6">
        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.office-tests.create') }}"
               class="btn btn-primary mb-3">
                Add Office Test
            </a>
        </div>
    </div>
</div>


{{-- FILTER SECTION --}}

<form method="GET" id="filterForm" class="p-3 rounded mb-3" style="background:#f1f3f8">

<div class="row">

<div class="col-md-3 mb-2">
<label>Session</label>
<select name="session_id" class="form-select filterchange">
<option value="">All</option>

@foreach($sessionsList as $session)
<option value="{{ $session->id }}"
{{ request('session_id')==$session->id ? 'selected':'' }}>
{{ $session->session_name }}
</option>
@endforeach

</select>
</div>


<div class="col-md-3 mb-2">
<label>Batch</label>
<select name="batch_id" class="form-select filterchange">
<option value="">All</option>

@foreach($batches as $batch)
<option value="{{ $batch->id }}"
{{ request('batch_id')==$batch->id ? 'selected':'' }}>
{{ $batch->batch_name }}
</option>
@endforeach

</select>
</div>


<div class="col-md-3 mb-2">
<label>Trainer</label>
<select name="trainer_id" class="form-select filterchange">
<option value="">All</option>

@foreach($trainers as $trainer)
<option value="{{ $trainer->id }}"
{{ request('trainer_id')==$trainer->id ? 'selected':'' }}>
{{ $trainer->name }}
</option>
@endforeach

</select>
</div>


<div class="col-md-2 mb-2">
<label>Mode</label>
<select name="exam_mode" class="form-select filterchange">

<option value="">All</option>

<option value="online"
{{ request('exam_mode')=='online' ? 'selected':'' }}>
Online
</option>

<option value="offline"
{{ request('exam_mode')=='offline' ? 'selected':'' }}>
Offline
</option>

</select>
</div>


<div class="col-md-1 mb-2 d-flex align-items-end">
<!-- <button class="btn btn-primary w-100">Apply</button> -->
    <a href="{{ route('admin.office-tests.index') }}"
       class="btn btn-secondary">
        Reset
    </a>
</div>

</div>

</form>



<div class="table-responsive">

<table class="table table-bordered table-striped" id="officeTestTable">

<thead>

<tr>

<th>#</th>
<th>Title</th>
<th>Session</th>
<th>Batch</th>
<th>Trainer</th>
<th>Mode</th>
<th>Status</th>
<th>Date</th>
<th style="width:180px;">TEST LINK</th>
<th>Questions</th>
<th>Action</th>

</tr>

</thead>


<tbody>

@foreach($tests as $test)

<tr>

<td>{{ $loop->iteration }}</td>

<td>{{ $test->title }}</td>

<td>{{ $test->session?->session_name ?? '-' }}</td>

<td>{{ $test->batch?->batch_name ?? '-' }}</td>

<td>{{ $test->trainer?->name ?? '-' }}</td>


<td>
@if($test->exam_mode == 'online')
<span class="badge bg-info">Online</span>
@else
<span class="badge bg-secondary">Offline</span>
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

{{ $test->test_date
? \Carbon\Carbon::parse($test->test_date)->format('d M Y')
: '-' }}

</td>

<td style="min-width:170px">

<div class="d-flex flex-column gap-1">

<a 
href="{{ route('student.office.enter',$test->slug) }}" 
target="_blank"
class="btn btn-sm btn-primary"
>
Open Link
</a>

<button 
class="btn btn-sm btn-secondary"
onclick="copyTestLink('{{ route('student.office.enter',$test->slug) }}', this)"
>
Copy Link
</button>

</div>

</td>


<td>

<a href="{{ route('admin.office-tests.office-questions.index',$test->id) }}"
   class="badge bg-success text-decoration-none">

{{ $test->questions_count ?? 0 }} Questions

</a>

</td>


<td class="text-center">

<div class="d-flex justify-content-center gap-2">


{{-- Questions --}}
<a href="{{ route('admin.office-tests.office-questions.index',$test->id) }}"
   class="btn btn-sm btn-outline-primary"
   title="Questions">

<i class="fa fa-list"></i>

</a>

<!-- <a 
href="{{ route('admin.office-test.download',$test->slug) }}"
class="btn btn-success btn-sm"
>
Download Answers
</a> -->

{{-- Add Question --}}
<a href="{{ route('admin.office-test.download',$test->slug) }}"
   class="btn btn-sm btn-outline-success"
   title="Download Answer Sheet">

<i class="fa fa-download"></i>

</a>

{{-- Add Question --}}
<a href="{{ route('admin.office-tests.office-questions.create',$test->id) }}"
   class="btn btn-sm btn-outline-success"
   title="Add Question">

<i class="fa fa-plus"></i>

</a>


{{-- Download PDF --}}
<a href="{{ route('admin.office-tests.download.pdf',$test->id) }}"
   class="btn btn-sm btn-outline-dark"
   title="Download Question Paper">

<i class="fa fa-download"></i>

</a>


{{-- Edit --}}
<a href="{{ route('admin.office-tests.edit',$test->id) }}"
   class="btn btn-sm btn-outline-secondary">

<i class="fa fa-edit"></i>

</a>


{{-- Delete --}}
<form action="{{ route('admin.office-tests.destroy',$test->id) }}"
method="POST"
class="d-inline"
data-swal-confirm="Delete this test?">

@csrf
@method('DELETE')

<button type="submit"
class="btn btn-sm btn-outline-danger">

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

@endsection


@push('scripts')

<script>

$(document).ready(function(){

var table = $('#officeTestTable').DataTable({

    pageLength:25,
    lengthMenu:[10,25,50,100],
    columnDefs: [
        {
            targets: 0, // first column
            searchable: false,
            orderable: false
        }
    ]

});

table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
});

</script>
<script>
function copyTestLink(link, btn) {

    navigator.clipboard.writeText(link).then(function() {

        Swal.fire({
            icon: 'success',
            title: 'Link Copied',
            text: 'The test link has been copied to your clipboard.',
            confirmButtonText: 'OK',
            showCancelButton: true,
            cancelButtonText: 'Close'
        });

    }).catch(function() {

        Swal.fire({
            icon: 'error',
            title: 'Copy Failed',
            text: 'Unable to copy the link.',
            confirmButtonText: 'OK'
        });

    });

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