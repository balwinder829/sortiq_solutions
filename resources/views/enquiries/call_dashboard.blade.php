@extends('layouts.app')

@section('content')
<style>
/* Force background on TR and TD */
.row-call-done,
.row-call-done td {
    background-color: #198754 !important;
    color: #fff !important;
}

.row-follow-up,
.row-follow-up td {
    background-color: #0d6efd !important;
    color: #fff !important;
}

.row-not-picked,
.row-not-picked td {
    background-color: #fd7e14 !important;
    color: #212529 !important;
}

.row-registered,
.row-registered td {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

/* Fix links & badges */
.row-call-done a,
.row-follow-up a {
    color: #fff !important;
}
</style>


<div class="container-fluid px-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">📞 Call Activity Dashboard</h3>
    </div>

    {{-- ================= KPI CARDS ================= --}}
    <div class="row g-3 mb-4">

        {{-- Total Calls --}}
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <small class="text-muted">Total Calls</small>
                    <h2 class="fw-bold text-primary mt-1">{{ $totalCalls }}</h2>
                </div>
            </div>
        </div>

        {{-- Calls Per Salesperson --}}
        @foreach($callsByUser as $row)
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <small class="text-muted">{{ $row->user->name }}</small>
                    <h3 class="fw-semibold mt-1">{{ $row->total_calls }}</h3>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- ================= FILTER CARD ================= --}}
    <div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white d-flex align-items-center">
        <i class="fa fa-search me-2"></i>
        <strong>Filter Calls</strong>
    </div>

    <div class="card-body">
        <form method="GET" id="filterForm">

            <div class="row g-3 align-items-end">

                {{-- Quick Date --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Quick Date</label>
                    <select name="quick_date" id="quickDate" class="form-control">
                        <option value="">Select</option>
                        <option value="today" {{ request('quick_date')=='today'?'selected':'' }}>Today</option>
                        <option value="yesterday" {{ request('quick_date')=='yesterday'?'selected':'' }}>Yesterday</option>
                        <option value="last7" {{ request('quick_date')=='last7'?'selected':'' }}>Last 7 Days</option>
                    </select>
                </div>

                {{-- Salesperson --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Salesperson</label>
                    <select name="salesperson_id" class="form-control">
                        <option value="">All Salespersons</option>
                        @foreach($sales as $s)
                            <option value="{{ $s->id }}"
                                {{ request('salesperson_id')==$s->id?'selected':'' }}>
                                {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- From --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">From</label>
                    <input type="date" name="from_date" id="fromDate"
                           class="form-control"
                           value="{{ request('from_date') }}">
                </div>

                {{-- To --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold">To</label>
                    <input type="date" name="to_date" id="toDate"
                           class="form-control"
                           value="{{ request('to_date') }}">
                </div>

                {{-- Buttons --}}
              <div class="col-md-2">
    <label class="form-label fw-semibold d-block">&nbsp;</label>
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center">
            <i class="fa fa-search me-1"></i> Apply
        </button>

        <a href="{{ route('admin.calls') }}"
           class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center">
            Reset
        </a>
    </div>
</div>


            </div>

            {{-- Active Filters Summary --}}
            @if(request()->anyFilled(['quick_date','salesperson_id','from_date','to_date']))
                <div class="mt-3">
                    <span class="badge bg-light text-dark">
                        🔍 Filters Applied
                    </span>

                    @if(request('quick_date'))
                        <span class="badge bg-info text-dark">
                            {{ ucfirst(request('quick_date')) }}
                        </span>
                    @endif

                    @if(request('salesperson_id'))
                        <span class="badge bg-secondary">
                            {{ optional($sales->firstWhere('id', request('salesperson_id')))->name }}
                        </span>
                    @endif
                </div>
            @endif

        </form>
    </div>
</div>


    {{-- ================= CALL LOG TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>📋 Call Logs</strong>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date & Time</th>
                        <th>Salesperson</th>
                        <th>Lead</th>
                        <th>Mobile</th>
                        <th>Call Status</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($calls as $call)
                @php
    $rowClass = '';

    if (in_array($call->call_status, ['Interested', 'Visited Office'])) {
        $rowClass = 'row-call-done';
    } elseif (in_array($call->call_status, ['Follow-up Required', 'Call Back Later'])) {
        $rowClass = 'row-follow-up';
    } elseif (in_array($call->call_status, ['Not Answered', 'Ringing', 'Switched Off'])) {
        $rowClass = 'row-not-picked';
    } elseif ($call->call_status == 'Registered') {
        $rowClass = 'row-registered';
    }
@endphp
                    <tr class="{{ $rowClass }}">
                        <td>
                            <small class="text-muted">
                                {{ $call->created_at->format('d M Y') }}<br>
                                {{ $call->created_at->format('h:i A') }}
                            </small>
                        </td>

                        <td class="fw-semibold">
                            {{ $call->user->name }}
                        </td>

                        <td>
                            {{ $call->enquiry->name }}
                        </td>

                        <td>
                            <a href="tel:{{ $call->enquiry->mobile }}">
                                {{ $call->enquiry->mobile }}
                            </a>
                        </td>

                        <td>
                            <span class="badge 
                                @if(in_array($call->call_status,['Interested','Visited Office'])) bg-success
                                @elseif(in_array($call->call_status,['Not Interested','Wrong Number'])) bg-danger
                                @else bg-secondary
                                @endif">
                                {{ $call->call_status }}
                            </span>
                        </td>

                        <td class="text-center">
                            <a href="{{ route('enquiries.show', $call->enquiry->id) }}"
                               class="btn btn-sm btn-outline-primary"
                               title="View Lead">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No call records found
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
