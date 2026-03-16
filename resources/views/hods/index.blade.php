@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="page_heading mb-3">Authority Management</h1>

    {{-- FILTER FORM --}}
    <form method="GET" id="filterForm" class="row mb-3">
        <div class="col-md-4">
            <select name="state" id="stateFilter" class="form-control">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}"
                        {{ request('state') == $state->name ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        

        <div class="col-md-4">
            <!-- <button class="btn btn-primary">Filter</button> -->
            <a href="{{ route('hods.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

        <div class="col-md-4 text-end">
            <a href="{{ route('hods.create') }}" class="btn btn-primary">
                Add Authority
            </a>
        </div>
    </form>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABLE --}}
    <table class="table table-bordered table-striped" id="hodtable">
        <thead>
            <tr>
                <th>#</th>
                <th>College</th>
                <th>State</th>
                <th>District</th>
                <th>TPO</th>
                <th>Contact</th>
                <th>HOD</th>
                <th>Contact</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
@foreach($hods as $hod)
<tr>
    <td>{{ $loop->iteration }}</td>

    <td>{{ $hod->college->college_name ?? '-' }}</td>
    <td>{{ $hod->college->state->name ?? '-' }}</td>
    <td>{{ $hod->college->district->name ?? '-' }}</td>

    <td>
        <strong></strong> {{ $hod->tpo_name ?? '-' }} <br>
        <small>{{ $hod->tpo_gender ?? '' }}</small>
    </td>

    <td>
        {{ $hod->tpo_contact ?? '-' }}
    </td>

    <td>
        <strong></strong> {{ $hod->hod_name ?? '-' }} <br>
        <small>{{ $hod->hod_gender ?? '' }}</small>
    </td>

    <td>
        {{ $hod->hod_contact ?? '-' }}
    </td>

    <td class="text-center">
        <a href="{{ route('hods.edit', $hod->id) }}"
           class="btn btn-sm">
           <i class="fas fa-edit"></i>
        </a>

          {{-- Delete Button --}}
        <form action="{{ route('hods.destroy', $hod->id) }}"
              method="POST"
              style="display:inline-block;"
              data-swal-confirm="Are you sure you want to delete this record?">

            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-sm">
                <i class="fas fa-trash"></i>
            </button>
        </form>
    </td>
</tr>

@endforeach
</tbody>
    </table>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#hodtable').DataTable({
        pageLength: 100,
        lengthMenu: [5,10,25,50,100],
        orders: []
    });
});
</script>

<script>

$(document).ready(function(){

$('#stateFilter').on('change', function(){

$('#filterForm').submit();

});

});

</script>
@endpush
