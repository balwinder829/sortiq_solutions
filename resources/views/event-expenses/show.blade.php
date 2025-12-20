@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">

        <div class="card-header">
            <h4>Event Cost Details</h4>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Expense Date</div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Title</div>
                <div class="col-md-8">{{ $expense->title }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Amount</div>
                <div class="col-md-8">{{ number_format($expense->amount, 2) }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">{{ $expense->description ?? '-' }}</div>
            </div>

            <a href="{{ route('event-expenses.edit', $expense->id) }}" class="btn btn-primary">
                Edit
            </a>

            <a href="{{ route('event-expenses.index') }}" class="btn btn-secondary">
                Back
            </a>

        </div>
    </div>
</div>
@endsection
