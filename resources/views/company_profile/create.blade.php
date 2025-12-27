@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Create Company Profile</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                  <a href="{{ route('company_profile.index') }}" class="btn mb-3" style="background-color:#6b51df;color:#fff;">
                        Back
                    </a>
            </div>
        </div>
    </div>


     

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
