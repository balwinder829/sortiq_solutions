@extends('layouts.app')

@section('title', 'Certificates')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
.pending-row {
    background-color: blue !important;  /* light yellow */
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
<div class="container mt-4">
    {{-- UNIVERSAL POPUP CONTAINER --}}
<div id="popup-container"
     class="position-fixed top-0 end-0 p-3"
     style="z-index: 2000; width: 360px;">

    {{-- PENDING FEE POPUP --}}
    @if($pendingStudents->count() > 0)
        <div id="pending-fee-alert"
             class="mb-3 animate__animated animate__fadeInRight">
            @include('dashboard.popup.pending_fee')
        </div>
    @endif
</div>
    <div class="d-flex justify-content-between mb-3">
        <h1 class="page_heading">Certificates</h1>
    </div>




    {{-- Search / Filter Form --}}
<form method="GET"  id="filterForm" class="mb-4">
    <div class="row g-2">
        {{-- Student Name --}}
        <!-- <div class="col-md-2">
            <input type="text" name="student_name" class="form-control"
                   placeholder="Student Name" value="{{ request('student_name') }}">
        </div> -->

        {{-- Father Name --}}
       <!--  <div class="col-md-2">
            <input type="text" name="f_name" class="form-control"
                   placeholder="Father Name" value="{{ request('f_name') }}">
        </div> -->

        {{-- Gender --}}
        <!-- <div class="col-md-1">
            <select name="gender" class="form-control">
                <option value="">Gender</option>
                <option value="Male" {{ request('gender')=='Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender')=='Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div> -->

        {{-- S no. --}}
        <!-- <div class="col-md-2">
            <input type="text" name="sno" class="form-control"
                   placeholder="S. No" value="{{ request('sno') }}">
        </div> -->

        {{-- Session --}}
        <!-- <div class="col-md-2">
            <select name="session" class="form-control session" id="ddl_session">
                <option value="">--Session Name--</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}"
                        {{ request('session') == $session->id ? 'selected' : '' }}>
                        {{ $session->session_name }}
                    </option>
                @endforeach
            </select>
        </div>
 -->
        {{-- College --}}
        <div class="col-md-3">
            <select name="college_name" class="form-control collegeName filterchange select2" id="txtcollege">
                <option value="">--College--</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ request('college_name') == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Email --}}
        <!-- <div class="col-md-2">
            <input type="text" name="email_id" class="form-control"
                   placeholder="Email" value="{{ request('email_id') }}">
        </div> -->

        {{-- Status --}}
        <div class="col-md-2">
            <select name="status" class="form-control statusData filterchange">
                <option value="" {{ request('status') == '' ? 'selected' : '' }}>--Status--</option>

                @foreach($student_status as $s)
                    <option value="{{ $s->status }}"
                        {{ request('status') == $s->status ? 'selected' : '' }}>
                        {{ $s->name }}
                    </option>
                @endforeach
            </select>
        </div>
 

        {{-- Technology / Course --}}
        <div class="col-md-2">
            <select name="technology" class="form-control technology filterchange" id="txttechnology">
                <option value="">--Technology--</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}"
                        {{ request('technology') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Part-Time Offer --}}
        <div class="col-md-2">
            <select name="part_time_offer" class="form-control filterchange">
                <option value="">--Part-Time Offer--</option>
                <option value="1" {{ request('part_time_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('part_time_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Placement Offer --}}
        <div class="col-md-2">
            <select name="placement_offer" class="form-control filterchange">
                <option value="">--Placement Offer--</option>
                <option value="1" {{ request('placement_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('placement_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- PG Offer --}}
        <div class="col-md-2">
            <select name="pg_offer" class="form-control filterchange">
                <option value="">--PG Offer--</option>
                <option value="1" {{ request('pg_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('pg_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

         {{-- Is Intern --}}
        <div class="col-md-2 col-12">
            <select name="is_intern" class="form-control filterchange">
                <option value="">--Is Intern--</option>
                <option value="1" {{ request('is_intern') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('is_intern') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

         {{-- Study Mode --}}
        <div class="col-md-2 col-12">
            <select name="is_online" class="form-control filterchange">
                <option value="">--Study Mode--</option>
                <option value="0" {{ request('is_online') === '0' ? 'selected' : '' }}>Offline</option>
                <option value="1" {{ request('is_online') === '1' ? 'selected' : '' }}>Online</option>
            </select>
        </div>

        {{-- Certificate Sent --}}
        <div class="col-md-2">
            <select name="certificate_sent" class="form-control filterchange">
                <option value="">--Certificate Sent--</option>
                <option value="1" {{ request('certificate_sent') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('certificate_sent') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>


{{-- Regsiteration Fee --}}
<div class="col-md-2">

<input type="number"
name="registration_fee"
class="form-control filterchangetext"
placeholder="Regsiteration Fee"
value="{{ request('registration_fee') }}">

</div>

        {{-- Department --}}
      <!--   <div class="col-md-2">
            <select name="department" class="form-control" id="txtdepartment">
                <option value="">--Department--</option>
                @foreach($departments as $department)
                    <option value="{{ $department->name }}"
                        {{ request('department') == $department->name ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div> -->

         <div class="col-md-2 col-6">
            <select name="fee_filter" class="form-control filterchange">
                <option value="">-- Fee Related Filter --</option>

                {{-- Fee Status --}}
                <option value="completed"
                    {{ request('fee_filter')=='completed' ? 'selected' : '' }}>
                    Completed Fees (No Pending)
                </option>

                <option value="pending"
                    {{ request('fee_filter')=='pending' ? 'selected' : '' }}>
                    Pending Fees Only
                </option>

                {{-- Sorting --}}
                <option value="pending_high"
                    {{ request('fee_filter')=='pending_high' ? 'selected' : '' }}>
                    Pending Fees: High → Low
                </option>

                <option value="pending_low"
                    {{ request('fee_filter')=='pending_low' ? 'selected' : '' }}>
                    Pending Fees: Low → High
                </option>

                <option value="fees_high"
                    {{ request('fee_filter')=='fees_high' ? 'selected' : '' }}>
                    Total Fees: High → Low
                </option>

                <option value="fees_low"
                    {{ request('fee_filter')=='fees_low' ? 'selected' : '' }}>
                    Total Fees: Low → High
                </option>
            </select>
        </div>

         <div class="col-md-2 col-12">
            <select name="gender" class="form-control filterchange">
                <option value="">--Gender--</option>

                <option value="male"
                    {{ request('gender') == 'male' ? 'selected' : '' }}>
                    Male
                </option>

                <option value="female"
                    {{ request('gender') == 'female' ? 'selected' : '' }}>
                    Female
                </option>
            </select>
        </div>
         {{-- Amount Range Slider --}}
<!-- <div class="col-md-3 col-12">
    <label class="form-label fw-bold">Amount Range</label>

    <div id="amountSlider"></div>

    <div class="d-flex justify-content-between mt-2">
        <span>Min: <strong id="amountMinText"></strong></span>
        <span>Max: <strong id="amountMaxText"></strong></span>
    </div>

    {{-- Hidden inputs for GET --}}
    <input type="hidden" name="amount_min" id="amountMin" value="{{ request('amount_min', 0) }}">
    <input type="hidden" name="amount_max" id="amountMax" value="{{ request('amount_max', 200000) }}">
</div> -->
 

<!-- <div class="row mt-2"> -->
      {{-- Amount Range Slider --}}
<!-- <div class="col-md-4 col-12 mx-4 mt-4">
    

    <div id="amountSlider"></div>

    <div class="d-flex justify-content-between mt-2">
        <span>Min: <strong id="amountMinText"></strong></span>
        <span>Max: <strong id="amountMaxText"></strong></span>
    </div>

    {{-- Hidden inputs for GET --}}
    <input type="hidden" name="amount_min" id="amountMin" value="{{ request('amount_min', 0) }}">
    <input type="hidden" name="amount_max" id="amountMax" value="{{ request('amount_max', 200000) }}">
</div> -->
<!-- </div> -->

{{-- Amount Range Slider --}}
<div class="row mt-2">
<div class="col-md-4 col-12">
    <label class="form-label fw-bold">Amount Range</label>

    <div id="amountSlider" class="mb-2"></div>

    <div class="d-flex gap-2 align-items-center">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Min</span>
            <input type="text"
                   name="amount_min"
                   id="amountMin"
                   class="form-control text-end filterchange"
                   value="{{ request('amount_min', 0) }}">
        </div>

        <span class="fw-bold">–</span>

        <div class="input-group input-group-sm">
            <span class="input-group-text">Max</span>
            <input type="text"
                   name="amount_max"
                   id="amountMax"
                   class="form-control text-end filterchange"
                   value="{{ request('amount_max', 200000) }}">
        </div>

         <input type="hidden"
                   name="certificate_status"
                   id="amountMax"
                   class="form-control"
                   value="1">
    </div>
</div>

 {{-- Buttons --}}
        <!-- <div class="col-md-1 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Search</button>
        </div> -->
       <div class="col-md-4 d-flex align-items-end gap-2">
            <a href="{{ route('certificates.index') }}" class="btn btn-secondary">Reset</a>

            <a href="{{ route('students.export.excel', array_merge(request()->query(), ['certificate_status' => '1'])) }}"
               class="btn btn-primary">

                Download Excel

            </a>
            <button type="button"
                id="copySelected"
                class="btn btn-success">

                Copy to Session

            </button>
        </div>
</div>


       
    </div>
</form>

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
    {{-- Students Table --}}
    <div class="table-responsive">
        <table id="certificatesTable" class="table table-bordered table-striped">
                   <thead class="table-light">
            <tr>
            <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th class="text-center">#</th>
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
                
                <th class="text-center" width="100px">Registered Date</th>
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
                <th width="100px" class="text-center">Action</th>
            </tr>
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
                <td>
                    <input
                        type="checkbox"
                        class="record_checked"
                        value="{{ $student->id }}"
                        data-email="{{ $student->email_count_confirmation ?? 0 }}"
                        data-receipt="{{ $student->count_receipt_download ?? 0 }}"
                        data-fees="{{ $student->pending_fees ?? 0 }}"
                        title="Certificate not eligible"
                    >
                </td>
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
                <td>{{ $student->course_name ?? '-' }}</td>
                <td>{{ $student->total_fees }}</td>
                <td>{{ $student->reg_fees }}</td>
                <td class="{{ $student->pending_fees > 0 ? 'text-danger fw-bold' : '' }}">{{ $student->pending_fees }}</td>
                
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
                <td>{{ $student->email_count_certificate ?? 0 }}</td>
                 <td>{{ $student->count_receipt_download ?? 0 }}</td>
                 <td>
                    {{ $student->created_at ? \Carbon\Carbon::parse($student->created_at)->format('d M Y') : '-' }}
                </td>
                <td>
                    {{ $student->updated_at ? \Carbon\Carbon::parse($student->updated_at)->format('d M Y') : '-' }}
                </td>
               <!--  <td class="text-center">
                    <div class="mb-2">
                                             {{-- Edit --}}
                        <a href="{{ route('certificates.edit',$student->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>

                    <span class="badge bg-light text-dark">Email count: {{ $student->email_count_certificate }}</span>
                </td> -->
                <td class="text-center">

                    <div class="d-flex gap-2 justify-content-center align-items-center mb-2">

                        {{-- Edit --}}
                        <a href="{{ route('certificates.edit',$student->id) }}"
                           class="btn btn-sm btn-outline-primary"
                           data-bs-toggle="tooltip"
                           title="Edit Student">

                            <i class="fa fa-edit"></i>
                        </a>

                        {{-- Certificate Sent Toggle --}}
                        <div class="d-flex align-items-center">

    <label class="switch mb-0">

        <input
            type="checkbox"
            class="certificateToggle"
            data-id="{{ $student->id }}"
            {{ $student->certificate_sent ? 'checked' : '' }}
        >

        <span class="slider round"></span>

    </label>

</div>

                    </div>

                    <span class="badge bg-light text-dark">
                        Email count: {{ $student->email_count_certificate ?? 0 }}
                    </span>

                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>
    {{-- Buttons for selected students --}}
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" id="isPursuing" name="is_pursuing">
        <label class="form-check-label" for="isPursuing">
            Check it if Pursuing
        </label>
    </div>
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" id="forceIssueDate" name="force_update" value="1">
        <label class="form-check-label" for="forceIssueDate">
            Overwrite Issue Date
        </label>
    </div>
    <div class="form-check mb-2">
        <input type="checkbox" class="form-check-input" id="isInternship" name="is_internship" value="1">
        <label class="form-check-label" for="isInternship">
            Is Internship
        </label>
    </div>
    {{-- Multi-action buttons --}}
    <div class="mt-3">
        <!-- <button id="issueSelected" class="btn btn-primary">Issue Certificate</button> -->
        <button id="downloadissueSelected" class="btn btn-primary">Download Certificates</button>
        <button id="downloadissueMonthNameSelected" class="btn btn-primary">Download Certificates (Month Name)</button>
        <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
        <button id="moveToPlacement" class="btn btn-success">Move to Placement</button>
        <button id="moveSelected" class="btn btn-warning">Shift To Confirmation</button>
        <button id="updateIssueDate" class="btn btn-warning">Update Issue Date</button>
        <button id="markCertificateSent" class="btn btn-success">Mark Certificate Sent</button>
        <button id="markCertificateNotSent" class="btn btn-secondary mt-2 ml-2">Mark Certificate Not Sent</button>
    </div>

     
</div>

{{-- Hidden form for bulk issuing (submits like single-row form) --}}
<form id="bulkIssueForm" method="POST" action="{{ route('students.issueMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkIds">
</form>

<form id="bulkIssueDateForm" method="POST" action="{{ route('students.updateCertificateIssueDateMultiple') }}" style="display:none;">
    @csrf
        <input type="hidden" name="ids" id="bulkIssueIds">
        <input type="hidden" name="issue_date" id="issueDateInput">
        <input type="hidden" name="force_update" id="forceUpdateInput">
</form>

<form id="bulkDownloadForm" method="POST" action="{{ route('students.downloadCertificateMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDownloadIds">
    <input type="hidden" name="college_name" value="{{ request('college_name') }}">
    <input type="hidden" name="is_pursuing">
    <input type="hidden" name="is_internship">
    <input type="hidden" name="is_month_name">
</form>

<form id="bulkDeleteForm" method="POST" action="{{ route('students.bulk.delete') }}">
    @csrf
    <input type="hidden" name="ids" id="deleteIds" value="">

</form>

{{-- Move students to Placements--}}
<form id="bulkPlacementForm" method="POST" action="{{ route('students.moveToPlacement') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkPlacementIds">
</form>

{{-- Move students to Certificates--}}
<form id="bulkMoveStudent" method="POST" action="{{ route('students.moveMultipleToConfirmation') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkmove">
</form>

{{-- Certificates Status chnage send or not sent--}}
<form id="bulkCertificateStatusForm"
      method="POST"
      action="{{ route('students.bulkCertificateStatus') }}"
      style="display:none;">

    @csrf

    <input type="hidden" name="ids" id="bulkCertificateIds">

    <input type="hidden" name="status" id="bulkCertificateStatus">

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

    noUiSlider.create(slider, {
        start: [startMin, startMax],
        connect: true,
        step: 1,
        range: {
            min: 0,
            max: 200000
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

        if (amountEnabledFilters.includes(value)) {
            slider.noUiSlider.enable();
            slider.style.opacity = 1;
        } else {
            slider.noUiSlider.disable();
            slider.style.opacity = 0.4;
        }
    }

    feeFilter.addEventListener('change', toggleSlider);
    toggleSlider(); // run on page load
});
</script> -->


<script>
$(document).ready(function () {
    // Initialize DataTable

    var savedPage = sessionStorage.getItem('students_certificate_page');

    var pageLength = 10;
    $.fn.dataTable.ext.pager.numbers_length = 12;
    $('form[action*="students"]').on('submit', function () {
        sessionStorage.removeItem('students_certificate_page');
    });
    $('a[href="{{ route('students.index') }}"]').on('click', function () {
        sessionStorage.removeItem('students_certificate_page');
    });
    var table = $('#certificatesTable').DataTable({
        "pageLength": pageLength,
        "displayStart": savedPage ? (savedPage * pageLength) : 0,
        'pagingType': "full_numbers", 
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true,
        columnDefs: [
            {
                targets: 1, // first column
                searchable: false,
                orderable: false
            }
        ],
       rowCallback: function (row, data) {

        // Correct way to access API inside callback
        var api = this.api();

        let pendingFees = parseFloat($('td:eq(13)', row).text().trim());

        if (!isNaN(pendingFees) && pendingFees > 0) {

            // Highlight row in the main table
            $(row).addClass("pending-row");

            // Highlight row in the scroll table
            $(api.row(row).node()).addClass("pending-row");

            // Highlight pending fee cell
            $('td:eq(13)', row).addClass("text-danger fw-bold");
        }
    }
    });

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(1, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
    // ✅ Save page whenever page changes
    table.on('page.dt', function () {
        sessionStorage.setItem(
            'students_certificate_page',
            table.page()
        );
    });

    // Check/uncheck all
    // $('#checkAll').click(function(){
    //     $('.record_checked').prop('checked', this.checked);
    // });

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
    // $('#issueSelected').click(function() {
    //     var ids = getSelectedIds();
    //     if(ids.length === 0) { alert('Select at least one student'); return; }
    //     if(confirm('Send certificates to selected students?')) {
    //         console.log('Issue certificates for:', ids);
    //         // Add AJAX call or form submission here
    //     }
    // });

    $('#issueSelected').click(function () {

            let eligibleIds = [];
            let skippedCount = 0;

            $('.record_checked:checked').each(function () {

                // let email = parseInt($(this).data('email'));
                // let receipt = parseInt($(this).data('receipt'));
                let fees = parseInt($(this).data('fees'));

                // Eligibility condition
                if (fees === 0) {
                    eligibleIds.push($(this).val());
                } else {
                    skippedCount++;
                }
            });

            if (eligibleIds.length === 0) {
                alert('None of the selected students are eligible for certificate issue.');
                return;
            }

            let msg = '';
            if (skippedCount > 0) {
                msg = skippedCount +
                    ' selected student(s) are not eligible and will be skipped.\n\n';
            }

            msg += 'Send certificates to eligible students?';

            if (!confirm(msg)) {
                return;
            }

            $('#bulkIds').val(JSON.stringify(eligibleIds));
            $('#bulkIssueForm').submit();
        });

$('#markCertificateSent').click(function () {

    let ids = getSelectedIds();

    if (ids.length === 0) {

        Swal.fire({
            icon: 'warning',
            text: 'Select at least one student'
        });

        return;
    }

    Swal.fire({
        title: 'Mark as Sent?',
        text: 'Selected students will be marked as certificate sent.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $('#bulkCertificateIds').val(JSON.stringify(ids));

        $('#bulkCertificateStatus').val(1);

        $('#bulkCertificateStatusForm').submit();

    });

});

$('#markCertificateNotSent').click(function () {

    let ids = getSelectedIds();

    if (ids.length === 0) {

        Swal.fire({
            icon: 'warning',
            text: 'Select at least one student'
        });

        return;
    }

    Swal.fire({
        title: 'Mark as Not Sent?',
        text: 'Selected students will be marked as certificate not sent.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $('#bulkCertificateIds').val(JSON.stringify(ids));

        $('#bulkCertificateStatus').val(0);

        $('#bulkCertificateStatusForm').submit();

    });

});
    $('#issueSelected1').click(function () {
        var ids = getSelectedIds();

        if (ids.length === 0) {
            alert('Select at least one student');
            return;
        }

        if (!confirm('Send certificates to selected students?')) {
            return;
        }

        // Put IDs as JSON into hidden input and submit the form
        $('#bulkIds').val(JSON.stringify(ids));
        $('#bulkIssueForm').submit(); // normal submit -> page reload
    });

    $('#updateIssueDate').click(function () {

        var ids = getSelectedIds();
        let forceUpdate = $('#forceIssueDate').is(':checked') ? 1 : 0;
        $('#forceUpdateInput').val(forceUpdate);

        if (ids.length === 0) {
            Swal.fire({
                icon: 'warning',
                text: 'Select at least one student'
            });
            return;
        }

        // 🔥 Ask for custom date
        Swal.fire({
            title: 'Select Issue Date',
            input: 'date',
            inputLabel: 'Issue Date',
            inputValue: new Date().toISOString().split('T')[0],
            showCancelButton: true,
            confirmButtonText: 'Update'
        }).then((result) => {

            if (!result.isConfirmed) return;

            let issueDate = result.value;

            if (!issueDate) {
                Swal.fire('Error', 'Please select a date', 'error');
                return;
            }

            // ✅ set hidden input
            $('#issueDateInput').val(issueDate);

            // ✅ set IDs
            $('#bulkIssueIds').val(JSON.stringify(ids));

            // submit form
            $('#bulkIssueDateForm').submit();
        });
    });
     // Download Confirm Letter(s)
    $('#downloadissueSelected').click(function () {
        var ids = getSelectedIds();
        let isPursuing = $('#isPursuing').is(':checked') ? 1 : 0;
        let isInternship = $('#isInternship').is(':checked') ? 1 : 0;
        $('input[name="is_pursuing"]').val(isPursuing);
        $('input[name="is_internship"]').val(isInternship);
        $('input[name="is_month_name"]').val("no");
        // if (ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }

        // if (!confirm('Download confirm letter(s) for selected student(s)?')) {
        //     return;
        // }
        pageBulkConfirm(ids, 'Download confirm letter(s) for selected student(s)?', function () {
        // Put JSON string of IDs into hidden input and submit form
            $('#bulkDownloadIds').val(JSON.stringify(ids));
            $('#bulkDownloadForm').submit();
        });
    });

 // Download Confirm Letter(s)
    $('#downloadissueMonthNameSelected').click(function () {
        var ids = getSelectedIds();
        let isPursuing = $('#isPursuing').is(':checked') ? 1 : 0;
        let isInternship = $('#isInternship').is(':checked') ? 1 : 0;
        $('input[name="is_pursuing"]').val(isPursuing);
        $('input[name="is_month_name"]').val("yes");
        $('input[name="is_internship"]').val(isInternship);
        // if (ids.length === 0) {
        //     alert('Select at least one student');
        //     return;
        // }

        // if (!confirm('Download confirm letter(s) for selected student(s)?')) {
        //     return;
        // }
        pageBulkConfirm(ids, 'Download confirm letter(s) for selected student(s)?', function () {
        // Put JSON string of IDs into hidden input and submit form
            $('#bulkDownloadIds').val(JSON.stringify(ids));
            $('#bulkDownloadForm').submit();
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

     $('#moveSelected').click(function () {

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
                text: 'Move selected students to Confirmation?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes'
            }).then((result) => {

                if (!result.isConfirmed) return;

                $('#bulkmove').val(JSON.stringify(ids));
                $('#bulkMoveStudent').submit();
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

        url: "{{ route('students.toggleCertificateSent') }}",

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
