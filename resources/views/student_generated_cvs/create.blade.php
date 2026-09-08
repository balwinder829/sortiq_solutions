@extends('layouts.app')

@section('content')

<div class="container">

<div class="row mb-2 align-items-end">
    <div class="col-md-6">
        <h1 class="page_heading">Create Student CV</h1>
    </div>

    <div class="col-md-6 text-end">
        <a href="{{ route('student-generated-cvs.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>

        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
 

<div class="card">
    <div class="card-body">

        <form action="{{ route('student-generated-cvs.store') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            {{-- Student --}}
            <div class="row">

                <div class="col-md-6 mb-3">

    <label for="student_id" class="form-label">
        Select Student <span class="text-danger">*</span>
    </label>

    <select name="student_id"
            id="student_id"
            class="form-control @error('student_id') is-invalid @enderror"
            required>

        <option value="">Select Student</option>

        @foreach ($students as $student)

            <option value="{{ $student->id }}"
                    data-name="{{ ucwords(strtolower($student->student_name ?? '')) }}"
                    data-contact="{{ $student->contact ?? '' }}"
                    data-email="{{ $student->email_id ?? '' }}"
                    data-college="{{ ucwords(strtolower($student->collegeData?->college_name ?? '')) }}"
                    {{ old('student_id') == $student->id ? 'selected' : '' }}>

                {{ ucwords(strtolower($student->student_name ?? '')) }}
                S/O {{ ucwords(strtolower($student->f_name ?? '')) }}
                from {{ ucwords(strtolower($student->collegeData?->college_name ?? 'N/A')) }}

            </option>


        @endforeach

    </select>

    @error('student_id')
        <div class="text-danger mt-1">
            {{ $message }}
        </div>
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
                           value="{{ old('title') }}"
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
                           value="{{ old('name') }}"
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
                           value="{{ old('professional_title', 'Software Engineer') }}"
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
                          placeholder="Phone, Email, Address, LinkedIn, GitHub, Portfolio, etc.">{{ old('contact') }}</textarea>

                @error('contact')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            @php
                $defaultSummary = 'To secure a suitable position where I can utilize my knowledge and skills, contribute to the success of the organization, and develop my professional abilities through continuous learning and growth.';
            @endphp

            {{-- Summary --}}
            <div class="mb-3">
                <label for="summary" class="form-label">
                    Professional Summary
                </label>

                <textarea name="summary"
                      id="summary"
                      class="form-control @error('summary') is-invalid @enderror"
                      rows="5"
                      placeholder="Write professional summary / career objective...">{{ old('summary', $defaultSummary) }}</textarea>

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
                          placeholder="Enter educational qualifications...">{{ old('education') }}</textarea>

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
                          placeholder="Enter work experience, internships, training, etc.">{{ old('experience') }}</textarea>

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
                          placeholder="Enter technical skills, soft skills, tools, technologies, etc.">{{ old('skills') }}</textarea>

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
                          placeholder="Enter academic / personal / professional projects...">{{ old('projects') }}</textarea>

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
                          placeholder="Certifications, achievements, languages, hobbies, activities, references, etc.">{{ old('additional_info') }}</textarea>

                @error('additional_info')
                    <div class="text-danger mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Photo --}}
            <div class="mb-3">
                <label for="photo" class="form-label">
                    Photo
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
                    <i class="fa fa-save"></i> Save CV
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
@push('scripts')

<script>
$(document).ready(function () {

    function fillStudentData() {

        let option = $('#student_id option:selected');

        if (!option.val()) {
            return;
        }

        let name = option.data('name') || '';
        let contact = option.data('contact') || '';
        let email = option.data('email') || '';
        let college = option.attr('data-college') || '';

        // Fill Name
        $('#name').val(name);

        // Fill Contact Information
        let contactText = '';

        if (contact) {
            contactText += 'Mobile no - ' + contact;
        }

        if (email) {

            if (contactText) {
                contactText += "\n";
            }

            contactText += 'Email - ' + email;
        }

        $('#contact').val(contactText);

        // Education
        if (college) {
            $('#education').val('Studied from ' + college);
        } else {
            $('#education').val('');
        }
    }


    // When student is changed
    $('#student_id').on('change', function () {
        fillStudentData();
    });


    // Automatically fill when page loads
    // Useful after validation error
    if ($('#student_id').val()) {

        // Only fill if old values are empty
        if (!$('#name').val() && !$('#contact').val()) {
            fillStudentData();
        }

    }

        // Education
        if (college) {
            $('#education').val('Studied from ' + college);
        } else {
            $('#education').val('');
        }

});
</script>

@endpush