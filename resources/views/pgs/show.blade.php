@extends('layouts.app')

@section('content')
<div class="container">
<div class="card">
<div class="card-header">
    <h4>PG Details</h4>
</div>

<div class="card-body">

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Name</div>
    <div class="col-md-8">{{ $pg->name }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Rent Estimate</div>
    <div class="col-md-8">{{ $pg->rent_estimate ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">PG Type</div>
    <div class="col-md-8">{{ ucfirst($pg->pg_type) }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Food Type</div>
    <div class="col-md-8">
        {{ $pg->food_type == 'food' ? 'Food' : 'Without Food' }}
    </div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Address</div>
    <div class="col-md-8">{{ $pg->address ?? '-' }}</div>
</div>

<div class="row mb-2">
    <div class="col-md-4 fw-bold">Description</div>
    <div class="col-md-8">{{ $pg->description ?? '-' }}</div>
</div>

<div class="row mb-4">
    <div class="col-md-4 fw-bold">Status</div>
    <div class="col-md-8">
        <span class="badge {{ $pg->status=='active' ? 'bg-success' : 'bg-secondary' }}">
            {{ ucfirst($pg->status) }}
        </span>
    </div>
</div>

<a href="{{ route('pgs.edit', $pg->id) }}" class="btn btn-primary">Edit</a>
<a href="{{ route('pgs.index') }}" class="btn btn-secondary">Back</a>

</div>
</div>
</div>
@endsection
