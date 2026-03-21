@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
        text-transform: capitalize;
     }
     .student-count {
        color: #0d6efd;
        font-weight: 600;
        cursor: pointer;
        text-decoration: underline;
        transition: all 0.2s ease-in-out;
    }
    .student-count:hover {
        color: #084298;
        transform: scale(1.05);
    }
    .student-count.badge-style {
        background-color: #e7f1ff;
        padding: 4px 10px;
        border-radius: 12px;
        text-decoration: none;
    }

    .switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 22px;
}
.switch input { display: none; }

.slider {
  position: absolute;
  cursor: pointer;
  background-color: #ccc;
  transition: .4s;
  border-radius: 22px;
  top: 0; left: 0; right: 0; bottom: 0;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 3px;
  bottom: 3px;
  background: white;
  transition: .4s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #0d6efd;
}

input:checked + .slider:before {
  transform: translateX(18px);
}

</style>

<div class="container">

    <!-- <div class="row mb-2">
        <div class="col-md-4">
            <h1 class="page_heading">Colleges/Places</h1>
        </div>
        <div class="col-md-2">
            
            <select id="filter-state" class="form-control">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            
            <select id="filter-district" class="form-control">
                <option value="">All Districts</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('colleges.export.excel') }}"
                   class="btn mb-3" style="background-color: #6b51df; color: #fff;">
                     Download Excel
                </a>
            </div>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('colleges.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add College/Place</a>
            </div>
        </div>
    </div> -->

    <div class="row mb-2 align-items-center">
    <div class="col-md-8">
        <h1 class="page_heading">Colleges / Places</h1>
    </div>

    <!-- <div class="col-md-2">
        <select id="student_filter" class="form-select">
            <option value="">All Colleges</option>
            <option value="zero">0 Students</option>
            <option value="more">More than 0 Students</option>
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-state" class="form-control">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->name }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-district" class="form-control">
            <option value="">All Districts</option>
        </select>
    </div> -->

    {{-- ACTION BUTTONS --}}
    <div class="col-md-4">
        <div class="d-flex justify-content-end gap-2">

            {{-- IMPORT COLLEGES --}}
            <a href="{{ route('colleges.import.view') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Import
            </a>

            {{-- DOWNLOAD EXCEL --}}
           <!--  <a href="{{ route('colleges.export.excel') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Export
            </a> -->

            <a href="javascript:void(0)"
               id="exportExcel"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
               Export
            </a>

            {{-- ADD COLLEGE --}}
            <a href="{{ route('colleges.create') }}"
               class="btn mb-3"
               style="background-color:#6b51df; color:#fff;">
                Add
            </a>
        </div>
    </div>
</div>
<div class="row mb-2 align-items-center">
    <div class="col-md-1">
        <h1 class="page_heading">FIlters</h1>
    </div>

    <div class="col-md-2">
        <select id="student_filter" class="form-select">
            <option value="">Student Count</option>
            <option value="asc">Low to High</option>
            <option value="desc">High to Low</option>
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-state" class="form-control">
            <option value="">All States</option>
            @foreach($states as $state)
                <option value="{{ $state->name }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter-district" class="form-control">
            <option value="">All Districts</option>
        </select>
    </div>

    <div class="col-md-2">
        <select id="filter_college_type" class="form-control">
            <option value="">College Type</option>

            @foreach(\App\Models\College::TYPES as $key => $value)
                <option value="{{ $key }}">{{ $value }}</option>
            @endforeach

        </select>
    </div>

    <div class="col-md-2">
        <select id="filter_training" class="form-control">
            <option value="">Training</option>
            <option value="1">Providing Training</option>
            <option value="0">Not Providing</option>
        </select>
    </div>

    <div class="col-md-2 mt-2">
        <select id="filter_status" class="form-control">
            <option value="">All</option>
            <option value="1">Call Done</option>
            <option value="0">Pending</option>
        </select>
    </div>

    <div class="col-md-1  mt-2">
        <a href="{{ route('colleges.index') }}" class="btn btn-secondary w-100">
            Reset
        </a>
    </div>
 
</div>

    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="colleges-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>College Name/Place</th>
                <th>State</th>
                <th>District</th>
                <th>Students</th>
                <th>College Type</th>
                <th>Offer Training</th>
                <th>No of times in year</th>
                <th>Call Status</th>
               <th style="width:250px!important;">Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Data loaded via server-side Ajax (no full dataset in page) --}}
        </tbody>
    </table>
</div>

 

@endsection


@section('scripts')
@push('scripts')

<script>
// districts grouped by state → coming from controller
let districtsByState = @json($districtsGrouped);

$(document).ready(function () {

    // SERVER-SIDE DATATABLE — only current page is loaded from server
    let table = $('#colleges-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('colleges.data') }}",
            type: 'GET',
            data: function (d) {
                d.state_name   = $('#filter-state').val();
                d.district_name = $('#filter-district').val();
                d.student_filter = $('#student_filter').val();
                d.college_type = $('#filter_college_type').val();
                d.offer_training = $('#filter_training').val();
                d.call_status = $('#filter_status').val();
            }
        },
        columns: [
            { data: 0, name: 'id' },
            { data: 1, name: 'college_name' },
            { data: 2, name: 'state' },
            { data: 3, name: 'district' },
            { data: 4, name: 'students_count', orderable: true, searchable: false },
            { data: 5, name: 'college_type' },
            { data: 6, name: 'offer_training' },
            { data: 7, name: 'training_in_year,' },
            { data: 8, name: 'call_status', orderable: false, searchable: false },
            { data: 9, name: 'actions', orderable: false, searchable: false }
        ],
        pageLength: 50,
        lengthMenu: [5, 10, 25, 50, 100],
        order:[]
    });

    $('#student_filter, #filter_college_type, #filter_training, #filter_status').change(function () {
        table.ajax.reload();
    });

    // STATE FILTER → Updates District Dropdown and reloads table
    $('#filter-state').on('change', function () {
        let selectedState = this.value;
        let districtDropdown = $('#filter-district');
        districtDropdown.empty().append('<option value="">All Districts</option>');

        if (selectedState && districtsByState) {
            let stateId = Object.keys(districtsByState).find(id => {
                return districtsByState[id][0]?.state_name === selectedState;
            });
            if (stateId && districtsByState[stateId]) {
                districtsByState[stateId].forEach(function (d) {
                    districtDropdown.append('<option value="' + d.name + '">' + d.name + '</option>');
                });
            }
        }
        table.ajax.reload();
    });

    $('#filter-district').on('change', function () {
        table.ajax.reload();
    });

});

// Bootstrap Tooltips Init
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
 
 
$('#exportExcel').on('click', function () {

    let $btn = $(this);

    // 🚫 Stop if already processing
    if ($btn.prop('disabled')) {
        return false;
    }

    // ✅ Disable button
    $btn.prop('disabled', true).text('Exporting...');

    let state    = $('#filter-state').val() ?? '';
    let district = $('#filter-district').val() ?? '';
    let student  = $('#student_filter').val() ?? '';

    let college_type = $('#filter_college_type').val();
    let offer_training = $('#filter_training').val();
    let call_status = $('#filter_status').val();

    let url = "{{ route('colleges.export.excel') }}?" +
        "state_name=" + encodeURIComponent(state) +
        "&district_name=" + encodeURIComponent(district) +
        "&student_filter=" + encodeURIComponent(student) +
        "&college_type=" + encodeURIComponent(college_type) +
        "&call_status=" + encodeURIComponent(call_status) +
        "&offer_training=" + encodeURIComponent(offer_training);

    // Trigger download
    window.location.href = url;

    // ⏳ Re-enable after 3 seconds (adjust if needed)
    setTimeout(function () {
        $btn.prop('disabled', false).text('Export');
    }, 3000);
});

$(document).on('change', '.toggle-status', function () {

    let checkbox = $(this);
    let id = checkbox.data('id');
    let status = checkbox.is(':checked') ? 1 : 0;

    $.ajax({
        url: "{{ route('colleges.toggle.status', ':id') }}".replace(':id', id),
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            status: status
        },
        success: function (res) {
            // optional toast
            console.log('Updated');
        },
        error: function () {
            alert('Something went wrong');

            // rollback UI
            checkbox.prop('checked', !status);
        }
    });
});
</script>


@endpush
@endsection
