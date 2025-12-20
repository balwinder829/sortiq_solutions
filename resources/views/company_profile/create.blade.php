@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Create Company Profile</h2>

    <a href="{{ route('company_profile.index') }}" class="btn btn-dark mb-3">
        <i class="bx bx-arrow-back"></i> Back
    </a>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company_profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title *</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        {{-- File --}}
        <div class="mb-3">
            <label class="form-label">Upload File (PDF / Image) *</label>
            <input type="file" name="file" class="form-control" accept="pdf,image/*" required>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Visibility</label>
            <select name="is_active" class="form-control">
                <option value="1">Active</option>
                <option value="0">Disabled</option>
            </select>
        </div>

        {{-- Schedule --}}
        <div class="row">
            <div class="col-md-6">
                <label>Start At</label>
                <input type="datetime-local" name="start_at" class="form-control">
            </div>

            <div class="col-md-6">
                <label>End At</label>
                <input type="datetime-local" name="end_at" class="form-control">
            </div>
        </div>

        <button class="btn btn-primary mt-3">Save Company Profile</button>

    </form>

</div>
@endsection
