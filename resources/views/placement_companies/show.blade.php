@extends('layouts.app')

@section('content')
<style>
    .company-website a {
        color: #0d6efd; /* bootstrap primary */
        text-decoration: none;
    }

    .company-website a:hover {
        color: #084298; /* darker blue */
        text-decoration: underline;
    }
</style>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Placement Company Details</h4>
        </div>

        <div class="card-body">

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Company Name:</div>
                <div class="col-md-8">{{ $company->name }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Contact Person:</div>
                <div class="col-md-8">{{ $company->contact_person ?? '-' }}</div>
            </div>

             

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Phone:</div>
                <div class="col-md-8">{{ $company->phone ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Website:</div>
                <div class="col-md-8 company-website">
                    @if($company->website)
                        <a href="{{ $company->website }}" target="_blank">
                            {{ $company->website }}
                        </a>
                    @else
                        -
                    @endif
                </div>
            </div>

             

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Address:</div>
                <div class="col-md-8">{{ $company->address ?? '-' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4 fw-bold">Remarks:</div>
                <div class="col-md-8">{{ $company->remarks ?? '-' }}</div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 fw-bold">Status:</div>
                <div class="col-md-8">
                    <span class="badge {{ $company->status=='active' ? 'bg-success' : 'bg-secondary' }}">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
            </div>

            <a href="{{ route('placement-companies.edit', $company->id) }}"
               class="btn btn-primary">
                Edit
            </a>

            <a href="{{ route('placement-companies.index') }}"
               class="btn btn-secondary">
                Back
            </a>

        </div>
    </div>
</div>
@endsection
