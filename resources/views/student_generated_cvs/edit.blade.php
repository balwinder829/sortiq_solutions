@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">
    <div class="col-md-6">
        <h1 class="page_heading">Edit Student CV</h1>
    </div>

    <div class="col-md-6 text-end">
        <a href="{{ route('student-generated-cvs.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">

        <form action="{{ route('student-generated-cvs.update', $cv->id) }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf
            @method('PUT')

             {{-- Student --}}

<div class="row">


<div class="col-md-6 mb-3">
    <label class="form-label">
        Student
    </label>

    <input type="text"
           class="form-control"
           value="{{ $cv->student?->student_name }}"
           readonly>

    <input type="hidden"
           name="student_id"
           value="{{ $cv->student_id }}">

    @error('student_id')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

{{-- CV Title --}}
<div class="col-md-6 mb-3">
    <label for="title" class="form-label">
        CV Title
    </label>

    <input type="text"
           name="title"
           id="title"
           class="form-control @error('title') is-invalid @enderror"
           value="{{ old('title', $cv->title) }}"
           placeholder="e.g. Rahul Sharma - Resume">

    @error('title')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>


</div>


            {{-- Name & Professional Title --}}
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">
                        Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $cv->name) }}"
                           required>

                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="professional_title" class="form-label">
                        Professional Title
                    </label>

                    <input type="text"
                           name="professional_title"
                           id="professional_title"
                           class="form-control @error('professional_title') is-invalid @enderror"
                           value="{{ old('professional_title', $cv->professional_title) }}"
                           placeholder="e.g. Full Stack Developer">

                    @error('professional_title')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

            </div>

            {{-- Contact --}}
            <div class="mb-3">
                <label for="contact" class="form-label">
                    Contact Information
                </label>

                <textarea name="contact"
                          id="contact"
                          class="form-control @error('contact') is-invalid @enderror"
                          rows="4"
                          placeholder="Phone, Email, Address, LinkedIn, GitHub, Portfolio, etc.">{{ old('contact', $cv->contact) }}</textarea>

                @error('contact')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Summary --}}
            <div class="mb-3">
                <label for="summary" class="form-label">
                    Professional Summary
                </label>

                <textarea name="summary"
                          id="summary"
                          class="form-control @error('summary') is-invalid @enderror"
                          rows="5"
                          placeholder="Write professional summary / career objective...">{{ old('summary', $cv->summary) }}</textarea>

                @error('summary')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Education --}}
            <div class="mb-3">
                <label for="education" class="form-label">
                    Education
                </label>

                <textarea name="education"
                          id="education"
                          class="form-control @error('education') is-invalid @enderror"
                          rows="6"
                          placeholder="Enter educational qualifications...">{{ old('education', $cv->education) }}</textarea>

                @error('education')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Experience --}}
            <div class="mb-3">
                <label for="experience" class="form-label">
                    Experience
                </label>

                <textarea name="experience"
                          id="experience"
                          class="form-control @error('experience') is-invalid @enderror"
                          rows="6"
                          placeholder="Enter work experience, internships, training, etc.">{{ old('experience', $cv->experience) }}</textarea>

                @error('experience')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Skills --}}
            <div class="mb-3">
                <label for="skills" class="form-label">
                    Skills
                </label>

                <textarea name="skills"
                          id="skills"
                          class="form-control @error('skills') is-invalid @enderror"
                          rows="5"
                          placeholder="Enter technical skills, soft skills, tools, technologies, etc.">{{ old('skills', $cv->skills) }}</textarea>

                @error('skills')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Projects --}}
            <div class="mb-3">
                <label for="projects" class="form-label">
                    Projects
                </label>

                <textarea name="projects"
                          id="projects"
                          class="form-control @error('projects') is-invalid @enderror"
                          rows="6"
                          placeholder="Enter academic / personal / professional projects...">{{ old('projects', $cv->projects) }}</textarea>

                @error('projects')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Additional Information --}}
            <div class="mb-3">
                <label for="additional_info" class="form-label">
                    Additional Information
                </label>

                <textarea name="additional_info"
                          id="additional_info"
                          class="form-control @error('additional_info') is-invalid @enderror"
                          rows="6"
                          placeholder="Certifications, achievements, languages, hobbies, activities, references, etc.">{{ old('additional_info', $cv->additional_info) }}</textarea>

                @error('additional_info')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Current Photo --}}
            @if($cv->photo)
                <div class="mb-3">
                    <label class="form-label">Current Photo</label>

                    <div>
                        <img src="{{ asset('storage/' . $cv->photo) }}"
                             alt="CV Photo"
                             style="max-width: 120px; max-height: 120px;"
                             class="img-thumbnail">
                    </div>
                </div>
            @endif

            {{-- New Photo --}}
            <div class="mb-3">
                <label for="photo" class="form-label">
                    {{ $cv->photo ? 'Change Photo' : 'Photo' }}
                </label>

                <input type="file"
                       name="photo"
                       id="photo"
                       class="form-control @error('photo') is-invalid @enderror"
                       accept="image/*">

                @error('photo')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror

                <small class="text-muted">
                    Upload a JPG, JPEG, or PNG photo.
                </small>
            </div>

            {{-- Buttons --}}
            <div class="mt-4">

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update CV
                </button>

                <a href="{{ route('student-generated-cvs.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </form>

    </div>
</div>


</div>
@endsection
