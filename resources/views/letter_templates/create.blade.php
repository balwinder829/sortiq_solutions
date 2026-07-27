@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Create Letter Template</h1>
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

    <form action="{{ route('letter-templates.store') }}" method="POST">

        @csrf

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
                            value="{{ old('title') }}"
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
                            value="{{ old('letter_type') }}"
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

                        <select
                            name="department"
                            class="form-control @error('department') is-invalid @enderror">

                            <option value="">Select Department</option>

                            <option value="Training" {{ old('department')=='Training' ? 'selected' : '' }}>
                                Training
                            </option>

                            <option value="HR" {{ old('department')=='HR' ? 'selected' : '' }}>
                                HR
                            </option>

                            <option value="Sales" {{ old('department')=='Sales' ? 'selected' : '' }}>
                                Sales
                            </option>

                            <option value="Placement" {{ old('department')=='Placement' ? 'selected' : '' }}>
                                Placement
                            </option>

                            <option value="Office" {{ old('department')=='Office' ? 'selected' : '' }}>
                                Office
                            </option>

                            <option value="Student" {{ old('department')=='Student' ? 'selected' : '' }}>
                                Student
                            </option>

                            <option value="General" {{ old('department')=='General' ? 'selected' : '' }}>
                                General
                            </option>

                        </select>

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

                            <option value="1" {{ old('status',1)==1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0" {{ old('status')==='0' ? 'selected' : '' }}>
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
                        class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>

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
                    Save Template
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