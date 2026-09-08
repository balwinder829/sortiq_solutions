@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h3 class="page_heading">Colleges Calling Panel</h3>
        </div>

        <div class="col-md-6 text-end">
            <button id="editCallStatus"
                type="button"
                class="btn btn-success">
                Edit Status
            </button>

            <button id="callSelected" class="btn btn-primary">
                Start Calling (Selected)
            </button>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="col-md-12 mb-3">
        <div class="row g-2 align-items-end">

            <div class="col-md-2">
                <select id="filter-state" class="form-select">
                    <option value="">State</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <select id="filter-district" class="form-select">
                    <option value="">District</option>
                </select>
            </div>

            <div class="col-md-3">
                <select id="filter-college" class="form-select select2">
                    <option value="">College</option>
                </select>
            </div>

            <div class="col-md-2">
                <select id="filter-call-status" class="form-select">
                    <option value="">Call Status</option>
                    <option value="called">Called</option>
                    <option value="not_called">Not Called</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="date" id="date_from" class="form-control">
            </div>

            <div class="col-md-2">
                <input type="date" id="date_to" class="form-control">
            </div>

            <div class="col-md-2">
                <select id="filter-range" class="form-select">
                    <option value="">Range</option>
                    <option value="today">Today</option>
                    <option value="yesterday">Yesterday</option>
                    <option value="current_week_past">Current Week (Till Today)</option>
                    <option value="last_week">Last Week</option>
                    <option value="last_month">Last Month</option>
                    <option value="last_30_days">Last 30 Days</option>
                </select>
            </div>

            <div class="col-md-2">
                <a href="{{ route('admin.college-calls.index') }}" class="btn btn-secondary w-100">
                    Reset
                </a>
            </div>

        </div>
    </div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table id="callTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="30"><input type="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>College</th>
                    <th>Call Count</th>
                    <th>Called To</th>
                    <th>Status</th>
                    <th width="200">Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>
{{-- CALL STATUS MODAL --}}
<div class="modal fade" id="callStatusModal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Call Status
                </h5>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <div class="mb-3">

                    <label class="form-label">
                        Call Done <span class="text-danger">*</span>
                    </label>

                    <select id="callDoneValue"
                        class="form-select"
                        required>

                        <option value="">
                            Select Status
                        </option>

                        <option value="1">
                            Called
                        </option>

                        <option value="0">
                            Not Called
                        </option>

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
                    id="saveCallStatus"
                    class="btn btn-success">
                    Update
                </button>

            </div>

        </div>

    </div>

</div>
@endsection

@push('scripts')

<script>
let selectedIds = new Set();

let table = $('#callTable').DataTable({

    processing: true,
    serverSide: true,

    ajax: {
        url: "{{ route('admin.college-calls.index') }}",
        data: function (d) {
            d.state_id = $('#filter-state').val();
            d.district_id = $('#filter-district').val();
            d.college_id = $('#filter-college').val();
            d.call_status = $('#filter-call-status').val();
            d.date_from = $('#date_from').val();
            d.date_to = $('#date_to').val();
            d.range = $('#filter-range').val();
        }
    },

    columns: [
        { orderable: false },
        { orderable: true },
        { orderable: true },
        { orderable: true },
        { orderable: true },
        { orderable: true },
        { orderable: false }
    ],

    order: [[1, 'desc']],

    drawCallback: function () {

        $('.record_checkbox').each(function () {
            let id = $(this).val();
            $(this).prop('checked', selectedIds.has(id));
        });

        let allChecked = true;

        $('.record_checkbox').each(function () {
            if (!selectedIds.has($(this).val())) {
                allChecked = false;
            }
        });

        $('#checkAll').prop('checked', allChecked);
    }
});


// SELECT
$(document).on('change', '.record_checkbox', function () {
    let id = $(this).val();
    $(this).is(':checked') ? selectedIds.add(id) : selectedIds.delete(id);
});

$('#checkAll').on('change', function () {

    let checked = this.checked;

    $('.record_checkbox').each(function () {
        let id = $(this).val();

        checked ? selectedIds.add(id) : selectedIds.delete(id);

        $(this).prop('checked', checked);
    });
});


// BULK
$('#callSelected').click(function () {


    if (selectedIds.size === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one college'
        });

        return;
    }

    Swal.fire({
        title: 'Proceed?',
        text: 'Add call record for selected colleges?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.college-calls.storeSelection') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: Array.from(selectedIds)
            },
            success: function (res) {

                if (res.status) {
                    window.location.href = "{{ route('admin.college-calls.create') }}";
                }

            }
        });

    });

    // if (selectedIds.size === 0) {
    //     Swal.fire({
    //         icon: 'warning',
    //         title: 'No Selection',
    //         text: 'Please select at least one college'
    //     });

    //     return;
    // }

    // $.post("{{ route('admin.college-calls.storeSelection') }}", {
    //     _token: '{{ csrf_token() }}',
    //     ids: Array.from(selectedIds)
    // }, function (res) {
    //     if (res.status) {
    //         window.location.href = "{{ route('admin.college-calls.create') }}";
    //     }
    // });
});


// SINGLE
$(document).on('click', '.call-single', function () {

    let id = $(this).data('id');

    $.post("{{ route('admin.college-calls.storeSelection') }}", {
        _token: '{{ csrf_token() }}',
        ids: [id]
    }, function (res) {
        if (res.status) {
            window.location.href = "{{ route('admin.college-calls.create') }}";
        }
    });
});


// RETRY
$(document).on('click', '.retry-single', function () {

    let id = $(this).data('id');

    $.post("{{ route('admin.college-calls.retryByCollege') }}", {
        _token: '{{ csrf_token() }}',
        college_id: id
    }, function (res) {
        if (res.status) {
            window.location.href = res.redirect;
        }
    });
});


// FILTER TRIGGERS
$('#filter-state, #filter-district, #filter-college, #filter-call-status, #date_from, #date_to, #filter-range')
.on('change', function () {
    table.ajax.reload();
});

let colleges = @json($colleges);
let districtsByState = @json($districtsGrouped);

function loadFilteredColleges(){

    let state    = $('#filter-state').val();
    let district = $('#filter-district').val();

    let collegeDropdown = $('#filter-college');

    collegeDropdown.empty();
    collegeDropdown.append('<option value="">College</option>');

    colleges.forEach(function(c){

        if(state && c.state_id != state) return;
        if(district && c.district_id != district) return;

        collegeDropdown.append(
            `<option value="${c.id}">${c.full_name ?? c.college_name}</option>`
        );

    });
}


// 🔥 STATE CHANGE
$('#filter-state').on('change', function () {

    let stateId = $(this).val();
    let districtDropdown = $('#filter-district');

    districtDropdown.empty().append('<option value="">District</option>');

    if(stateId && districtsByState[stateId]){

        districtsByState[stateId].forEach(function(d){

            districtDropdown.append(
                `<option value="${d.id}">${d.name}</option>`
            );

        });

    }

    loadFilteredColleges();
    table.ajax.reload();
});


// 🔥 DISTRICT CHANGE
$('#filter-district').on('change', function(){
    loadFilteredColleges();
    table.ajax.reload();
});


// 🔥 COLLEGE CHANGE
$('#filter-college').on('change', function(){
    table.ajax.reload();
});

// EDIT CALL STATUS
$('#editCallStatus').on('click', function () {

    if (selectedIds.size === 0) {

        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one college'
        });

        return;
    }

    // Reset status
    $('#callDoneValue').val('');

    // Open modal
    $('#callStatusModal').modal('show');
});


// SAVE CALL STATUS
$('#saveCallStatus').on('click', function () {

    if (selectedIds.size === 0) {
        return;
    }

    let callDone = $('#callDoneValue').val();

    // Required
    if (callDone === '') {

        Swal.fire({
            icon: 'warning',
            title: 'Status Required',
            text: 'Please select Call Status.'
        });

        return;
    }

    $.ajax({

        url: "{{ route('admin.college-calls.updateCallStatus') }}",

        type: 'POST',

        data: {

            _token: "{{ csrf_token() }}",

            ids: Array.from(selectedIds),

            call_done: callDone

        },

        success: function (response) {

            if (response.status) {

                $('#callStatusModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Updated',
                    text: response.message
                });

                selectedIds.clear();

                $('#checkAll').prop('checked', false);

                table.ajax.reload(null, false);
            }

        },

        error: function (xhr) {

            let message = 'Something went wrong.';

            if (
                xhr.responseJSON &&
                xhr.responseJSON.message
            ) {
                message = xhr.responseJSON.message;
            }

            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message
            });

        }

    });

});
</script>

@endpush