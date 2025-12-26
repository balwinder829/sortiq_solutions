@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add New Tutorial</h4>

    <form method="POST" action="{{ route('tutorials.store') }}">
        @csrf

        <div class="row">

            {{-- Title --}}
            <div class="form-group col-md-6 mb-3">
                <label for="title">Tutorial Title *</label>
                <input type="text"
                       name="title"
                       id="title"
                       class="form-control @error('title') is-invalid @enderror"
                       value="{{ old('title') }}"
                       required
                       placeholder="e.g., Laravel Eloquent Relationships">
                @error('title')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- YouTube URL (Input for user) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="youtube_url">YouTube Video URL *</label>
                <input type="url"
                       name="youtube_url"
                       id="youtube_url"
                       class="form-control @error('youtube_url') is-invalid @enderror"
                       value="{{ old('youtube_url') }}"
                       required
                       placeholder="Paste the full URL here (e.g., https://youtu.be/dQw4w9WgXcQ)">
                @error('youtube_url')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Technology Field --}}
            <div class="form-group col-md-6 mb-3">
                <label for="technology">Technology/Topic *</label>
                <input type="text"
                       name="technology"
                       id="technology"
                       class="form-control @error('technology') is-invalid @enderror"
                       value="{{ old('technology') }}"
                       required
                       placeholder="e.g., Laravel Eloquent, React Hooks, MySQL">
                @error('technology')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Level Field (Select) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="level">Difficulty Level</label>
                <select name="level"
                        id="level"
                        class="form-control @error('level') is-invalid @enderror">
                    @php $currentLevel = old('level'); @endphp
                    <option value="" disabled selected>-- Select Level --</option>
                    <option value="Beginner" {{ $currentLevel === 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ $currentLevel === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ $currentLevel === 'Advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
                @error('level')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Empty Column for Spacing --}}
            <div class="form-group col-md-6 mb-3"></div>

            {{-- Description --}}
            <div class="form-group col-12 mb-3">
                <label for="description">Description (Optional)</label>
                <textarea name="description"
                          id="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="4"
                          placeholder="Brief summary of the tutorial content.">{{ old('description') }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">Save Tutorial</button>
        <a href="{{ route('tutorials.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection