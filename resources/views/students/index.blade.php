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


 </style>

 @php
    $role = (int) auth()->user()->role;
@endphp
 {{-- UNIVERSAL POPUP CONTAINER --}}
 
<div class="container mt-4">
   
    <div class="row mb-4">
        <div class="col-md-2">
            <h1 class="page_heading">Students</h1>
        </div>

    <div class=" col-md-10 d-flex justify-content-end gap-2">
        <a href="{{ route('students.create') }}" class="btn" 
           style="background-color: #6b51df; color: #fff;">+ Add Student</a>

        <a href="{{ route('students.importForm') }}" class="btn btn-primary" style="background-color: #6b51df; color: #fff;">
            Import Students
        </a>
         <a href="{{ route('students.importFeeForm') }}" class="btn btn-primary" style="background-color: #6b51df; color: #fff;">
            Update Fee
        </a>
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
<div class="col-md-2 col-6">

<select name="college_name"
class="form-control collegeName filterchange">

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



{{-- Fee Filter --}}
<div class="col-md-3">

<select name="fee_filter"
class="form-control filterchange">

<option value="">-- Fee Related Filter --</option>

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

<a href="{{ route('students.index') }}"
class="btn btn-secondary">

Reset

</a>

<a href="{{ route('students.export.excel', request()->query()) }}"
class="btn btn-primary">

Download Excel

</a>

<button type="button"
id="copySelected"
class="btn btn-success">

Copy to Session

</button>

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
                <th width="180px" class="text-center">Action</th>
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
        <td class="text-center">
    <div class="mb-2">
        <a href="{{ route('students.idcard', $student) }}"
           class="btn btn-sm"
           data-bs-toggle="tooltip"
           title="Download ID Card">
           <i class="fas fa-id-card"></i>
        </a>
        {{-- Issue --}}

        <!-- <form action="{{ route('students.confirmStudent', $student->id) }}" method="POST" style="display:inline-block;"  class="confirm-single-form" >
            @csrf
            <input type="hidden" name="is_internship" class="isInternshipHiddenSingle">
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate"
                data-swal-confirm="Send certificate to {{ $student->email_id }}?">
                <i class="fa-solid fa-file-lines"></i>
            </button>
        </form>
 -->


        {{-- Edit --}}
        <a href="{{ route('students.edit',$student->id) }}" class="btn btn-sm edit_btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
            <i class="fa fa-edit"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('students.destroy',$student->id) }}" method="POST" style="display:inline-block;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student"  data-swal-confirm="Delete this student?">
                <i class="fa fa-trash"></i>
            </button>
        </form>
    </div>

    <span class="badge bg-light text-dark">Email count: {{ $student->email_count_confirmation }}</span>
</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
<div class="mobile-view">
<div class="accordion table-accordian" id="accordionExample">
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
      <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">Vikas Mehra</button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <div class="table-card-view">
			<div class="table-card-wd">
				<div class="table-card">
					<div class="tble-hd">
						<div class="tbl-status">Joined</div>
						<h2><input type="checkbox" id="checkAll1" /> <span>Serial No: 123432</span></h2>
					</div>
					<div class="tble-rep">
						<h3>Personal detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Name:</span> <span>Vikas Mehra</span></li>
							<li><span class="sp-dtl">Father Name:</span> <span>Vikram</span></li>
							<li><span class="sp-dtl">Gender:</span> <span>Male</span></li>
							<li><span class="sp-dtl">Contact:</span> <span>9127150108</span></li>
							<li><span class="sp-dtl">Email:</span> <span>vikas.mehra@yopmail.com</span></li>
							<li><span class="sp-dtl">College:</span> <span>Royal Professional College</span></li>
						</ul>
					</div>
					<div class="tble-rep">
						<h3>Course detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Session:</span> <span>8 Week Session</span></li>
							<li><span class="sp-dtl">Technology:</span> <span>Python/AI</span></li>
							<li><span class="sp-dtl">Total Fees:</span> <span>10000.00</span></li>
							<li><span class="sp-dtl">Reg Fees:</span> <span>1000.00</span></li>
							<li><span class="sp-dtl">Pending Fees:</span> <span>9000.00</span></li>
							<li><span class="sp-dtl">Next Due Date:</span> <span></span></li>
							<li><span class="sp-dtl">Date of Joining:</span> <span>13 Dec 2025</span></li>
							<li><span class="sp-dtl">Duration:</span> <span></span></li>
							<li><span class="sp-dtl">Start Date:</span> <span>19 Dec 2025</span></li>
							<li><span class="sp-dtl">End Date:</span> <span>19 Dec 2025</span></li>
						</ul>
					</div>
					<div class="tble-action">
						<div class="tble-row">
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm view-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate" >
										<i class="fa-solid fa-file-lines"></i>
									</button>
								</form>
							</div>
							<div class="tb-cl-4">
								<a href="#" class="btn btn-sm edit-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student" >
									<i class="fa fa-edit"></i>
								</a>
							</div>
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm delete-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" >
										<i class="fa fa-trash"></i>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="table-card-wd">
				<div class="table-card">
					<div class="tble-hd">
						<div class="tbl-status">Joined</div>
						<h2><input type="checkbox" id="checkAll2" /> <span>Serial No: 123432</span></h2>
					</div>
					<div class="tble-rep">
						<h3>Personal detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Name:</span> <span>Vikas Mehra</span></li>
							<li><span class="sp-dtl">Father Name:</span> <span>Vikram</span></li>
							<li><span class="sp-dtl">Gender:</span> <span>Male</span></li>
							<li><span class="sp-dtl">Contact:</span> <span>9127150108</span></li>
							<li><span class="sp-dtl">Email:</span> <span>vikas.mehra@yopmail.com</span></li>
							<li><span class="sp-dtl">College:</span> <span>Royal Professional College</span></li>
						</ul>
					</div>
					<div class="tble-rep">
						<h3>Course detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Session:</span> <span>8 Week Session</span></li>
							<li><span class="sp-dtl">Technology:</span> <span>Python/AI</span></li>
							<li><span class="sp-dtl">Total Fees:</span> <span>10000.00</span></li>
							<li><span class="sp-dtl">Reg Fees:</span> <span>1000.00</span></li>
							<li><span class="sp-dtl">Pending Fees:</span> <span>9000.00</span></li>
							<li><span class="sp-dtl">Next Due Date:</span> <span></span></li>
							<li><span class="sp-dtl">Date of Joining:</span> <span>13 Dec 2025</span></li>
							<li><span class="sp-dtl">Duration:</span> <span></span></li>
							<li><span class="sp-dtl">Start Date:</span> <span>19 Dec 2025</span></li>
							<li><span class="sp-dtl">End Date:</span> <span>19 Dec 2025</span></li>
						</ul>
					</div>
					<div class="tble-action">
						<div class="tble-row">
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm view-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate" >
										<i class="fa-solid fa-file-lines"></i>
									</button>
								</form>
							</div>
							<div class="tb-cl-4">
								<a href="#" class="btn btn-sm edit-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student" >
									<i class="fa fa-edit"></i>
								</a>
							</div>
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm delete-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" >
										<i class="fa fa-trash"></i>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingTwo">
      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">Accordion Item #2</button>
    </h2>
    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
      <div class="accordion-body">
        <div class="table-card-view">
			<div class="table-card-wd">
				<div class="table-card">
					<div class="tble-hd">
						<div class="tbl-status">Joined</div>
						<h2><input type="checkbox" id="checkAll2" /> <span>Serial No: 123432</span></h2>
					</div>
					<div class="tble-rep">
						<h3>Personal detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Name:</span> <span>Vikas Mehra</span></li>
							<li><span class="sp-dtl">Father Name:</span> <span>Vikram</span></li>
							<li><span class="sp-dtl">Gender:</span> <span>Male</span></li>
							<li><span class="sp-dtl">Contact:</span> <span>9127150108</span></li>
							<li><span class="sp-dtl">Email:</span> <span>vikas.mehra@yopmail.com</span></li>
							<li><span class="sp-dtl">College:</span> <span>Royal Professional College</span></li>
						</ul>
					</div>
					<div class="tble-rep">
						<h3>Course detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Session:</span> <span>8 Week Session</span></li>
							<li><span class="sp-dtl">Technology:</span> <span>Python/AI</span></li>
							<li><span class="sp-dtl">Total Fees:</span> <span>10000.00</span></li>
							<li><span class="sp-dtl">Reg Fees:</span> <span>1000.00</span></li>
							<li><span class="sp-dtl">Pending Fees:</span> <span>9000.00</span></li>
							<li><span class="sp-dtl">Next Due Date:</span> <span></span></li>
							<li><span class="sp-dtl">Date of Joining:</span> <span>13 Dec 2025</span></li>
							<li><span class="sp-dtl">Duration:</span> <span></span></li>
							<li><span class="sp-dtl">Start Date:</span> <span>19 Dec 2025</span></li>
							<li><span class="sp-dtl">End Date:</span> <span>19 Dec 2025</span></li>
						</ul>
					</div>
					<div class="tble-action">
						<div class="tble-row">
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm view-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate" >
										<i class="fa-solid fa-file-lines"></i>
									</button>
								</form>
							</div>
							<div class="tb-cl-4">
								<a href="#" class="btn btn-sm edit-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student" >
									<i class="fa fa-edit"></i>
								</a>
							</div>
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm delete-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" >
										<i class="fa fa-trash"></i>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="table-card-wd">
				<div class="table-card">
					<div class="tble-hd">
						<div class="tbl-status">Joined</div>
						<h2><input type="checkbox" id="checkAll1" /> <span>Serial No: 123432</span></h2>
					</div>
					<div class="tble-rep">
						<h3>Personal detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Name:</span> <span>Vikas Mehra</span></li>
							<li><span class="sp-dtl">Father Name:</span> <span>Vikram</span></li>
							<li><span class="sp-dtl">Gender:</span> <span>Male</span></li>
							<li><span class="sp-dtl">Contact:</span> <span>9127150108</span></li>
							<li><span class="sp-dtl">Email:</span> <span>vikas.mehra@yopmail.com</span></li>
							<li><span class="sp-dtl">College:</span> <span>Royal Professional College</span></li>
						</ul>
					</div>
					<div class="tble-rep">
						<h3>Course detail:</h3>
						<ul class="tbl-list">
							<li><span class="sp-dtl">Session:</span> <span>8 Week Session</span></li>
							<li><span class="sp-dtl">Technology:</span> <span>Python/AI</span></li>
							<li><span class="sp-dtl">Total Fees:</span> <span>10000.00</span></li>
							<li><span class="sp-dtl">Reg Fees:</span> <span>1000.00</span></li>
							<li><span class="sp-dtl">Pending Fees:</span> <span>9000.00</span></li>
							<li><span class="sp-dtl">Next Due Date:</span> <span></span></li>
							<li><span class="sp-dtl">Date of Joining:</span> <span>13 Dec 2025</span></li>
							<li><span class="sp-dtl">Duration:</span> <span></span></li>
							<li><span class="sp-dtl">Start Date:</span> <span>19 Dec 2025</span></li>
							<li><span class="sp-dtl">End Date:</span> <span>19 Dec 2025</span></li>
						</ul>
					</div>
					<div class="tble-action">
						<div class="tble-row">
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm view-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate" >
										<i class="fa-solid fa-file-lines"></i>
									</button>
								</form>
							</div>
							<div class="tb-cl-4">
								<a href="#" class="btn btn-sm edit-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student" >
									<i class="fa fa-edit"></i>
								</a>
							</div>
							<div class="tb-cl-4">
								<form>
									<button type="submit" class="btn btn-sm delete-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" >
										<i class="fa fa-trash"></i>
									</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
      </div>
    </div>
  </div>
</div>   
</div>
{{-- Buttons for selected students --}}
<div class="form-check mb-2">
    <input type="checkbox" class="form-check-input" id="isInternship" name="is_internship">
    <label class="form-check-label" for="isInternship">
        Check it for Internship Certificate
    </label>
</div>

<div class="mt-3 tble-bts">

    <!-- <button id="issueSelected" class="btn btn-primary">Confirm Student</button> -->
    <button id="downloadissueSelected" class="btn btn-primary">Download Confirm Letter</button>
    <button id="downloadReceipts" class="btn btn-warning">Download Receipts</button>
    <button id="moveSelected" class="btn btn-warning">Shift To Certificates</button>
    <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
    <!-- <button id="copySelected" class="btn btn-success">Copy to Session</button> -->
    <button id="makeInterns" class="btn btn-success">Update as Intern</button>
    <button id="moveToPlacement" class="btn btn-success">Move to Placement</button>

</div>

</div>

{{-- Hidden form for bulk issuing (submits like single-row form) --}}
<input type="hidden" id="isInternshipHidden">
<form id="bulkIssueForm" method="POST" action="{{ route('students.confirmMultiple') }}" style="display:none;">
    @csrf
        <input type="hidden" name="is_internship">
    <input type="hidden" name="ids" id="bulkIds">
</form>

<form id="bulkDeleteForm" method="POST" action="{{ route('students.bulk.delete') }}">
    @csrf
    <input type="hidden" name="ids" id="deleteIds" value="">

</form>

<form id="bulkDownloadForm" method="POST" action="{{ route('students.downloadconfirmMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="is_internship">
    <input type="hidden" name="ids" id="bulkDownloadIds">
</form>

<form id="bulkReceiptForm" method="POST" action="{{ route('students.downloadMultipleReceipts') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkReceiptIds">
</form>
{{-- Move students to Certificates--}}
<form id="bulkMoveStudent" method="POST" action="{{ route('students.moveMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkmove">
</form>

{{-- Move students to Interns--}}
<form id="bulkMakeInternForm" method="POST" action="{{ route('students.make_interns') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkmakeInterns">
</form>

{{-- Move students to Placements--}}
<form id="bulkPlacementForm" method="POST" action="{{ route('students.moveToPlacement') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkPlacementIds">
</form>
<!-- Copy Students Modal -->
<div class="modal fade" id="copyStudentsModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="copyStudentsForm" method="POST" action="{{ route('students.copy') }}">
        @csrf

        <input type="hidden" name="student_ids" id="copyStudentIds">

        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Copy Students to Session</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">
                    Session <span class="text-danger">*</span>
                </label>
                <select name="session" class="form-control" required>
                    <option value="">-- Select Session --</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}">
                            {{ $session->session_name }}
                        </option>
                    @endforeach
                </select>
            </div>
          </div>

          <div class="modal-footer">
            <button type="submit" class="btn btn-success">
                Copy
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                Cancel
            </button>
          </div>
        </div>
    </form>
  </div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">


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

<!-- <script>
document.addEventListener('DOMContentLoaded', function () {

    const slider = document.getElementById('amountSlider');
    const feeFilter = document.querySelector('select[name="fee_filter"]');

    const minInput = document.getElementById('amountMin');
    const maxInput = document.getElementById('amountMax');
    const minText  = document.getElementById('amountMinText');
    const maxText  = document.getElementById('amountMaxText');

    const startMin = Number(minInput.value || 0);
    const startMax = Number(maxInput.value || 200000);

    // noUiSlider.create(slider, {
    //     start: [startMin, startMax],
    //     connect: true,
    //     step: 1,
    //     range: {
    //         min: 0,
    //         max: 200000
    //     }
    // });

    noUiSlider.create(slider, {
        start: [startMin, startMax],
        connect: true,
        step: 1,          // move 1 by 1
        // snap: true,       // lock to integers
         behaviour: 'tap',
        range: {
            min: 0,
            max: 200000
        },
        format: {
            to: value => Math.round(value),
            from: value => Number(value)
        }
    });


    slider.noUiSlider.on('update', function (values) {
        minInput.value = Math.round(values[0]);
        maxInput.value = Math.round(values[1]);

        minText.textContent = values[0];
        maxText.textContent = values[1];
    });

    /**
     * ✅ Enable slider ONLY for these fee_filter values
     */
    const amountEnabledFilters = [
        'pending_high',
        'pending_low',
        'fees_high',
        'fees_low'
    ];

    function toggleSlider() {
        const value = feeFilter.value;

        slider.noUiSlider.enable(); // always draggable

        slider.style.opacity = amountEnabledFilters.includes(value)
            ? 1
            : 0.6;
    }

    // function toggleSlider() {
    //     const value = feeFilter.value;

    //     if (amountEnabledFilters.includes(value)) {
    //         slider.noUiSlider.enable();
    //         slider.style.opacity = 1;
    //     } else {
    //         slider.noUiSlider.disable();
    //         slider.style.opacity = 0.4;
    //     }
    // }

    feeFilter.addEventListener('change', toggleSlider);
    toggleSlider(); // run on page load
});
</script>
 -->

<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>

<script>
$(document).ready(function () {
    $('.confirm-single-form').on('submit', function () {
        let isInternship = $('#isInternship').is(':checked') ? 1 : 0;
        $(this).find('.isInternshipHiddenSingle').val(isInternship);
    });
var savedPage = sessionStorage.getItem('students_confirmation_page');

var pageLength = 10;
$.fn.dataTable.ext.pager.numbers_length = 12;
// Reset page index on search submit
$('form[action*="students"]').on('submit', function () {
    sessionStorage.removeItem('students_confirmation_page');
});
$('a[href="{{ route('students.index') }}"]').on('click', function () {
    sessionStorage.removeItem('students_confirmation_page');
});

    
    var table = $('#studentsTable').DataTable({
        "pageLength": pageLength,
        "displayStart": savedPage ? (savedPage * pageLength) : 0,
        'pagingType': "full_numbers", 
        "lengthMenu": [10,15,20, 25, 50, 100],
        "scrollX": true,
//         "rowCallback": function(row, data) {

//     let nextDueDate = data[14];
//     let today = new Date().toISOString().slice(0,10);

//     if (nextDueDate && nextDueDate !== "-") {

//         let parts = nextDueDate.split("-");
//         let mysqlFormat = parts[2] + '-' + parts[1] + '-' + parts[0];

//         if (mysqlFormat < today) {
//             $('td', row).css('background-color', '#ffe5e5');  // RED
//         } 
//         else if (mysqlFormat === today) {
//             $('td', row).css('background-color', '#fff3cd');  // YELLOW
//         }
//     }
// }

    });

     // ✅ Save page whenever page changes
    table.on('page.dt', function () {
        sessionStorage.setItem(
            'students_confirmation_page',
            table.page()
        );
    });



    $('#checkAll').on('change', function () {
        const checked = this.checked;

        $('.record_checked').each(function () {
            if (this.disabled) {
                this.checked = false; // FORCE UNCHECK
            } else {
                this.checked = checked;
            }
        });
    });


    // Get selected IDs
    function getSelectedIds() {
        var ids = [];
        $('.record_checked:checked').each(function() {
            ids.push($(this).val());
        });
        return ids;
    }

    // Multi-action buttons
    $('#issueSelected2').click(function() {
        var ids = getSelectedIds();
        if(ids.length === 0) { alert('Select at least one student'); return; }
        if(confirm('Send certificates to selected students?')) {
            console.log('Issue certificates for:', ids);
            // Add AJAX call or form submission here
        }
    });

    $('#issueSelected').click(function () {
        var ids = getSelectedIds();

        // if (ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }

        // if (!confirm('Send certificates to selected students?')) {
        //     return;
        // }
        pageBulkConfirm(ids, 'Send certificates to selected students?', function () {

            let isInternship = $('#isInternship').is(':checked') ? 1 : 0;

            // set global hidden value
            $('#isInternshipHidden').val(isInternship);

            // copy value into each form before submit
            $('input[name="is_internship"]').val(isInternship);

            // Put IDs as JSON into hidden input and submit the form
            $('#bulkIds').val(JSON.stringify(ids));
             $('#isInternshipHidden').val(isInternship);
            $('#bulkIssueForm').submit(); // normal submit -> page reload
        });
    });

   
    // $('#moveSelected').click(function () {
    $('#moveSelected').off('click').on('click', function () {

        let selectedCount = $('.record_checked:checked').length;

        // 🔴 Nothing selected
        if (selectedCount === 0) {
            Swal.fire({
                icon: 'warning',
                text: 'Select at least one student'
            });
            return;
        }


        let eligibleIds = [];
        let ineligibleIds = [];

        $('.record_checked:checked').each(function () {
            let pending_fees = parseInt($(this).data('pending_fees')) || 0;

            if (pending_fees === 0) {
                eligibleIds.push($(this).val());
            } else {
                ineligibleIds.push($(this).val());
            }
        });

        // if (eligibleIds.length === 0) {
        //     alert('None of the selected students are eligible for certificates.');
        //     return;
        // }

        if (eligibleIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                text: 'None of the selected students are eligible for certificates.'
            });
            return;
        }

        let message = '';
        if (ineligibleIds.length > 0) {
            message = ineligibleIds.length +
                ' selected student(s) have pending fees and will be skipped.\n\n';
        }

        message += 'Send eligible students to certificate section?';

        // if (!confirm(message)) {
        //     return;
        // }

        // 🟡 SweetAlert confirm
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes'
        }).then((result) => {

            if (!result.isConfirmed) return;

            $('#bulkmove').val(JSON.stringify(eligibleIds));
            $('#bulkMoveStudent').submit();
            // $('#bulkmove').val(JSON.stringify(eligibleIds));

            // 🔥 native submit (avoids swal loop)
            // document.getElementById('bulkMoveStudent').submit();

        });

        
    });

    

    // Download Confirm Letter(s)
    $('#downloadissueSelected').click(function () {
        var ids = getSelectedIds();

        // if (ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }

        // if (!confirm('Download confirm letter(s) for selected student(s)?')) {
        //     return;
        // }
        pageBulkConfirm(ids, 'Download confirm letter(s) for selected student(s)?', function () {

             let isInternship = $('#isInternship').is(':checked') ? 1 : 0;

            // set global hidden value
            $('#isInternshipHidden').val(isInternship);

            // copy value into each form before submit
            $('input[name="is_internship"]').val(isInternship);

            // Put JSON string of IDs into hidden input and submit form
            $('#bulkDownloadIds').val(JSON.stringify(ids));
            $('#bulkDownloadForm').submit();
        });
    });

    $('#downloadReceipts').click(function () {
        var ids = getSelectedIds();

        // if (ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }

        
        pageBulkConfirm(ids, 'Download payment receipts for selected students?', function () {

            $('#bulkReceiptIds').val(JSON.stringify(ids));
            $('#bulkReceiptForm').submit();
        });
    });

 

        $('#deleteSelected').click(function() {
            var ids = getSelectedIds();

            // if(ids.length === 0) {
            //     alert('Select at least one student');
            //     return;
            // }
            pageBulkConfirm(ids, 'Delete selected students?', function () {
            // if(confirm('Delete selected students?')) {
                // $('#deleteIds').val(ids);
                $('#deleteIds').val(JSON.stringify(ids));
                $('#bulkDeleteForm').submit();
            });
        });

        $('#copySelected').on('click', function () {

            let ids = getSelectedIds();

            // ❌ No student selected
            // if (ids.length === 0) {
            //     alert('Please select at least one student first');
            //     return false;
            // }
             if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Select at least one student'
                });
                return;
            }
            // ✅ Student(s) selected
            $('#copyStudentIds').val(JSON.stringify(ids));
            $('#copyStudentsModal').modal('show');
        });


        $('#makeInterns').click(function() {
            var ids = getSelectedIds();

            // if(ids.length === 0) {
            //     alert('Select at least one student');
            //     return;
            // }
            pageBulkConfirm(ids, 'Make Interns?', function () {
            // if(confirm('Make Interns?')) {
                // $('#deleteIds').val(ids);
                $('#bulkmakeInterns').val(JSON.stringify(ids));
                $('#bulkMakeInternForm').submit();
             });
        });

        $('#moveToPlacement').click(function () {

            let ids = getSelectedIds();

            if (ids.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Select at least one student'
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: 'Move selected students to Placement?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $('#bulkPlacementIds').val(JSON.stringify(ids));
                $('#bulkPlacementForm').submit();
            });
        });
        // $('.edit_btn').on('click', function () {
            
        //     console.log('Edit clicked');
        //     let table = $('#studentsTable').DataTable();
        //     let page = table.page(); // zero-based index
        //     sessionStorage.setItem('students_datatable_page', page);
        // });

        // $('.edit_btn').on('click', function (e) {

        //     let table = $('#studentsTable').DataTable();
        //     let page = table.page(); // zero-based
        //     alert(page);
        //     let url = $(this).attr('href');

        //     // append ?page_index=#
        //     $(this).attr('href', url + '?datatable_page=' + page);
        // });


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
</script>

@endpush
