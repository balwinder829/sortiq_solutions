@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
        text-transform: capitalize;
     }
</style>

<div class="container">

    <div class="row mb-2">
        <div class="col-md-2">
            <h1 class="page_heading">Colleges</h1>
        </div>
        <div class="col-md-4">
            <!-- <label><strong>Filter by State</strong></label> -->
            <select id="filter-state" class="form-control">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}">{{ $state->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <!-- <label><strong>Filter by District</strong></label> -->
            <select id="filter-district" class="form-control">
                <option value="">All Districts</option>
            </select>
        </div>
        <div class="col-md-2">
            <div class="d-flex justify-content-end">
                <a href="{{ route('colleges.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add College</a>
            </div>
        </div>
    </div>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="colleges-table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>College Name</th>
                <th>State</th>
                <th>District</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($colleges as $college)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $college->college_name }}</td>
                <td>{{ $college->state->name ?? '-' }}</td>
                <td>{{ $college->district->name ?? '-' }}</td>
                <td class="text-center">
                    <div class="mb-2">
                        <a href="{{ route('colleges.edit', $college->id) }}" class="btn btn-sm"
                           data-bs-toggle="tooltip" title="Edit">
                           <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('colleges.destroy', $college->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm"
                                onclick="return confirm('Are you sure?')" data-bs-toggle="tooltip" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </td>
            </tr>
            @endforeach
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

    // INIT DATATABLE
    let table = $('#colleges-table').DataTable({
        "pageLength": 50,
        "lengthMenu": [5, 10, 25, 50, 100],
    });

    // ----------------------------------------
    // STATE FILTER → Updates District Dropdown
    // ----------------------------------------
    $('#filter-state').on('change', function () {

    let selectedState = this.value;

    let districtDropdown = $('#filter-district');

    // Reset district dropdown
    districtDropdown.empty().append('<option value="">All Districts</option>');

    if (selectedState === "") {
        // CLEAR state filter
        table.column(2).search("").draw();

        // CLEAR district filter
        table.column(3).search("").draw();

        return; // IMPORTANT
    }

    // APPLY state filter
    table.column(2).search(selectedState).draw();

    // Find state ID by matching name
    let stateId = Object.keys(districtsByState).find(id => {
        return districtsByState[id][0]?.state_name === selectedState;
    });

    // Populate district dropdown for selected state
    if (stateId && districtsByState[stateId]) {
        districtsByState[stateId].forEach(function (d) {
            districtDropdown.append(`<option value="${d.name}">${d.name}</option>`);
        });
    }

    // Reset district DataTable filter
    table.column(3).search("").draw();
});


    // ----------------------------------------
    // DISTRICT FILTER
    // ----------------------------------------
    $('#filter-district').on('change', function () {
        table.column(3).search(this.value).draw();
    });

});

// Bootstrap Tooltips Init
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>

@endpush
@endsection
