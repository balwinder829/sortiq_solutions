@extends('layouts.app')

@section('content')
<div class="container">

<h3>Create Placement</h3>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<a href="{{ route('placements.index') }}" class="btn mb-3" 
   style="background-color:#343957;color:white;">Back</a>

<form method="POST"
      action="{{ route('placements.store') }}"
      enctype="multipart/form-data">

    @csrf

    {{-- NAME --}}
    <div class="mb-3">
        <label>Placement Name</label>
        <input type="text" 
               name="name"
               class="form-control"
               value="{{ old('name') }}"
               required>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- DESCRIPTION --}}
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description"
                  class="form-control">{{ old('description') }}</textarea>
    </div>

    <hr>

    {{-- DRAG DROP UPLOAD --}}
    <div class="mb-3">
        <label>Upload Images / Videos <span class="text-danger">*</span></label>

        <div id="dropArea"
             style="border:2px dashed #6b51df;padding:30px;text-align:center;
                    cursor:pointer;border-radius:8px;">
            <p class="text-muted mb-1">Drag & Drop files here</p>
            <p class="text-muted">or click to browse</p>
        </div>

        <input type="file"
               name="media[]"
               id="mediaInput"
               multiple
               accept="image/*,video/*"
               style="display:none;">

        @error('media') <small class="text-danger">{{ $message }}</small> @enderror
        @error('media.*') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    {{-- COVER IMAGE --}}
    <div class="mb-3">
        <label>Choose Cover Image (required)</label>
        <div id="coverImageOptions" class="row"></div>

        @error('cover_image')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- PREVIEW --}}
    <div id="previewArea" class="row"></div>

    <button class="btn mt-3" style="background-color:#343957;color:white;">
        Save Placement
    </button>

</form>

</div>
@endsection

@push('scripts')
<script>
let dropArea       = document.getElementById('dropArea');
let mediaInput     = document.getElementById('mediaInput');
let previewArea    = document.getElementById('previewArea');
let coverOptions   = document.getElementById('coverImageOptions');

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

    Array.from(files).forEach(file => {
        let ext = file.name.split(".").pop().toLowerCase();
        let url = URL.createObjectURL(file);

        let div = document.createElement("div");
        div.classList.add("col-md-3", "mb-3");

        if(["jpg","jpeg","png","webp","gif"].includes(ext)){
            div.innerHTML = `
                <div class="card">
                    <img src="${url}" style="width:100%;height:150px;object-fit:cover;">
                </div>
                <div class="text-center mt-1">
                    <input type="radio" name="cover_image" value="${file.name}" required>
                    <label class="small">Set as cover</label>
                </div>
            `;
        } else {
            div.innerHTML = `
                <div class="card">
                    <video src="${url}" style="width:100%;height:150px;object-fit:cover;" muted></video>
                </div>
                <p class="text-danger small text-center">(Cannot be cover)</p>
            `;
        }

        previewArea.appendChild(div);
    });
}
</script>
@endpush
