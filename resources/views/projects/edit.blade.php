@extends('layouts.app')

@section('content')
<div class="container">
    {{-- Assuming $project is passed from the controller's edit method --}}
    <h4>Edit Project: {{ $project->name }}</h4>

    <form method="POST" action="{{ route('projects.update', $project) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Project Name --}}
            <div class="form-group col-md-6 mb-3">
                <label for="name">Project Name *</label>
                <input type="text"
                       name="name"
                       id="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $project->name) }}"
                       required
                       placeholder="e.g., Portfolio Website">
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Primary Tech Stack --}}
            <div class="form-group col-md-6 mb-3">
                <label for="tech_stack">Primary Tech Stack *</label>
                <input type="text"
                       name="tech_stack"
                       id="tech_stack"
                       class="form-control @error('tech_stack') is-invalid @enderror"
                       value="{{ old('tech_stack', $project->tech_stack) }}"
                       required
                       placeholder="e.g., Laravel, Vue.js, Bootstrap">
                @error('tech_stack')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Backend Language/Framework --}}
            <div class="form-group col-md-6 mb-3">
                <label for="backend_lang">Backend</label>
                <input type="text"
                       name="backend_lang"
                       id="backend_lang"
                       class="form-control @error('backend_lang') is-invalid @enderror"
                       value="{{ old('backend_lang', $project->backend_lang) }}"
                       placeholder="e.g., PHP, Node.js, Python">
                @error('backend_lang')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Frontend Framework --}}
            <div class="form-group col-md-6 mb-3">
                <label for="frontend_framework">Frontend</label>
                <input type="text"
                       name="frontend_framework"
                       id="frontend_framework"
                       class="form-control @error('frontend_framework') is-invalid @enderror"
                       value="{{ old('frontend_framework', $project->frontend_framework) }}"
                       placeholder="e.g., Vue 3, React, Blade">
                @error('frontend_framework')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Versions --}}
            <div class="form-group col-md-6 mb-3">
                <label for="versions">Versions</label>
                <input type="text"
                       name="versions"
                       id="versions"
                       class="form-control @error('versions') is-invalid @enderror"
                       value="{{ old('versions', $project->versions) }}"
                       placeholder="e.g., Laravel 10.x, Tailwind CSS 3">
                @error('versions')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- GitHub Link --}}
            <div class="form-group col-md-6 mb-3">
                <label for="github_link">GitHub Link</label>
                <input type="url"
                       name="github_link"
                       id="github_link"
                       class="form-control @error('github_link') is-invalid @enderror"
                       value="{{ old('github_link', $project->github_link) }}"
                       placeholder="https://github.com/...">
                @error('github_link')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Description --}}
            <div class="form-group col-12 mb-3">
                <label for="description">Description *</label>
                <textarea name="description"
                          id="description"
                          class="form-control @error('description') is-invalid @enderror"
                          rows="5"
                          required
                          placeholder="A detailed overview of the project and its features.">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Project</button>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection