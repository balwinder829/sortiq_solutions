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
            <h1 class="page_heading">Manage Passouts Data</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                    <button type="button" id="sendPassoutWhatsApp" class="btn btn-success mb-3 ml-2">
                        <i class="fab fa-whatsapp"></i> Send WhatsApp
                    </button>
                
                   <a href="{{ route('passouts.create') }}" class="btn mb-3 ml-2" style="background-color: #6b51df; color: #fff;">        Add Data </a>

                    <a href="{{ route('passouts.importForm') }}" class="btn mb-3 ml-2" style="background-color: #6b51df; color: #fff;">        Import </a>
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

            
            <div class="col-md-3 mb-2">
                <label><strong>Source</strong></label>
                <select name="source_type" class="form-control filterchange">
                    <option value="">All</option>
                    <option value="excel" {{ request('source_type')=='excel' ? 'selected' : '' }}>Excel</option>
                    <option value="manual" {{ request('source_type')=='manual' ? 'selected' : '' }}>Manual</option>
                    <option value="online" {{ request('source_type')=='online' ? 'selected' : '' }}>Online</option>
                    <option value="offline" {{ request('source_type')=='offline' ? 'selected' : '' }}>Offline</option>
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


        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <!-- <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button> -->
                
                <a href="{{ route('passouts.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>

                <a href="{{ route('passouts.export', request()->query()) }}"
                   class="btn btn-success">
                    <i class="fa fa-file-excel"></i> Download Excel
                </a>
            </div>
        </div>
    </form>
    {{-- ======================== END FILTERS ======================== --}}

    {{-- ======================== TABLE ======================== --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="enquiriesTable">
            <thead>
                <tr>
                    @if(auth()->user()->isAdmin())
                        <th><input type="checkbox" id="passoutSelectAll"></th>
                    @endif
                    <th>#</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Email</th>
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
                                <input
                                    type="checkbox"
                                    class="passoutRowCheck"
                                    value="{{ $enquiry->id }}"
                                     data-name="{{ $enquiry->name }}"
                                    data-mobile="{{ $enquiry->mobile }}">
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
                            <a href="{{ route('passouts.show', $enquiry->id) }}" class="btn btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('passouts.edit', $enquiry->id) }}" class="btn btn-sm">
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
    {{-- ======================== PASSOUT BULK ACTIONS ======================== --}}
@if(auth()->user()->isAdmin())
    
    

    <div
        id="passoutBulkActionBar"
        class="mt-3"
        style="display:block;">

        <div class="d-flex align-items-center gap-2">

            <button
                type="button"
                id="passoutMoveCloseBtn"
                class="btn btn-warning">

                
                Move / Close
            </button>

            <button
                type="button"
                id="passoutAssignTrainerBtn"
                class="btn btn-primary">

                
                Assign Trainer
            </button>

            <button
                type="button"
                id="passoutClearSelection"
                class="btn btn-secondary">

                Clear Selection

            </button>

        </div>

    </div>

@endif

{{-- ======================== PASSOUT MOVE / CLOSE MODAL ======================== --}}
@if(auth()->user()->isAdmin())

<div class="modal fade"
     id="passoutMoveCloseModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Passout Bulk Action
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Action
                    </label>

                    <select
                        id="passoutBulkAction"
                        class="form-control">

                        <option value="">
                            Select Action
                        </option>

                        <option value="move">
                            Move To Another Session
                        </option>

                        <option value="close">
                            Close Selected
                        </option>

                    </select>

                </div>


                {{-- SESSION --}}
                <div
                    id="passoutSessionSection"
                    style="display:none;">

                    <div class="mb-3">

                        <label class="form-label">
                            Select Session
                        </label>

                        <select
                            id="passoutTargetSession"
                            class="form-control">

                            <option value="">
                                Select Session
                            </option>

                            @foreach($saleSessions as $session)

                                <option value="{{ $session->id }}">

                                    {{ ucwords($session->session_name) }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>


                {{-- CLOSE REASON --}}
                <div
                    id="passoutReasonSection"
                    style="display:block;">

                    <div class="mb-3">

                        <label class="form-label">
                            Reason
                        </label>

                        <textarea
                            id="passoutCloseReason"
                            class="form-control"
                            rows="3"
                            placeholder="Enter reason for closing">
                        </textarea>

                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    type="button"
                    id="passoutConfirmBulkAction"
                    class="btn btn-primary">

                    Continue

                </button>

            </div>

        </div>

    </div>

</div>

@endif

{{-- ======================== PASSOUT TRAINER MODAL ======================== --}}
@if(auth()->user()->isAdmin())

<div class="modal fade"
     id="passoutTrainerModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Assign Trainer
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <label class="form-label">
                    Select Trainer
                </label>

                <select
                    id="passoutTrainer"
                    class="form-control">

                    <option value="">
                        Select Trainer
                    </option>

                    @foreach($sales ?? [] as $trainer)

                        <option value="{{ $trainer->id }}">
                            {{ $trainer->name }}
                        </option>

                    @endforeach

                </select>

            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancel

                </button>

                <button
                    type="button"
                    id="passoutConfirmTrainer"
                    class="btn btn-primary">

                    Assign Trainer

                </button>

            </div>

        </div>

    </div>

</div>

@endif

</div>
@endsection
{{-- ======================== SCRIPTS ======================== --}}
@push('scripts')

<script>
$(document).ready(function () {
    $('#enquiriesTable').DataTable({
        paging: false,
        info: false,
        ordering: false,   // 🔒 STOP ROW SHUFFLING
    searching: false,  // 🔒 PREVENT REDRAW
        pageLength: 50
    });
});

// $('#selectAll').on('change', function() {
//     $('.rowCheck:enabled').prop('checked', this.checked);
// });



/* =========================================================
   INITIAL LOAD
   ========================================================= */
 /* =========================================================
   PASSOUT SELECTION + BULK ACTIONS
   ========================================================= */

let selectedPassouts =
    JSON.parse(localStorage.getItem('selected_passouts')) || [];

    let selectedPassoutWhatsAppRecords = new Map(
        JSON.parse(localStorage.getItem('selected_passout_whatsapp_records') || '[]')
    );

    function savePassoutWhatsAppRecords() {
        localStorage.setItem(
            'selected_passout_whatsapp_records',
            JSON.stringify(Array.from(selectedPassoutWhatsAppRecords.entries()))
        );
    }


/* =========================================================
   SAVE SELECTION
   ========================================================= */

function savePassoutSelection() {

    localStorage.setItem(
        'selected_passouts',
        JSON.stringify(selectedPassouts)
    );

}


/* =========================================================
   SELECT ALL STATE
   ========================================================= */

function syncPassoutSelectAll() {

    let total =
        $('.passoutRowCheck:enabled').length;

    let checked =
        $('.passoutRowCheck:enabled:checked').length;

    $('#passoutSelectAll').prop(
        'checked',
        total > 0 && total === checked
    );

}


/* =========================================================
   RESTORE SELECTION AFTER PAGE LOAD
   ========================================================= */

function restorePassoutSelection() {

    $('.passoutRowCheck').each(function () {

        let id = String($(this).val());

        $(this).prop(
            'checked',
            selectedPassouts.includes(id)
        );

    });

    syncPassoutSelectAll();

}


/* =========================================================
   SINGLE CHECKBOX
   ========================================================= */

$(document).on(
    'change',
    '.passoutRowCheck',
    function () {

        let id = String($(this).val());

        if ($(this).is(':checked')) {

            if (!selectedPassouts.includes(id)) {

                selectedPassouts.push(id);

            }

            selectedPassoutWhatsAppRecords.set(id, {
                id: id,
                name: $(this).data('name') || '',
                mobile: $(this).data('mobile') || ''
            });

            savePassoutWhatsAppRecords();

        } else {

            selectedPassouts =
                selectedPassouts.filter(
                    item => item !== id
                );

            selectedPassoutWhatsAppRecords.delete(id);
            savePassoutWhatsAppRecords();

        }

        savePassoutSelection();

        syncPassoutSelectAll();

    }
);


/* =========================================================
   SELECT ALL
   ========================================================= */

$(document).on(
    'change',
    '#passoutSelectAll',
    function () {

        let checked =
            $(this).is(':checked');

        $('.passoutRowCheck:enabled').each(function () {

            let id = String($(this).val());

            $(this).prop(
                'checked',
                checked
            );

            if (checked) {

                if (!selectedPassouts.includes(id)) {

                    selectedPassouts.push(id);

                }

                selectedPassoutWhatsAppRecords.set(id, {
                    id: id,
                    name: $(this).data('name') || '',
                    mobile: $(this).data('mobile') || ''
                });

            } else {

                selectedPassouts =
                    selectedPassouts.filter(
                        item => item !== id
                    );

                    selectedPassoutWhatsAppRecords.delete(id);

            }

        });

        savePassoutSelection();

        syncPassoutSelectAll();

        savePassoutWhatsAppRecords();

    }
);


/* =========================================================
   CLEAR SELECTION
   ========================================================= */

$(document).on(
    'click',
    '#passoutClearSelection',
    function () {

        selectedPassouts = [];

        selectedPassoutWhatsAppRecords.clear();

        localStorage.removeItem('selected_passout_whatsapp_records');

        localStorage.removeItem(
            'selected_passouts'
        );

        $('.passoutRowCheck')
            .prop('checked', false);

        $('#passoutSelectAll')
            .prop('checked', false);

        Swal.fire(
            'Selection Cleared',
            'Selected passouts have been cleared.',
            'success'
        );

    }
);


/* =========================================================
   INITIAL LOAD
   ========================================================= */

$(document).ready(function () {

    restorePassoutSelection();

});


/* =========================================================
   MOVE / CLOSE BUTTON
   ========================================================= */

$(document).on(
    'click',
    '#passoutMoveCloseBtn',
    function () {

        if (selectedPassouts.length === 0) {

            Swal.fire(
                'No Selection',
                'Please select at least one passout.',
                'warning'
            );

            return;
        }

        /*
        | Reset modal
        */

        $('#passoutBulkAction').val('');

        $('#passoutSessionSection').hide();

        $('#passoutReasonSection').hide();

        $('#passoutTargetSession').val('');

        $('#passoutCloseReason').val('');

        /*
        | Bootstrap 5
        */

        let modalElement =
            document.getElementById(
                'passoutMoveCloseModal'
            );

        let modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();

    }
);


/* =========================================================
   ACTION CHANGE
   ========================================================= */

$(document).on(
    'change',
    '#passoutBulkAction',
    function () {

        let action = $(this).val();

        if (action === 'move') {

            $('#passoutSessionSection').show();

            $('#passoutReasonSection').show();

        }

        else if (action === 'close') {

            $('#passoutSessionSection').hide();

            $('#passoutReasonSection').show();

        }

        else {

            $('#passoutSessionSection').hide();

            $('#passoutReasonSection').hide();

        }

    }
);


/* =========================================================
   CONTINUE MOVE / CLOSE
   ========================================================= */

$(document).on(
    'click',
    '#passoutConfirmBulkAction',
    function () {

        let action =
            $('#passoutBulkAction').val();

        if (!action) {

            Swal.fire(
                'Action Required',
                'Please select Move or Close.',
                'warning'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | MOVE
        |--------------------------------------------------------------------------
        */

        if (action === 'move') {

            let sessionId =
                $('#passoutTargetSession').val();

            if (!sessionId) {

                Swal.fire(
                    'Session Required',
                    'Please select a session.',
                    'warning'
                );

                return;
            }


            // Reason is OPTIONAL
            let reason =
                $('#passoutCloseReason')
                    .val()
                    .trim();


            Swal.fire({
                title: 'Move Passouts?',
                text:
                    'Move ' +
                    selectedPassouts.length +
                    ' selected passout record(s)?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Move'
            }).then(function (result) {

                if (!result.isConfirmed) {
                    return;
                }

                submitPassoutBulkAction(
                    'move',
                    sessionId,
                    reason
                );

            });

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */

        if (action === 'close') {

            // Reason is OPTIONAL
            let reason =
                $('#passoutCloseReason')
                    .val()
                    .trim();


            Swal.fire({
                title: 'Close Passouts?',
                text:
                    'Close ' +
                    selectedPassouts.length +
                    ' selected passout record(s)?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Close'
            }).then(function (result) {

                if (!result.isConfirmed) {
                    return;
                }

                submitPassoutBulkAction(
                    'close',
                    '',
                    reason
                );

            });

        }

    }
);


/* =========================================================
   SUBMIT MOVE / CLOSE
   ========================================================= */

function submitPassoutBulkAction(
    action,
    sessionId,
    reason
) {

    let form = $('<form>', {

        method: 'POST',

        action: "{{ route('enquiries.bulkMove') }}"

    });


    form.append(
        $('<input>', {

            type: 'hidden',

            name: '_token',

            value: "{{ csrf_token() }}"

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'action',

            value: action

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'ids',

            value: JSON.stringify(
                selectedPassouts
            )

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'session_id',

            value: sessionId || ''

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'reason',

            value: reason || ''

        })
    );


    $('body').append(form);

    /*
    | Clear selection only when submitting
    */

    localStorage.removeItem(
        'selected_passouts'
    );

    form.submit();

}


/* =========================================================
   ASSIGN TRAINER BUTTON
   ========================================================= */

$(document).on(
    'click',
    '#passoutAssignTrainerBtn',
    function () {

        if (selectedPassouts.length === 0) {

            Swal.fire(
                'No Selection',
                'Please select at least one passout.',
                'warning'
            );

            return;
        }


        $('#passoutTrainer').val('');


        let modalElement =
            document.getElementById(
                'passoutTrainerModal'
            );

        let modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        modal.show();

    }
);


/* =========================================================
   CONFIRM TRAINER
   ========================================================= */

$(document).on(
    'click',
    '#passoutConfirmTrainer',
    function () {

        let trainerId =
            $('#passoutTrainer').val();

        if (!trainerId) {

            Swal.fire(
                'Trainer Required',
                'Please select a trainer.',
                'warning'
            );

            return;
        }


        Swal.fire({
            title: 'Assign Trainer?',
            text:
                'Assign trainer to ' +
                selectedPassouts.length +
                ' selected passout record(s)?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Assign'
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            submitPassoutTrainerAssignment(
                trainerId
            );

        });

    }
);


/* =========================================================
   SUBMIT TRAINER ASSIGNMENT
   ========================================================= */
function submitPassoutTrainerAssignment(trainerId) {

    let form = $('<form>', {
        method: 'POST',
        action: "{{ route('enquiries.assign') }}"
    });

    form.append(
        $('<input>', {
            type: 'hidden',
            name: '_token',
            value: "{{ csrf_token() }}"
        })
    );

    form.append(
        $('<input>', {
            type: 'hidden',
            name: 'salesperson_id',
            value: trainerId
        })
    );

    form.append(
        $('<input>', {
            type: 'hidden',
            name: 'enquiry_ids',
            value: JSON.stringify(selectedPassouts)
        })
    );

    $('body').append(form);

    localStorage.removeItem('selected_passouts');

    form.submit();
}
function ewsubmitPassoutTrainerAssignment(
    trainerId
) {

    let form = $('<form>', {

        method: 'POST',

        action: "{{ route('enquiries.assign') }}"

    });


    form.append(
        $('<input>', {

            type: 'hidden',

            name: '_token',

            value: "{{ csrf_token() }}"

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'trainer_id',

            value: trainerId

        })
    );


    form.append(
        $('<input>', {

            type: 'hidden',

            name: 'ids',

            value: JSON.stringify(
                selectedPassouts
            )

        })
    );


    $('body').append(form);


    localStorage.removeItem(
        'selected_passouts'
    );


    form.submit();

}
function showPopup(message) {
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


/* =========================================================
   PASSOUT BULK WHATSAPP - SAME OLD STUDENTS POPUP
========================================================= */

// Format Indian mobile number
function formatPassoutWhatsAppMobile(mobile) {
    mobile = String(mobile || '').replace(/\D/g, '');

    if (mobile.length === 10) {
        mobile = '91' + mobile;
    } else if (mobile.length === 11 && mobile.startsWith('0')) {
        mobile = '91' + mobile.substring(1);
    }

    if (!/^91[6-9]\d{9}$/.test(mobile)) return null;

    return mobile;
}


// Open WhatsApp chat
function openPassoutWhatsApp(record, message, appendName) {
    const mobile = formatPassoutWhatsAppMobile(record.mobile);

    if (!mobile) return false;

    let finalMessage = message;

    if (appendName && record.name) {
        finalMessage = 'Hi ' + record.name + ',\n\n' + message;
    }

    const url =
        'https://web.whatsapp.com/send?phone=' +
        mobile +
        '&text=' +
        encodeURIComponent(finalMessage);

    window.open(url, '_blank');

    return true;
}


// Start bulk WhatsApp sending
function startPassoutWhatsApp(records) {

    if (!records || records.length === 0) {
        Swal.fire(
            'No Selection',
            'Please select at least one passout.',
            'warning'
        );
        return;
    }

    const validRecords = records.filter(record =>
        formatPassoutWhatsAppMobile(record.mobile)
    );

    const invalidCount = records.length - validRecords.length;

    if (validRecords.length === 0) {
        Swal.fire(
            'Invalid Mobile Numbers',
            'No selected record has a valid Indian mobile number.',
            'warning'
        );
        return;
    }

    Swal.fire({
        title: 'Send WhatsApp Message',

        html: `
            <div class="text-start">
                <label class="form-label">Message</label>

                <textarea
                    id="passoutWhatsappMessage"
                    class="form-control"
                    rows="5"
                    placeholder="Type your WhatsApp message..."
                ></textarea>

                <div class="form-check mt-3">
                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="passoutWhatsappAppendName"
                        checked
                    >

                    <label
                        class="form-check-label"
                        for="passoutWhatsappAppendName"
                    >
                        Add student name
                    </label>
                </div>

                <p class="text-muted mt-3 mb-0">
                    Selected: ${records.length}<br>
                    Valid: ${validRecords.length}<br>
                    Invalid: ${invalidCount}
                </p>
            </div>
        `,

        showCancelButton: true,
        confirmButtonText: 'Start Sending',
        cancelButtonText: 'Cancel',
        reverseButtons: false,

        preConfirm: () => {

            const message = document
                .getElementById('passoutWhatsappMessage')
                .value.trim();

            const appendName = document
                .getElementById('passoutWhatsappAppendName')
                .checked;

            if (!message) {
                Swal.showValidationMessage('Please enter a message.');
                return false;
            }

            const opened = openPassoutWhatsApp(
                validRecords[0],
                message,
                appendName
            );

            if (!opened) {
                Swal.showValidationMessage('Unable to open WhatsApp chat.');
                return false;
            }

            return {
                message: message,
                appendName: appendName
            };
        }

    }).then(result => {

        if (!result.isConfirmed) return;

        let currentIndex = 1;

        function openNextPassoutWhatsApp() {

            if (currentIndex >= validRecords.length) {

                Swal.fire({
                    icon: 'success',
                    title: 'All Chats Opened',
                    text:
                        `Opened ${validRecords.length} valid WhatsApp chats. ` +
                        `Invalid numbers skipped: ${invalidCount}.`
                });

                return;
            }

            const currentRecord = validRecords[currentIndex];

            const safeName = $('<div>')
                .text(currentRecord.name || 'Student')
                .html();

            const safeMobile = $('<div>')
                .text(currentRecord.mobile || '')
                .html();

            Swal.fire({

                title: 'WhatsApp Sending',

                html: `
                    <p>
                        Chat ${currentIndex + 1}
                        of ${validRecords.length}
                    </p>

                    <p><strong>${safeName}</strong></p>

                    <p class="text-muted">${safeMobile}</p>

                    <p>
                        Send the message in WhatsApp, then click
                        <strong>Open Next</strong>.
                    </p>
                `,

                showCancelButton: true,
                confirmButtonText: 'Open Next',
                cancelButtonText: 'Finish',
                reverseButtons: false,

                preConfirm: () => {

                    const opened = openPassoutWhatsApp(
                        currentRecord,
                        result.value.message,
                        result.value.appendName
                    );

                    if (!opened) {
                        Swal.showValidationMessage(
                            'Unable to open this WhatsApp chat.'
                        );
                        return false;
                    }

                    currentIndex++;
                }

            }).then(nextResult => {

                if (nextResult.isConfirmed) {
                    openNextPassoutWhatsApp();
                }

            });
        }

        if (validRecords.length > 1) {
            openNextPassoutWhatsApp();
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Chat Opened',
                text: 'The WhatsApp chat has been opened.'
            });
        }

    });
}


// WhatsApp button
$(document).on('click', '#sendPassoutWhatsApp', function () {

    if (selectedPassouts.length === 0) {
        Swal.fire(
            'No Selection',
            'Please select at least one passout.',
            'warning'
        );
        return;
    }

    const selectedRecords = selectedPassouts
        .map(id => selectedPassoutWhatsAppRecords.get(String(id)))
        .filter(Boolean);

    if (selectedRecords.length !== selectedPassouts.length) {
        Swal.fire(
            'Selection Details Missing',
            'Some selected records do not have saved contact details. Please unselect and select those records again.',
            'warning'
        );
        return;
    }

    startPassoutWhatsApp(selectedRecords);
});

 
</script>
@endpush


