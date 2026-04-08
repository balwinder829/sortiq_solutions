@extends('layouts.app')

@section('content')
<div class="container">

     <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Edit Company PPT</h1>
        </div>
        
    </div>
    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('student_ppt.update', ['student_ppt' => $companyPpt->id]) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Title *</label>
            <input type="text"
                   name="title"
                   class="form-control"
                   value="{{ old('title', $companyPpt->title) }}"
                   required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="description"
                      class="form-control">{{ old('description', $companyPpt->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Current File</label>
            <div class="border rounded p-3 bg-light">
                <span class="text-muted">PPT file uploaded</span>
            </div>
        </div>

        <div class="mb-3">
            <label>Replace PPT (optional)</label>
            <input type="file"
                   name="file"
                   class="form-control"
                   accept=".ppt,.pptx">
        </div>

        <div class="mb-3">
            <label>Visibility</label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $companyPpt->is_active ? 'selected' : '' }}>
                    Active
                </option>
                <option value="0" {{ !$companyPpt->is_active ? 'selected' : '' }}>
                    Disabled
                </option>
            </select>
        </div>

        <div class="row">
            <div class="col-md-6">
                <label>Start At</label>
                <input type="datetime-local"
                       name="start_at"
                       class="form-control"
                       value="{{ $companyPpt->start_at ? $companyPpt->start_at->format('Y-m-d\TH:i') : '' }}">
            </div>

            <div class="col-md-6">
                <label>End At</label>
                <input type="datetime-local"
                       name="end_at"
                       class="form-control"
                       value="{{ $companyPpt->end_at ? $companyPpt->end_at->format('Y-m-d\TH:i') : '' }}">
            </div>
        </div>

        <button class="btn btn-primary mt-3">
            Update PPT
        </button>
         
         <a href="{{ route('student_ppt.index') }}"
               class="btn btn-primary mt-3 ms-2"
               style="background-color:#6b51df;color:#fff;">
                Back
            </a>

    </form>

</div>
@endsection
