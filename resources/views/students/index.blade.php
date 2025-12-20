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
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Students</h3>

    <div class="d-flex gap-2">
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
        <div class="col-md-2">
            <input type="text" name="student_name" class="form-control"
                   placeholder="Student Name" value="{{ request('student_name') }}">
        </div>

        {{-- Father Name --}}
        <!-- <div class="col-md-2">
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
        <div class="col-md-2">
            <input type="text" name="sno" class="form-control"
                   placeholder="S. No" value="{{ request('sno') }}">
        </div>
        {{-- Session --}}
        <div class="col-md-2">
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
        <div class="col-md-2">
            <select name="college_name" class="form-control collegeName" id="txtcollege">
                <option value="">--College--</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ request('college_name') == $college->id ? 'selected' : '' }}>
                        {{ $college->college_name }}
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
        <div class="col-md-2">
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
        <div class="col-md-2">
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
        <div class="col-md-2">
            <select name="part_time_offer" class="form-control">
                <option value="">--Part-Time Offer--</option>
                <option value="1" {{ request('part_time_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('part_time_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- Placement Offer --}}
        <div class="col-md-2">
            <select name="placement_offer" class="form-control">
                <option value="">--Placement Offer--</option>
                <option value="1" {{ request('placement_offer') === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ request('placement_offer') === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        {{-- PG Offer --}}
        <div class="col-md-2">
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

        {{-- Buttons --}}
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Search</button>
        </div>
        <div class="col-md-1 d-grid">
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Reset</a>
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
                <td><input type="checkbox" class="record_checked" value="{{ $student->id }}"  {{ ($student->email_count_confirmation > 0 && $student->count_receipt_download > 0) ? '' : 'disabled' }}></td>
                <td>{{ $student->sno }}</td>
                <td>{{ $student->student_name }}</td>
                <td>{{ $student->f_name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->sessionData->session_name ?? '-' }}</td>
                <td>{{ $student->collegeData->college_name ?? '-' }}</td>
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

        <form action="{{ route('students.confirmStudent', $student->id) }}" method="POST" style="display:inline-block;">
            @csrf
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

{{-- Buttons for selected students --}}
<div class="mt-3">
    <button id="issueSelected" class="btn btn-primary">Confirm Student</button>
    <button id="downloadissueSelected" class="btn btn-primary">Download Confirm Letter</button>
    <button id="downloadReceipts" class="btn btn-warning">Download Receipts</button>
    <button id="moveSelected" class="btn btn-warning">Shift To Certificates</button>
    <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>
</div>

</div>

{{-- Hidden form for bulk issuing (submits like single-row form) --}}
<form id="bulkIssueForm" method="POST" action="{{ route('students.confirmMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkIds">
</form>

<form id="bulkDeleteForm" method="POST" action="{{ route('students.bulk.delete') }}">
    @csrf
    <input type="hidden" name="ids" id="deleteIds" value="">

</form>

<form id="bulkDownloadForm" method="POST" action="{{ route('students.downloadconfirmMultiple') }}" style="display:none;">
    @csrf
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

        // Put IDs as JSON into hidden input and submit the form
        $('#bulkIds').val(JSON.stringify(ids));
        $('#bulkIssueForm').submit(); // normal submit -> page reload
    });

    $('#moveSelected').click(function () {
        var ids = getSelectedIds();

        if (ids.length === 0) {
            alert('Select at least one student');
            return;
        }

        if (!confirm('Send students to certificate section?')) {
            return;
        }

        // Put IDs as JSON into hidden input and submit the form
        $('#bulkmove').val(JSON.stringify(ids));
        $('#bulkMoveStudent').submit(); // normal submit -> page reload
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
