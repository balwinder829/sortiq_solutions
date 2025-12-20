@extends('layouts.app')

@section('content')
<div class="container">

    <h3 class="mb-4">Enquiry Analytics Dashboard</h3>

    {{-- Top Stats --}}
    <div class="row mb-4">

        <div class="col-md-3">
            <div class="card card-body text-center bg-primary text-white">
                <h5>Total Enquiries</h5>
                <h2>{{ $total }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-body text-center bg-warning">
                <h5>Today Follow-ups</h5>
                <h2>{{ $today_followups }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-body text-center bg-danger text-white">
                <h5>Missed Follow-ups</h5>
                <h2>{{ $missed_followups }}</h2>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-body text-center bg-success text-white">
                <h5>Upcoming Follow-ups</h5>
                <h2>{{ $upcoming }}</h2>
            </div>
        </div>
    </div>


    {{-- Charts Row --}}
    <div class="row">

        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="text-center">Lead Status Distribution</h5>
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card p-3">
                <h5 class="text-center">Call Status Breakdown</h5>
                <canvas id="callChart"></canvas>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // LEAD STATUS CHART
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($status_chart->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($status_chart->pluck('total')) !!},
            }]
        }
    });

    // CALL STATUS CHART
    new Chart(document.getElementById('callChart'), {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($call_status_chart->pluck('call_status')) !!},
            datasets: [{
                data: {!! json_encode($call_status_chart->pluck('total')) !!},
            }]
        }
    });
</script>
@endpush
