@extends('layouts.app')

@section('title', 'Students')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}

/* 🔴 OVERDUE */
table.table-striped.dataTable tbody tr.row-overdue > * {
    background-color: red !important;
    color: white!important;
}

/* 🟡 DUE TODAY */
table.table-striped.dataTable tbody tr.row-due-today > * {
    background-color: yellow !important;
}

/* Hover */
table.table-striped.dataTable tbody tr.row-overdue:hover > * {
    background-color: red !important;
}

table.table-striped.dataTable tbody tr.row-due-today:hover > * {
    background-color: yellow !important;
}
/* Amount slider base */
#amountSlider {
    height: 6px;
    margin-top: 8px;
    margin-bottom: 8px;
    position: relative;
    z-index: 10;
}
 

#amountSlider {
    height: 6px;
}

#amountSlider .noUi-handle {
    width: 14px;
    height: 14px;
    right: -7px;
    top: -5px;
    border-radius: 50%;
}

#amountSlider .noUi-handle::before,
#amountSlider .noUi-handle::after {
    display: none;
}

/* =========================
   MOBILE STYLE TOGGLE
========================= */

.switch {
    position: relative;
    display: inline-block;
    width: 42px;
    height: 22px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background-color: #ccc;
    transition: .3s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

.switch input:checked + .slider {
    background-color: #198754;
}

.switch input:checked + .slider:before {
    transform: translateX(20px);
}
 </style>

 @php
    $role = (int) auth()->user()->role;
@endphp
 {{-- UNIVERSAL POPUP CONTAINER --}}
 
<div class="container mt-4">
   
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Dropout Students</h1>
        </div>
</div>

{{-- Search / Filter Form --}}
@php

$filtersApplied = collect([
request('college_name'),
request('start_date'),
request('end_date'),
request('status'),
request('technology'),
request('part_time_offer'),
request('placement_offer'),
request('pg_offer'),
request('is_intern'),
request('is_online'),
request('fee_filter'),
request('gender'),
request('limit'),
request('amount_min'),
request('amount_max')
])->filter()->isNotEmpty();

@endphp


{{-- ACTIVE FILTER CHIPS --}}
@if($filtersApplied)

<div class="mb-3">

<strong>Active Filters:</strong>

<div class="d-flex flex-wrap gap-2 mt-2">
{{-- College --}}
@if(request('college_name'))
@php
$collegeName = $colleges->firstWhere('id', request('college_name'))->FullName ?? request('college_name');
@endphp

<a href="{{ request()->fullUrlWithQuery(['college_name'=>null]) }}"
class="badge bg-primary text-decoration-none">
College name : {{ $collegeName }} ✕
</a>
@endif


{{-- Technology --}}
@if(request('technology'))
@php
$techName = $courses->firstWhere('id', request('technology'))->course_name ?? request('technology');
@endphp

<a href="{{ request()->fullUrlWithQuery(['technology'=>null]) }}"
class="badge bg-primary text-decoration-none">
Technology : {{ $techName }} ✕
</a>
@endif
@foreach(request()->except(['page','college_name','technology']) as $key => $value)

@if($value !== '' && $value !== null)
@php
$displayValue = $value;

/* Convert 0/1 to Yes/No for specific filters */
if(in_array($key, [
    'part_time_offer',
    'placement_offer',
    'pg_offer',
    'is_intern',
    'is_online'
])){
    $displayValue = $value == 1 ? 'Yes' : 'No';
}
if($key == 'is_online'){
    $displayValue = $value == 1 ? 'Online' : 'Offline';
}
@endphp
<a href="{{ request()->fullUrlWithQuery([$key => null]) }}"
class="badge bg-primary text-decoration-none">
{{ ucfirst(str_replace('_',' ',$key)) }} : {{ $displayValue }} ✕
</a>

@endif

@endforeach

<a href="{{ route('students.index') }}"
class="badge bg-danger text-decoration-none">
Clear All ✕
</a>

</div>

</div>

@endif



{{-- FILTER PANEL --}}
<div class="accordion mb-3" id="filtersAccordion">

<div class="accordion-item">

<h2 class="accordion-header">

<button class="accordion-button collapsed"
type="button"
data-bs-toggle="collapse"
data-bs-target="#collapseFilters">

<b>Filters</b>

</button>

</h2>

<div id="collapseFilters"
class="accordion-collapse collapse"
data-bs-parent="#filtersAccordion">

<div class="accordion-body">


<form method="GET" id="filterForm" class="mb-4">

<div class="row g-2">


{{-- College --}}
<div class="col-md-3 col-6">

<select name="college_name"
class="form-control collegeName filterchange select2">

<option value="">--College--</option>

@foreach($colleges as $college)

<option value="{{ $college->id }}"
{{ request('college_name') == $college->id ? 'selected' : '' }}>

{{ $college->FullName }}

</option>

@endforeach

</select>

</div>



{{-- Date Range --}}
<div class="col-md-3">

<div class="input-group">

<input type="date"
name="start_date"
class="form-control filterchange"
value="{{ request('start_date') }}">

<input type="date"
name="end_date"
class="form-control filterchange"
value="{{ request('end_date') }}">

</div>

</div>



{{-- Status --}}
<div class="col-md-2 col-6">

<select name="status"
class="form-control statusData filterchange">

<option value="">--Status--</option>

@foreach($student_status as $s)

<option value="{{ $s->status }}"
{{ request('status') == $s->status ? 'selected' : '' }}>

{{ $s->name }}

</option>

@endforeach

</select>

</div>



{{-- Technology --}}
<div class="col-md-2 col-6">

<select name="technology"
class="form-control technology filterchange">

<option value="">--Technology--</option>

@foreach($courses as $course)

<option value="{{ $course->id }}"
{{ request('technology') == $course->id ? 'selected' : '' }}>

{{ $course->course_name }}

</option>

@endforeach

</select>

</div>



{{-- Part Time --}}
<div class="col-md-2 col-6">

<select name="part_time_offer"
class="form-control filterchange">

<option value="">--Part-Time Offer--</option>

<option value="1"
{{ request('part_time_offer')==='1'?'selected':'' }}>
Yes
</option>

<option value="0"
{{ request('part_time_offer')==='0'?'selected':'' }}>
No
</option>

</select>

</div>



{{-- Placement --}}
<div class="col-md-2 col-6">

<select name="placement_offer"
class="form-control filterchange">

<option value="">--Placement Offer--</option>

<option value="1"
{{ request('placement_offer')==='1'?'selected':'' }}>
Yes
</option>

<option value="0"
{{ request('placement_offer')==='0'?'selected':'' }}>
No
</option>

</select>

</div>



{{-- PG Offer --}}
<div class="col-md-2">

<select name="pg_offer"
class="form-control filterchange">

<option value="">--PG Offer--</option>

<option value="1"
{{ request('pg_offer')==='1'?'selected':'' }}>
Yes
</option>

<option value="0"
{{ request('pg_offer')==='0'?'selected':'' }}>
No
</option>

</select>

</div>



{{-- Intern --}}
<div class="col-md-2">

<select name="is_intern"
class="form-control filterchange">

<option value="">--Is Intern--</option>

<option value="1"
{{ request('is_intern')==='1'?'selected':'' }}>
Yes
</option>

<option value="0"
{{ request('is_intern')==='0'?'selected':'' }}>
No
</option>

</select>

</div>



{{-- Study Mode --}}
<div class="col-md-2">

<select name="is_online"
class="form-control filterchange">

<option value="">--Study Mode--</option>

<option value="0"
{{ request('is_online')==='0'?'selected':'' }}>
Offline
</option>

<option value="1"
{{ request('is_online')==='1'?'selected':'' }}>
Online
</option>

</select>

</div>

{{-- Confirmation Sent --}}
    <div class="col-md-2">
        <select name="confirmation_sent" class="form-control filterchange">
            <option value="">--Confirmation Sent--</option>
            <option value="1" {{ request('confirmation_sent') === '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ request('confirmation_sent') === '0' ? 'selected' : '' }}>No</option>
        </select>
    </div>



{{-- Fee Filter --}}
<div class="col-md-3">

<select name="fee_filter"
class="form-control filterchange">

<option value="">-- Fee Related Filter --</option>

<option value="not_paid"
{{ request('fee_filter')=='not_paid'?'selected':'' }}>
Not Paid
</option>

<option value="completed"
{{ request('fee_filter')=='completed'?'selected':'' }}>
Completed Fees
</option>

<option value="pending"
{{ request('fee_filter')=='pending'?'selected':'' }}>
Pending Fees
</option>

<option value="pending_high"
{{ request('fee_filter')=='pending_high'?'selected':'' }}>
Pending High → Low
</option>

<option value="pending_low"
{{ request('fee_filter')=='pending_low'?'selected':'' }}>
Pending Low → High
</option>

<option value="fees_high"
{{ request('fee_filter')=='fees_high'?'selected':'' }}>
Fees High → Low
</option>

<option value="fees_low"
{{ request('fee_filter')=='fees_low'?'selected':'' }}>
Fees Low → High
</option>

</select>

</div>



{{-- Gender --}}
<div class="col-md-2">

<select name="gender"
class="form-control filterchange">

<option value="">--Gender--</option>

<option value="male"
{{ request('gender')=='male'?'selected':'' }}>
Male
</option>

<option value="female"
{{ request('gender')=='female'?'selected':'' }}>
Female
</option>

</select>

</div>

<div class="col-md-2">
    <input type="date"
        name="next_due_date"
        class="form-control filterchange"
        value="{{ request('next_due_date') }}">
</div>


{{-- Regsiteration Fee --}}
<div class="col-md-2">

<input type="number"
name="registration_fee"
class="form-control filterchangetext"
placeholder="Regsiteration Fee"
value="{{ request('registration_fee') }}">

</div>

{{-- Limit --}}
<div class="col-md-1">

<input type="number"
name="limit"
class="form-control filterchangetext"
placeholder="limit"
value="{{ request('limit') }}">

</div>




{{-- Amount Range --}}
<div class="col-md-4">

<label class="form-label fw-bold">
Amount Range
</label>

<div id="amountSlider" class="mb-2"></div>

<div class="d-flex gap-2">

<input type="text"
name="amount_min"
id="amountMin"
class="form-control filterchange"
value="{{ request('amount_min',0) }}">

<input type="text"
name="amount_max"
id="amountMax"
class="form-control filterchange"
value="{{ request('amount_max',200000) }}">

</div>

</div>



</div>


{{-- BUTTONS --}}
<div class="mt-2">

<a href="{{ route('dropout-students.index') }}"
class="btn btn-secondary">

Reset

</a>

 <a href="{{ route('dropout.export.excel', request()->query()) }}"
class="btn btn-primary">Download Excel</a>

<!-- <a href="{{ route('students.export.excel', array_merge(request()->query(), ['without_fee' => true])) }}"
class="btn btn-primary">Without Fee Download Excel</a> -->
 

</div>

</form>


</div>

</div>

</div>

</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
<style></style>

<div class="desktop-view">
<div class="table-responsive">
    <table id="studentsTable" class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th class="text-center" width="100px">#</th>
                <th class="text-center" width="100px">Serial No.</th>
                <th class="text-center">Name</th>
                <th class="text-center">Father Name</th>
                <th class="text-center">Gender</th>
                <th class="text-center" width="100px">Session</th>
                <th class="text-center" width="180px">College</th>
                <th class="text-center">Contact</th>
                <th class="text-center">Email</th>
                <th class="text-center">Status</th>
                <th class="text-center">Technology</th>
                <th class="text-center">Total Fees(Rs.)</th>
                <th class="text-center">Reg Fees(Rs.)</th>
                <th class="text-center">Paid Fees(Rs. )</th>
                <th class="text-center">Pending Fees(Rs.)</th>
                <th class="text-center" width="100px">Pending Fees Due Date</th>
                <!-- <th class="text-center">Department</th> -->
                <th class="text-center" width="100px">Registered date</th>
                <th class="text-center">Duration</th>
                <th class="text-center" width="100px">Start Date</th>
                <th class="text-center" width="100px">End Date</th>
                <th class="text-center">Part-Time Job Offer</th>
                <th class="text-center">Placement Offer</th>
                <th class="text-center">PG Offer</th>
                <th class="text-center">Is Intern</th>
                <th class="text-center">Study Mode</th>
                <th class="text-center">Email Count</th>
                <th class="text-center">Receipt Count</th>
                <th class="text-center">Created At</th>
                <th class="text-center">Updated At</th>
                <!-- <th width="180px" class="text-center">Action</th> -->
            </tr>
        </thead>
        <tbody>
            
            @foreach ($students as $student)
           @php
    $rowClass = '';

    if ($student->next_due_date && $student->pending_fees > 0) {
        $pending_amnt = $student->pending_fees;
        $nextDue = \Carbon\Carbon::createFromFormat('Y-m-d', $student->next_due_date)->startOfDay();
        $today   = \Carbon\Carbon::today();

        if ($nextDue->lt($today)) {
            $rowClass = 'row-overdue';
        } elseif ($nextDue->eq($today)) {
            $rowClass = 'row-due-today';
        }
    }
@endphp

<tr class="{{ $rowClass }}">
<td><input type="checkbox" class="record_checked" value="{{ $student->id }}" data-email="{{ $student->email_count_confirmation }}"
       data-receipt="{{ $student->count_receipt_download }}" data-pending_fees="{{ $student->pending_fees }}"></td>
                <td></td>
                <td>{{ $student->sno }}</td>
                <td>{{ $student->student_name }}</td>
                <td>{{ $student->f_name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->sessionData->session_name ?? '-' }}</td>
                <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                <td>{{ $student->contact }}</td>
                <td>{{ $student->email_id }}</td>
                <td><span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }}">{{ $student->status }}</span></td>
                 <!-- <td>{{ $student->courseData->course_name ?? '-' }}</td> -->
                 <td style="width:150px;">{{ $student->course_name }}</td>

                <td>{{ $student->total_fees }}</td>
                <td>{{ $student->reg_fees }}</td>
                <td>{{ $student->paid_fees }}</td>
                <td>{{ $student->pending_fees }}</td>
                <td>
                    {{ $student->next_due_date ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') : '-' }}
                </td>

                <!-- <td>{{ $student->department }}</td> -->
                <td>{{ \Carbon\Carbon::parse($student->join_date)->format('d M Y') }}</td>
                
                <!-- <td>{{ $student->durationData->name ?? '-' }}</td> -->
                <td>{{ $student->sessionData->session_display_name ?? '-' }}</td>
                <td>{{ \Carbon\Carbon::parse($student->start_date)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($student->end_date)->format('d M Y') }}</td>
                <td class="text-center">
                    <span class="badge bg-{{ $student->part_time_offer ? 'success' : 'secondary' }}">
                        {{ $student->part_time_offer ? 'Yes' : 'No' }}
                    </span>
                </td>

                <td class="text-center">
                    <span class="badge bg-{{ $student->placement_offer ? 'success' : 'secondary' }}">
                        {{ $student->placement_offer ? 'Yes' : 'No' }}
                    </span>
                </td>

                <td class="text-center">
                    <span class="badge bg-{{ $student->pg_offer ? 'success' : 'secondary' }}">
                        {{ $student->pg_offer ? 'Yes' : 'No' }}
                    </span>
                </td>

                <td class="text-center">
                    <span class="badge bg-{{ $student->is_intern ? 'success' : 'secondary' }}">
                        {{ $student->is_intern ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="text-center">
                    <span class="badge bg-{{ $student->is_online ? 'success' : 'secondary' }}">
                        {{ $student->is_online ? 'Online' : 'Offline' }}
                    </span>
                </td>

                <td>{{ $student->email_count_confirmation ?? 0 }}</td>
                <td>{{ $student->count_receipt_download ?? 0 }}</td>
                <td>
                    {{ $student->created_at ? \Carbon\Carbon::parse($student->created_at)->format('d M Y') : '-' }}
                </td>
                <td>
                    {{ $student->updated_at ? \Carbon\Carbon::parse($student->updated_at)->format('d M Y') : '-' }}
                </td>
        <!-- <td class="text-center">
    <div class="mb-2"> -->
          
 
        {{-- Edit --}}
        <!-- <a href="{{ route('students.edit',$student->id) }}" class="btn btn-sm edit_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
            <i class="fa fa-edit"></i>
        </a> -->

        {{-- Delete --}}
       <!--  <form action="{{ route('students.destroy',$student->id) }}" method="POST" style="display:inline-block;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student"  data-swal-confirm="Delete this student?">
                <i class="fa fa-trash"></i>
            </button>
        </form> -->

 
   <!--  </div>

    
</td> -->
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
 
{{-- Buttons for selected students --}}
 

<div class="mt-3 tble-bts">

    <button id="movetoConfirmation" class="btn btn-primary">Move to Confimration</button>
    <button id="movetoCertificate" class="btn btn-success">Move to Certificate</button>
</div>

</div>

{{-- Hidden form for bulk issuing (submits like single-row form) --}}
<input type="hidden" id="isInternshipHidden">

 
 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">

{{-- Move students to confirmation--}}
<form id="MoveStudentToConfirmation" method="POST" action="{{ route('dropout.move_to_confirmation') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="movetoconfirmation">
</form>

{{-- Move students to Certification--}}
<form id="MoveStudentToCertificate" method="POST" action="{{ route('dropout.move_to_certification') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="movetocertification">
</form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('amountSlider');
    const feeFilter = document.querySelector('select[name="fee_filter"]');

    const minInput = document.getElementById('amountMin');
    const maxInput = document.getElementById('amountMax');

    const MIN = 0;
    const MAX = 200000;

    const startMin = Number(minInput.value || MIN);
    const startMax = Number(maxInput.value || MAX);

    /* ----------------------------
       CREATE SLIDER (NO SNAP)
    -----------------------------*/
    noUiSlider.create(slider, {
        start: [startMin, startMax],
        connect: true,
        step: 1,
        behaviour: 'tap',
        range: {
            min: MIN,
            max: MAX
        },
        format: {
            to: v => Math.round(v),
            from: v => Number(v)
        }
    });

    /* ----------------------------
       SLIDER → INPUT SYNC
    -----------------------------*/
    slider.noUiSlider.on('update', function (values) {
        minInput.value = values[0];
        maxInput.value = values[1];
    });

    /* ----------------------------
       INPUT SANITIZER
    -----------------------------*/
    function sanitize(value) {
        value = parseInt(String(value).replace(/\D/g, ''), 10);
        if (isNaN(value)) value = MIN;
        return Math.min(Math.max(value, MIN), MAX);
    }

    /* ----------------------------
       INPUT → SLIDER SYNC
    -----------------------------*/
    function syncInputsToSlider() {
        let min = sanitize(minInput.value);
        let max = sanitize(maxInput.value);

        if (min > max) min = max;

        minInput.value = min;
        maxInput.value = max;

        slider.noUiSlider.set([min, max]);
    }

    /* ----------------------------
       INPUT EVENTS
    -----------------------------*/
    [minInput, maxInput].forEach(input => {

        // allow only digits while typing
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '');
        });

        // ENTER → update slider
        input.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                e.preventDefault();
                syncInputsToSlider();
            }
        });

        // blur → update slider
        input.addEventListener('blur', syncInputsToSlider);
    });

    /* ----------------------------
       ENABLE SLIDER ONLY FOR
       SPECIFIC FEE FILTERS
    -----------------------------*/
    const amountEnabledFilters = [
        'pending_high',
        'pending_low',
        'fees_high',
        'fees_low'
    ];
    
    function toggleSlider() {
    const value = feeFilter.value;
    const enabled = amountEnabledFilters.includes(value);

    if (enabled) {
        // enable slider
        slider.noUiSlider.enable();
        slider.style.opacity = 1;
        slider.style.pointerEvents = 'auto';

        // enable inputs
        minInput.disabled = false;
        maxInput.disabled = false;
    } else {
        // disable slider
        slider.noUiSlider.disable();
        slider.style.opacity = 0.4;
        slider.style.pointerEvents = 'none';

        // disable inputs
        minInput.disabled = true;
        maxInput.disabled = true;

        // reset values
        slider.noUiSlider.set([MIN, MAX]);
        minInput.value = MIN;
        maxInput.value = MAX;
    }
}

     

    feeFilter.addEventListener('change', toggleSlider);
    toggleSlider(); // run on page load

});
</script>
 

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<script>
$(document).ready(function () {
    // =========================
    // Store selected IDs globally
    // =========================
    let selectedStudents = [];

    $('.confirm-single-form').on('submit', function () {
        let isInternship = $('#isInternship').is(':checked') ? 1 : 0;
        $(this).find('.isInternshipHiddenSingle').val(isInternship);
    });

var savedPage = sessionStorage.getItem('students_confirmation_page');

var pageLength = 10;
$.fn.dataTable.ext.pager.numbers_length = 12;
// Reset page index on search submit
$('form[action*="dropout-students"]').on('submit', function () {
    sessionStorage.removeItem('students_confirmation_page');
});
$('a[href="{{ route('dropout-students.index') }}"]').on('click', function () {
    sessionStorage.removeItem('students_confirmation_page');
});

    
    var table = $('#studentsTable').DataTable({
        "pageLength": pageLength,
        "displayStart": savedPage ? (savedPage * pageLength) : 0,
        'pagingType': "full_numbers", 
        "lengthMenu": [10,15,20, 25, 50, 100],
        "scrollX": true,
        columnDefs: [
            {
                targets: 1, // first column
                searchable: false,
                orderable: false
            }
        ]
 

    });
 


     // ✅ Save page whenever page changes
    table.on('page.dt', function () {
        sessionStorage.setItem(
            'students_confirmation_page',
            table.page()
        );
    });
 

// 27 july start

    function syncCheckAll() {
        var currentPageCheckboxes = $(table.rows({ page: 'current' }).nodes())
            .find('.record_checked:not(:disabled)');
        var checkedCount = currentPageCheckboxes.filter(':checked').length;

        $('#checkAll').prop(
            'checked',
            currentPageCheckboxes.length > 0 && checkedCount === currentPageCheckboxes.length
        );

        console.log('selectedStudents', selectedStudents);
        console.log('getSelectedIds()', getSelectedIds());
    }

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });

        syncCheckAll();
    }).draw();

    $('#checkAll').on('click', function (e) {
        e.stopPropagation();
    });

    $('#checkAll').on('change', function () {
        const checked = this.checked;

        $(table.rows({ page: 'current' }).nodes()).find('.record_checked').each(function () {
            if (this.disabled) {
                this.checked = false; // FORCE UNCHECK
            } else {
                this.checked = checked;
            }
        });

        syncCheckAll();


    });

    $('#studentsTable').on('change', '.record_checked', syncCheckAll);
// 27 july end


    function getSelectedIds() {

        $('.record_checked:checked').each(function() {

            let id = $(this).val();

            if (!selectedStudents.includes(id)) {
                selectedStudents.push(id);
            }

        });

        return selectedStudents;
    }



    $('#movetoConfirmation').click(function() {
        var ids = getSelectedIds();
console.log('movetoConfirmation');
        // if(ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }
        pageBulkConfirm(ids, 'Move to Confirmation?', function () {
        // if(confirm('Make Interns?')) {
            // $('#deleteIds').val(ids);
            $('#movetoconfirmation').val(JSON.stringify(ids));
            $('#MoveStudentToConfirmation').submit();
         });
    });

    $('#movetoCertificate').click(function() {
        var ids = getSelectedIds();
console.log('movetoCertificate');
        // if(ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }
        pageBulkConfirm(ids, 'Move to Certificate?', function () {
        // if(confirm('Make Interns?')) {
            // $('#deleteIds').val(ids);
            $('#movetocertification').val(JSON.stringify(ids));
            $('#MoveStudentToCertificate').submit();
         });
    });

  
  

});

// ===============================
// BULK CONFIRM HELPER (PAGE ONLY)
// ===============================
function pageBulkConfirm(ids, message, onConfirm, emptyText = 'Select at least one student') {

    if (!ids || ids.length === 0) {
        Swal.fire({
            icon: 'warning',
            text: emptyText
        });
        return;
    }

    Swal.fire({
        title: 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {
        if (result.isConfirmed && typeof onConfirm === 'function') {
            onConfirm();
        }
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

$(document).on('change', '.certificateToggle', function () {

    let toggle = $(this);

    let id = toggle.data('id');

    let status = toggle.is(':checked') ? 1 : 0;

    $.ajax({

        url: "{{ route('students.toggleConfirmationSent') }}",

        type: "POST",

        data: {
            _token: "{{ csrf_token() }}",
            id: id,
            status: status
        },

        success: function (response) {

            toastr.success(response.message);

        },

        error: function () {

            // revert toggle if failed
            toggle.prop('checked', !status);

            toastr.error('Failed to update status');

        }

    });

});

</script>

@endpush
