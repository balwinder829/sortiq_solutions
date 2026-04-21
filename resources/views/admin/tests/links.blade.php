@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2">
    <div class="col-md-6">
        <h1 class="page_heading">
            Student Links : {{ $test->title }}
        </h1>
    </div>


<div class="col-md-6">
    <div class="d-flex justify-content-end">
        <a href="{{ route('admin.tests.index') }}"
           class="btn btn-secondary">
            Back to Tests
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

@if(session('error'))

<div class="alert alert-danger alert-dismissible fade show">
    {{ session('error') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="table-responsive">

<table class="table table-bordered table-striped" id="linksTable">

<thead>
<tr>
    <th>#</th>
    <th>College</th>
    <th>Student Count</th>
    <th>Last Test Activity</th>
    <th>Student Link</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

@foreach($links as $link)

@php
$testUrl = route('student.test.slug',$link->slug);

$count = $collegeStats[$link->college_id]->total_students ?? 0;

@endphp

<tr>

<td>{{ $loop->iteration }}</td>

<td>
    {{ $link->college->full_name ?? '-' }}
</td>
 <td>
    {{ $collegeStats[$link->college_id]->total_students ?? 0 }}
</td>

<td>
@if(isset($collegeStats[$link->college_id]))
    {{ \Carbon\Carbon::parse($collegeStats[$link->college_id]->last_attempt)->format('d M Y') }}
@else
    -
@endif
</td>

<td>
<input type="text"
       class="form-control"
       value="{{ $testUrl }}"
       readonly>
</td>

<td class="text-center">

<div class="d-flex justify-content-center gap-2">

<a href="{{ $testUrl }}"
target="_blank"
class="btn btn-sm btn-outline-primary">
Open </a>

<button type="button"
     class="btn btn-sm btn-outline-secondary"
     onclick="copyTestLink('{{ $testUrl }}')">
Copy </button>

<form action="{{ route('admin.tests.links.regenerate',$link->id) }}"
      method="POST"
      class="d-inline"
      onsubmit="confirmRegenerate(event)">

@csrf

<button class="btn btn-sm btn-outline-danger">
    Regenerate
</button>

</form>
@if($count == 0)
<form action="{{ route('admin.tests.links.destroy', $link->id) }}"
      method="POST"
      class="d-inline delete-form">
    @csrf
    @method('DELETE')

    <button type="button" class="btn btn-sm btn-danger btn-delete">
        Delete
    </button>
</form>
@endif

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

$(document).ready(function () {

    $('#linksTable').DataTable({
        pageLength: 25,
        lengthMenu: [10,25,50,100],
        language: {
            emptyTable: `
                No links available. 
                <br><br>
                <a href="{{ route('admin.tests.edit',$test->id) }}" 
                   class="btn btn-sm btn-primary">
                   Add College for this Test
                </a>
            `
        }
    });

});

</script>

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
        confirmButtonText: 'Yes regenerate'
    }).then((result) => {

        if (result.isConfirmed) {
            e.target.submit();
        }

    });

}

</script>
<script>
document.querySelectorAll('.btn-delete').forEach(button => {

    button.addEventListener('click', function () {

        let form = this.closest('form');

        Swal.fire({
            title: 'Delete Link?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it'
        }).then((result) => {

            if (result.isConfirmed) {
                form.submit();
            }

        });

    });

});
</script>

@endpush
