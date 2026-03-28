@extends('layouts.app')

@section('content')
<style>
.batch-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #0d6efd;
    color: white;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease-in-out;
}
.batch-circle:hover {
    background: #0b5ed7;
    transform: scale(1.1);
}
 
table.dataTable td {
    text-transform: capitalize;
}
 </style>

<div class="container">

<div class="row mb-2 align-items-end">

    {{-- LEFT: PAGE TITLE --}}
    <div class="col-md-8">
        <h1 class="page_heading">Workshops</h1>
    </div>

    {{-- RIGHT: ADD MENTOR BUTTON --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end  gap-2">
           <!--  <a href="{{ route('workshops.export.excel') }}" class="btn btn-primary mb-3">
                Export
            </a> -->
            <a href="javascript:void(0)"
               id="exportWorkshopExcel"
               class="btn btn-primary mb-3">
               Export
            </a>
            <a href="{{ route('workshops.create') }}"
               style="background-color: #6b51df; color: #fff;"
               class="btn btn-primary mb-3">
                Add Workshop
            </a>
        </div>
    </div>



</div>
 {{-- MIDDLE: FILTER FORM --}}
    <div class="col-md-12 mb-3">
        <form method="GET" action="{{ route('workshops.index') }}" class="row g-2 align-items-end">

        <div class="col-md-2">
            <select id="filter-state" name="state_id" class="form-select">
                <option value="">State</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select id="filter-district" name="district_id" class="form-select">
                <option value="">District</option>
            </select>
        </div>

        <div class="col-md-2">
            <select id="filter-college-type" name="college_type" class="form-select">
                <option value="">College Type</option>
                <option value="0">Degree</option>
                <option value="1">Diploma</option>
            </select>
        </div>
           {{-- COLLEGE FILTER --}}
        <div class="col-md-3">
            <select name="college_id" class="form-select select2">
                <option value="">College</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}"
                        {{ request('college_id') == $college->id ? 'selected' : '' }}>
                        {{ $college->FullName }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- STATUS FILTER --}}
        @php $statuses = ['done','decided','meeting','hold','cancel']; @endphp
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}"
                        {{ request('status') == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-2">
            <select name="type" class="form-select">
                <option value="">Workshop Type</option>
                <option value="campus" {{ request('type')=='campus'?'selected':'' }}>Campus</option>
                <option value="office" {{ request('type')=='office'?'selected':'' }}>Office</option>
            </select>
        </div>

        <div class="col-md-2">
            <select name="event_type" class="form-select">
                <option value="">Event Type</option>
                <option value="seminar" {{ request('event_type')=='seminar'?'selected':'' }}>Seminar</option>
                <option value="placement_drive" {{ request('event_type')=='placement_drive'?'selected':'' }}>Placement Drive</option>
                <option value="both" {{ request('event_type')=='both'?'selected':'' }}>Both</option>
            </select>
        </div>
        {{-- DATE FILTER --}}
        <div class="col-md-2">
            <input type="date" name="date" class="form-control"
                   value="{{ request('date') }}">
        </div>

        {{-- CUSTOM FILTER --}}
        <div class="col-md-2">
            <select name="range" class="form-select">
                <option value="">Range</option>
               <!--  <option value="upcoming" {{ request('range')=='upcoming'?'selected':'' }}>Upcoming</option>
                <option value="today" {{ request('range')=='today'?'selected':'' }}>Today</option>
                <option value="past" {{ request('range')=='past'?'selected':'' }}>Past</option> -->
                <option value="today">Today</option>
                <option value="yesterday">Yesterday</option>
                <option value="upcoming">Upcoming</option>
                <option value="past">Past</option>
                <option value="current_week_past">Current Week (Till Today)</option>
                <option value="next_week">Next Week</option>
                <option value="last_week">Last Week</option>
                <option value="last_30_days">Last 30 Days</option>
                <option value="last_month">Last Month</option>
            </select>
        </div>




            {{-- BUTTONS WITH SMALL GAP --}}
            <div class="col-md-4 d-flex gap-2">
                <!-- <button type="submit" class="btn btn-primary">
                    Search
                </button> -->

                <a href="{{ route('workshops.index') }}" class="btn btn-secondary">
                    Reset
                </a>
            </div>

        </form>
    </div>


    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive">
        <table id="trainers-table" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>College</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Workshop Type</th>
                    <th>Event Type</th>
                    <th width="100">Actions</th>
                </tr>
            </thead>

            <tbody></tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
<!-- <script>
$(document).ready(function () {
    var params = new URLSearchParams(window.location.search);
    $('#trainers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('workshops.data') }}",
            data: function (d) {
                // d.course = params.get('course') || '';
            }
        },
        columns: [
            { data: 0 },
            { data: 1 },
            { data: 2 },
            { data: 3 }
            
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100]
    });
});
</script> -->
<script>
let colleges = @json($colleges);

function loadFilteredColleges(){

    let state    = $('#filter-state').val();
    let district = $('#filter-district').val();
    let type     = $('#filter-college-type').val();

    let collegeDropdown = $('select[name=college_id]');
    collegeDropdown.empty();
    collegeDropdown.append('<option value="">College</option>');

    colleges.forEach(function(c){

        if(state && c.state_id != state) return;
        if(district && c.district_id != district) return;
        if(type !== '' && c.college_type != type) return;

        collegeDropdown.append(
            `<option value="${c.id}">${c.full_name ?? c.college_name}</option>`
        );

    });

}
</script>

<script>
$(document).ready(function () {
    let params = new URLSearchParams(window.location.search);

    // If range exists in URL, set dropdown value
    if (params.get('range')) {
        $('select[name=range]').val(params.get('range'));
    }
    var table = $('#trainers-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('workshops.data') }}",
            data: function (d) {
                d.state_id     = $('#filter-state').val();
                d.district_id  = $('#filter-district').val();
                d.college_type = $('#filter-college-type').val();

                d.college_id = $('select[name=college_id]').val();
                d.status     = $('select[name=status]').val();
                d.date       = $('input[name=date]').val();
                d.range      = $('select[name=range]').val();
                d.type        = $('select[name=type]').val();
                d.event_type  = $('select[name=event_type]').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },        // ID
            { data: 1, name: 'name' },      // Title
            { data: 2, name: 'college_id', orderable:false, searchable:false }, 
            { data: 3, name: 'date' },      // Date
            { data: 4, name: 'status' },    // Status
            { data: 5, name: 'type' },    // Status
            { data: 6, name: 'event_type' },
            { data: 7, name: 'actions', orderable:false, searchable:false } // Actions
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        orders: []
    });

    // $('select[name=college_id], select[name=status], select[name=range]').on('change', function () {
    //     table.ajax.reload();
    // });

    $('select[name=college_id],select[name=status], select[name=range],select[name=state_id],select[name=district_id],select[name=college_type], select[name=type], select[name=event_type]').on('change', function () {

        table.ajax.reload();
    });

    $('#filter-state, #filter-district, #filter-college-type').on('change', function(){

        loadFilteredColleges();
        table.ajax.reload();

    });

    // For date input
    $('input[name=date]').on('change', function () {
        table.ajax.reload();
    });

    let districtsByState = @json($districtsGrouped);

    // STATE → DISTRICT
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

        table.ajax.reload();
    });
});




$('#exportWorkshopExcel').on('click', function () {

    let $btn = $(this);

    if ($btn.prop('disabled')) {
        return false;
    }

    $btn.prop('disabled', true).text('Exporting...');

    let state      = $('#filter-state').val() ?? '';
    let district   = $('#filter-district').val() ?? '';
    let type       = $('#filter-college-type').val() ?? '';
    let college = $('select[name=college_id]').val() ?? '';
    let status  = $('select[name=status]').val() ?? '';
    let date    = $('input[name=date]').val() ?? '';
    let range   = $('select[name=range]').val() ?? '';
    let event_type   = $('select[name=event_type]').val() ?? '';

    let url = "{{ route('workshops.export.excel') }}?" +
    "state_id=" + encodeURIComponent(state) +
    "&district_id=" + encodeURIComponent(district) +
    "&college_type=" + encodeURIComponent(type) +
    "&college_id=" + encodeURIComponent(college) +
    "&status=" + encodeURIComponent(status) +
    "&event_type=" + encodeURIComponent(event_type) +
    "&date=" + encodeURIComponent(date) +
    "&range=" + encodeURIComponent(range);

    window.location.href = url;

    setTimeout(function () {
        $btn.prop('disabled', false).text('Export');
    }, 3000);
});
</script>

 

@endpush
