@extends('layouts.app')

@section('content')
<div class="container">
<div class="card">
<div class="card-header">
    <h4>Edit PG</h4>
</div>

<div class="card-body">
<form method="POST" action="{{ route('pgs.update', $pg->id) }}">
@csrf
@method('PUT')

<div class="form-row">

<div class="form-group col-md-6">
    <label>Name</label>
    <input type="text"
           name="name"
           value="{{ old('name', $pg->name) }}"
           class="form-control @error('name') is-invalid @enderror"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-md-6">
    <label>Rent Estimate</label>
    <input type="text"
           name="rent_estimate"
           value="{{ old('rent_estimate', $pg->rent_estimate) }}"
           class="form-control">
</div>

<div class="form-group col-md-6">
    <label>PG Type</label>
    <select name="pg_type"
            class="form-control @error('pg_type') is-invalid @enderror">
        <option value="boys" {{ old('pg_type', $pg->pg_type)=='boys' ? 'selected' : '' }}>Boys</option>
        <option value="girls" {{ old('pg_type', $pg->pg_type)=='girls' ? 'selected' : '' }}>Girls</option>
    </select>
    @error('pg_type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-md-6">
    <label>Food Type</label>
    <select name="food_type"
            class="form-control @error('food_type') is-invalid @enderror">
        <option value="food" {{ old('food_type', $pg->food_type)=='food' ? 'selected' : '' }}>Food</option>
        <option value="without_food" {{ old('food_type', $pg->food_type)=='without_food' ? 'selected' : '' }}>
            Without Food
        </option>
    </select>
    @error('food_type')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-md-12">
    <label>Address</label>
    <textarea name="address"
              rows="2"
              class="form-control">{{ old('address', $pg->address) }}</textarea>
</div>

<div class="form-group col-md-12">
    <label>Description</label>
    <textarea name="description"
              rows="2"
              class="form-control">{{ old('description', $pg->description) }}</textarea>
</div>

<div class="form-group col-md-6">
    <label>Status</label>
    <select name="status"
            class="form-control @error('status') is-invalid @enderror">
        <option value="active" {{ old('status', $pg->status)=='active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $pg->status)=='inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('pgs.index') }}" class="btn btn-secondary">Back</a>

</form>
</div>
</div>
</div>
@endsection
