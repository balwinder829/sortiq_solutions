@extends('layouts.app')

@section('content')
<div class="container">
<div class="card">
<div class="card-header">
    <h4>Edit Part-Time Job</h4>
</div>

<div class="card-body">
<form method="POST" action="{{ route('part-time-jobs.update', $job->id) }}">
@csrf
@method('PUT')

<div class="form-row">

<div class="form-group col-md-6">
    <label>Name</label>
    <input type="text"
           name="name"
           value="{{ old('name', $job->name) }}"
           class="form-control @error('name') is-invalid @enderror"
           required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="form-group col-md-6">
    <label>Salary Estimate</label>
    <input type="text"
           name="salary_estimate"
           required
           value="{{ old('salary_estimate', $job->salary_estimate) }}"
           class="form-control">
</div>

<div class="form-group col-md-6">
    <label>Job Type</label>
    <input type="text"
           name="job_type"
           required
           value="{{ old('job_type', $job->job_type) }}"
           class="form-control">
</div>

<div class="form-group col-md-6">
    <label>Shift</label>
    <input type="text"
           name="shift"
           required
           value="{{ old('shift', $job->shift) }}"
           class="form-control">
</div>

<div class="form-group col-md-6">
    <label>Location</label>
    <input type="text"
           name="location"
           required
           value="{{ old('location', $job->location) }}"
           class="form-control">
</div>

<div class="form-group col-md-6">
    <label>Mobile</label>
    <input type="text"
           name="mobile"
           value="{{ old('mobile', $job->mobile) }}"
           class="form-control"
            pattern="[0-9]{10}"
            title="Enter a valid 10-digit mobile number"
            maxlength="10"
           inputmode="numeric"
           oninput="this.value=this.value.replace(/[^0-9]/g,'')"
           placeholder="10 digit number">
</div>

<div class="form-group col-md-12">
    <label>Address</label>
    <textarea name="address"
              rows="2"
              class="form-control">{{ old('address', $job->address) }}</textarea>
</div>

<div class="form-group col-md-6">
    <label>Status</label>
    <select name="status"
            class="form-control @error('status') is-invalid @enderror">
        <option value="active" {{ old('status', $job->status)=='active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ old('status', $job->status)=='inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

</div>

<button class="btn btn-primary">Update</button>
<a href="{{ route('part-time-jobs.index') }}" class="btn btn-secondary">Back</a>

</form>
</div>
</div>
</div>
@endsection
