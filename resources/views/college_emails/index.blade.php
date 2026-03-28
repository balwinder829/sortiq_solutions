@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h3 class="page_heading">Colleges Email Panel</h3>
        </div>

        <div class="col-md-6 text-end">
            <button id="sendSelected" class="btn btn-primary">
                Send Email (Selected)
            </button>
        </div>
    </div>

    {{-- FILTERS --}}
    <!-- <div class="row mb-3">

        <div class="col-md-3">
            <select id="filter_email_status" class="form-control">
                <option value="">Email Status</option>
                <option value="sent">Sent</option>
                <option value="not_sent">Not Sent</option>
            </select>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.college-emails.index') }}" class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div> -->

    <div class="col-md-12 mb-3">
    <div class="row g-2 align-items-end">

        {{-- STATE --}}
        <div class="col-md-2">
            <select id="filter-state" class="form-select">
                <option value="">State</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- DISTRICT --}}
        <div class="col-md-2">
            <select id="filter-district" class="form-select">
                <option value="">District</option>
            </select>
        </div>

        {{-- COLLEGE --}}
        <div class="col-md-3">
            <select id="filter-college" class="form-select select2">
                <option value="">College</option>
            </select>
        </div>

        {{-- EMAIL STATUS --}}
        <div class="col-md-2">
            <select id="filter-email-status" class="form-select">
                <option value="">Email Status</option>
                <option value="sent">Sent</option>
                <option value="not_sent">Not Sent</option>
                <option value="failed">Failed</option>
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

        {{-- RESET --}}
        <div class="col-md-2">
            <a href="{{ route('admin.college-emails.index') }}" class="btn btn-secondary w-100">
                Reset
            </a>
        </div>

    </div>
</div>

    {{-- TABLE --}}
    <div class="table-responsive">
        <table id="collegeTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th width="30">
                        <input type="checkbox" id="checkAll">
                    </th>
                    <th>ID</th>
                    <th>College</th>
                    <th>Email Count</th>
                    <th>Sent To</th>
                    <th>Status</th>
                    <th width="200">Action</th>
                </tr>
            </thead>
        </table>
    </div>

</div>

@endsection


@push('scripts')

<script>
let selectedIds = new Set();

let table = $('#collegeTable').DataTable({

    processing: true,
    serverSide: true,

    ajax: {
        url: "{{ route('admin.college-emails.index') }}",
        data: function (d) {
            // d.email_status = $('#filter_email_status').val();
             d.state_id      = $('#filter-state').val();
            d.district_id   = $('#filter-district').val();
            // d.college_type  = $('#filter-college-type').val();
            d.college_id    = $('#filter-college').val();
            d.email_status  = $('#filter-email-status').val();
            d.date_from = $('#date_from').val();
            d.date_to   = $('#date_to').val();
            d.range     = $('#filter-range').val();
        }
    },

    columns: [
        { orderable: false, searchable: false }, // checkbox
        { orderable: true },  // id
        { orderable: true },  // college name
        { orderable: true },  // email count
        { orderable: true },  // sent to
        { orderable: true },  // status
        { orderable: false, searchable: false } // action
    ],

    order: [[1, 'desc']], // default sort by ID desc

    drawCallback: function () {

        // restore checked state
        $('.record_checkbox').each(function () {
            let id = $(this).val();
            $(this).prop('checked', selectedIds.has(id));
        });

        // reset "select all" checkbox if needed
        let allChecked = true;

        $('.record_checkbox').each(function () {
            if (!selectedIds.has($(this).val())) {
                allChecked = false;
            }
        });

        $('#checkAll').prop('checked', allChecked);
    }
});


// SELECT / DESELECT
$(document).on('change', '.record_checkbox', function () {

    let id = $(this).val();

    if ($(this).is(':checked')) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
    }

});


// SELECT ALL (CURRENT PAGE)
$('#checkAll').on('change', function () {

    let checked = this.checked;

    $('.record_checkbox').each(function () {

        let id = $(this).val();

        if (checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        $(this).prop('checked', checked);
    });

});


// BULK SEND
$('#sendSelected').click(function () {

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
        text: 'Send email to selected colleges?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.college-emails.storeSelection') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: Array.from(selectedIds)
            },
            success: function (res) {

                if (res.status) {
                    window.location.href = "{{ route('admin.college-emails.create') }}";
                }

            }
        });

    });

});


// RETRY PER COLLEGE
$(document).on('click', '.retry-single', function () {

    let id = $(this).data('id');

    Swal.fire({
        title: 'Retry?',
        text: 'Retry failed emails for this college?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.college-emails.retryByCollege') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                college_id: id
            },
            success: function (res) {

                Swal.fire({
                    icon: 'success',
                    title: 'Done',
                    text: res.message
                });

                table.ajax.reload();

            }
        });

    });

});


// FILTER
$('#filter_email_status').change(function () {
    table.ajax.reload();
});


$(document).on('click', '.send-single', function () {

    let id = $(this).data('id');

    Swal.fire({
        title: 'Send Email?',
        text: 'Proceed with this college?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
    }).then((result) => {

        if (!result.isConfirmed) return;

        $.ajax({
            url: "{{ route('admin.college-emails.storeSelection') }}",
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ids: [id] // 🔥 single ID
            },
            success: function (res) {

                if (res.status) {
                    window.location.href = "{{ route('admin.college-emails.create') }}";
                }

            }
        });

    });

});

let colleges = @json($colleges);
let districtsByState = @json($districtsGrouped);

// function loadFilteredColleges(){

//     let state    = $('#filter-state').val();
//     let district = $('#filter-district').val();
//     let type     = $('#filter-college-type').val();

//     let collegeDropdown = $('#filter-college');

//     collegeDropdown.empty();
//     collegeDropdown.append('<option value="">College</option>');

//     colleges.forEach(function(c){

//         if(state && c.state_id != state) return;
//         if(district && c.district_id != district) return;
//         if(type !== '' && c.college_type != type) return;

//         collegeDropdown.append(
//             `<option value="${c.id}">${c.full_name ?? c.college_name}</option>`
//         );

//     });
// }

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

$('#filter-district, #filter-college-type').on('change', function(){
    loadFilteredColleges();
    table.ajax.reload();
});

$('#filter-college, #filter-email-status').on('change', function(){
    table.ajax.reload();
});

$('#filter-range').on('change', function(){
    if($(this).val()){
        $('#date_from').val('');
        $('#date_to').val('');
    }
});

$('#date_from, #date_to, #filter-range').on('change', function () {
    table.ajax.reload();
});
</script>

@endpush