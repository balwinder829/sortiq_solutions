@extends('layouts.students.app')

@section('content')

<div class="container">

<h1 class="page_heading mb-3">My Fees</h1>

<div class="row mb-3">

    <div class="col-md-3">
        <div class="card text-center p-3">
            <h5>Total Fees</h5>
            <h3>₹ {{ number_format($totalFees) }}</h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-3">
            <h5>Paid</h5>
            <h3 class="text-success">
                ₹ {{ number_format($paidFees) }}
            </h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-3">
            <h5>Pending</h5>

            @if($pendingFees > 0)
                <h3 class="text-danger">
                    ₹ {{ number_format($pendingFees) }}
                </h3>
            @else
                <h3 class="text-success">0</h3>
            @endif

        </div>
    </div>

    <div class="col-md-3">
        <div class="card text-center p-3">
            <h5>Next Due</h5>

            @php
    use Carbon\Carbon;

    $today = Carbon::today();
@endphp

@if($pendingFees > 0)

    @if($nextDueDate)

        @php
            $due = Carbon::parse($nextDueDate);
        @endphp

        @if($due->isPast())
            <span class="badge bg-danger">
                {{ $due->format('d M Y') }} (Overdue)
            </span>

        @elseif($due->isToday())
            <span class="badge bg-warning text-dark">
                {{ $due->format('d M Y') }} (Today)
            </span>

        @elseif($due->isTomorrow())
            <span class="badge bg-info text-dark">
                {{ $due->format('d M Y') }} (Tomorrow)
            </span>

        @else
            <span class="badge bg-success">
                {{ $due->format('d M Y') }}
            </span>
        @endif

    @else
        <span class="badge bg-secondary">No Due Date</span>
    @endif

@else
    <span class="badge bg-success">Paid</span>
@endif

        </div>
    </div>

</div>

{{-- Optional Status Row (like summary feel) --}}
<div class="card p-3 text-center">

    @if($pendingFees > 0)
        <h5 class="text-danger mb-0">
            Payment Pending
        </h5>
    @else
        <h5 class="text-success mb-0">
            Fully Paid ✅
        </h5>
    @endif

</div>

</div>

@endsection