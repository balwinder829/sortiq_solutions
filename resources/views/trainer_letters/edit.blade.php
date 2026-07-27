@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Edit Trainer Letter</h4>

    <form method="POST"
          action="{{ route('trainer-letters.update', $letter) }}">

        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>Trainer</label>

                <select name="trainer_id"
                        class="form-control"
                        required>

                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ $letter->trainer_id == $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select name="letter_type"
                        class="form-control">

                    <option value="trainer_consent" selected>
                        Trainer Consent Letter
                    </option>
                </select>
            </div>

            <div class="form-group col-md-6 mt-3">
                <label>Issue Date</label>

                <input
                    type="date"
                    name="issue_date"
                    class="form-control"
                    value="{{ $letter->issue_date }}"
                    required
                >
            </div>

             {{-- Letter Content --}}
            <div class="form-group col-md-12" id="letter_content_field">
                <label>Letter Content</label>
                <textarea
                    name="letter_content"
                    id="editor"
                    class="form-control @error('letter_content') is-invalid @enderror"
                    rows="15">{!! old('letter_content', $letter->letter_content ?: ($template->content ?? '')) !!}</textarea>

                @error('letter_content')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">
            Update
        </button>

        <a href="{{ route('trainer-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection
@push('scripts')
<!-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> -->
 <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('editor');
</script>

@endpush