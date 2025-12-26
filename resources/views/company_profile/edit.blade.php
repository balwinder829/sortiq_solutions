@extends('layouts.app')

@section('content')
<div class="container">

    <h2>Edit Company Profile</h2>

    <a href="{{ route('company_profile.index') }}" class="btn btn-dark mb-3">
        <i class="bx bx-arrow-back"></i> Back
    </a>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('company_profile.update', $companyProfile->id) }}" 
          method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-3">
            <label>Title *</label>
            <input type="text" name="title" class="form-control"
                   value="{{ old('title', $companyProfile->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control">
                {{ old('description', $companyProfile->description) }}
            </textarea>
        </div>

        {{-- Existing preview --}}
        <div class="mb-3">
            <label>Current File</label>
            <div class="border rounded p-3 bg-light">

                @if($companyProfile->file_type === 'image')
                    <img src="{{ route('company_profile.admin.view', $companyProfile->id) }}"
                         style="height:180px;object-fit:cover;">
                @else
                    <iframe src="{{ route('company_profile.admin.view', $companyProfile->id) }}"
                        style="width:100%;height:180px;border:1px solid #ddd;"
                        class="rounded">
                    </iframe>
                @endif

            </div>
        </div>

        {{-- Replace --}}
        <div class="mb-3">
            <label>Replace File (optional)</label>
            <input type="file" name="file" class="form-control" accept="pdf,image/*">
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label>Visibility</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $companyProfile->is_active ? 'selected' : '' }}>Active</option>
                <option value="0" {{ !$companyProfile->is_active ? 'selected' : '' }}>Disabled</option>
            </select>
        </div>

        {{-- Schedule --}}
        <div class="row">
            <div class="col-md-6">
                <label>Start At</label>
                <input type="datetime-local" name="start_at"
                       class="form-control"
                       value="{{ $companyProfile->start_at ? $companyProfile->start_at->format('Y-m-d\TH:i') : '' }}">
            </div>

            <div class="col-md-6">
                <label>End At</label>
                <input type="datetime-local" name="end_at"
                       class="form-control"
                       value="{{ $companyProfile->end_at ? $companyProfile->end_at->format('Y-m-d\TH:i') : '' }}">
            </div>
        </div>

        <button class="btn btn-primary mt-3">Update Company Profile</button>

    </form>

</div>
@endsection
