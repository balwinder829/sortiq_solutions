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
        <h3>Certificates</h3>
    </div>




    {{-- Search / Filter Form --}}
<form method="GET" action="{{ route('certificates.index') }}" class="mb-4">
    <div class="row g-2">
        {{-- Student Name --}}
        <div class="col-md-2">
            <input type="text" name="student_name" class="form-control"
                   placeholder="Student Name" value="{{ request('student_name') }}">
        </div>

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

        {{-- Buttons --}}
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
        <div class="col-md-1 d-grid">
            <a href="{{ route('certificates.index') }}" class="btn btn-secondary">Reset</a>
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
                <th class="text-center">ID</th>
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
                
                <th class="text-center" width="100px">Date of Joining</th>
                <th class="text-center">Duration</th>
                <th class="text-center" width="100px">Start Date</th>
                <th class="text-center" width="100px">End Date</th>
                 <th class="text-center">Part-Time Job Offer</th>
                <th class="text-center">Placement Offer</th>
                <th class="text-center">PG Offer</th>
                <th class="text-center">Email Count</th>
                <th class="text-center">Receipt Count</th>
                <th width="100px" class="text-center">Action</th>
            </tr>
            </tr>
        </thead>
            <tbody>
            @foreach ($students as $student)
            <tr>
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
                <td>{{ $loop->iteration }}</td>
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
                <td class="{{ $student->pending_fees > 0 ? 'text-danger fw-bold' : '' }}">{{ $student->pending_fees }}</td>
                
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
                <td>{{ $student->email_count_certificate ?? 0 }}</td>
                 <td>{{ $student->count_receipt_download ?? 0 }}</td>
                <td class="text-center">
                    <div class="mb-2">
                        {{-- Issue --}}
                        <form action="{{ route('students.issueCertificate', $student->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Issue Certificate" onclick="return confirm('Send certificate to {{ $student->email_id }}?')">
                                <i class="fa-solid fa-file-lines"></i>
                            </button>
                        </form>

                        {{-- Edit --}}
                        <a href="{{ route('certificates.edit',$student->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>

                    <span class="badge bg-light text-dark">Email count: {{ $student->email_count_certificate }}</span>
                </td>
            </tr>
            @endforeach
        </tbody>
        </table>
    </div>

    {{-- Multi-action buttons --}}
    <div class="mt-3">
        <button id="issueSelected" class="btn btn-primary">Issue Certificate</button>
        <button id="downloadissueSelected" class="btn btn-primary">Download Certificates</button>
    </div>

    {{ $students->links() }}
</div>

{{-- Hidden form for bulk issuing (submits like single-row form) --}}
<form id="bulkIssueForm" method="POST" action="{{ route('students.issueMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkIds">
</form>

<form id="bulkDownloadForm" method="POST" action="{{ route('students.downloadCertificateMultiple') }}" style="display:none;">
    @csrf
    <input type="hidden" name="ids" id="bulkDownloadIds">
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Initialize DataTable
    var table = $('#certificatesTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true,
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

});
</script>
@endpush
