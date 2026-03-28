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
    Current College : {{ $test->college_full_name }}
</h5>


{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif
@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

{{-- FILTER FORM --}}
<form method="GET"
      id="filterForm"
      class="row g-2 mb-4">

     <div class="col-md-2">
        <select name="college_id" class="form-select filterchange">

        <option value="">All Colleges</option>

        @foreach($colleges as $college)
        <option value="{{ $college->id }}"
           {{ $defaultCollegeId == $college->id ? 'selected' : '' }}>

            {{ $college->full_name }}

        </option>
        @endforeach

        </select>
        </div>
    <div class="col-md-2">
        <input type="text"
               name="sno"
               value="{{ request('sno') }}"
               class="form-control filterchangetext"
               placeholder="S.No">
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
               name="student_mobile"
               value="{{ request('student_mobile') }}"
               class="form-control filterchangetext"
               placeholder="Mobile">
    </div>

    <div class="col-md-2">
        <input type="number"
               name="top_n"
               value="{{ request('top_n') }}"
               class="form-control filterchangetext"
               placeholder="Top N">
    </div>

    <div class="col-md-2">
        <select name="gender" class="form-select filterchange">
            <option value="">All</option>
            <option value="male" {{ request('gender')==='male'?'selected':'' }}>
                Male
            </option>
            <option value="female" {{ request('gender')==='female'?'selected':'' }}>
                Female
            </option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="course_type" class="form-select filterchange">
            <option value="">Course Type</option>
            <option value="Degree" {{ request('course_type')=='Degree'?'selected':'' }}>Degree</option>
            <option value="Diploma" {{ request('course_type')=='Diploma'?'selected':'' }}>Diploma</option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="class" class="form-select filterchange">
            <option value="">Class</option>
            <option value="BCA" {{ request('class')=='BCA'?'selected':'' }}>BCA</option>
            <option value="MCA" {{ request('class')=='MCA'?'selected':'' }}>MCA</option>
            <option value="BTech" {{ request('class')=='BTech'?'selected':'' }}>BTech</option>
            <option value="BSc IT" {{ request('class')=='BSc IT'?'selected':'' }}>BSc IT</option>
            <option value="BSc CS" {{ request('class')=='BSc CS'?'selected':'' }}>BSc CS</option>
        </select>
    </div>

    <div class="col-md-2">
        <select name="semester" class="form-select filterchange">
            <option value="">Semester</option>
            @for ($i = 1; $i <= 8; $i++)
                <option value="{{ $i }}" {{ request('semester') == $i ? 'selected' : '' }}>
                    {{ $i }}
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-2">
        <select name="finalized" class="form-select filterchange">
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
        <select name="moved" class="form-select filterchange">
            <option value="">All</option>
            <option value="1" {{ request('moved')==='1' ? 'selected' : '' }}>
                Moved to Enquiries
            </option>
            <option value="0" {{ request('moved')==='0' ? 'selected' : '' }}>
                Not Moved
            </option>
        </select>
    </div>

    <!-- <div class="col-md-1"> -->
    <!-- <button class="btn btn-primary w-100">Go</button> -->
<!-- </div> -->

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
     

    {{-- MOVE TO ENQUIRIES --}}
    

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
<form method="POST" id="bulkForm">
    @csrf
<!-- 
    <div class="row mb-3">
    <div class="col-md-4">
        <input type="text"
               name="course_name"
               class="form-control"
               placeholder="Enter Course Name (Required)"
               >
    </div>
</div> -->
    <div class="d-flex gap-2 mb-3">

<button type="submit"
        class="btn btn-success"
        formaction="{{ route('admin.tests.bulk.finalize') }}">
    Finalize Selected
</button>

<button type="submit"
        class="btn btn-warning"
        formaction="{{ route('admin.tests.move.enquiries', $test->id) }}">
    Move to Enquiries
</button>

   {{-- ✅ NEW BUTTON --}}
    <button type="submit"
            class="btn btn-primary"
            formaction="{{ route('admin.tests.certificate.download', $test->id) }}">
        Download Certificate
    </button>

    <button type="button"
        class="btn btn-info download-letter"
        data-type="letter1"
        data-action="{{ route('admin.tests.certificate.download',  [$test->id, 'mode' => 'free']) }}">
    Download Free Internship Letter
</button>

<button type="button"
        class="btn btn-info download-letter"
        data-type="letter2"
        data-action="{{ route('admin.tests.certificate.download',  [$test->id, 'mode' => 'offer']) }}">
    Download Offer Letter
</button>

</div>
<div class="row mb-4">

    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">

                <h6 class="text-muted mb-1">Selected College</h6>

                <h5 class="fw-semibold">
                    {{ $colleges->firstWhere('id', $defaultCollegeId)->full_name ?? 'All Colleges' }}
                </h5>

            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card shadow-sm border-0 text-center">
            <div class="card-body">

                <h6 class="text-muted mb-1">Total Students</h6>

                <h2 class="fw-bold text-primary">
                    {{ $totalFilteredStudents }}
                </h2>

                <small class="text-muted">
                    Based on applied filters
                </small>

            </div>
        </div>
    </div>

</div>
<!-- <h5 class="text-success mb-3">
    Total Students: {{ $totalFilteredStudents }}
</h5>
<div class="mb-3">
    @foreach($colleges as $college)
        <span class="badge bg-primary me-2">
            {{ $college->full_name }} :
            {{ $collegeStudentCounts[$college->id] ?? 0 }}
        </span>
    @endforeach
</div> -->
<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>
        <input type="checkbox" id="selectAll">
    </th>
    <th>Rank</th>
    <th>S.No</th>
    <th>College</th>
    <th>Name</th>
    <th>Email</th>
    <th>Mobile</th>
    <th>Class</th>
    <th>Semester</th>
    <th>Gender</th>
    <th>Score</th>
    <th>Status</th>
</tr>
</thead>

<tbody>
@forelse($studentTests as $i => $st)
<tr>
    <td>
   
        <input type="checkbox"
               class="student-checkbox"
               name="student_test_ids[]"
               value="{{ $st->id }}">
   
</td>

    <td>{{ $i + 1 }}</td>
    <td>{{ $st->sno }}</td>
    <td>{{ $st->college->full_name ?? '-' }}</td>
    <td>{{ $st->student_name }}</td>
    <td>{{ $st->student_mobile }}</td>
    <td>{{ $st->student_email }}</td>
    <td>{{ $st->class ?? '-' }}</td>
    <td>{{ $st->semester ?? '-' }}</td>
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
    <td colspan="10" class="text-center text-muted">
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
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bulkForm');
    const courseInput = document.getElementById('course_name');

    form.addEventListener('submit', function (e) {
        const action = document.activeElement.getAttribute('formaction');

        // Check if certificate button was clicked
        if (action && action.includes('certificate')) {
            if (!courseInput.value.trim()) {
                e.preventDefault();
                alert('Course name is required for certificate download.');
                courseInput.focus();
            }
        }
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
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('bulkForm');

    form.querySelectorAll('button[type="submit"]').forEach(button => {

        button.addEventListener('click', function (e) {
            e.preventDefault();

            let selected = document.querySelectorAll('.student-checkbox:checked');
            let action = this.getAttribute('formaction');

            // ✅ Check at least 1 student
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Students Selected',
                    text: 'Please select at least one student'
                });
                return;
            }

            // ✅ Confirmation for all actions
            Swal.fire({
                title: 'Are you sure?',
                text: selected.length + " student(s) selected",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Proceed'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.action = action;
                    form.submit();
                }
            });

        });

    });

});
document.querySelectorAll('.download-letter').forEach(button => {

    button.addEventListener('click', function () {

        let selected = document.querySelectorAll('.student-checkbox:checked');

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student'
            });
            return;
        }

        Swal.fire({
            title: 'Enter Course Name',
            input: 'text',
            inputPlaceholder: 'Course Name',
            showCancelButton: true,
            confirmButtonText: 'Download',
            inputValidator: (value) => {
                if (!value) {
                    return 'Course name is required!';
                }
            }
        }).then((result) => {

            if (result.isConfirmed) {

                let form = document.getElementById('bulkForm');

                // ✅ Create hidden input for course
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'course_name';
                input.value = result.value;

                form.appendChild(input);

                // ✅ Optional: send which letter type
                let typeInput = document.createElement('input');
                typeInput.type = 'hidden';
                typeInput.name = 'letter_type';
                typeInput.value = this.dataset.type;

                form.appendChild(typeInput);

                form.action = this.dataset.action;
                form.submit();
            }
        });

    });

});
</script>

<!-- <script>
document.querySelector('[formaction*="certificate"]')
?.addEventListener('click', function (e) {

    e.preventDefault();

    let course = document.querySelector('[name="course_name"]').value;
    let selected = document.querySelectorAll('.student-checkbox:checked');

    if (!course.trim()) {
        Swal.fire({
            icon: 'warning',
            title: 'Course Required',
            text: 'Please enter course name'
        });
        return;
    }

    if (selected.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Students Selected',
            text: 'Please select at least one student'
        });
        return;
    }

    Swal.fire({
        title: 'Generate Certificate?',
        text: selected.length > 1 
              ? "Multiple certificates will be downloaded as ZIP"
              : "Certificate will be downloaded",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Download'
    }).then((result) => {

        if (result.isConfirmed) {

            let form = document.getElementById('bulkForm');

            // ✅ IMPORTANT: set action manually
            form.action = this.getAttribute('formaction');

            form.submit();
        }
    });

});
</script> -->

@endsection
