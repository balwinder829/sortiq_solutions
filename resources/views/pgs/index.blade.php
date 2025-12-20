@extends('layouts.app')

@section('content')
<style>
    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }
</style>

<div class="container">

    {{-- ADD BUTTON --}}
    <a href="{{ route('pgs.create') }}"
       class="btn mb-3"
       style="background:#6b51df;color:#fff;">
        Add PG
    </a>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= FILTERS ================= --}}
    <form method="GET" action="{{ route('pgs.index') }}" class="mb-4">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="fw-bold">PG Type</label>
                <select name="pg_type" class="form-control">
                    <option value="">All</option>
                    <option value="boys" {{ request('pg_type')=='boys' ? 'selected' : '' }}>Boys</option>
                    <option value="girls" {{ request('pg_type')=='girls' ? 'selected' : '' }}>Girls</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Food Type</label>
                <select name="food_type" class="form-control">
                    <option value="">All</option>
                    <option value="food" {{ request('food_type')=='food' ? 'selected' : '' }}>Food</option>
                    <option value="without_food" {{ request('food_type')=='without_food' ? 'selected' : '' }}>
                        Without Food
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Location</label>
                <input type="text"
                       name="address"
                       value="{{ request('address') }}"
                       class="form-control"
                       placeholder="Search address">
            </div>

        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <button class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>

                <a href="{{ route('pgs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
    {{-- ================= END FILTERS ================= --}}

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="pgTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>PG Type</th>
                    <th>Food Type</th>
                    <th>Rent</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($pgs as $pg)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $pg->name }}</td>
                    <td>{{ ucfirst($pg->pg_type) }}</td>
                    <td>{{ $pg->food_type == 'food' ? 'Food' : 'Without Food' }}</td>
                    <td>{{ $pg->rent_estimate ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $pg->status=='active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($pg->status) }}
                        </span>
                    </td>
                    <td class="text-center">

                        <a href="{{ route('pgs.show', $pg->id) }}" class="btn btn-sm" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('pgs.edit', $pg->id) }}" class="btn btn-sm" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('pgs.destroy', $pg->id) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm"
                                    onclick="return confirm('Are you sure?')"
                                    title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#pgTable').DataTable({
        pageLength: 25,
        order: [[0,'desc']]
    });
});
</script>
@endpush
