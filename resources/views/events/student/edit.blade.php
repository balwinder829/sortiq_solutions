@extends('layouts.app')

@section('content')
<div class="container">

<h3>Edit {{ ucfirst($routePrefix) }} Event</h3>

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


<form method="POST"
      action="{{ route($routePrefix.'.events.update', $event->id) }}"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

    {{-- TITLE --}}
    <div class="mb-3">
        <label>Title</label>
        <input type="text" name="title" class="form-control"
               value="{{ old('title', $event->title) }}" required>

        @error('title')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- DESCRIPTION --}}
    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ old('description', $event->description) }}</textarea>

        @error('description')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- EVENT DATE --}}
    <div class="mb-3">
        <label>Event Date</label>
        <input type="date" name="event_date"
               value="{{ old('event_date', $event->event_date) }}"
               class="form-control">

        @error('event_date')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>


    {{-- EXISTING MEDIA --}}
    <h5>Existing Media</h5>
    <div class="row mb-4">

        {{-- Existing Images --}}
        @foreach($event->images as $image)
        <div class="col-md-3 mb-3">
            <div class="card" 
                 style="border: {{ $event->cover_image == $image->image_path ? '3px solid #6b51df' : '1px solid #ddd' }};">

                <img src="{{ asset($image->image_path) }}"
                     style="width:100%;height:150px;object-fit:cover;">

                <div class="text-center mt-1">

                    {{-- SET AS COVER --}}
                    @if($event->cover_image != $image->image_path)
                    <button type="button"
                            class="btn btn-sm btn-success mb-2"
                            onclick="setCover({{ $image->id }})">
                        Set as Cover
                    </button>
                    @else
                    <span class="badge bg-primary mb-2">Current Cover</span>
                    @endif

                    {{-- DELETE IMAGE --}}
                    <button type="button"
                            class="btn btn-sm btn-danger"
                            onclick="deleteImage({{ $image->id }})">
                        Delete
                    </button>

                </div>
            </div>
        </div>
        @endforeach


        {{-- Existing Videos --}}
        @foreach($event->videos as $video)
        <div class="col-md-3 mb-3">
            <div class="card">
                <video src="{{ asset($video->video_path) }}"
                       style="width:100%;height:150px;object-fit:cover;" muted></video>

                <div class="text-center mt-2">
                    <button type="button"
                            class="btn btn-sm btn-danger"
                            onclick="deleteVideo({{ $video->id }})">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>


    {{-- DRAG & DROP UPLOAD --}}
    <div class="mb-3">
        <label>Upload More Images / Videos</label>

        <div id="dropArea"
             style="border:2px dashed #6b51df;padding:30px;
                    text-align:center;cursor:pointer;border-radius:8px;">
            <p class="text-muted mb-1">Drag & Drop files here</p>
            <p class="text-muted">or click to browse</p>
        </div>

        <input type="file" name="media[]" id="mediaInput"
               multiple accept="image/*,video/*" style="display:none;">

        @error('media')
            <small class="text-danger">{{ $message }}</small>
        @enderror
        @error('media.*')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    {{-- PREVIEW AREA --}}
    <div id="previewArea" class="row"></div>


    <button type="submit" class="btn mt-3" style="background-color: #343957; color: white;">Update</button>
    <a href="{{ route($routePrefix.'.events.show', $event->id) }}" 
       class="btn btn-secondary mt-3">Back</a>

</form>

</div>
@endsection



@push('scripts')
<script>
/* =================== DRAG & DROP =================== */
let dropArea = document.getElementById('dropArea');
let mediaInput = document.getElementById('mediaInput');
let previewArea = document.getElementById('previewArea');

dropArea.addEventListener('click', (e) => {
    e.preventDefault();
    mediaInput.click();
});

dropArea.addEventListener('dragover', e => {
    e.preventDefault();
    dropArea.style.background="#f5f0ff";
});

dropArea.addEventListener('dragleave', () => {
    dropArea.style.background="white";
});

dropArea.addEventListener('drop', (e) => {
    e.preventDefault();
    dropArea.style.background="white";
    mediaInput.files = e.dataTransfer.files;
    previewFiles(e.dataTransfer.files);
});

mediaInput.addEventListener('change', () => {
    previewFiles(mediaInput.files);
});

function previewFiles(files){
    previewArea.innerHTML = "";

    Array.from(files).forEach(file => {
        let ext = file.name.split('.').pop().toLowerCase();
        let url = URL.createObjectURL(file);

        let preview = document.createElement('div');
        preview.classList.add('col-md-3','mb-3');

        if(['jpg','jpeg','png','gif','webp'].includes(ext)) {
            preview.innerHTML = `
                <div class="card">
                    <img src="${url}" style="width:100%;height:150px;object-fit:cover;">
                </div>
            `;
        } else {
            preview.innerHTML = `
                <div class="card">
                    <video src="${url}" style="width:100%;height:150px;object-fit:cover;" muted></video>
                </div>
            `;
        }

        previewArea.appendChild(preview);
    });
}


/* =================== AJAX – DELETE EXISTING IMAGE =================== */
function deleteImage(id){
    if(!confirm("Delete this image?")) return;

    let url = "{{ route($routePrefix.'.event-image.delete', ['image' => '__ID__']) }}"
                .replace('__ID__', id);

    fetch(url, {
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":"{{ csrf_token() }}",
            "X-HTTP-Method-Override":"DELETE"
        }
    }).then(()=>location.reload());
}


/* =================== AJAX – DELETE EXISTING VIDEO =================== */
function deleteVideo(id){
    if(!confirm("Delete this video?")) return;

    let url = "{{ route($routePrefix.'.event-video.delete', ['video' => '__ID__']) }}"
                .replace('__ID__', id);

    fetch(url, {
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":"{{ csrf_token() }}",
            "X-HTTP-Method-Override":"DELETE"
        }
    }).then(()=>location.reload());
}


/* =================== AJAX – SET COVER IMAGE =================== */
function setCover(id){
    let url = "{{ route($routePrefix.'.event.set.cover', ['eventImage' => '__ID__']) }}"
                .replace('__ID__', id);

    fetch(url, {
        method:"POST",
        headers:{
            "X-CSRF-TOKEN":"{{ csrf_token() }}"
        }
    }).then(()=>location.reload());
}
</script>
@endpush
