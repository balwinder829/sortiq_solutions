@extends('layouts.app')

@section('content')
<div class="container">

{{-- BACK BUTTON --}}
<a href="{{ route('placements.index') }}" class="btn btn-dark mb-3">
    <i class="fa fa-arrow-left"></i> Back
</a>

{{-- HEADER CARD --}}
<div class="card mb-4 shadow-sm">
    <div class="card-body">

        <h3 class="mb-1">{{ $placement->student_name }}</h3>

        <p class="text-muted mb-2">
            {{ $placement->companyRelation->name }}
        </p>

        <span class="badge bg-primary me-1">
            {{ $placement->course->course_name }}
        </span>

        <span class="badge bg-secondary">
            {{ $placement->session->session_name ?? '' }}
        </span>

    </div>
</div>

{{-- COVER IMAGE --}}
@if($placement->cover_image)
<div class="card mb-4 shadow-sm">
    <img src="{{ asset($placement->cover_image) }}"
         class="img-fluid rounded"
         style="max-height:350px;object-fit:cover;">
</div>
@endif

{{-- DETAILS --}}
<div class="row">

    {{-- LEFT COLUMN --}}
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold">
                Placement Details
            </div>
            <div class="card-body">

                <p class="mb-2">
                    <strong>College:</strong><br>
                    {{ $placement->college_full_name }}
                </p>

                <p class="mb-2">
                    <strong>Location:</strong><br>
                    {{ $placement->location ?? '' }}
                </p>

                <p class="mb-2">
                    <strong>Placement Date:</strong><br>
                    {{ \Carbon\Carbon::parse($placement->placement_date)->format('d M Y') }}
                </p>

                <p class="mb-0">
                    <strong>Phone:</strong><br>
                    {{ $placement->phone_no }}
                </p>

            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <div class="card-header fw-bold">
                Description
            </div>
            <div class="card-body">
                @if($placement->description)
                    <p class="mb-0">{{ $placement->description }}</p>
                @else
                    <p class="text-muted mb-0">No description provided.</p>
                @endif
            </div>
        </div>
    </div>

</div>

{{-- PHOTOS --}}
@if($placement->images->count())
<div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">
        Photos
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($placement->images as $img)
                <div class="col-md-3 mb-3">
                    <img src="{{ asset($img->path) }}"
                         class="img-fluid rounded"
                         style="height:180px;object-fit:cover;">
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- VIDEOS --}}
@if($placement->videos->count())
<div class="card mb-4 shadow-sm">
    <div class="card-header fw-bold">
        Videos
    </div>
    <div class="card-body">
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
    </div>
</div>
@endif

</div>
@endsection
