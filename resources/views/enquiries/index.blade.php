@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>

<div class="container">

    {{-- Add Enquiry Button --}}
    

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Manage Data</h1>
        </div>

        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                   <a href="{{ route('enquiries.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">        Add Data </a>
                    <a href="{{ route('enquiries.importForm') }}" class="btn mb-3 ml-2" style="background-color: #6b51df; color: #fff;">        Import </a>
            </div>
        </div>
    </div>
    <div class="row mb-3">

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6>Total Leads</h6>
                <h3 class="text-primary">{{ $totalLeads }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6>Assigned Leads</h6>
                <h3 class="text-success">{{ $assignedLeads }}</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm text-center">
            <div class="card-body">
                <h6>Unassigned Leads</h6>
                <h3 class="text-danger">{{ $unassignedLeads }}</h3>
            </div>
        </div>
    </div>

</div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ======================== FILTERS ======================== --}}
    <form method="GET" id="filterForm" class="mb-3">
        <div class="row">

            <div class="col-md-3 mb-2">
                <label><strong>College</strong></label>
                <select name="college" class="form-control filterchange select2">
                    <option value="">All</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}"
                            {{ request('college') == $college->id ? 'selected' : '' }}>
                            {{ $college->FullName }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Salesperson Filter (ADMIN ONLY) --}}
            @if(auth()->user()->isAdmin())
            <div class="col-md-3 mb-2">
                <label><strong>Salesperson</strong></label>
                <select name="salesperson_id" class="form-control filterchange">
                    <option value="">All</option>
                    @foreach($sales as $s)
                        <option value="{{ $s->id }}"
                            {{ request('salesperson_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif


            <div class="col-md-3 mb-2">
                <label><strong>Study</strong></label>
                <input type="text" name="study" class="form-control filterchangetext"
                       value="{{ request('study') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label><strong>Semester</strong></label>
                <input type="text" name="semester" class="form-control filterchangetext"
                       value="{{ request('semester') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label><strong>Lead Status</strong></label>
                <select name="lead_status" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="new" {{ request('lead_status')=='new' ? 'selected' : '' }}>New</option>
                    <option value="followup" {{ request('lead_status')=='followup' ? 'selected' : '' }}>Follow-up</option>
                    <option value="registered" {{ request('lead_status')=='registered' ? 'selected' : '' }}>Registered</option>
                    <option value="closed" {{ request('lead_status')=='closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            
            <!-- <div class="col-md-3 mb-2">
                <label><strong>Source</strong></label>
                <select name="source_type" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="excel" {{ request('source_type')=='excel' ? 'selected' : '' }}>Excel</option>
                    <option value="manual" {{ request('source_type')=='manual' ? 'selected' : '' }}>Manual</option>
                    <option value="online" {{ request('source_type')=='online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('source_type')=='offline' ? 'selected' : '' }}>Offline</option>
                </select>
            </div> -->
            <div class="col-md-3 mb-2">
                <label><strong>Source</strong></label>
            <select name="source_type" class="form-control filterchange">
                <option value="">All</option>

                <option value="excel" {{ request('source_type')=='excel' ? 'selected' : '' }}>Excel</option>
                <option value="manual" {{ request('source_type')=='manual' ? 'selected' : '' }}>Manual</option>
                <option value="online" {{ request('source_type')=='online' ? 'selected' : '' }}>Online Exam</option>
                <!-- <option value="offline" {{ request('source_type')=='offline' ? 'selected' : '' }}>Offline</option> -->
                <option value="gmail" {{ request('source_type')=='gmail' ? 'selected' : '' }}>Gmail</option>
                <option value="manual_data" {{ request('source_type')=='manual_data' ? 'selected' : '' }}>Manual Data</option>
                <option value="attendance" {{ request('source_type')=='attendance' ? 'selected' : '' }}>Attendance</option>
                <option value="hard_data" {{ request('source_type')=='hard_data' ? 'selected' : '' }}>Hard Data</option>
            </select>
        </div>

            <div class="col-md-3 mb-2">
                <label><strong>Registered</strong></label>
                <select name="registered" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="yes" {{ request('registered')=='yes' ? 'selected' : '' }}>Yes</option>
                    <option value="no" {{ request('registered')=='no' ? 'selected' : '' }}>No</option>
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <label><strong>Quick Date</strong></label>
                <select name="quick_date" class="form-control filterchange">
                    <option value="">Select</option>
                    <option value="today" {{ request('quick_date')=='today' ? 'selected' : '' }}>Today</option>
                    <option value="yesterday" {{ request('quick_date')=='yesterday' ? 'selected' : '' }}>Yesterday</option>
                    <option value="last7" {{ request('quick_date')=='last7' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="this_month" {{ request('quick_date')=='this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="last_month" {{ request('quick_date')=='last_month' ? 'selected' : '' }}>Last Month</option>
                </select>
            </div>


            <div class="col-md-3 mb-2">
                <label><strong>From Date</strong></label>
                <input type="date" name="from_date" class="form-control filterchange"
                       value="{{ request('from_date') }}">
            </div>

            <div class="col-md-3 mb-2">
                <label><strong>To Date</strong></label>
                <input type="date" name="to_date" class="form-control filterchange"
                       value="{{ request('to_date') }}">
            </div>

            <div class="col-md-3">
                <label><strong>Follow-up Status</strong></label>
                <select name="followup_filter" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="today" {{ request('followup_filter')=='today' ? 'selected' : '' }}>
                        Due Today
                    </option>
                    <option value="overdue" {{ request('followup_filter')=='overdue' ? 'selected' : '' }}>
                        Overdue / Missed
                    </option>
                    <option value="upcoming" {{ request('followup_filter')=='upcoming' ? 'selected' : '' }}>
                        Upcoming
                    </option>
                    <option value="none" {{ request('followup_filter')=='none' ? 'selected' : '' }}>
                        No Follow-up Set
                    </option>
                </select>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="col-md-3">
                <label><strong>Assigned Status</strong></label>
                <select name="assigned_status" class="form-control filterchange">
                    <option value="">All</option>

                    <option value="assigned" {{ request('assigned_status')=='assigned' ? 'selected' : '' }}>
                        Assigned
                    </option>

                    <option value="unassigned" {{ request('assigned_status')=='unassigned' ? 'selected' : '' }}>
                        Unassigned
                    </option>
                </select>
            </div>
            @endif


        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <!-- <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button> -->
                <a href="{{ route('enquiries.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>

                <a href="{{ route('enquiries.export', request()->query()) }}"
                   class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Download Excel
                </a>
            </div>
        </div>
    </form>
    {{-- ======================== END FILTERS ======================== --}}

    
{{-- ======================== UPLOAD EXCEL (UPDATED) ======================== --}}
<!-- <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <strong>Import Data</strong>
    </div>

    <div class="card-body">
        <form action="{{ route('enquiries.import') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row align-items-center">

                {{-- File Input --}}
                <div class="col-md-5 mb-2">
                    <input type="file" name="file" class="form-control" required>
                </div>

                {{-- Upload Button --}}
                <div class="col-md-3 mb-2">
                    <button class="btn btn-secondary w-100">
                        <i class="fa fa-upload"></i> Upload Excel/CSV
                    </button>
                </div>

                {{-- Download Sample File --}}
                <div class="col-md-4 mb-2 text-end">
                    <a href="{{ asset('sample/sample_record_file.xlsx') }}" 
                       class="btn btn-outline-primary"
                       download>
                        <i class="fa fa-file-excel"></i> Download Sample File
                    </a>
                </div>

            </div>
        </form>
    </div>
</div> -->
{{-- ======================== END UPLOAD ======================== --}}

    {{-- ======================== TABLE ======================== --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="enquiriesTable">
            <thead>
                <tr>
                    @if(auth()->user()->isAdmin())
                        <th><input type="checkbox" id="selectAll"></th>
                    @endif
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>College</th>
                    <th>Study</th>
                    <th>Semester</th>
                    <th>Assigned To</th>
                    <th>Lead Status</th>
                    <th>Follow-up</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($enquiries as $enquiry)
                    <tr>
                        @if(auth()->user()->isAdmin())
                            <td class="text-center">
                                @if(!$enquiry->assigned_to)
                                    <input type="checkbox" class="rowCheck" value="{{ $enquiry->id }}">
                                @else
                                    <span class="badge bg-secondary">Assigned</span>
                                @endif
                            </td>
                        @endif

                        <td>{{ $enquiries->firstItem() + $loop->index }}</td>
                        <td>{{ $enquiry->name }}</td>
                        <td>{{ $enquiry->mobile }}</td>
                        <td>{{ $enquiry->email }}</td>
                        <td>{{ $enquiry->collegeData->FullName ?? '-' }}</td>
                        <td>{{ $enquiry->study }}</td>
                        <td>{{ $enquiry->semester }}</td>
                        <td>{{ $enquiry->assignedTo->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">
                                {{ $enquiry->lead_status }}
                            </span>
                        </td>
                        <td>
                            @if($enquiry->next_followup_at)
                                @if(optional($enquiry->next_followup_at)->isToday())
                                    <span class="badge bg-warning text-dark">Today</span>
                                @elseif($enquiry->next_followup_at->isPast())
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-success">
                                        {{ $enquiry->next_followup_at->format('d M') }}
                                    </span>
                                @endif
                            @else
                                <span class="badge bg-secondary">Not Set</span>
                            @endif
                        </td>

                        <td class="no-wrap" style="width: 150px;">
                            <div class="d-flex gap-1">
                            <a href="{{ route('enquiries.show', $enquiry->id) }}" class="btn btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('enquiries.edit', $enquiry->id) }}" class="btn btn-sm">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $enquiries->links('pagination::bootstrap-5') }}
    </div>

    {{-- ======================== ASSIGN MULTIPLE ======================== --}}
    @if(auth()->user()->isAdmin())
        <div class="mt-4">
            <h5><strong>Assign Selected Enquiries</strong></h5>

            <select id="salesperson" class="form-control mb-2">
                <option value="">Select Salesperson</option>
                @foreach($sales as $s)
                    <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>

            <button id="assignBtn" class="btn btn-primary">
                Assign Selected
            </button>
        </div>
    @endif

    {{-- Popup Modal --}}
    <div class="modal fade" id="popupModal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title">Alert</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center" id="popupMessage"></div>
                <div class="modal-footer justify-content-center py-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
{{-- ======================== SCRIPTS ======================== --}}
@push('scripts')

<script>

let selectedEnquiries = JSON.parse(localStorage.getItem('selected_enquiries')) || [];

/* ================================
   DATATABLE
================================ */
$(document).ready(function () {

    $('#enquiriesTable').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false,
        pageLength: 50,
        order:[]
    });

    restoreCheckedBoxes();

});

/* ================================
   RESTORE CHECKBOXES WHEN PAGE LOAD
================================ */
function restoreCheckedBoxes(){

    $('.rowCheck').each(function(){

        let id = $(this).val();

        if(selectedEnquiries.includes(id)){
            $(this).prop('checked', true);
        }

    });

}

/* ================================
   SINGLE CHECKBOX SELECT
================================ */
$(document).on('change','.rowCheck',function(){

    let id = $(this).val();

    if($(this).is(':checked')){

        if(!selectedEnquiries.includes(id)){
            selectedEnquiries.push(id);
        }

    }else{

        selectedEnquiries = selectedEnquiries.filter(e => e != id);

    }

    localStorage.setItem('selected_enquiries',JSON.stringify(selectedEnquiries));

});

/* ================================
   SELECT ALL (CURRENT PAGE)
================================ */
$('#selectAll').on('change',function(){

    $('.rowCheck:enabled').each(function(){

        let id = $(this).val();

        if($('#selectAll').is(':checked')){

            $(this).prop('checked',true);

            if(!selectedEnquiries.includes(id)){
                selectedEnquiries.push(id);
            }

        }else{

            $(this).prop('checked',false);
            selectedEnquiries = selectedEnquiries.filter(e => e != id);

        }

    });

    localStorage.setItem('selected_enquiries',JSON.stringify(selectedEnquiries));

});

/* ================================
   ASSIGN BUTTON
================================ */
$('#assignBtn').on('click',function(){

    let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];
    let salesId = $('#salesperson').val();

    if(ids.length === 0){
        showPopup('Please select at least one enquiry.');
        return;
    }

    if(!salesId){
        showPopup('Please select a salesperson.');
        return;
    }

    fetch("{{ route('enquiries.assign') }}",{
        method:"POST",
        headers:{
            "Content-Type":"application/json",
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        },
        body:JSON.stringify({
            enquiry_ids:ids,
            salesperson_id:salesId
        })
    })
    .then(res=>res.json())
    .then(data=>{

        localStorage.removeItem('selected_enquiries');

        if(data.message){
            location.reload();
        }

    });

});

/* ================================
   POPUP FUNCTION
================================ */
function showPopup(message){

    document.getElementById('popupMessage').innerHTML = message;
    var popup = new bootstrap.Modal(document.getElementById('popupModal'));
    popup.show();

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



