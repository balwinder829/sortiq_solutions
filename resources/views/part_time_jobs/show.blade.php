@extends('layouts.app')

@section('content')
<div class="container">
<div class="card">
<div class="card-header">
    <h4>Part-Time Job Details</h4>
</div>

<div class="card-body">

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Job Name</div>
    <div class="col-md-8">{{ $job->name }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Salary Estimate</div>
    <div class="col-md-8">{{ $job->salary_estimate ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Job Type</div>
    <div class="col-md-8">{{ $job->job_type ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Shift</div>
    <div class="col-md-8">{{ $job->shift ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Location</div>
    <div class="col-md-8">{{ $job->location ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Mobile</div>
    <div class="col-md-8">{{ $job->mobile ?? '-' }}</div>
</div>

<div class="row mb-3">
    <div class="col-md-4 fw-bold">Address</div>
    <div class="col-md-8">{{ $job->address ?? '-' }}</div>
</div>

<div class="row mb-4">
    <div class="col-md-4 fw-bold">Status</div>
    <div class="col-md-8">
        <span class="badge {{ $job->status=='active' ? 'bg-success' : 'bg-secondary' }}">
            {{ ucfirst($job->status) }}
        </span>
    </div>
</div>

<a href="{{ route('part-time-jobs.edit', $job->id) }}" class="btn btn-primary">Edit</a>
<a href="{{ route('part-time-jobs.index') }}" class="btn btn-secondary">Back</a>

</div>
</div>
</div>
@endsection
