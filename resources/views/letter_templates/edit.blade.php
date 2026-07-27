@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Edit Letter Template</h1>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('letter-templates.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('letter-templates.update', $template) }}" method="POST">

        @csrf
        @method('PUT')

        <div class="card">

            <div class="card-header">
                <strong>Letter Template Details</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Template Title <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $template->title) }}"
                            placeholder="Example : Trainer Consent Letter">

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Letter Type <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="letter_type"
                            class="form-control @error('letter_type') is-invalid @enderror"
                            value="{{ old('letter_type', $template->letter_type) }}"
                            placeholder="Example : trainer_consent">

                        <small class="text-muted">
                            Use lowercase with underscore (_).
                            Example :
                            trainer_consent,
                            employee_responsibility,
                            sales_staff_consent
                        </small>

                        @error('letter_type')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Department <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="department"
                            class="form-control @error('department') is-invalid @enderror"
                            value="{{ old('department', $template->department ?? '') }}"
                            placeholder="Example : HR">

                        <small class="text-muted">
                            Example: HR, Training, Sales, Placement, Office
                        </small>

                        @error('department')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            class="form-control @error('status') is-invalid @enderror">

                            <option value="1" {{ old('status', $template->status)==1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ old('status', $template->status)==0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

                <div class="mb-3">

                    <label class="form-label">
                        Letter Template Content
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        id="editor"
                        name="content"
                        rows="18"
                        class="form-control @error('content') is-invalid @enderror">{{ old('content', $template->content) }}</textarea>

                    @error('content')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

            <div class="card-footer text-end">

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Template
                </button>

                <a href="{{ route('letter-templates.index') }}" class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </div>

    </form>

</div>

@endsection

@push('scripts')
<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('editor');
</script>
@endpush