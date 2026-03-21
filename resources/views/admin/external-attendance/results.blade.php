@extends('layouts.app')

@section('content')
<div class="container my-5">

<a href="{{ route('admin.external-attendance.index') }}"
   class="btn btn-outline-secondary mb-2">
    ← Back to Forms
</a>

<h2 class="mb-1 text-primary">
    Results : {{ $test->title }}
</h2>

<h5 class="mb-3 text-muted">
    Attendance Submissions
</h5>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- FILTER FORM --}}
<form method="GET" id="filterForm" class="row g-2 mb-4">

    <div class="col-md-2">
        <select name="college_id" class="form-select filterchange">
            <option value="">All Colleges</option>
            @foreach($colleges as $college)
                <option value="{{ $college->id }}"
                    {{ request('college_id') == $college->id ? 'selected' : '' }}>
                    {{ $college->full_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <input type="text"
               name="name"
               value="{{ request('name') }}"
               class="form-control filterchangetext"
               placeholder="Student Name">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="email"
               value="{{ request('email') }}"
               class="form-control filterchangetext"
               placeholder="Email">
    </div>

    <div class="col-md-2">
        <input type="text"
               name="mobile"
               value="{{ request('mobile') }}"
               class="form-control filterchangetext"
               placeholder="Mobile">
    </div>

    {{-- Course --}}
<div class="col-md-2">
    <select name="course_id" class="form-select filterchange">
        <option value="">All Courses</option>
        @foreach($courses as $course)
            <option value="{{ $course->id }}"
                {{ request('course_id') == $course->id ? 'selected' : '' }}>
                {{ $course->course_name }}
            </option>
        @endforeach
    </select>
</div>

{{-- Class --}}
<div class="col-md-2">
    <select name="class" class="form-select filterchange">
        <option value="">All Classes</option>
        <option value="BCA" {{ request('class')=='BCA' ? 'selected' : '' }}>BCA</option>
        <option value="B.Tech" {{ request('class')=='B.Tech' ? 'selected' : '' }}>B.Tech</option>
        <option value="MCA" {{ request('class')=='MCA' ? 'selected' : '' }}>MCA</option>
        <option value="MBA" {{ request('class')=='MBA' ? 'selected' : '' }}>MBA</option>
    </select>
</div>

{{-- Semester --}}
<div class="col-md-2">
    <select name="semester" class="form-select filterchange">
        <option value="">All Semester</option>
        @for($i = 1; $i <= 8; $i++)
            <option value="{{ $i }}"
                {{ request('semester') == $i ? 'selected' : '' }}>
                {{ $i }}
            </option>
        @endfor
    </select>
</div>

    <div class="col-md-2">
        <select name="gender" class="form-select filterchange">
            <option value="">All Gender</option>
            <option value="male" {{ request('gender')=='male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ request('gender')=='female' ? 'selected' : '' }}>Female</option>
        </select>
    </div>

    <div class="col-md-2">
        <a href="{{ route('admin.external-attendance.results', $test->id) }}"
           class="btn btn-secondary w-100">
            Reset
        </a>
    </div>

</form>

{{-- DOWNLOAD BUTTONS (KEEPED SAME STRUCTURE) --}}
<div class="d-flex gap-2 mb-3">

    <a href="{{ route('admin.external-attendance.export.all', $test->id) }}?{{ http_build_query(request()->query()) }}"
       class="btn btn-outline-primary">
        <i class="fa fa-download"></i> Download All
    </a>

</div>

{{-- TABLE --}}
<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>#</th>
    <th>College</th>
    <th>Name</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>Gender</th>
    <th>Class</th>
    <th>Semester</th>
    <th>Course</th>
    <th>Submitted At</th>
</tr>
</thead>

<tbody>
@forelse($students as $i => $st)
<tr>
    <td>{{ $i + 1 }}</td>
    <td>{{ $st->college->full_name ?? '-' }}</td>
    <td>{{ $st->student_name }}</td>
    <td>{{ $st->student_email }}</td>
    <td>{{ $st->student_mobile }}</td>
    <td>{{ ucfirst($st->gender) }}</td>
    <td>{{ $st->class ?? '-' }}</td>
    <td>{{ $st->semester ?? '-' }}</td>
    <td>{{ $st->course->course_name ?? '-' }}</td>

    

    <td>
        {{ $st->exam_submitted_at
            ? \Carbon\Carbon::parse($st->exam_submitted_at)->format('d M Y h:i A')
            : '-' }}
    </td>

</tr>
@empty
<tr>
    <td colspan="10" class="text-center text-muted">
        No submissions found
    </td>
</tr>
@endforelse
</tbody>

</table>

</div>

{{-- FILTER SCRIPT --}}
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