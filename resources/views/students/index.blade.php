@extends('layouts.app')

@section('title', 'Students')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
 @php
    $role = (int) auth()->user()->role;
@endphp
 {{-- UNIVERSAL POPUP CONTAINER --}}
 
<div class="container mt-4">
    <!-- <div class="d-flex justify-content-between mb-3">
        <h3>Students</h3>
       <a href="{{ route('students.create') }}" class="btn" style="background-color: #6b51df; color: #fff;">+ Add Student</a>
       <a href="{{ route('students.importForm') }}" class="btn btn-info">Import Students</a>


    </div> -->
    <!-- <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 st-head"> -->
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
    </div>
</div>

{{-- Search / Filter Form --}}
<form method="GET" action="{{ route('students.index') }}" class="mb-4">
    <div class="row g-2">
        {{-- Student Name --}}
        <div class="col-md-2 col-6">
            <input type="text" name="student_name" class="form-control"
                   placeholder="Student Name" value="{{ request('student_name') }}">
        </div>

         
         {{-- S no. --}}
        <div class="col-md-2 col-6">
            <input type="text" name="sno" class="form-control"
                   placeholder="S. No" value="{{ request('sno') }}">
        </div>
        {{-- Session --}}
        <div class="col-md-2 col-6">
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

        {{-- College --}}
        <div class="col-md-2 col-6">
            <select name="college_name" class="form-control collegeName" id="txtcollege">
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
        {{-- Date Range --}}
        <div class="col-md-3">
            <div class="input-group">
                <input type="date" name="start_date" class="form-control" 
                    value="{{ request('start_date') }}" placeholder="Start Date">
                <input type="date" name="end_date" class="form-control" 
                    value="{{ request('end_date') }}" placeholder="End Date">
            </div>
        </div>
        {{-- Status --}}
        <div class="col-md-2 col-6">
            <select name="status" class="form-control statusData">
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
        <div class="col-md-2 col-6">
            <select name="technology" class="form-control technology" id="txttechnology">
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
        <div class="col-md-2 col-6">
            <select name="part_time_offer" class="form-control">
                <option value="">--Part-Time Offer--</option>
                <option value="1" {{ request('part_time_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('part_time_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Placement Offer --}}
        <div class="col-md-2 col-6">
            <select name="placement_offer" class="form-control">
                <option value="">--Placement Offer--</option>
                <option value="1" {{ request('placement_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('placement_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- PG Offer --}}
        <div class="col-md-2 col-12">
            <select name="pg_offer" class="form-control">
                <option value="">--PG Offer--</option>
                <option value="1" {{ request('pg_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('pg_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>


        {{-- Department --}}
        <!-- <div class="col-md-2">
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
        <div class="col-md-2">
            <div class="form-check d-flex align-items-center">
                <input type="checkbox" class="form-check-input me-2" name="pending_fees" id="pending_fees" value="1" 
                {{ request('pending_fees') == 1 ? 'checked' : '' }}>
                <label class="form-check-label" for="pending_fees">Pending Fee Only</label>
            </div>
        </div>
    </div>
	{{-- Buttons --}}
	<div class="mt-2 tble-bts">
		<button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Search</button>
		<a href="{{ route('students.index') }}" class="btn btn-secondary">Reset</a>
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
@endif<style></style>

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
                <th class="text-center">Total Fees</th>
                <th class="text-center">Reg Fees</th>
                <th class="text-center">Pending Fees</th>
                <th class="text-center" width="100px">Next Due Date</th>
                <!-- <th class="text-center">Department</th> -->
                <th class="text-center" width="100px">Date of Joining</th>
                <th class="text-center">Duration</th>
                <th class="text-center" width="100px">Start Date</th>
                <th class="text-center" width="100px">End Date</th>
                <th class="text-center">Part-Time Job Offer</th>
                <th class="text-center">Placement Offer</th>
                <th class="text-center">PG Offer</th>
                <th class="text-center">Email Count</th>
                <th class="text-center">Receipt Count</th>
                <th width="180px" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach ($students as $student)
            @php
                $today = now()->toDateString();
                $nextDate = $student->next_due_date 
                    ? \Carbon\Carbon::parse($student->next_due_date)->toDateString()
                    : null;
            @endphp
            <tr 
    @if($nextDate && $nextDate < $today)
        style="background-color:#ffe5e5;" {{-- Overdue --}}
    @elseif($nextDate && $nextDate == $today)
        style="background-color:#fff3cd;" {{-- Due Today --}}
    @endif
>
                <td><input type="checkbox" class="record_checked" value="{{ $student->id }}" data-email="{{ $student->email_count_confirmation }}"
       data-receipt="{{ $student->count_receipt_download }}"></td>
                <td>{{ $student->sno }}</td>
                <td>{{ $student->student_name }}</td>
                <td>{{ $student->f_name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->sessionData->session_name ?? '-' }}</td>
                <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                <td>{{ $student->contact }}</td>
                <td>{{ $student->email_id }}</td>
                <td><span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }}">{{ $student->status }}</span></td>
                 <td>{{ $student->courseData->course_name ?? '-' }}</td>
                <td>{{ $student->total_fees }}</td>
                <td>{{ $student->reg_fees }}</td>
                <td>{{ $student->pending_fees }}</td>
                <td>
                    {{ $student->next_due_date ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') : '-' }}
                </td>

                <!-- <td>{{ $student->department }}</td> -->
                <td>{{ \Carbon\Carbon::parse($student->join_date)->format('d M Y') }}</td>
                
                <td>{{ $student->durationData->name ?? '-' }}</td>
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

                <td>{{ $student->email_count_confirmation ?? 0 }}</td>
                <td>{{ $student->count_receipt_download ?? 0 }}</td>
        <td class="text-center">
    <div class="mb-2">
        {{-- Issue --}}

        <form action="{{ route('students.confirmStudent', $student->id) }}" method="POST" style="display:inline-block;"  class="confirm-single-form" >
            @csrf
            <input type="hidden" name="is_internship" class="isInternshipHiddenSingle">
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate"
                onclick="return confirm('Send certificate to {{ $student->email_id }}?')">
                <i class="fa-solid fa-file-lines"></i>
            </button>
        </form>



        {{-- Edit --}}
        <a href="{{ route('students.edit',$student->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
            <i class="fa fa-edit"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('students.destroy',$student->id) }}" method="POST" style="display:inline-block;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" onclick="return confirm('Delete this student?')">
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

    <button id="issueSelected" class="btn btn-primary">Confirm Student</button>
    <button id="downloadissueSelected" class="btn btn-primary">Download Confirm Letter</button>
    <button id="downloadReceipts" class="btn btn-warning">Download Receipts</button>
    <button id="moveSelected" class="btn btn-warning">Shift To Certificates</button>
    <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
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

@endsection

@push('scripts')
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

    
    var table = $('#studentsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true,
        "rowCallback": function(row, data) {

    let nextDueDate = data[14];
    let today = new Date().toISOString().slice(0,10);

    if (nextDueDate && nextDueDate !== "-") {

        let parts = nextDueDate.split("-");
        let mysqlFormat = parts[2] + '-' + parts[1] + '-' + parts[0];

        if (mysqlFormat < today) {
            $('td', row).css('background-color', '#ffe5e5');  // RED
        } 
        else if (mysqlFormat === today) {
            $('td', row).css('background-color', '#fff3cd');  // YELLOW
        }
    }
}

    });

    // Check/uncheck all
    // $('#checkAll').click(function(){
    //     $('.record_checked').prop('checked', this.checked);
    // });

    // $('#checkAll').on('click', function () {
    //     $('.record_checked:not(:disabled)').prop('checked', this.checked);
    // });
    // $('#checkAll').on('change', function () {
    //     const checked = this.checked;

    //     $('.record_checked').each(function () {
    //         if (this.disabled === false) {
    //             this.checked = checked;
    //         }
    //     });
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

        if (ids.length === 0) {
            alert('Select at least one student');
            return;
        }

        if (!confirm('Send certificates to selected students?')) {
            return;
        }

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

    // $('#moveSelected').click(function () {
    //     var ids = getSelectedIds();

    //     if (ids.length === 0) {
    //         alert('Select at least one student');
    //         return;
    //     }

    //     if (!confirm('Send students to certificate section?')) {
    //         return;
    //     }

    //     // Put IDs as JSON into hidden input and submit the form
    //     $('#bulkmove').val(JSON.stringify(ids));
    //     $('#bulkMoveStudent').submit(); // normal submit -> page reload
    // });

    $('#moveSelected').click(function () {

    let eligibleIds = [];
    let ineligibleIds = [];

    $('.record_checked:checked').each(function () {
        let emailCount = parseInt($(this).data('email'));
        let receiptCount = parseInt($(this).data('receipt'));

        if (emailCount > 0 && receiptCount > 0) {
            eligibleIds.push($(this).val());
        } else {
            ineligibleIds.push($(this).val());
        }
    });

    if (eligibleIds.length === 0) {
        alert('None of the selected students are eligible for certificates.');
        return;
    }

    let message = '';
    if (ineligibleIds.length > 0) {
        message = ineligibleIds.length +
            ' selected student(s) are not eligible and will be skipped.\n\n';
    }

    message += 'Send eligible students to certificate section?';

    if (!confirm(message)) {
        return;
    }

    $('#bulkmove').val(JSON.stringify(eligibleIds));
    $('#bulkMoveStudent').submit();
});

    $('#moveSelected1').click(function () {
    let ids = [];
    let invalidFound = false;

    $('.record_checked:checked').each(function () {
        let emailCount = parseInt($(this).data('email'));
        let receiptCount = parseInt($(this).data('receipt'));

        if (emailCount > 0 && receiptCount > 0) {
            ids.push($(this).val());
        } else {
            invalidFound = true;
        }
    });

    if (ids.length === 0) {
        alert('Selected students are not eligible for certificates.');
        return;
    }

    if (invalidFound) {
        if (!confirm('Some selected students are not eligible and will be skipped. Continue?')) {
            return;
        }
    }

    if (!confirm('Send students to certificate section?')) {
        return;
    }

    $('#bulkmove').val(JSON.stringify(ids));
    $('#bulkMoveStudent').submit();
});



    

    // Download Confirm Letter(s)
    $('#downloadissueSelected').click(function () {
        var ids = getSelectedIds();

        if (ids.length === 0) {
            alert('Select at least one student');
            return;
        }

        if (!confirm('Download confirm letter(s) for selected student(s)?')) {
            return;
        }

         let isInternship = $('#isInternship').is(':checked') ? 1 : 0;

        // set global hidden value
        $('#isInternshipHidden').val(isInternship);

        // copy value into each form before submit
        $('input[name="is_internship"]').val(isInternship);

        // Put JSON string of IDs into hidden input and submit form
        $('#bulkDownloadIds').val(JSON.stringify(ids));
        $('#bulkDownloadForm').submit();
    });

    $('#downloadReceipts').click(function () {
        var ids = getSelectedIds();

        if (ids.length === 0) {
            alert('Select at least one student');
            return;
        }

        if (!confirm('Download payment receipts for selected students?')) {
            return;
        }

        $('#bulkReceiptIds').val(JSON.stringify(ids));
        $('#bulkReceiptForm').submit();
    });


    // $('#deleteSelected').click(function() {
    //     var ids = getSelectedIds();
    //     if(ids.length === 0) { alert('Select at least one student'); return; }
    //     if(confirm('Delete selected students?')) {
    //         console.log('Delete students:', ids);
    //         // Add AJAX or form submission to delete here
    //     }
    // });

        $('#deleteSelected').click(function() {
            var ids = getSelectedIds();

            if(ids.length === 0) {
                alert('Select at least one student');
                return;
            }

            if(confirm('Delete selected students?')) {
                // $('#deleteIds').val(ids);
                $('#deleteIds').val(JSON.stringify(ids));
                $('#bulkDeleteForm').submit();
            }
        });
});
</script>
@endpush
