@extends('layouts.app')

@section('content')
<div class="container">

<h3>Create {{ ucfirst($routePrefix) }} Event</h3>

{{-- GLOBAL ERRORS --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<a href="{{ route($routePrefix.'.events.index') }}" class="btn btn-dark mb-3">Back</a>

<form method="POST"
      action="{{ route($routePrefix.'.events.store') }}"
      enctype="multipart/form-data">
    @csrf

    {{-- TITLE --}}
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control"
               value="{{ old('title') }}" required>
        @error('title')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- DESCRIPTION --}}
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
        @error('description')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- EVENT DATE --}}
    <div class="mb-3">
        <label>Event Date</label>
        <input type="date" name="event_date"
               value="{{ old('event_date') }}"
               class="form-control">
        @error('event_date')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <hr>

    {{-- DRAG & DROP UPLOAD --}}
    <div class="mb-3">
        <label>Upload Images / Videos <span class="text-danger">*</span></label>

        <div id="dropArea"
             style="border:2px dashed #6b51df;padding:30px;
                    text-align:center;cursor:pointer;border-radius:8px;">
            <p class="text-muted mb-1">Drag & Drop files here</p>
            <p class="text-muted">or click to browse</p>
        </div>

        <input type="file" name="media[]" id="mediaInput"
               multiple accept="image/*,video/*"
               style="display:none;">

        @error('media')     <small class="text-danger">{{ $message }}</small> @enderror
        @error('media.*')   <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- COVER IMAGE REQUIRED --}}
    <div class="mb-3">
        <label>Choose Cover Image <span class="text-danger">*</span></label>
        <div id="coverImageOptions" class="row"></div>

        @error('cover_image')
            <small class="text-danger d-block">{{ $message }}</small>
        @enderror
    </div>

    {{-- PREVIEW AREA --}}
    <div id="previewArea" class="row"></div>

    <button class="btn btn-primary mt-3">Save Event</button>
</form>

</div>
@endsection


@push('scripts')
<script>
let dropArea = document.getElementById('dropArea');
let mediaInput = document.getElementById('mediaInput');
let previewArea = document.getElementById('previewArea');
let coverOptions = document.getElementById('coverImageOptions');

dropArea.addEventListener('click', () => mediaInput.click());
dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.style.background="#f5f0ff"; });
dropArea.addEventListener('dragleave', () => dropArea.style.background="white");

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.background="white";
    mediaInput.files = e.dataTransfer.files;
    previewFiles(e.dataTransfer.files);
});

mediaInput.addEventListener('change', function(){
    previewFiles(this.files);
});

function previewFiles(files){
    previewArea.innerHTML = "";
    coverOptions.innerHTML = "";

    Array.from(files).forEach((file) => {

        let ext = file.name.split('.').pop().toLowerCase();
        let url = URL.createObjectURL(file);

        let preview = document.createElement('div');
        preview.classList.add('col-md-3','mb-3');

        if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
            preview.innerHTML = `
                <div class="card">
                    <img src="${url}" style="width:100%;height:150px;object-fit:cover;">
                    <div class="text-center mt-1">
                        <input type="radio" name="cover_image" value="${file.name}" required>
                        <label class="small">Set as cover</label>
                    </div>
                </div>
            `;
        } else {
            preview.innerHTML = `
                <div class="card">
                    <video src="${url}" style="width:100%;height:150px;object-fit:cover;" muted></video>
                    <div class="text-center mt-1 text-danger small">(Video cannot be cover)</div>
                </div>
            `;
        }

        previewArea.appendChild(preview);
    });
}
</script>
@endpush
