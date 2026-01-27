@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Scanner</h4>

    <form method="POST"
          action="{{ route('scanners.update', $scanner) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            

            {{-- Name --}}
            <div class="form-group col-md-6">
                <label>Scanner Name</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $scanner->name) }}"
                       class="form-control @error('name') is-invalid @enderror"
                       required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Image Upload --}}
            <div class="form-group col-md-6">
                <label>Scanner Image</label>

                <div id="dropZone"
                     class="border rounded p-3 text-center
                            @error('image') border-danger @enderror"
                     style="border-style:dashed; cursor:pointer;">

                    <input type="file"
                           name="image"
                           id="imageInput"
                           accept="image/*"
                           hidden>

                    <p class="text-muted mb-2">
                        Drag & drop image here or click to replace
                    </p>

                    <img id="imagePreview"
                         src="{{ asset($scanner->image_path) }}"
                         class="img-fluid rounded"
                         style="max-height:180px;">
                </div>

                @error('image')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Source --}}
            <div class="form-group col-md-6">
                <label>Source</label>
                <select name="source"
                        class="form-control @error('source') is-invalid @enderror">
                    <option value="">Manual</option>
                    <option value="facebook" {{ old('source',$scanner->source)=='facebook'?'selected':'' }}>Facebook</option>
                    <option value="instagram" {{ old('source',$scanner->source)=='instagram'?'selected':'' }}>Instagram</option>
                    <option value="youtube" {{ old('source',$scanner->source)=='youtube'?'selected':'' }}>YouTube</option>
                    <option value="website" {{ old('source',$scanner->source)=='website'?'selected':'' }}>Website</option>
                </select>
                @error('source')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Source URL --}}
            <div class="form-group col-md-12">
                <label>Source URL</label>
                <input type="url"
                       name="source_url"
                       value="{{ old('source_url', $scanner->source_url) }}"
                       class="form-control @error('source_url') is-invalid @enderror">
                @error('source_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group col-md-12">
                <label>Description</label>
                <textarea name="description"
                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $scanner->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Active --}}
            <div class="form-group col-md-6">
                <label>
                    <input type="checkbox"
                           name="is_active"
                           {{ old('is_active', $scanner->is_active) ? 'checked' : '' }}>
                    Active
                </label>
            </div>

            {{-- Public --}}
            <div class="form-group col-md-6">
                <label>
                    <input type="checkbox"
                           name="is_public"
                           {{ old('is_public', $scanner->is_public) ? 'checked' : '' }}>
                    Public / Shareable
                </label>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('scanners.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const dropZone = document.getElementById('dropZone');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');

    if (!dropZone) return;

    dropZone.addEventListener('click', () => imageInput.click());

    dropZone.addEventListener('dragover', e => {
        e.preventDefault();
        dropZone.classList.add('bg-light');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('bg-light');
    });

    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('bg-light');
        if (e.dataTransfer.files.length) {
            imageInput.files = e.dataTransfer.files;
            previewImage(e.dataTransfer.files[0]);
        }
    });

    imageInput.addEventListener('change', () => {
        if (imageInput.files.length) {
            previewImage(imageInput.files[0]);
        }
    });

    function previewImage(file) {
        if (!file.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            imagePreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
