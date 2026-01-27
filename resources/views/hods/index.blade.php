@extends('layouts.app')

@section('content')
<div class="container">

    <h1 class="page_heading mb-3">Authority Management</h1>

    {{-- FILTER FORM --}}
    <form method="GET" action="{{ route('hods.index') }}" class="row mb-3">
        <div class="col-md-3">
            <select name="state" class="form-control">
                <option value="">All States</option>
                @foreach($states as $state)
                    <option value="{{ $state->name }}"
                        {{ request('state') == $state->name ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <select name="hod_status" class="form-control">
                <option value="">All Colleges</option>
                <option value="yes" {{ request('hod_status') == 'yes' ? 'selected' : '' }}>
                    With HOD
                </option>
                <option value="no" {{ request('hod_status') == 'no' ? 'selected' : '' }}>
                    Without HOD
                </option>
            </select>
        </div>

        <div class="col-md-3">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('hods.index') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

        <div class="col-md-3 text-end">
            <a href="{{ route('hods.create') }}" class="btn btn-primary">
                Add HOD
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
                <th>HOD Details</th>
                <th>Contact</th>
                <th width="120">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($colleges as $college)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $college->college_name }}</td>
                    <td>{{ $college->state->name ?? '-' }}</td>
                    <td>{{ $college->district->name ?? '-' }}</td>
                    <td>
                        @if($college->hod)
                            <strong>{{ $college->hod->name }}</strong><br>
                            {{ $college->hod->designation }} ({{ $college->hod->position }})
                        @else
                            <span class="text-danger">No HOD</span>
                        @endif
                    </td>
                    <td>{{ $college->hod->contact_no ?? '-' }}</td>
                    <td class="text-center">
                        @if($college->hod)
                            <a href="{{ route('hods.edit', $college->hod->id) }}"
                               class="btn btn-sm"><i class="fas fa-edit"></i></a>
                        @else
                            —
                        @endif
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
        lengthMenu: [5,10,25,50,100]
    });
});
</script>
@endpush
