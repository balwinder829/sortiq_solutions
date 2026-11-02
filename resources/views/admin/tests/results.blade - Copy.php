@extends('layouts.app')

@section('content')
<div class="container my-5">
<a href="{{ route('admin.tests.index') }}"
   class="btn btn-outline-secondary mb-2">
    ← Back to Tests
</a>
<h2 class="mb-3 text-primary">
    Results : {{ $test->title }}
</h2>

 


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

    <!-- <div class="col-md-2">
        <select name="class" class="form-select filterchange">
            <option value="">Class</option>
            <option value="BCA" {{ request('class')=='BCA'?'selected':'' }}>BCA</option>
            <option value="MCA" {{ request('class')=='MCA'?'selected':'' }}>MCA</option>
            <option value="BTech" {{ request('class')=='BTech'?'selected':'' }}>BTech</option>
            <option value="BSc IT" {{ request('class')=='BSc IT'?'selected':'' }}>BSc IT</option>
            <option value="BSc CS" {{ request('class')=='BSc CS'?'selected':'' }}>BSc CS</option>
            <option value="Polytechnic" {{ request('class')=='Polytechnic'?'selected':'' }}>Polytechnic</option>
        </select>
    </div> -->

    {{-- Branch Filter --}}
        <div class="col-md-2">
            <select name="branch" class="form-select filterchange">
                <option value="">All Branches</option>

                
                    <option value="Computer Science"
                        {{ request('branch') == 'Computer Science' ? 'selected' : '' }}>
                        Computer Science
                    </option>
                    <option value="IT"
                        {{ request('branch') == 'IT' ? 'selected' : '' }}>
                        IT
                    </option>
                    <option value="Mechanical"
                        {{ request('branch') == 'Mechanical' ? 'selected' : '' }}>
                        Mechanical
                    </option>
                    <option value="Civil"
                        {{ request('branch') == 'Civil' ? 'selected' : '' }}>
                        Civil
                    </option>
                    <option value="Electrical"
                        {{ request('branch') == 'Electrical' ? 'selected' : '' }}>
                        Electrical
                    </option>
                    <option value="Management"
                        {{ request('branch') == 'Management' ? 'selected' : '' }}>
                        Management
                    </option>
                
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

 

<div class="d-flex gap-2 mb-3">

    {{-- DOWNLOAD ALL (FILTERED) --}}
    <a href="#"
   onclick="downloadWithFilters('{{ route('admin.tests.export.all', $test->id) }}')"
   class="btn btn-outline-primary">
    <i class="fa fa-download"></i> Download All
</a>

<a href="#"
   onclick="downloadWithFilters('{{ route('admin.tests.export.finalized', $test->id) }}')"
   class="btn btn-outline-success">
    <i class="fa fa-download"></i> Download Selected
</a>
<button type="button"
        class="btn btn-outline-success"
        id="downloadCheckedStudents">
    <i class="fa fa-download"></i> Download Checked Students
</button>

<button type="button"
        class="btn btn-secondary"
        id="resetStudentChecks">
    <i class="fa fa-undo"></i> Reset Checks
</button>

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
    <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">

     

    
<button type="button"
        class="btn btn-primary"
        id="openMoveRoundModal">
    <i class="fa fa-arrow-right"></i> Move Selected
</button>


<button type="submit"
        class="btn btn-success"
        formaction="{{ route('admin.tests.bulk.finalize') }}">
    Finalize Selected
</button>

<button type="submit"
        class="btn btn-warning move-enquiry-btn"
        formaction="{{ route('admin.tests.move.enquiries', $test->id) }}">
    Move to Enquiries
</button>

   {{-- ✅ NEW BUTTON --}}
    <button type="button"
            class="btn btn-primary download-letter"
            data-type="letter3"
            data-action="{{ route('admin.tests.certificate.download', $test->id) }}">
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

<button type="button"
        class="btn btn-info"
        id="updateSelectedScoresBtn">
    <i class="fa fa-pencil"></i> Update Score
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
 
<table class="table table-bordered table-striped" id="resultsTable" >
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
    <th width="150">Edit Score</th>
    <th>Status</th>
    <th>Current Location</th>
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
    <td>{{ $st->student_email }}</td>
    <td>{{ $st->student_mobile }}</td>
    <td>{{ $st->class ?? '-' }}</td>
    <td>{{ $st->semester ?? '-' }}</td>
    <td>
        {{ $st->gender ? ucfirst(strtolower($st->gender)) : '-' }}
    </td>

    <td>
        {{ (int)$st->score }}/{{ $test->questions_count }}
    </td>
    <td>
        <button
            type="button"
            class="btn btn-sm btn-outline-primary edit-score-btn"
            data-url="{{ route('admin.tests.update-score', [
                'test_id' => $test->id,
                'student_test_id' => $st->id
            ]) }}"
            data-score="{{ (int) $st->score }}"
            data-max="{{ (int) $test->questions_count }}"
            data-name="{{ $st->student_name }}"
        >
            <i class="fa fa-pencil"></i> Edit
        </button>
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

{{-- CURRENT LOCATION --}}
<td>
    @if(in_array($st->id, $movedStudentTestIds))
        <span class="badge bg-info">
            Enquiries
        </span>

    @elseif(!empty($st->moved_to_round_type))
        @php
            $roundLabels = [
                'technical'     => 'Technical Round',
                'machine_round' => 'Machine Round',
                'final'         => 'Final Students',
            ];

            $roundLabel = $roundLabels[$st->moved_to_round_type]
                ?? ucfirst(str_replace('_', ' ', $st->moved_to_round_type));
        @endphp

        <span class="badge bg-primary">
            {{ $roundLabel }}
        </span>

        @if(!empty($st->moved_to_round_at))
            <br>
            <small class="text-muted">
                {{ \Carbon\Carbon::parse($st->moved_to_round_at)->format('d M Y') }}
            </small>
        @endif

    @else
        <span class="badge bg-secondary">
            Original Test
        </span>
    @endif
</td>


</tr>
@empty
<tr>
    <td colspan="14" class="text-center text-muted">
        No students found
    </td>
</tr>
@endforelse
</tbody>
</table>
</form>


<!-- EDIT SCORE MODAL -->
<div class="modal fade" id="editScoreModal" tabindex="-1"
     aria-labelledby="editScoreModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="editScoreForm" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title" id="editScoreModalLabel">
                        Update Student Score
                    </h5>
                    <button type="button" class="btn-close"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-1">
                        <strong>Student:</strong>
                        <span id="editScoreStudentName"></span>
                    </p>

                    <p class="text-muted mb-3">
                        Maximum score:
                        <strong id="editScoreMaximum"></strong>
                    </p>

                    <label for="editScoreInput" class="form-label">
                        New Score
                    </label>

                    <input
                        type="number"
                        name="score"
                        id="editScoreInput"
                        class="form-control"
                        min="0"
                        step="1"
                        required
                    >

                    <small class="text-muted">
                        Enter a score from 0 to the maximum shown above.
                        Values above the maximum will be capped automatically.
                    </small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary"
                            id="saveScoreBtn">
                        <i class="fa fa-save"></i> Save Score
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- MOVE SELECTED MODAL --}}
<div class="modal fade"
     id="moveRoundModal"
     tabindex="-1"
     aria-labelledby="moveRoundModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="moveRoundModalLabel">
                    Move Selected Students
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <p class="text-muted mb-3">
                    Selected students:
                    <strong id="selectedRoundCount">0</strong>
                </p>

                <label for="destination_round" class="form-label">
                    Select Destination
                </label>

                
                    <select name="destination_round"
                        id="destination_round"
                        class="form-select"
                        form="bulkForm"
                        required>
                    <option value="">-- Choose Round --</option>
                    <option value="technical">Technical Round</option>
                    <option value="machine_round">Machine Round</option>
                    <option value="final">Final Students</option>
                </select>

                {{-- Category is required only when moving to Final Students. --}}
                <div class="mt-3 d-none" id="finalCategoryWrapper">
                    <label for="final_category" class="form-label">
                        Final Student Category <span class="text-danger">*</span>
                    </label>
                    <select name="benefit_type"
                            id="final_category"
                            class="form-select"
                            form="bulkForm"
                            disabled>
                        <option value="">-- Choose Category --</option>
                        <option value="free">Free Internship</option>
                        <option value="discount">Discounted Internship</option>
                        <option value="stipend">Stipend-Based Internship</option>
                        <option value="3_month_ppo">3-Month Internship with PPO Opportunity</option>
                    </select>
                    <small class="text-muted">
                        Select the category to save for all selected Final Students.
                    </small>
                </div>

            </div>

            <div class="modal-footer">
                

                <button type="button"
                        class="btn btn-primary"
                        id="confirmMoveRound">
                    <i class="fa fa-arrow-right"></i> Confirm Move
                </button>

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>
            </div>

        </div>
    </div>
</div>

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
    const sessionOptions = @json($sessionsList); // ✅ pass from controller

    form.querySelectorAll('button[type="submit"]').forEach(button => {

        button.addEventListener('click', function (e) {
            e.preventDefault();

            let selected = document.querySelectorAll('.student-checkbox:checked');
            let action = this.getAttribute('formaction');

            // ✅ Check selection
            if (selected.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Students Selected',
                    text: 'Please select at least one student'
                });
                return;
            }

            // Validate the destination for round movement.
// if (this.classList.contains('move-round-btn')) {
//     const destination = document.getElementById('destination_round').value;

//     if (!destination) {
//         Swal.fire({
//             icon: 'warning',
//             title: 'Select Destination',
//             text: 'Please select Technical Round, Machine Round, or Final Students.'
//         });
//         return;
//     }
// }

            // 🔥 ONLY for Move to Enquiries
                 if (this.classList.contains('move-enquiry-btn')) {

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

                    // ✅ add hidden input
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'session_id';
                    input.value = session_id;

                    form.appendChild(input);

                    form.action = action;
                    form.submit();
                });

            } else {

                // ✅ normal flow for other buttons
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

            }

        });

    });

});
</script>
<script>
// document.addEventListener('DOMContentLoaded', function () {

//     const form = document.getElementById('bulkForm');

//     form.querySelectorAll('button[type="submit"]').forEach(button => {

//         button.addEventListener('click', function (e) {
//             e.preventDefault();

//             let selected = document.querySelectorAll('.student-checkbox:checked');
//             let action = this.getAttribute('formaction');

//             // ✅ Check at least 1 student
//             if (selected.length === 0) {
//                 Swal.fire({
//                     icon: 'warning',
//                     title: 'No Students Selected',
//                     text: 'Please select at least one student'
//                 });
//                 return;
//             }

//             // ✅ Confirmation for all actions
//             Swal.fire({
//                 title: 'Are you sure?',
//                 text: selected.length + " student(s) selected",
//                 icon: 'question',
//                 showCancelButton: true,
//                 confirmButtonText: 'Yes, Proceed'
//             }).then((result) => {
//                 if (result.isConfirmed) {
//                     form.action = action;
//                     form.submit();
//                 }
//             });

//         });

//     });

// });

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
<script>
document.addEventListener("DOMContentLoaded", function () {

    const url = new URL(window.location.href);

    // If college_id missing → inject it
    if (!url.searchParams.has('college_id')) {

        const defaultCollegeId = "{{ $defaultCollegeId }}";

        if (defaultCollegeId) {
            url.searchParams.set('college_id', defaultCollegeId);

            // Update URL WITHOUT reload
            window.history.replaceState({}, '', url);
        }
    }

});
</script>
<script>
function downloadWithFilters(baseUrl) {

    const params = new URLSearchParams(window.location.search);

    // safety fallback (in case still missing)
    if (!params.has('college_id')) {
        params.set('college_id', "{{ $defaultCollegeId }}");
    }

    window.location.href = baseUrl + '?' + params.toString();
}


document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('bulkForm');
    const openButton = document.getElementById('openMoveRoundModal');
    const confirmButton = document.getElementById('confirmMoveRound');
    const destination = document.getElementById('destination_round');
    const categoryWrapper = document.getElementById('finalCategoryWrapper');
    const category = document.getElementById('final_category');
    const countElement = document.getElementById('selectedRoundCount');
    const modalElement = document.getElementById('moveRoundModal');

    if (!form || !openButton || !confirmButton || !modalElement ||
        !destination || !categoryWrapper || !category) {
        return;
    }

    const moveModal = bootstrap.Modal.getOrCreateInstance(modalElement);

    function toggleFinalCategory() {
        const isFinal = destination.value === 'final';
        categoryWrapper.classList.toggle('d-none', !isFinal);
        category.disabled = !isFinal;
        category.required = isFinal;

        if (!isFinal) {
            category.value = '';
        }
    }

    destination.addEventListener('change', toggleFinalCategory);
    toggleFinalCategory();

    // Open modal only if at least one student is selected.
    openButton.addEventListener('click', function () {
        const selected = form.querySelectorAll(
            '.student-checkbox:checked:not(:disabled)'
        );

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student.',
                confirmButtonText: 'OK'
            });
            return;
        }

        countElement.textContent = selected.length;
        destination.value = '';
        category.value = '';
        toggleFinalCategory();
        moveModal.show();
    });

    // Validate destination and submit to the existing route.
    confirmButton.addEventListener('click', function () {
        const selected = form.querySelectorAll(
            '.student-checkbox:checked:not(:disabled)'
        );

        if (selected.length === 0) {
            moveModal.hide();

            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least one student.'
            });
            return;
        }

        if (!destination.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Destination',
                text: 'Please choose a round before proceeding.'
            });
            return;
        }

        if (destination.value === 'final' && !category.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Category',
                text: 'Please choose a category for Final Students.'
            });
            category.focus();
            return;
        }

        const roundLabels = {
            technical: 'Technical Round',
            machine_round: 'Machine Round',
            final: 'Final Students'
        };

        const selectedRound = destination.value;
        const count = selected.length;

        Swal.fire({
            icon: 'question',
            title: 'Confirm Move',
            text: 'Move ' + count + ' student(s) to ' +
                  roundLabels[selectedRound] +
                  (selectedRound === 'final'
                      ? ' (' + category.options[category.selectedIndex].text + ')'
                      : '') + '?',
            showCancelButton: true,
            confirmButtonText: 'Yes, Move',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            // Use the existing movement route.
            form.action = @json(route('admin.tests.move.round', $test->id));

            // Prevent another click while submitting.
            confirmButton.disabled = true;
            confirmButton.innerHTML = 'Moving...';

            form.submit();
        });
    });
});



document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('editScoreModal');
    const form = document.getElementById('editScoreForm');
    const scoreInput = document.getElementById('editScoreInput');
    const maxElement = document.getElementById('editScoreMaximum');
    const nameElement = document.getElementById('editScoreStudentName');
    const saveButton = document.getElementById('saveScoreBtn');
    const bulkButton = document.getElementById('updateSelectedScoresBtn');
    const bulkForm = document.getElementById('bulkForm');

    if (!modalElement || !form || !scoreInput) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    const maximumScore = Number(@json((int) $test->questions_count));
    let activeMaxScore = maximumScore;

    function clearHiddenStudentIds() {
        form.querySelectorAll('input[name="student_test_ids[]"]').forEach(input => input.remove());
    }

    function openScoreModal(actionUrl, score, label, selectedIds, maxScore) {
        clearHiddenStudentIds();
        form.action = actionUrl;
        activeMaxScore = Number(maxScore);
        (selectedIds || []).forEach(function (id) {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'student_test_ids[]';
            hidden.value = id;
            form.appendChild(hidden);
        });

        scoreInput.min = 0;
        scoreInput.max = activeMaxScore;
        scoreInput.step = 1;
        scoreInput.value = score;
        scoreInput.placeholder = 'Enter score (0–' + activeMaxScore + ')';
        maxElement.textContent = activeMaxScore;
        nameElement.textContent = label;
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fa fa-save"></i> Save Score';
        modal.show();
    }

    // Individual score update.
    document.querySelectorAll('.edit-score-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            openScoreModal(
                this.dataset.url,
                this.dataset.score || 0,
                this.dataset.name || 'Selected student',
                [],
                Number(this.dataset.max || maximumScore)
            );
        });
    });

    // Bulk score update for checked students.
    if (bulkButton && bulkForm) {
        bulkButton.addEventListener('click', function () {
            const selected = Array.from(
                bulkForm.querySelectorAll('.student-checkbox:checked:not(:disabled)')
            );

            if (selected.length === 0) {
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Students Selected',
                        text: 'Please select at least one student.'
                    });
                } else {
                    alert('Please select at least one student.');
                }
                return;
            }

            const ids = selected.map(checkbox => checkbox.value);
            const label = selected.length === 1
                ? 'Selected student: ' + (selected[0].closest('tr')?.cells[4]?.textContent.trim() || '')
                : selected.length + ' students selected';

            openScoreModal(
                @json(route('admin.tests.update-scores', ['test_id' => $test->id])),
                '',
                label,
                ids,
                maximumScore
            );
        });
    }

    scoreInput.addEventListener('input', function () {
        if (this.value !== '' && Number(this.value) > activeMaxScore) {
            this.value = activeMaxScore;
        }
    });

    form.addEventListener('submit', function (event) {
        const raw = scoreInput.value.trim();
        const value = Number(raw);

        if (raw === '' || !Number.isInteger(value) || value < 0) {
            event.preventDefault();
            if (window.Swal) {
                Swal.fire('Invalid Score', 'Enter a non-negative whole number.', 'error');
            } else {
                alert('Enter a non-negative whole number.');
            }
            return;
        }

        if (value > activeMaxScore) scoreInput.value = activeMaxScore;

        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        saveButton.disabled = false;
        saveButton.innerHTML = '<i class="fa fa-save"></i> Save Score';
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const button = document.getElementById('downloadCheckedStudents');

    if (!button) return;

    button.addEventListener('click', function () {
        const checked = Array.from(
            document.querySelectorAll(
                '#bulkForm .student-checkbox:checked:not(:disabled)'
            )
        );

        if (checked.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Students Selected',
                text: 'Please select at least 1 record.',
                reverseButtons: false
            });
            return;
        }

        Swal.fire({
            icon: 'question',
            title: 'Download Checked Students?',
            text: checked.length + ' student(s) selected.',
            showCancelButton: true,
            confirmButtonText: 'Yes, Download',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then(function (result) {
            if (!result.isConfirmed) return;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = @json(route('admin.tests.export.checked', $test->id));

            const csrf = document.querySelector(
                '#bulkForm input[name="_token"]'
            );

            if (!csrf) {
                Swal.fire('Error', 'CSRF token not found. Please refresh the page.', 'error');
                return;
            }

            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = csrf.value;
            form.appendChild(token);

            checked.forEach(function (checkbox) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'student_test_ids[]';
                input.value = checkbox.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            form.remove();
        });
    });
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const resetButton = document.getElementById('resetStudentChecks');

    if (!resetButton) return;

    resetButton.addEventListener('click', function () {
        document.querySelectorAll('.student-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });

        const selectAll = document.getElementById('selectAll');

        if (selectAll) {
            selectAll.checked = false;
            selectAll.indeterminate = false;
        }
    });
});
</script>
<script>
$(document).ready(function () {
    if (!$.fn.DataTable) {
        return;
    }

    if (!$.fn.DataTable.isDataTable('#resultsTable')) {
        $('#resultsTable').DataTable({
            autoWidth: false,
            scrollX: true
        });
    }
});
</script>

@endsection
