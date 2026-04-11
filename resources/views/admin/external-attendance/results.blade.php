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

{{-- SUCCESS --}}
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

{{-- FILTER --}}
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
        <input type="text" name="name" value="{{ request('name') }}"
               class="form-control filterchangetext"
               placeholder="Student Name">
    </div>

    <div class="col-md-2">
        <input type="text" name="email" value="{{ request('email') }}"
               class="form-control filterchangetext"
               placeholder="Email">
    </div>

    <div class="col-md-2">
        <input type="text" name="mobile" value="{{ request('mobile') }}"
               class="form-control filterchangetext"
               placeholder="Mobile">
    </div>

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

    <div class="col-md-2">
        <select name="class" class="form-select filterchange">
            <option value="">All Classes</option>
            <option value="BCA" {{ request('class')=='BCA' ? 'selected' : '' }}>BCA</option>
            <option value="B.Tech" {{ request('class')=='B.Tech' ? 'selected' : '' }}>B.Tech</option>
            <option value="MCA" {{ request('class')=='MCA' ? 'selected' : '' }}>MCA</option>
            <option value="MBA" {{ request('class')=='MBA' ? 'selected' : '' }}>MBA</option>
        </select>
    </div>

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
    <select name="status" class="form-select filterchange">
        <option value="">Status</option>
        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Moved</option>
        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Not Moved</option>
    </select>
</div>

    <div class="col-md-2">
        <a href="{{ route('admin.external-attendance.results', $test->id) }}"
           class="btn btn-secondary w-100">
            Reset
        </a>
    </div>

</form>

{{-- BULK FORM --}}
<form method="POST" id="bulkForm">
@csrf

<div class="d-flex gap-2 mb-3">
    <button type="submit"
            class="btn btn-warning"
            formaction="{{ route('admin.attendance.move.enquiries', $test->id) }}">
        Move to Enquiries
    </button>
    <a href="{{ route('admin.external-attendance.export.results', [$test->id] + request()->query()) }}"
   class="btn btn-success">
    Export Excel
</a>
</div>

<table class="table table-bordered table-striped">

<thead>
<tr>
    <th>
        <input type="checkbox" id="selectAll">
    </th>
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

    <td>
        @if($st->is_moved_to_enquiry)
            <span class="badge bg-info">Moved</span>
        @else
            <input type="checkbox"
                   class="student-checkbox"
                   name="attendance_ids[]"
                   value="{{ $st->id }}">
        @endif
    </td>

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
    <td colspan="11" class="text-center text-muted">
        No submissions found
    </td>
</tr>
@endforelse
</tbody>

</table>
</form>

</div>

{{-- SELECT ALL --}}
<script>
document.getElementById('selectAll')?.addEventListener('change', function () {
    document.querySelectorAll('.student-checkbox')
        .forEach(cb => cb.checked = this.checked);
});
</script>

{{-- FILTER --}}
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

{{-- SESSION POPUP --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('bulkForm');
    const sessionOptions = @json($sessionsList);

    form.querySelectorAll('button[type="submit"]').forEach(button => {

        button.addEventListener('click', function (e) {
            e.preventDefault();

            let selected = document.querySelectorAll('.student-checkbox:checked');
            let action = this.getAttribute('formaction');

            if (selected.length === 0) {
                Swal.fire('No Students Selected', 'Please select at least one student', 'warning');
                return;
            }

            let optionsHtml = '';
            Object.keys(sessionOptions).forEach(function(key) {
                optionsHtml += `<option value="${key}">${sessionOptions[key]}</option>`;
            });

            Swal.fire({
                title: 'Select Session',
                html: `<select id="session_id" class="form-control">${optionsHtml}</select>`,
                showCancelButton: true,
                confirmButtonText: 'Move'
            }).then((result) => {

                if (!result.isConfirmed) return;

                let session_id = document.getElementById('session_id').value;

                if (!session_id) {
                    Swal.fire('Error', 'Session is required', 'error');
                    return;
                }

                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'session_id';
                input.value = session_id;

                form.appendChild(input);

                form.action = action;
                form.submit();
            });

        });

    });

});
</script>

@endsection