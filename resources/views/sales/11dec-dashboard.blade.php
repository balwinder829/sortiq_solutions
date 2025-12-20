@extends('layouts.app')

@section('content')
<style>
.chart-box {
    height: 280px;     /* Adjust as needed */
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.chart-box canvas {
    max-height: 260px !important;
    width: 100% !important;
}
</style>

<div class="container">

    <h2 class="mb-4">Sales Dashboard</h2>

    {{-- ADMIN USER SELECTOR --}}
     

   @if(auth()->user()->role != 3)
<div class="row mb-4">
    <form method="GET" action="{{ route('sales.dashboard') }}" class="row g-2">

        {{-- Sales Person Filter --}}
        <div class="col-md-3">
            <select name="assigned_to" class="form-control" onchange="this.form.submit()">
                <option value="">All Sales Users</option>
                @foreach($salesUsers as $user)
                    <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                        {{ $user->username }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Date Range Filter --}}
        <div class="col-md-3">
            <select name="range" class="form-control" onchange="this.form.submit()">
                <option value="">-- Select Range --</option>
                <option value="today" {{ request('range')=='today'?'selected':'' }}>Today</option>
                <option value="yesterday" {{ request('range')=='yesterday'?'selected':'' }}>Yesterday</option>
                <option value="last_7" {{ request('range')=='last_7'?'selected':'' }}>Last 7 Days</option>
                <option value="this_month" {{ request('range')=='this_month'?'selected':'' }}>This Month</option>
                <option value="last_month" {{ request('range')=='last_month'?'selected':'' }}>Last Month</option>
            </select>
        </div>

        {{-- Custom Date --}}
        <div class="col-md-2">
            <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div class="col-md-2">
            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        {{-- Filter Button --}}
        <div class="col-md-1 d-grid">
            <button class="btn btn-primary">Filter</button>
        </div>

        {{-- Reset Button --}}
        <div class="col-md-1 d-grid">
            <a href="{{ route('sales.dashboard') }}" class="btn btn-secondary">
                Reset
            </a>
        </div>

    </form>
</div>
@endif



    {{-- Stats Row --}}
    <div class="row">

        @php
        // Forward assigned_to filter if admin selected a user
        $assignedFilter = (auth()->user()->role != 3 && $userId)
            ? ['assigned_to' => $userId]
            : [];
    @endphp

    @foreach([
        'new' => 'new',
        'contacted' => 'contacted',
        'follow_up' => 'follow_up',
        'not_interested' => 'not_interested',
        'onboarded' => 'onboarded',
        'calls_today' => 'calls_today',
        'today_followups' => 'today_followups',
    ] as $key => $status)

        @php
            // Build correct URL parameters
            $params = array_merge(
                ['search' => ''], // always include search param
                $assignedFilter // include assigned_to if needed
            );

            // Status cards should apply ?status=xxx
            if (in_array($key, ['new','contacted','follow_up','not_interested','onboarded'])) {
                $params['status'] = $status;
            }

            // Calls Today & Today Followups special params
            if ($key == 'calls_today') {
                $params['calls_today'] = 1;
            }
            if ($key == 'today_followups') {
                $params['today_followups'] = 1;
            }
        @endphp

            <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', $params) }}"
                   class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats[$key] }}</h4>
                        <p class="text-muted m-0">
                            {{ ucwords(str_replace('_',' ', $key)) }}
                        </p>
                    </div>
                </a>
            </div>
        </div>


        @endforeach

        {{-- TOTAL LEADS CARD --}}
        @php
            $totalParams = $assignedFilter;
        @endphp

        <div class="col-md-3 mb-3">
            <div class="card shadow-sm">
                <a href="{{ route('leads.index', $totalParams) }}" 
                   class="text-decoration-none text-dark">
                    <div class="card-body text-center">
                        <h4>{{ $stats['total'] }}</h4>
                        <p class="text-muted m-0">Total Leads Assigned</p>
                    </div>
                </a>
            </div>
        </div>

    </div>

    {{-- CHARTS --}}
   
   <div class="row mt-4">

    <div class="col-md-4">
        <h5>Calls (Last 7 Days)</h5>
        <div class="chart-box">
            <canvas id="callsChart"></canvas>
        </div>
    </div>

    <div class="col-md-4">
        <h5>Follow-ups (Last 7 Days)</h5>
        <div class="chart-box">
            <canvas id="followupChart"></canvas>
        </div>
    </div>

    <div class="col-md-4">
        <h5>Lead Status Breakdown</h5>
        <div class="chart-box">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

</div>


    {{-- Leaderboard --}}
    <h4 class="mt-5">Sales Leaderboard</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Sales User</th>
                    <th>Assigned</th>
                    <th>Contacted</th>
                    <th>Follow Ups</th>
                    <th>Onboarded</th>
                </tr>
            </thead>

            <tbody>
                @foreach($leaderboard as $rank => $user)
                <tr>
                    <td>{{ $rank + 1 }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->assigned_leads_count }}</td>
                    <td>{{ $user->contacted_count }}</td>
                    <td>{{ $user->followup_count }}</td>
                    <td>{{ $user->onboarded_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recent Leads --}}
    <h4 class="mt-5">Recent Leads</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th>Follow-up Date</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                @foreach($recentLeads as $lead)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $lead->name }}</td>
                    <td>{{ $lead->phone }}</td>
                    <td>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</td>
                    <td>{{ $lead->follow_up_date ? $lead->follow_up_date->format('d-m-Y') : '-' }}</td>
                    <td>{{ $lead->created_at->format('d-m-Y h:i A') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection


@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>


<script>
    // ---------- SAFE FALLBACK HELPERS ----------
    function safeLabels(arr) {
        return (arr && arr.length) ? arr : ['No Data'];
    }
    function safeData(arr) {
        return (arr && arr.length) ? arr : [0];
    }

    // ---------------- CALLS CHART ------------------
new Chart(document.getElementById('callsChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($callsChart->pluck('day')) !!},
        datasets: [{
            label: 'Calls',
            data: {!! json_encode($callsChart->pluck('total')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.2)',
            pointBackgroundColor: '#4e73df',
            borderWidth: 2,
            tension: 0.3
        }]
    }
});


// ---------------- FOLLOW-UP CHART ------------------
new Chart(document.getElementById('followupChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($followupChart->pluck('day')) !!},
        datasets: [{
            label: 'Follow Ups',
            data: {!! json_encode($followupChart->pluck('total')) !!},
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.2)',
            pointBackgroundColor: '#1cc88a',
            borderWidth: 2,
            tension: 0.3
        }]
    }
});


// ---------------- STATUS PIE CHART ------------------
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode($statusChart->pluck('status')) !!},
        datasets: [{
            data: {!! json_encode($statusChart->pluck('total')) !!},
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
            ]
        }]
    }
});

</script>
@endpush

