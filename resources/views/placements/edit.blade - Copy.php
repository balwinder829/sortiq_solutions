@extends('layouts.app')

@section('content')
<div class="container">

<h3>Edit Placement</h3>

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
      action="{{ route('placements.update', $placement->id) }}"
      enctype="multipart/form-data">

    @csrf
    @method('PUT')

    {{-- NAME --}}
    <div class="mb-3">
        <label>Placement Name</label>
        <input type="text" name="name" class="form-control"
               value="{{ old('name', $placement->name) }}" required>
    </div>

    {{-- DESCRIPTION --}}
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control">
            {{ old('description', $placement->description) }}
        </textarea>
    </div>

    <hr>

    {{-- EXISTING MEDIA --}}
    <h5>Existing Media</h5>
    <div class="row mb-4">

        {{-- Images --}}
        @foreach($placement->images as $img)
        <div class="col-md-3 mb-3">
            <div class="card"
                style="border:{{ $placement->cover_image == $img->path ? '3px solid #6b51df' : '1px solid #ddd' }};">

                <img src="{{ asset($img->path) }}" 
                     style="width:100%;height:150px;object-fit:cover;">

                <div class="text-center mt-2">
                    @if($placement->cover_image !== $img->path)
                        <button type="button" class="btn btn-sm btn-success"
                                onclick="setCover({{ $img->id }})">Set Cover</button>
                    @else
                        <span class="badge bg-primary">Cover</span>
                    @endif

                    <button type="button" class="btn btn-sm btn-danger mt-1"
                            onclick="deleteImage({{ $img->id }})">Delete</button>
                </div>

            </div>
        </div>
        @endforeach

        {{-- Videos --}}
        @foreach($placement->videos as $vid)
        <div class="col-md-3 mb-3">
            <div class="card">
                <video src="{{ asset($vid->path) }}"
                        style="width:100%;height:150px;object-fit:cover;" controls></video>

                <div class="text-center mt-2">
                    <button type="button" class="btn btn-sm btn-danger"
                            onclick="deleteVideo({{ $vid->id }})">Delete</button>
                </div>
            </div>
        </div>
        @endforeach

    </div>

    {{-- DRAG DROP UPLOAD --}}
    <div class="mb-3">
        <label>Upload More Media</label>

        <div id="dropArea"
             style="border:2px dashed #6b51df;padding:30px;text-align:center;
                    cursor:pointer;border-radius:8px;">
            <p class="text-muted mb-1">Drag & Drop files here</p>
            <p class="text-muted">or click to browse</p>
        </div>

        <input type="file" name="media[]" id="mediaInput"
               multiple accept="image/*,video/*"
               style="display:none;">
    </div>

    {{-- PREVIEW --}}
    <div id="previewArea" class="row"></div>

    <button class="btn mt-3" style="background-color:#343957;color:white;">
        Update Placement
    </button>

</form>

</div>
@endsection


@push('scripts')
<script>
/* Drag + Preview */
let dropArea = document.getElementById('dropArea');
let mediaInput = document.getElementById('mediaInput');
let previewArea = document.getElementById('previewArea');

dropArea.addEventListener('click', () => mediaInput.click());
dropArea.addEventListener('dragover', e => { e.preventDefault(); dropArea.style.background="#f5f0ff"; });
dropArea.addEventListener('dragleave', () => dropArea.style.background="white");

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.background="white";
    mediaInput.files = e.dataTransfer.files;
    previewFiles(e.dataTransfer.files);
});

mediaInput.addEventListener('change', () => previewFiles(mediaInput.files));

function previewFiles(files){
    previewArea.innerHTML = "";

    Array.from(files).forEach(file => {
        let ext = file.name.split('.').pop().toLowerCase();
        let url = URL.createObjectURL(file);

        let div = document.createElement('div');
        div.classList.add('col-md-3','mb-3');

        if(["jpg","jpeg","png","gif","webp"].includes(ext)){
            div.innerHTML = `
                <div class="card">
                    <img src="${url}" style="width:100%;height:150px;object-fit:cover;">
                </div>
            `;
        } else {
            div.innerHTML = `
                <div class="card">
                    <video src="${url}" style="width:100%;height:150px;object-fit:cover;" muted></video>
                </div>
            `;
        }

        previewArea.appendChild(div);
    });
}

/* AJAX Delete Image */
function deleteImage(id){
    if(!confirm("Delete this image?")) return;

    fetch("{{ route('placements.media.image.delete', ':id') }}".replace(':id', id), {
        method: "POST",
        headers:{
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-HTTP-Method-Override": "DELETE"
        }
    }).then(()=>location.reload());
}

/* AJAX Delete Video */
function deleteVideo(id){
    if(!confirm("Delete this video?")) return;

    fetch("{{ route('placements.media.video.delete', ':id') }}".replace(':id', id), {
        method: "POST",
        headers:{
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "X-HTTP-Method-Override": "DELETE"
        }
    }).then(()=>location.reload());
}

/* AJAX Set Cover */
function setCover(id){
    fetch("{{ route('placements.media.setCover', ':id') }}".replace(':id', id), {
        method: "POST",
        headers:{
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        }
    }).then(()=>location.reload());
}
</script>
@endpush
