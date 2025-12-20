@extends('layouts.app')

@section('content')
<div class="container">

<a href="{{ route('placements.index') }}" class="btn btn-dark mb-3">
    <i class="fa fa-arrow-left"></i> Back
</a>

<h3>{{ $placement->name }}</h3>

{{-- COVER IMAGE --}}
@if($placement->cover_image)
    <img src="{{ asset($placement->cover_image) }}"
         style="width:100%;max-height:300px;object-fit:cover;"
         class="mb-3 rounded">
@endif

{{-- DESCRIPTION --}}
<p class="mt-2">{{ $placement->description }}</p>

<hr>

{{-- IMAGES --}}
@if($placement->images->count())
<h4>Photos</h4>
<div class="row">
    @foreach($placement->images as $img)
        <div class="col-md-3 mb-3">
            <img src="{{ asset($img->path) }}" 
                 class="img-fluid rounded"
                 style="height:180px;object-fit:cover;">
        </div>
    @endforeach
</div>
@endif

{{-- VIDEOS --}}
@if($placement->videos->count())
<h4>Videos</h4>
<div class="row">
    @foreach($placement->videos as $video)
        <div class="col-md-4 mb-3">
            <video src="{{ asset($video->path) }}" 
                   controls
                   class="w-100 rounded"
                   style="height:200px;object-fit:cover;">
            </video>
        </div>
    @endforeach
</div>
@endif

</div>
@endsection
