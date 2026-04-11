@extends('layouts.app')

@section('content')
<div class="container my-5">

<a href="{{ route('admin.office-online-tests.index') }}"
   class="btn btn-outline-secondary mb-3">
    ← Back to Tests
</a>

<h2 class="mb-3 text-primary">
    Results : {{ $test->title }}
</h2>

{{-- FILTER FORM --}}
<form method="GET" id="filterForm" class="row g-2 mb-4">

    <div class="col-md-2">
        <input type="text" name="student_sno" value="{{ request('name') }}"
               class="form-control filterchangetext" placeholder="SNo">
    </div>

     <div class="col-md-2">
        <input type="text" name="name" value="{{ request('name') }}"
               class="form-control filterchangetext" placeholder="Name">
    </div>

    <div class="col-md-2">
        <input type="text" name="email" value="{{ request('email') }}"
               class="form-control filterchangetext" placeholder="Email">
    </div>

    <div class="col-md-2">
        <input type="text" name="mobile" value="{{ request('mobile') }}"
               class="form-control filterchangetext" placeholder="Mobile">
    </div>

    <div class="col-md-2">
        <input type="number" name="top_n" value="{{ request('top_n') }}"
               class="form-control filterchangetext" placeholder="Top N">
    </div>

    <div class="col-md-2">
        <input type="date" name="from_date" value="{{ request('from_date') }}"
               class="form-control filterchange">
    </div>

    <div class="col-md-2">
        <input type="date" name="to_date" value="{{ request('to_date') }}"
               class="form-control filterchange">
    </div>

    <div class="col-md-2">
        <a href="{{ route('admin.office-online-tests.results', $test->id) }}"
           class="btn btn-secondary w-100">
            Reset
        </a>
    </div>

</form>

{{-- TOTAL --}}
<div class="card shadow-sm border-0 mb-4 text-center">
    <div class="card-body">
        <h6 class="text-muted">Total Students</h6>
        <h2 class="fw-bold text-primary">{{ $totalStudents }}</h2>
    </div>
</div>

{{-- TABLE --}}
<table class="table table-bordered table-striped">
<thead>
<tr>
     
    <th>Name</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>Score</th>
    <th>Submitted At</th>
</tr>
</thead>

<tbody>
@forelse($studentTests as $i => $st)
<tr>
     

    <td>{{ $st->student_name }}</td>
    <td>{{ $st->student_email }}</td>
    <td>{{ $st->student_mobile }}</td>

    <td>
        {{ (int)$st->score }}/{{  $test->questions_count }}
       
    </td>

    <td>
        {{ $st->exam_submitted_at ? $st->exam_submitted_at->format('d M Y H:i') : '-' }}
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="text-center text-muted">
        No students found
    </td>
</tr>
@endforelse
</tbody>
</table>

</div>

{{-- AUTO FILTER SCRIPT --}}
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
        }, 500);
    });

});
</script>

@endsection