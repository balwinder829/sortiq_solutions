@extends('layouts.app')

@section('content')
<div class="container">

<div class="row mb-2">
    <div class="col-md-6">
        <h1 class="page_heading">Create College Attendance Form</h1>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.external-attendance.store') }}">
@csrf

<div class="row">

    {{-- Title --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Title</label>
        <input type="text" name="title"
               value="{{ old('title') }}"
               class="form-control @error('title') is-invalid @enderror">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- College --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">College</label>
        <select name="college_ids[]"
                class="form-control select2 @error('college_ids') is-invalid @enderror"
                multiple>
            @foreach($colleges as $col)
                <option value="{{ $col->id }}"
                    {{ collect(old('college_ids'))->contains($col->id) ? 'selected' : '' }}>
                    {{ $col->FullName }}
                </option>
            @endforeach
        </select>
        @error('college_ids')
            <div class="text-danger">{{ $message }}</div>
        @enderror
    </div>

    {{-- Status --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Status</label>
        <select name="status"
                class="form-control @error('status') is-invalid @enderror">
            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
            <option value="unpublished" {{ old('status') == 'unpublished' ? 'selected' : '' }}>Unpublished</option>
        </select>
        @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Active --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Active</label>
        <select name="is_active"
                class="form-control @error('is_active') is-invalid @enderror">
            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('is_active')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Date --}}
    <div class="col-md-6 mb-3">
        <label class="fw-bold">Date</label>
        <input type="date" name="test_date"
               value="{{ old('test_date') }}"
               class="form-control @error('test_date') is-invalid @enderror">
        @error('test_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    {{-- Description --}}
    <div class="col-md-12 mb-3">
        <label class="fw-bold">Description</label>
        <textarea name="description"
                  class="form-control @error('description') is-invalid @enderror"
                  rows="3">{{ old('description') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>

<div class="form-group col-md-6">
    <button class="btn btn-primary">Save</button>
    <a href="{{ route('admin.external-attendance.index') }}" class="btn btn-secondary ml-2">Back</a>
</div>

</form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Search college name",
            allowClear: true
        });
    });
</script>
@endpush