@extends('layouts.app')

@section('content')

<style>

/* 🔴 OVERDUE */
table.table-striped.dataTable tbody tr.row-50 > * {
background-color: red !important;
color: white!important;
}

/* 🟡 50-80 */
table.table-striped.dataTable tbody tr.row-80 > * {
background-color: yellow !important;
color: white!important;

}

/* 🟠 80-99 */
table.table-striped.dataTable tbody tr.row-90 > * {
background-color: orange !important;
color: white!important;

}

/* ⚪ 100 */
table.table-striped.dataTable tbody tr.row-100 > * {
background-color: white !important;
}

/* Hover */
table.table-striped.dataTable tbody tr.row-50:hover > * { background:red !important; }
table.table-striped.dataTable tbody tr.row-80:hover > * { background:yellow !important; }
table.table-striped.dataTable tbody tr.row-90:hover > * { background:orange !important; }
table.table-striped.dataTable tbody tr.row-100:hover > * { background:white !important; }

.filter-chip{
font-size:13px;
cursor:pointer;
}

</style>

<div class="container">

{{-- PAGE TITLE --}}
<div class="row mb-4">
<div class="col-md-6">
<h1 class="page_heading">Fee Statistics</h1>
</div>
</div>

@php

$fmt = new \NumberFormatter('en_IN', \NumberFormatter::DECIMAL);
$fmt->setAttribute(\NumberFormatter::FRACTION_DIGITS, 2);

$filtersApplied =
request()->filled('college_id') ||
request()->filled('course_id') ||
request()->filled('percent_range');

$selectedCollege = $colleges->firstWhere('id', request('college_id'));
$selectedCourse = $courses->firstWhere('id', request('course_id'));

@endphp


{{-- STAT CARDS --}}
<div class="row mb-4 align-items-stretch">

<div class="col-md-3">
<div class="card text-center shadow-sm h-100">
<div class="card-body">
<h6>Total Fees</h6>
<h4>Rs. {{ $fmt->format($totalFee) }}</h4>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm h-100 text-success">
<div class="card-body">
<h6>Paid Fees</h6>
<h4>Rs. {{ $fmt->format($paidFee) }}</h4>
<small>{{ $paidPercent }}%</small>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm h-100 text-danger">
<div class="card-body">
<h6>Pending Fees</h6>
<h4>Rs. {{ $fmt->format($pendingFee) }}</h4>
<small>{{ $pendingPercent }}%</small>
</div>
</div>
</div>

<div class="col-md-3">
<div class="card text-center shadow-sm h-100">
<div class="card-body">
<h6>Total Students</h6>
<h4>{{ $students->count() }}</h4>
</div>
</div>
</div>

</div>


{{-- ACTIVE FILTER CHIPS --}}
@if($filtersApplied)

<div class="mb-3">

<strong>Active Filters:</strong>

<div class="d-flex flex-wrap gap-2 mt-2">

@if(request('college_id') && $selectedCollege)
<a href="{{ request()->fullUrlWithQuery(['college_id'=>null]) }}"
class="badge bg-primary filter-chip text-decoration-none">
College: {{ $selectedCollege->college_name }} ✕
</a>
@endif

@if(request('course_id') && $selectedCourse)
<a href="{{ request()->fullUrlWithQuery(['course_id'=>null]) }}"
class="badge bg-success filter-chip text-decoration-none">
Technology: {{ $selectedCourse->course_name }} ✕
</a>
@endif

@if(request('percent_range'))
<a href="{{ request()->fullUrlWithQuery(['percent_range'=>null]) }}"
class="badge bg-warning text-dark filter-chip text-decoration-none">
Fee %: {{ request('percent_range') }} ✕
</a>
@endif

<a href="{{ route('fee.status') }}"
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

<form method="GET" id="filterForm" class="row g-2">

<div class="col-md-4">

<select name="college_id" class="form-control filterchange select2">

<option value="">--Colleges--</option>

@foreach($colleges as $college)

<option value="{{ $college->id }}"
{{ request('college_id') == $college->id ? 'selected' : '' }}>

{{ $college->college_name }}

</option>

@endforeach

</select>

</div>


<div class="col-md-4">

<select name="course_id" class="form-control filterchange">

<option value="">All Technologies</option>

@foreach($courses as $course)

<option value="{{ $course->id }}"
{{ request('course_id') == $course->id ? 'selected' : '' }}>

{{ $course->course_name }}

</option>

@endforeach

</select>

</div>


<div class="col-md-4">

<select name="percent_range" class="form-control filterchange">

<option value="">All Percentage</option>

<option value="upto50" {{ request('percent_range')=='upto50'?'selected':'' }}>Up to 50%</option>

<option value="50to80" {{ request('percent_range')=='50to80'?'selected':'' }}>50% - 80%</option>

<option value="80to99" {{ request('percent_range')=='80to99'?'selected':'' }}>80% - 99%</option>

<option value="100" {{ request('percent_range')=='100'?'selected':'' }}>100%</option>

</select>

</div>


<div class="col-md-4 d-flex gap-2">

<a href="{{ route('fee.status') }}"
class="btn btn-secondary">

Reset

</a>

<a href="{{ route('fee.status.export', request()->query()) }}"
class="btn btn-success">

Export

</a>

</div>

</form>

</div>

</div>

</div>

</div>



{{-- TABLE --}}
<table id="feeStatusTable" class="table table-bordered table-striped">

<thead>

<tr>
<th>#</th>
<th>Student</th>
<th>SNo</th>
<th>College</th>
<th>Contact No</th>
<th>Technology</th>
<th>Pending Fee(Rs.)</th>
<th>Paid %</th>
<th>Status</th>
</tr>

</thead>

<tbody>

@foreach($students as $student)

@php

$rowClass = '';

if ($student->paid_percentage <= 50) {
$rowClass = 'row-50';
}
elseif ($student->paid_percentage <= 80) {
$rowClass = 'row-80';
}
elseif ($student->paid_percentage < 100) {
$rowClass = 'row-90';
}
elseif ($student->paid_percentage == 100) {
$rowClass = 'row-100';
}

@endphp

<tr class="{{ $rowClass }}">
<td></td>
<td>{{ ucwords($student->student_name) }}</td>

<td>{{ $student->sno }}</td>

<td>{{ ucwords($student->collegeData->college_name ?? '-') }}</td>

<td>{{ $student->contact }}</td>

<td>{{ $student->course_name ?? '-' }}</td>

<td>{{ number_format($student->pending_fees) }}</td>

<td>
<span class="badge bg-success">
{{ $student->paid_percentage }}%
</span>
</td>

<td>

@if($student->fee_status === 'Fully Paid')
<span class="badge bg-success">Fully Paid</span>

@elseif($student->fee_status === 'Partially Paid')
<span class="badge bg-warning">Partially Paid</span>

@else
<span class="badge bg-danger">Not Paid</span>
@endif

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endsection



@push('styles')

<link rel="stylesheet"
href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

@endpush



@push('scripts')

<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
 -->


<script>

$(document).ready(function(){

var table = $('#feeStatusTable').DataTable({
pageLength:10,
lengthMenu:[10,15,20,25,50,100],
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



$(document).ready(function(){

$('.filterchange').on('change', function(){

$('#filterForm').submit();

$('#collapseFilters').collapse('hide');

});

});

</script>

@endpush