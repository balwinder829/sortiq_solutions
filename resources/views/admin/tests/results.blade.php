@extends('layouts.app')

@section('content')
<div class="container my-5">
<a href="{{ route('admin.tests.index') }}"
   class="btn btn-outline-secondary mb-2">
    ← Back to Tests
</a>
<h2 class="mb-1 text-primary">
    Results : {{ $test->title }}
</h2>

<h5 class="mb-3 text-muted">
    College : {{ $test->college_full_name }}
</h5>


{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- FILTER FORM --}}
<form method="GET"
      action="{{ route('admin.tests.results', $test->id) }}"
      class="row g-2 mb-4">

    <div class="col-md-2">
        <input type="text"
               name="sno"
               value="{{ request('sno') }}"
               class="form-control"
               placeholder="S.No">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="name"
               value="{{ request('name') }}"
               class="form-control"
               placeholder="Student Name">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="email"
               value="{{ request('email') }}"
               class="form-control"
               placeholder="Email">
    </div>

    <div class="col-md-2">
        <input type="number"
               name="top_n"
               value="{{ request('top_n') }}"
               class="form-control"
               placeholder="Top N">
    </div>

    <div class="col-md-2">
        <select name="finalized" class="form-select">
            <option value="">All</option>
            <option value="1" {{ request('finalized')==='1'?'selected':'' }}>
                Selected
            </option>
            <option value="0" {{ request('finalized')==='0'?'selected':'' }}>
                Not Selected
            </option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="moved" class="form-select">
            <option value="">All</option>
            <option value="1" {{ request('moved')==='1' ? 'selected' : '' }}>
                Moved to Enquiries
            </option>
            <option value="0" {{ request('moved')==='0' ? 'selected' : '' }}>
                Not Moved
            </option>
        </select>
    </div>

    <div class="col-md-1">
    <button class="btn btn-primary w-100">Go</button>
</div>

<div class="col-md-1 ms-2">
    <a href="{{ route('admin.tests.results', $test->id) }}"
       class="btn btn-secondary w-100">
        Reset
    </a>
</div>


</form>

{{-- ACTION BAR (ONLY 2 BUTTONS) --}}
<div class="d-flex gap-2 mb-3">

    {{-- FINALIZE SELECTED --}}
    <form method="POST" action="{{ route('admin.tests.bulk.finalize') }}">
        @csrf
        <button class="btn btn-success"
                onclick="return confirm('Finalize selected students?')">
            Finalize Selected
        </button>
    </form>

    {{-- MOVE TO ENQUIRIES --}}
    <form method="POST"
          action="{{ route('admin.tests.move.enquiries', $test->id) }}"
          onsubmit="return confirm('Move finalized students to Enquiries?')">
        @csrf
        <button class="btn btn-warning">
            Move to Enquiries
        </button>
    </form>

</div>

<div class="d-flex gap-2 mb-3">

    {{-- DOWNLOAD ALL (FILTERED) --}}
    <a href="{{ route('admin.tests.export.all', $test->id) }}?{{ http_build_query(request()->query()) }}"
       class="btn btn-outline-primary">
        <i class="fa fa-download"></i> Download All
    </a>

    {{-- DOWNLOAD FINALIZED (FILTERED) --}}
    <a href="{{ route('admin.tests.export.finalized', $test->id) }}?{{ http_build_query(request()->query()) }}"
       class="btn btn-outline-success">
        <i class="fa fa-download"></i> Download Selected
    </a>

</div>


{{-- RESULTS TABLE --}}
<form>
<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>
        <input type="checkbox" id="selectAll">
    </th>
    <th>Rank</th>
    <th>S.No</th>
    <th>Name</th>
    <th>Email</th>
    <th>Gender</th>
    <th>Score</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
@forelse($studentTests as $i => $st)
<tr>
    <td>
    @if(!$st->is_finalized && !in_array($st->id, $movedStudentTestIds))
        <input type="checkbox"
               class="student-checkbox"
               name="student_test_ids[]"
               value="{{ $st->id }}">
    @endif
</td>

    <td>{{ $i + 1 }}</td>
    <td>{{ $st->sno }}</td>
    <td>{{ $st->student_name }}</td>
    <td>{{ $st->student_email }}</td>
    <td>
        {{ $st->gender ? ucfirst(strtolower($st->gender)) : '-' }}
    </td>

    <td>
        {{ (int)$st->score }}/{{ $test->questions_count }}
    </td>

    <td>
    @if(in_array($st->id, $movedStudentTestIds))
        <span class="badge bg-info">
            Moved to Enquiries
        </span>
    @elseif($st->is_finalized)
        <span class="badge bg-success">
            Finalized
        </span>
    @else
        <span class="badge bg-secondary">
            Pending
        </span>
    @endif
</td>

</tr>
@empty
<tr>
    <td colspan="7" class="text-center text-muted">
        No students found
    </td>
</tr>
@endforelse
</tbody>
</table>
</form>

</div>

{{-- SELECT ALL SCRIPT --}}
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.student-checkbox')
        .forEach(cb => cb.checked = this.checked);
});
</script>

@endsection
