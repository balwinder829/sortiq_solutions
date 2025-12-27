@extends('layouts.app')

@section('title', 'Students')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container mt-4">
    <!-- <div class="d-flex justify-content-between mb-3">
        <h3>Students</h3>
       <a href="{{ route('students.create') }}" class="btn" style="background-color: #6b51df; color: #fff;">+ Add Student</a>
       <a href="{{ route('students.importForm') }}" class="btn btn-info">Import Students</a>


    </div> -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 st-head">
    <h3>Pending Students</h3>
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
@endif<style></style>

<div class="desktop-view">
<div class="table-responsive">
    <table id="studentsTable" class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <!-- <th><input type="checkbox" id="checkAll"></th> -->
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
<!--                 <td><input type="checkbox" class="record_checked" value="{{ $student->id }}" data-email="{{ $student->email_count_confirmation }}"
       data-receipt="{{ $student->count_receipt_download }}"></td> -->
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
        <td class="text-center">
    <div class="mb-2">
       


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

    <!-- <span class="badge bg-light text-dark">Email count: {{ $student->email_count_confirmation }}</span> -->
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
 

</div>

 
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
    var table = $('#studentsTable').DataTable({
        "pageLength": 100,
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true,
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

    

    $('#issueSelected').click(function () {
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
});
</script>
@endpush
