@extends('layouts.app')

@section('content')
<div class="container">

    <h4>Edit Letter</h4>

    {{-- ===============================
        VALIDATION ERRORS
    =============================== --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ===============================
        FORM
    =============================== --}}
    <form method="POST" action="{{ route('managements_letters.update', $managements_letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- LETTER TYPE --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <select
                    name="letter_type"
                    id="letterType"
                    class="form-control @error('letter_type') is-invalid @enderror"
                    required
                >
                    <option value="">Select Letter Type</option>

                    <option value="custom"
                        {{ old('letter_type', $managements_letter->letter_type) === 'custom' ? 'selected' : '' }}>
                        Custom Office Letter
                    </option>

                </select>

                @error('letter_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6" id="bondField">
                <label>Title</label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $managements_letter->title) }}"
                    placeholder="Title"
                    required
                >

                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            {{-- ISSUE DATE --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input
                    type="date"
                    name="issue_date"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', $managements_letter->issue_date) }}"
                    required
                >

                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- CONTENT --}}
            <div class="form-group col-md-12">
                <label>Content</label>
                <textarea
                    name="content"
                    id="content"
                    class="form-control @error('content') is-invalid @enderror" required
                >{{ old('content', $managements_letter->content) }}</textarea>

                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        {{-- BUTTONS --}}
        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('managements_letters.index') }}" class="btn btn-secondary mt-3">Back</a>

    </form>
</div>
@endsection


{{-- ===============================
    CKEDITOR
=============================== --}}
@push('scripts')

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('content');
</script>

@endpush
