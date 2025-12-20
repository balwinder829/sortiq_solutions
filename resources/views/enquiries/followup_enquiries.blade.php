@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4 fw-bold">Pending / Missed Follow-ups</h3>

    {{-- SUMMARY CARDS --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h6>Due Today</h6>
                    <h3 class="text-warning">{{ $summary['today'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center border-danger">
                <div class="card-body">
                    <h6>Missed</h6>
                    <h3 class="text-danger">{{ $summary['missed'] }}</h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h6>Upcoming</h6>
                    <h3 class="text-success">{{ $summary['upcoming'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <form method="GET" class="card mb-4">
        <div class="card-body row">

            <div class="col-md-3">
                <label>Type</label>
                <select name="type" class="form-control">
                    <option value="">All</option>
                    <option value="today" {{ request('type')=='today'?'selected':'' }}>Today</option>
                    <option value="missed" {{ request('type')=='missed'?'selected':'' }}>Missed</option>
                    <option value="upcoming" {{ request('type')=='upcoming'?'selected':'' }}>Upcoming</option>
                </select>
            </div>

            <div class="col-md-3">
                <label>Salesperson</label>
                <select name="salesperson_id" class="form-control">
                    <option value="">All</option>
                    @foreach($sales as $s)
                        <option value="{{ $s->id }}"
                            {{ request('salesperson_id')==$s->id?'selected':'' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label>Lead Status</label>
                <select name="lead_status" class="form-control">
                    <option value="">All</option>
                    <option value="followup">Follow-up</option>
                    <option value="registered">Registered</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100">Filter</button>
            </div>

        </div>
    </form>

    {{-- TABLE --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Salesperson</th>
                        <th>Lead Status</th>
                        <th>Last Call</th>
                        <th>Next Follow-up</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($enquiries as $e)
                    <tr>
                        <td>{{ $e->name }}</td>
                        <td>{{ $e->mobile }}</td>
                        <td>{{ $e->assignedTo->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-info">{{ $e->lead_status }}</span>
                        </td>
                        <td>{{ $e->last_call_status ?? '-' }}</td>
                        <td>
                            @if($e->next_followup_at)
                                {{ $e->next_followup_at->format('d M Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('enquiries.show', $e->id) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">
                            No follow-ups found
                        </td>
                    </tr>
                @endforelse
                </tbody>

            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $enquiries->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection
