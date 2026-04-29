@extends('layouts.app')

@section('title', 'Sales Analytics')

@section('content')

<div class="container mt-4">

    <div class="row mb-2">
        <div class="col-md-10">
            <h1 class="page_heading">Sales Analytics Dashboard</h1>
        </div>
    </div>

    {{-- Top Performing College --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body text-center">
            <h4 class="mb-2">Top Performing College</h4>

            @if($topCollege)
                <h5 class="fw-bold">{{ $topCollege->collegeData->FullName }}</h5>
                <p class="text-muted">{{ $topCollege->total_students }} Students</p>
            @else
                <p>No Data Available</p>
            @endif
        </div>
    </div>

    {{-- Charts --}}
    <div class="row">

        {{-- College-wise Student Count --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">College-wise Student Count</div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="collegeCountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- College-wise Revenue --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">College-wise Revenue (Total Fees)</div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="collegeRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session-wise Student Count --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">Session-wise Student Count</div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="sessionCountChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session-wise Revenue --}}
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header">Session-wise Revenue</div>
                <div class="card-body">
                    <div style="height:300px;">
                        <canvas id="sessionRevenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

// ===== Prepare Data =====
const fullCollegeNames = {!! json_encode(
    $collegeCounts->map(fn($c) => $c->collegeData->FullName ?? 'Unknown')->toArray()
) !!};

const shortCollegeNames = {!! json_encode(
    $collegeCounts->map(function($c) {
        $name = $c->collegeData->FullName ?? 'Unknown';
        return strlen($name) > 15 ? substr($name, 0, 15) . '...' : $name;
    })->toArray()
) !!};

// ===== Global Options =====
const baseOptions = {
    maintainAspectRatio: false,
    responsive: true,
    plugins: {
        legend: { position: 'bottom' }
    },
    scales: {
        x: { beginAtZero: true },
        y: {
            ticks: {
                autoSkip: false
            }
        }
    }
};

// =================== College-wise Student Count ===================
new Chart(document.getElementById('collegeCountChart'), {
    type: 'bar',
    data: {
        labels: shortCollegeNames,
        datasets: [{
            label: 'Students',
            backgroundColor: '#4e73df',
            data: {!! json_encode($collegeCounts->pluck('total_students')->toArray()) !!}
        }]
    },
    options: {
        ...baseOptions,
        indexAxis: 'y',
        plugins: {
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return fullCollegeNames[context[0].dataIndex];
                    },
                    label: function(context) {
                        return 'Students: ' + context.raw;
                    }
                }
            }
        }
    }
});

// =================== College-wise Revenue ===================
new Chart(document.getElementById('collegeRevenueChart'), {
    type: 'bar',
    data: {
        labels: shortCollegeNames,
        datasets: [{
            label: 'Revenue (Rs.)',
            backgroundColor: '#1cc88a',
            data: {!! json_encode($collegeRevenue->pluck('total_revenue')->toArray()) !!}
        }]
    },
    options: {
        ...baseOptions,
        indexAxis: 'y',
        plugins: {
            tooltip: {
                callbacks: {
                    title: function(context) {
                        return fullCollegeNames[context[0].dataIndex];
                    },
                    label: function(context) {
                        return 'Revenue: Rs. ' + context.raw;
                    }
                }
            }
        }
    }
});

// =================== Session-wise Student Count ===================
new Chart(document.getElementById('sessionCountChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode(
            $sessionCounts->map(fn($s) => $s->sessionData->session_name ?? 'Unknown')->toArray()
        ) !!},
        datasets: [{
            label: 'Students',
            borderColor: '#f6c23e',
            data: {!! json_encode($sessionCounts->pluck('total_students')->toArray()) !!},
            fill: false,
            tension: 0.3
        }]
    },
    options: baseOptions
});

// =================== Session-wise Revenue ===================
new Chart(document.getElementById('sessionRevenueChart'), {
    type: 'pie',
    data: {
        labels: {!! json_encode(
            $sessionCounts->map(fn($s) => $s->sessionData->session_name ?? 'Unknown')->toArray()
        ) !!},
        datasets: [{
            label: 'Revenue',
            backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b', '#36b9cc'],
            data: {!! json_encode($sessionRevenue->pluck('total_revenue')->toArray()) !!}
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

</script>
@endpush