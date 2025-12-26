@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-4">
            <a href="{{ route('tutorials.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>
        
        <div class="col-12">
            <h2>{{ $tutorial->title }}</h2>
            <p class="text-muted">Level: {{ ucfirst($tutorial->level) ?? 'N/A' }}</p>
            <hr>
        </div>

        {{-- Video Embed Section --}}
        <div class="col-lg-8">
            <div class="embed-responsive embed-responsive-16by9" style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; max-width: 100%;">
                <iframe class="embed-responsive-item" 
                        src="{{ $tutorial->embed_url }}" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen 
                        style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
                </iframe>
            </div>
            
            <h5 class="mt-4">Description</h5>
            <p>{{ $tutorial->description ?? 'No description provided.' }}</p>
        </div>

        {{-- Sidebar/Details Section --}}
        <div class="col-lg-4">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title">Tutorial Details</h5>
                    <p><strong>Tech:</strong> {{ $tutorial->technology }}</p>
                    <p><strong>Date Added:</strong> {{ $tutorial->created_at->format('M d, Y') }}</p>
                    <p><strong>Last Updated:</strong> {{ $tutorial->updated_at->format('M d, Y') }}</p>
                    <p><strong>Description:</strong> {{ $tutorial->description }}</p>
                    
                    <!-- <a href="{{ route('tutorials.edit', $tutorial) }}" class="btn btn-warning btn-sm mt-2">Edit Tutorial</a> -->
                    <a href="https://www.youtube.com/watch?v={{ $tutorial->youtube_id }}" target="_blank" class="btn btn-danger btn-sm mt-2">Open on YouTube</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection