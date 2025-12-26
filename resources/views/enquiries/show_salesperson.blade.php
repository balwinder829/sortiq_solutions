@extends('layouts.app')

@section('content')

<div class="container">

    {{-- ============================= --}}
    {{--   HEADER: SALESPERSON INFO    --}}
    {{-- ============================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex justify-content-between align-items-center">

            <div>
                <h3 class="mb-1">{{ $salesperson->name }}</h3>
                <p class="mb-0 text-muted"><strong>Mobile:</strong> {{ $salesperson->phone }}</p>
            </div>

            <a href="{{ route('salespersons.list') }}" class="btn btn-secondary">
                ← Back to List
            </a>

        </div>
    </div>

    {{-- ============================= --}}
    {{--          FILTERS AREA          --}}
    {{-- ============================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <strong>Filter Leads</strong>
        </div>

        <div class="card-body">
            <form method="GET">

                <div class="row">

                    {{-- SEARCH --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Search by Name or Phone</label>
                        <input type="text" name="search" class="form-control"
                               placeholder="Enter name or phone..."
                               value="{{ request('search') }}">
                    </div>

                    {{-- DATE RANGE — FROM --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="from_date" class="form-control"
                               value="{{ request('from_date') }}">
                    </div>

                    {{-- DATE RANGE — TO --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="to_date" class="form-control"
                               value="{{ request('to_date') }}">
                    </div>

                    {{-- STATUS FILTER --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="new" {{ request('status')=='new' ? 'selected' : '' }}>New</option>
                            <option value="followup" {{ request('status')=='followup' ? 'selected' : '' }}>Follow-up</option>
                            <option value="registered" {{ request('status')=='registered' ? 'selected' : '' }}>Registered</option>
                            <option value="closed" {{ request('status')=='closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>

                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        🔍 Search
                    </button>

                    <a href="{{ url()->current() }}" class="btn btn-secondary">
                        🔄 Reset
                    </a>
                </div>

            </form>
        </div>
    </div>


    {{-- ============================= --}}
    {{--      ASSIGNED LEADS TABLE     --}}
    {{-- ============================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white">
            <strong>Assigned Leads</strong> 
            <span class="badge bg-light text-dark ms-2">{{ $leads->total() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Lead ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Lead Status</th>
                        <th>Assigned At</th>
                        <th>Created At</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($leads as $lead)
                        <tr>
                            <td>{{ $lead->id }}</td>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->mobile }}</td>

                            <td>
                                @if($lead->lead_status == 'new')
                                    <span class="badge bg-secondary">New</span>
                                @elseif($lead->lead_status == 'followup')
                                    <span class="badge bg-info text-dark">Follow-up</span>
                                @elseif($lead->lead_status == 'registered')
                                    <span class="badge bg-success">Registered</span>
                                @elseif($lead->lead_status == 'closed')
                                    <span class="badge bg-danger">Closed</span>
                                @endif
                            </td>

                            <td>
    {{ $lead->assigned_at ? $lead->assigned_at->format('d M, Y') : '-' }}
</td>

                            <td>{{ $lead->created_at->format('d M, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No leads found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
       <div class="mt-4 d-flex justify-content-center">
    {{ $leads->appends(request()->query())->links('pagination::bootstrap-5') }}
</div>
    </div>


    {{-- ============================= --}}
    {{--     TODAY'S FOLLOW-UP LIST    --}}
    {{-- ============================= --}}
    <div class="card shadow-sm">
        <div class="card-header bg-warning">
            <strong>Today's Follow-ups</strong>
            <span class="badge bg-dark text-white ms-2">{{ $todayWork->count() }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Enquiry ID</th>
                        <th>Enquiry Name</th>
                        <th>Follow-up Remark</th>
                        <th>Time</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($todayWork as $work)
                        <tr>
                            <td>{{ $work->enquiry->id }}</td>
                            <td>{{ $work->enquiry->name }}</td>
                            <td>{{ $work->details }}</td>
                            <td>{{ $work->created_at->format('h:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No follow-ups today</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
