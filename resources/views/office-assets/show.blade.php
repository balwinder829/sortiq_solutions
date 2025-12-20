@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">

        <div class="card-header">
            <h4>Office Asset Details</h4>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Purchase Date</div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($asset->expense_date)->format('d M Y') }}
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Asset Name</div>
                <div class="col-md-8">{{ $asset->title }}</div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 fw-bold">Amount</div>
                <div class="col-md-8">{{ number_format($asset->amount, 2) }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">Description</div>
                <div class="col-md-8">{{ $asset->description ?? '-' }}</div>
            </div>

            <a href="{{ route('office-assets.edit', $asset->id) }}"
               class="btn btn-primary">
                Edit
            </a>

            <a href="{{ route('office-assets.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>
    </div>
</div>
@endsection
