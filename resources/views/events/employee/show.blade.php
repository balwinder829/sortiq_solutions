@extends('layouts.app')

@section('content')
<div class="container">

<a href="{{ route('employee.events.index') }}" class="btn btn-dark mb-3">
    <i class="fa fa-arrow-left"></i> Back
</a>

<h3>{{ $event->title }}</h3>

@if($event->cover_image)
<img src="{{ asset($event->cover_image) }}"
     style="width:100%;max-height:300px;object-fit:cover;"
     class="mb-3 rounded">
@endif

<p>{{ $event->description }}</p>

<h4>Photos</h4>
<div class="row">
@foreach($event->images as $img)
    <div class="col-md-3 mb-3">
        <img src="{{ asset($img->image_path) }}" class="img-fluid rounded">
    </div>
@endforeach
</div>

<h4>Videos</h4>
<div class="row">
@foreach($event->videos as $video)
    <div class="col-md-4 mb-3">
        <video src="{{ asset($video->video_path) }}" controls class="w-100"></video>
    </div>
@endforeach
</div>

</div>
@endsection
