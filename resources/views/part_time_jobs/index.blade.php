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
    <a href="{{ route('part-time-jobs.create') }}"
       class="btn mb-3"
       style="background:#6b51df;color:#fff;">
        Add Part-Time Job
    </a>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= FILTERS ================= --}}
    <form method="GET" action="{{ route('part-time-jobs.index') }}" class="mb-4">
        <div class="row align-items-end">

            <div class="col-md-3">
                <label class="fw-bold">Job Type</label>
                <input type="text"
                       name="job_type"
                       value="{{ request('job_type') }}"
                       class="form-control"
                       placeholder="Job type">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Shift</label>
                <input type="text"
                       name="shift"
                       value="{{ request('shift') }}"
                       class="form-control"
                       placeholder="Shift">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Location</label>
                <input type="text"
                       name="location"
                       value="{{ request('location') }}"
                       class="form-control"
                       placeholder="Location">
            </div>

            <div class="col-md-3">
                <label class="fw-bold">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

        </div>

        <div class="row mt-3">
            <div class="col-md-12 text-end">
                <button class="btn btn-primary">
                    <i class="fa fa-search"></i> Search
                </button>

                <a href="{{ route('part-time-jobs.index') }}" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> Reset
                </a>
            </div>
        </div>
    </form>
    {{-- ================= END FILTERS ================= --}}

    {{-- TABLE --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="jobTable">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Job Type</th>
                    <th>Shift</th>
                    <th>Location</th>
                    <th>Mobile</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $job->name }}</td>
                    <td>{{ $job->job_type ?? '-' }}</td>
                    <td>{{ $job->shift ?? '-' }}</td>
                    <td>{{ $job->location ?? '-' }}</td>
                    <td>{{ $job->mobile ?? '-' }}</td>
                    <td>
                        <span class="badge {{ $job->status=='active' ? 'bg-success' : 'bg-secondary' }}">
                            {{ ucfirst($job->status) }}
                        </span>
                    </td>
                    <td class="text-center">

                        <a href="{{ route('part-time-jobs.show', $job->id) }}"
                           class="btn btn-sm" title="View">
                            <i class="fa fa-eye"></i>
                        </a>

                        <a href="{{ route('part-time-jobs.edit', $job->id) }}"
                           class="btn btn-sm" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>

                        <form action="{{ route('part-time-jobs.destroy', $job->id) }}"
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
    $('#jobTable').DataTable({
        pageLength: 25,
        order: [[0,'desc']]
    });
});
</script>
@endpush
