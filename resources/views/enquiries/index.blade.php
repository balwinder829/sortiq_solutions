@extends('layouts.app')

@section('content')
<script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>
 <meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    table.dataTable td {
        text-transform: capitalize;
    }
</style>
<style>
        .whatsapp-btn {
            background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
            box-shadow: 0 4px 14px 0 rgba(37, 211, 102, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .whatsapp-btn:hover {
            box-shadow: 0 6px 20px 0 rgba(37, 211, 102, 0.4);
            transform: translateY(-2px);
        }
        .whatsapp-btn-global {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.3);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .whatsapp-btn-global:hover {
            box-shadow: 0 6px 20px 0 rgba(16, 185, 129, 0.5);
            transform: translateY(-2px);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
        }
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        /* CKEditor Dark Theme Styling */
        .ck-editor__editable_inline {
            background-color: rgba(15, 23, 42, 0.6) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            color: #f1f5f9 !important;
            min-height: 150px;
            border-bottom-left-radius: 12px !important;
            border-bottom-right-radius: 12px !important;
        }
        .ck-editor__editable_inline:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 1px #10b981 !important;
        }
        .ck-toolbar {
            background-color: rgba(30, 41, 59, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
        .ck-toolbar * {
            color: #e2e8f0 !important;
        }
        .ck-toolbar .ck-button:hover,
        .ck-toolbar .ck-button:active {
            background: rgba(255, 255, 255, 0.1) !important;
        }
        .ck-toolbar .ck-button.ck-on {
            background: rgba(16, 185, 129, 0.3) !important;
        }
        .ck-dropdown__panel {
            background: #1e293b !important;
        }
        .ck-list__item .ck-button:hover {
            background: rgba(255, 255, 255, 0.1) !important;
        }
    </style>

    @if(session('success') || session('error'))
        <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Clear localStorage
            localStorage.removeItem('selected_enquiries');

            // Clear JS variables
            selectedEnquiries = [];
            selectedRecordIds = new Set();

            // Uncheck checkboxes
            $('.rowCheck').prop('checked', false);
            $('#selectAll').prop('checked', false);

            // Hide bulk action bar
            syncBulkActionBar();

        });
        </script>
    @endif
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
                @include('partials.whatsapp-popover')
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


    <!-- Message Composer Panel -->
    <section class="glass rounded-2xl p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label for="message-input" class="block text-sm font-semibold text-slate-200 tracking-wide uppercase">
                        1. Compose Message
                    </label>
                    <span id="char-count" class="text-xs text-slate-500">0 characters</span>
                </div>
                <div class="relative">
                    <textarea 
                        id="message-input" 
                        rows="3" 
                        class="w-full bg-slate-900/60 border border-slate-700/80 rounded-xl px-4 py-3 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none text-base"
                        placeholder="Type your message here..."></textarea>
                </div>
            </div>
        </div>
    </section>

    <!-- Browser Popup Warning Alert -->
    <!-- <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4 flex gap-3 text-amber-400 text-sm glass">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <span class="font-semibold">Browser Popup Blockers:</span> When broadcasting to multiple students, your browser will try to block the tabs. Please click <strong>"Allow popups from this site"</strong> in your address bar when prompted so all chats open correctly.
        </div>
    </div> -->

    <!-- Sticky Bulk Action Bar -->
    <div id="bulk-bar" class="hidden glass border-emerald-500/30 bg-emerald-950/20 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 transition-all duration-300">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 font-bold" id="selected-count-badge">
                0
            </div>
            <div class="text-white font-medium">
                Students selected for broadcast
            </div>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button onclick="sendBulkWhatsApp()" class="w-full sm:w-auto whatsapp-btn-global px-5 py-2.5 rounded-lg text-white font-semibold flex items-center justify-center gap-2 hover:scale-102 transition-all text-sm">
                Send to Selected (<span id="btn-count">0</span>)
            </button>
            <button onclick="sendBulkWhatsAppWithName()" class="w-full sm:w-auto whatsapp-btn px-5 py-2.5 rounded-lg text-white font-semibold flex items-center justify-center gap-2 hover:scale-102 transition-all text-sm">
                Send with Name (<span id="btn-count-name">0</span>)
            </button>
        </div>
    </div>

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
    <div class="d-inline-flex align-items-center gap-1">

        <input type="checkbox"
               class="rowCheck"
               value="{{ $enquiry->id }}">

        @if($enquiry->assignedTo)
            <i class="fas fa-user-check text-warning  ml-1"
               data-bs-toggle="tooltip"
               title="Assigned to {{ $enquiry->assignedTo->name }}"></i>
        @endif

    </div>
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
                                @php
                                    $mobile = preg_replace('/\D/', '', $enquiry->mobile); // remove non-digits
                                    if(strlen($mobile) == 10){
                                        $mobile = '91'.$mobile;
                                    }
                                @endphp
                                 <button 
                                    onclick="sendSingleWhatsApp('{{ $mobile }}')"
                                    class="border-0"
                                >
                                    <i class="fa-brands fa-whatsapp"></i>
                                </button>
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

    

    <div class="mt-3">
        <button id="bulkMoveEnquiry" class="btn btn-warning">
        Move / Close Enquiry
    </button>
    <button id="assignBtn"
            class="btn btn-primary">
        Assign Selected Enquiries
    </button>
    </div>

    {{-- ======================== ASSIGN MULTIPLE ======================== --}}
    @if(auth()->user()->isAdmin())
    <!--     <div class="mt-4">
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
        </div> -->
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

<div class="modal fade" id="moveEnquiryModal" tabindex="-1">

    <div class="modal-dialog">

        <form id="moveEnquiryForm"
              method="POST"
              action="{{ route('enquiries.bulkMove') }}">

            @csrf

            <input type="hidden"
                   name="ids"
                   id="bulkMoveIds">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        Move / Close Enquiries
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <div class="mb-3">

                        <label class="form-label">
                            Action
                        </label>

                        <select class="form-control"
                                name="action"
                                id="bulkAction"
                                required>

                            <option value="">Select Action</option>

                            <option value="move">
                                Move To Another Session
                            </option>

                            <option value="close">
                                Close Enquiry
                            </option>

                        </select>

                    </div>


                    <div id="moveSection" style="display:none;">

                        <div class="mb-3">

                            <label class="form-label">
                                Session
                            </label>

                            <select class="form-control"
                                    name="session_id">

                                <option value="">
                                    Select Session
                                </option>

                                @foreach($saleSessions as $session)

                                    <option value="{{ $session->id }}">
                                        {{ $session->session_name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">

                            Reason

                        </label>

                        <textarea class="form-control"
                                  rows="3"
                                  name="reason"></textarea>

                    </div>

                </div>

                <div class="modal-footer">

                    <button class="btn btn-success">

                        Save

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="modal fade" id="assignModal" tabindex="-1">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Assign Selected Enquiries
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Salesperson
                    </label>

                    <select id="salesperson"
                            class="form-control">

                        <option value="">
                            Select Salesperson
                        </option>

                        @foreach($sales as $s)

                            <option value="{{ $s->id }}">
                                {{ $s->name }}
                            </option>

                        @endforeach

                    </select>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="confirmAssign"
                        class="btn btn-primary">

                    Assign

                </button>

            </div>

        </div>

    </div>

</div>
@endsection
{{-- ======================== SCRIPTS ======================== --}}
@push('scripts')


<script>

$('#bulkMoveEnquiry').click(function () {

    
    let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];

    if (ids.length === 0) {

        Swal.fire({
            icon: 'warning',
            text: 'Please select at least one enquiry'
        });

        return;
    }

    $('#bulkMoveIds').val(JSON.stringify(ids));

    $('#moveEnquiryModal').modal('show');

});

$('#bulkAction').change(function () {

    if ($(this).val() == 'move') {

        $('#moveSection').show();

    } else {

        $('#moveSection').hide();

    }

});

// $('#moveEnquiryForm').submit(function(e){

//     e.preventDefault();

//     let ids = $('#bulkMoveIds').val();

//     $('<input>')
//         .attr({
//             type:'hidden',
//             name:'action',
//             value:$('#bulkAction').val()
//         })
//         .appendTo('#bulkMoveEnquiryForm');

//     $('<input>')
//         .attr({
//             type:'hidden',
//             name:'session_id',
//             value:$('select[name=session_id]').val()
//         })
//         .appendTo('#bulkMoveEnquiryForm');

//     $('<input>')
//         .attr({
//             type:'hidden',
//             name:'reason',
//             value:$('textarea[name=reason]').val()
//         })
//         .appendTo('#bulkMoveEnquiryForm');

//     $('#bulkMoveEnquiryForm').submit();

// });


    $('#moveEnquiryForm').submit(function(e){

    let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];

    if(ids.length===0){

        e.preventDefault();

        showPopup('Please select at least one enquiry.');

        return;

    }

    $('#bulkMoveIds').val(JSON.stringify(ids));

    if($('#bulkAction').val()=="move" &&
       $('[name=session_id]').val()==""){

        e.preventDefault();

        showPopup('Please select session.');

        return;

    }

    localStorage.removeItem('selected_enquiries');

});

    $('#moveEnquiryModal').on('hidden.bs.modal',function(){

        $('#moveEnquiryForm')[0].reset();

        $('#moveSection').hide();

        $('#otherReason').hide();

    });

let selectedEnquiries = JSON.parse(localStorage.getItem('selected_enquiries')) || [];
let selectedRecordIds = new Set(selectedEnquiries.map(id => parseInt(id)));
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
        syncBulkActionBar();
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
            console.log({ selectedRecordIds });
            selectedRecordIds.add(parseInt(id));
        }

    }else{

        // selectedEnquiries = selectedEnquiries.filter(e => e != id);
        // selectedRecordIds = selectedEnquiries.map(id => parseInt(id));
        // Remove from array
        selectedEnquiries = selectedEnquiries.filter(e => e != id);

        // Remove from Set
        selectedRecordIds.delete(parseInt(id));

    }
    syncBulkActionBar();
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
                selectedRecordIds.add(parseInt(id));
            }

        }else{

            $(this).prop('checked',false);
            selectedEnquiries = selectedEnquiries.filter(e => e != id);
            // selectedRecordIds = new Set(selectedEnquiries.map(id => parseInt(id)));
            selectedRecordIds.delete(parseInt(id));
        }
    });
    syncBulkActionBar();

    localStorage.setItem('selected_enquiries',JSON.stringify(selectedEnquiries));
    console.log(selectedEnquiries);

});

/* ================================
   ASSIGN BUTTON
================================ */
// $('#assignBtn').on('click',function(){

//     let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];
//     let salesId = $('#salesperson').val();

//     if(ids.length === 0){
//         showPopup('Please select at least one enquiry.');
//         return;
//     }

//     if(!salesId){
//         showPopup('Please select a salesperson.');
//         return;
//     }

//     fetch("{{ route('enquiries.assign') }}",{
//         method:"POST",
//         headers:{
//             "Content-Type":"application/json",
//             "X-CSRF-TOKEN":"{{ csrf_token() }}"
//         },
//         body:JSON.stringify({
//             enquiry_ids:ids,
//             salesperson_id:salesId
//         })
//     })
//     .then(res=>res.json())
//     .then(data=>{

//         localStorage.removeItem('selected_enquiries');

//         if(data.message){
//             location.reload();
//         }

//     });

// });

$('#assignBtn').on('click',function(){

    let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            text: 'Please select at least one enquiry'
        });
        return;
    }

    $('#assignModal').modal('show');

});

$('#confirmAssign').on('click',function(){

    let ids = JSON.parse(localStorage.getItem('selected_enquiries')) || [];
    let salesId = $('#salesperson').val();

    if(ids.length === 0){
        Swal.fire({
            icon: 'warning',
            text: 'Please select at least one enquiry'
        });
        return;
    }

    if(!salesId){
        Swal.fire({
            icon: 'warning',
            text: 'Please select a salesperson'
        });
        // showPopup('Please select a salesperson.');
        // return;
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


new bootstrap.Popover(document.getElementById('whatsappBtn'), {
    html: true,
    sanitize: false,
    customClass: 'whatsapp-popover',
    content: function () {
        return $('#whatsappPopoverContent').html();
    }
});

$(document).on('click', '#sendWhatsappNotification', function () {
    
    let popover = $(this).closest('.popover');
    let custom_message = popover.find('textarea[name="customMessage"]').val();
    let append_name = $('input[name="append_name"]:checked').val() || false;

    if (selectedRecordIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }
    
    if (custom_message?.length > 0) {
        custom_message = custom_message;
    } else {
        Swal.fire('Message Required', 'Please enter a custom message', 'warning');
        return;
    }

    let formData = new FormData();
    formData.append('append_name',append_name);
    formData.append('message',custom_message);
    formData.append('model', "Enquiry");
    formData.append('_token', "{{ csrf_token() }}");
    formData.append('message_type', append_name ? 'with_name' : 'same_message');
    Array.from(selectedRecordIds).forEach(function(id) {
        formData.append('ids[]', id);
    });
    
    formData.append('existing_file_path', popover.find('#existingFile').val());
    let fileInput = popover.find('input[type="file"]')[0];
    if (fileInput && fileInput.files.length > 0) {
        formData.append('whatsappFile', fileInput.files[0]);
        //console.log(fileInput.files[0].name);
    }

    $.ajax({
        url: "{{ route('admin.message.send_whatsapp') }}",
        type: "POST",
        // processData: false,
        // contentType: false,
        data: formData,
        beforeSend: function () {
            popover.find('#message_loader').show();
            popover.find('#sendWhatsappNotification').prop('disabled', true);
        },
        success: function (res) {
            if(res.status === 'error' || res.status === false) {
                Swal.fire('Error', res.message, 'error');
                return;
            }
            Swal.fire('Success', res.message, 'success');
        },
        error: function () {
            Swal.fire('Error', 'Something went wrong', 'error');
        },
        complete: function () {
            popover.find('#message_loader').hide();
            popover.find('#sendWhatsappNotification').prop('disabled', false);
        }
    });
});

    const students = @json($enquiries);
    // State variables

    let filteredStudents = [...students?.data];
    let selectedStudentIds = new Set();
    let currentPage = 1;
    let pageSize = 15;
    let searchQuery = '';

    // Dom elements
    const tableBody = document.getElementById('table-body');
    const paginationInfo = document.getElementById('pagination-info');
    const paginationControls = document.getElementById('pagination-controls');

    const selectedCountBadge = document.getElementById('selected-count-badge');
    const btnCount = document.getElementById('btn-count');
    const btnCountName = document.getElementById('btn-count-name');
    const btnCountApi = document.getElementById('btn-count-api');
    const selectAllCheckbox = document.getElementById('select-all-checkbox');

    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }
        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('file', file);

                    fetch('/upload-pdf', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: data
                    })
                    .then(response => response.json())
                    .then(result => {
                        if (result.success) {
                            resolve({
                                default: result.url
                            });
                        } else {
                            reject(result.message || 'Upload failed');
                        }
                    })
                    .catch(error => {
                        reject(error);
                    });
                }));
        }
        abort() {}
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    // Initialize CKEditor 5
    let editorInstance;
    ClassicEditor
        .create(document.querySelector('#message-input'), {
            extraPlugins: [MyCustomUploadAdapterPlugin]
        })
        .then(editor => {
            editorInstance = editor;
            
            // Track character count
            editor.model.document.on('change:data', () => {
                const data = editor.getData();
                const countSpan = document.getElementById('char-count');
                const plainText = data.replace(/<[^>]*>/g, '');
                countSpan.textContent = `${plainText.length} characters`;
            });

            // Dynamically inject our "Attach File" button into the CKEditor toolbar DOM
            const toolbarItems = editor.ui.view.toolbar.element.querySelector('.ck-toolbar__items');
            if (toolbarItems) {
                const separator = document.createElement('span');
                separator.className = 'ck ck-toolbar__separator';
                toolbarItems.appendChild(separator);

                const uploadBtn = document.createElement('button');
                uploadBtn.type = 'button';
                uploadBtn.className = 'ck ck-button ck-off custom-upload-btn';
                uploadBtn.setAttribute('title', 'Attach File (PDF, Doc, Image)');
                uploadBtn.setAttribute('aria-label', 'Attach File');
                uploadBtn.innerHTML = `
                    <svg class="ck-icon" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" style="width:16px; height:16px;">
                        <path fill="currentColor" d="M11.666 4.793v7.354a3.146 3.146 0 0 1-5.37 2.224 3.146 3.146 0 0 1 0-4.448l4.447-4.448a1.573 1.573 0 0 1 2.224 0 1.573 1.573 0 0 1 0 2.224l-4.447 4.448a.524.524 0 0 1-.74 0 .524.524 0 0 1 0-.741l4.077-4.077a.524.524 0 1 0-.741-.74l-4.077 4.076a1.573 1.573 0 0 0 0 2.225 1.573 1.573 0 0 0 2.224 0l4.448-4.448a2.622 2.622 0 0 0-3.708-3.708l-4.448 4.448a4.195 4.195 0 0 0 0 5.93 4.195 4.195 0 0 0 5.93 0l4.448-4.448a.524.524 0 1 0-.74-.741l-4.449 4.448a3.146 3.146 0 0 1-4.448-4.448l4.448-4.448a.524.524 0 0 1 .74 0 .524.524 0 0 1 0 .741z"/>
                    </svg>
                `;
                toolbarItems.appendChild(uploadBtn);

                uploadBtn.addEventListener('click', () => {
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.accept = '.pdf,.doc,.docx,.png,.jpg,.jpeg';
                    
                    fileInput.onchange = () => {
                        const file = fileInput.files[0];
                        if (!file) return;

                        uploadBtn.style.opacity = '0.5';
                        uploadBtn.setAttribute('title', 'Uploading...');

                        const formData = new FormData();
                        formData.append('file', file);

                        fetch('/upload-pdf', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            uploadBtn.style.opacity = '1';
                            uploadBtn.setAttribute('title', 'Attach File (PDF, Doc, Image)');

                            if (data.success) {
                                editor.model.change(writer => {
                                    const insertPosition = editor.model.document.selection.getFirstPosition();
                                    const link = writer.createText(` ${data.fileName} `, { linkHref: data.url });
                                    editor.model.insertContent(link, insertPosition);
                                });
                            } else {
                                alert('Upload failed: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            uploadBtn.style.opacity = '1';
                            uploadBtn.setAttribute('title', 'Attach File (PDF, Doc, Image)');
                            console.error('Error:', error);
                            alert('An error occurred during file upload.');
                        });
                    };
                    fileInput.click();
                });
            }
        })
        .catch(error => {
            console.error(error);
        });


    // Sync visual bulk banner
    function syncBulkActionBar() {
        const count = selectedRecordIds.size;
        console.log(count);
        console.log(selectedRecordIds);
        if (count > 0) {
            const bulkBar = document.getElementById('bulk-bar');
            bulkBar.classList.remove('hidden');
            selectedCountBadge.textContent = count;
            btnCount.textContent = count;
            if (btnCountName) {
                btnCountName.textContent = count;
            }
            if (btnCountApi) {
                btnCountApi.textContent = count;
            }
        } else {
            const bulkBar = document.getElementById('bulk-bar');
            bulkBar.classList.add('hidden');
            btnCount.textContent = count;
            selectedCountBadge.textContent = count;
            if (btnCountName) {
                btnCountName.textContent = count;
            }
            if (btnCountApi) {
                btnCountApi.textContent = count;
            }
        }
    }


    // Helper to convert CKEditor HTML to WhatsApp plain-text markdown
    function getWhatsAppMessage() {
        let html = editorInstance ? editorInstance.getData() : document.getElementById('message-input').value;
        
        // 1. Replace bold tags with *
        html = html.replace(/<(strong|b)>(.*?)<\/\1>/gi, '*$2*');
        
        // 2. Replace italic tags with _
        html = html.replace(/<(em|i)>(.*?)<\/\1>/gi, '_$2_');
        
        // 3. Replace strikethrough tags with ~
        html = html.replace(/<(s|strike|del)>(.*?)<\/\1>/gi, '~$2~');
        
        // 4. Handle links - format as "Text (URL)"
        html = html.replace(/<a\s+(?:[^>]*?\s+)?href="([^"]*)"[^>]*>(.*?)<\/a>/gi, '$2 ($1)');
        
        // 5. Replace list items and blockquotes to clean text format
        html = html.replace(/<li>(.*?)<\/li>/gi, '• $1\n');
        html = html.replace(/<\/li>/gi, '\n');
        html = html.replace(/<blockquote>(.*?)<\/blockquote>/gi, '> $1\n');

        // 6. Replace paragraph end tags or break tags with newlines
        html = html.replace(/<\/p>/gi, '\n');
        html = html.replace(/<p>/gi, '');
        html = html.replace(/<br\s*\/?>/gi, '\n');
        
        // 7. Strip all other HTML tags
        let tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;
        let text = tempDiv.textContent || tempDiv.innerText || "";
        
        return text.trim();
    }

    // Single WhatsApp Redirect
    function sendSingleWhatsApp(phone) {
        const cleanMessage = getWhatsAppMessage();
        const url = `https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent(cleanMessage)}`;
        window.open(url, '_blank');
    }

    // Dynamic Bulk WhatsApp Sending
    function sendBulkWhatsApp() {
        const cleanMessage = getWhatsAppMessage();
        const encodedMessage = encodeURIComponent(cleanMessage);
        
        // Filter original list for selected students to resolve their phone numbers
        const selectedStudents = filteredStudents.filter(student => selectedRecordIds.has(student.id));
        console.log({ selectedStudents, selectedRecordIds });
        if (selectedStudents.length === 0) return;

        // Open all tabs synchronously within the click event thread context
        selectedStudents.forEach((student) => {

            let mobile = (student.mobile || '').replace(/\D/g, '');
            if (mobile.length === 10) {
                mobile = '91' + mobile;
            }
            const url = `https://api.whatsapp.com/send?phone=${mobile}&text=${encodedMessage}`;
            window.open(url, '_blank');
        });
    }

    // Dynamic Bulk WhatsApp Sending with personalized student names
    function sendBulkWhatsAppWithName() {
        const cleanMessage = getWhatsAppMessage();
        
        // Filter original list for selected students to resolve their phone numbers and names
        const selectedStudents = filteredStudents.filter(student => selectedRecordIds.has(student.id));
        console.log({ selectedStudents, selectedRecordIds})
        if (selectedStudents.length === 0) return;

        // Open all tabs synchronously within the click event thread context
        selectedStudents.forEach((student) => {
            let mobile = (student.mobile || '').replace(/\D/g, '');
            if (mobile.length === 10) {
                mobile = '91' + mobile;
            }
            const personalizedMessage = `Hii ${student.name}\n\n${cleanMessage}`;
            const url = `https://api.whatsapp.com/send?phone=${mobile}&text=${encodeURIComponent(personalizedMessage)}`;
            window.open(url, '_blank');
        });
    }

</script>
@endpush