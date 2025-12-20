@extends('layouts.app')

@section('content')
<div class="container">

<a href="{{ route('events.index') }}" class="btn btn-dark mb-3">
    <i class="fa fa-arrow-left"></i> Back
</a>

<h3>{{ $event->title }}</h3>

@if($event->cover_image)
    <img src="{{ asset($event->cover_image) }}"
         style="width:100%;max-height:300px;object-fit:cover;"
         class="mb-3 rounded open-media"
         data-type="image"
         data-src="{{ asset($event->cover_image) }}">
@endif

<p><strong>Date:</strong> {{ $event->event_date }}</p>
<p>{{ $event->description }}</p>

<hr>

<h4>Gallery</h4>

<div class="row">

{{-- IMAGES --}}
@foreach($event->images as $image)
<div class="col-md-3 mb-3 text-center">
    <img src="{{ asset($image->image_path) }}"
         class="open-media"
         data-type="image"
         data-src="{{ asset($image->image_path) }}"
         style="width:100%;height:150px;object-fit:cover;border-radius:5px;cursor:pointer;">

    <div class="mt-2 d-flex justify-content-center gap-2">

        {{-- SET COVER --}}
        @if($event->cover_image != $image->image_path)
        <form action="{{ route('event.set.cover', $image->id) }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-success">Set Cover</button>
        </form>
        @else
        <span class="badge bg-primary">Cover</span>
        @endif

        {{-- DELETE IMAGE --}}
        <form action="{{ route('event-image.delete', $image->id) }}" method="POST">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Delete</button>
        </form>
    </div>
</div>
@endforeach

{{-- VIDEOS --}}
@foreach($event->videos as $video)
<div class="col-md-3 mb-3 text-center">

    <video
        src="{{ asset($video->video_path) }}"
        class="open-media"
        data-type="video"
        data-src="{{ asset($video->video_path) }}"
        muted
        style="width:100%;height:150px;object-fit:cover;border-radius:5px;cursor:pointer;">
    </video>

    <form action="{{ route('event-video.delete', $video->id) }}" method="POST" class="mt-2">
        @csrf @method('DELETE')
        <button class="btn btn-sm btn-danger">Delete Video</button>
    </form>
</div>
@endforeach

</div>

</div>

{{-- ==================== MEDIA POPUP ==================== --}}
<div id="mediaPopup"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;
            background:rgba(0,0,0,0.85);z-index:9999;justify-content:center;
            align-items:center;padding:20px;">
    <span id="closePopup"
          style="position:absolute;top:20px;right:30px;font-size:34px;color:white;cursor:pointer;">
        &times;
    </span>

    <img id="popupImage" style="display:none;max-width:95%;max-height:90%;border-radius:10px;">
    <video id="popupVideo" style="display:none;max-width:95%;max-height:90%;border-radius:10px;" controls></video>
</div>

@endsection

@push('scripts')
<script>
const popup = document.getElementById("mediaPopup");
const popupImg = document.getElementById("popupImage");
const popupVideo = document.getElementById("popupVideo");
const closePopup = document.getElementById("closePopup");

// Handle click on any image/video
document.querySelectorAll(".open-media").forEach(item => {
    item.addEventListener("click", function () {

        const type = this.dataset.type;
        const src = this.dataset.src;

        popup.style.display = "flex";

        if(type === "image"){
            popupImg.src = src;
            popupImg.style.display = "block";
            popupVideo.style.display = "none";
        } else {
            popupVideo.src = src;
            popupVideo.style.display = "block";
            popupImg.style.display = "none";
        }
    });
});

// Close popup
closePopup.onclick = () => { 
    popup.style.display = "none";
    popupVideo.pause(); 
};

// Close when clicking outside
popup.onclick = (e) => {
    if(e.target === popup){
        popup.style.display = "none";
        popupVideo.pause();
    }
};
</script>
@endpush
