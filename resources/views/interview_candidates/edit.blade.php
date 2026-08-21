@extends('layouts.app')

@section('content')

<div class="container">

    {{-- PAGE HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">
                Edit Interview Candidate
            </h1>
        </div>

        <div class="col-md-6 text-end">
            <a
                href="{{ route('admin.interview_candidates.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>
        </div>

    </div>


    <div class="card">

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('admin.interview_candidates.update', $candidate->id) }}"
                enctype="multipart/form-data"
            >

                @csrf
                @method('PUT')


                {{-- Candidate Name / Mobile --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Candidate Name
                        </label>

                        <input
                            type="text"
                            name="candidate_name"
                            class="form-control @error('candidate_name') is-invalid @enderror"
                            value="{{ old('candidate_name', $candidate->candidate_name) }}"
                            required
                        >

                        @error('candidate_name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Mobile
                        </label>

                        <input
                            type="text"
                            name="mobile"
                            class="form-control @error('mobile') is-invalid @enderror"
                            value="{{ old('mobile', $candidate->mobile) }}"
                            minlength="10"
                            maxlength="10"
                            pattern="[0-9]{10}"
                            required
                        >

                        @error('mobile')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Email / Location --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $candidate->email) }}"
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Current Location
                        </label>

                        <input
                            type="text"
                            name="current_location"
                            class="form-control @error('current_location') is-invalid @enderror"
                            value="{{ old('current_location', $candidate->current_location) }}"
                        >

                        @error('current_location')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Company / Position --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Current Company
                        </label>

                        <input
                            type="text"
                            name="current_company"
                            class="form-control @error('current_company') is-invalid @enderror"
                            value="{{ old('current_company', $candidate->current_company) }}"
                        >

                        @error('current_company')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Position Applied For
                        </label>

                        <input
                            type="text"
                            name="position_applied"
                            class="form-control @error('position_applied') is-invalid @enderror"
                            value="{{ old('position_applied', $candidate->position_applied) }}"
                            required
                        >

                        @error('position_applied')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Qualification / Experience --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Qualification
                        </label>

                        <input
                            type="text"
                            name="qualification"
                            class="form-control @error('qualification') is-invalid @enderror"
                            value="{{ old('qualification', $candidate->qualification) }}"
                        >

                        @error('qualification')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Experience
                        </label>

                        <input
                            type="text"
                            name="experience"
                            class="form-control @error('experience') is-invalid @enderror"
                            value="{{ old('experience', $candidate->experience) }}"
                            placeholder="e.g. Fresher / 2 Years"
                        >

                        @error('experience')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Technology --}}
                <div class="row mb-3">

                    <div class="col-md-12">

                        <label class="form-label">
                            Technology Known
                        </label>

                        <input
                            type="text"
                            name="technology_known"
                            class="form-control @error('technology_known') is-invalid @enderror"
                            value="{{ old('technology_known', $candidate->technology_known) }}"
                            placeholder="e.g. PHP, Laravel, MySQL, React"
                        >

                        @error('technology_known')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Date / Time --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Preferred Interview Date
                        </label>

                        <input
                            type="date"
                            id="preferred_date"
                            name="preferred_date"
                            class="form-control @error('preferred_date') is-invalid @enderror"
                            value="{{ old('preferred_date', $candidate->preferred_date?->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                        >

                        @error('preferred_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Sunday is not available.
                        </small>

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Preferred Interview Time
                        </label>

                        <input
                            type="time"
                            name="preferred_time"
                            class="form-control @error('preferred_time') is-invalid @enderror"
                            value="{{ old('preferred_time', $candidate->preferred_time) }}"
                        >

                        @error('preferred_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Existing Resume / New Resume --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Current Resume
                        </label>

                        @if($candidate->resume)

                            <div>
                                <a
                                    href="{{ asset($candidate->resume) }}"
                                    target="_blank"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="fa fa-file"></i>
                                    View Resume
                                </a>
                            </div>

                        @else

                            <div class="text-muted">
                                No resume uploaded.
                            </div>

                        @endif

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Upload New Resume
                        </label>

                        <input
                            type="file"
                            name="resume"
                            class="form-control @error('resume') is-invalid @enderror"
                            accept=".pdf,.doc,.docx"
                        >

                        <small class="text-muted">
                            PDF, DOC, DOCX — Max 5 MB
                        </small>

                        @error('resume')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Message --}}
                <div class="row mb-3">

                    <div class="col-md-12">

                        <label class="form-label">
                            Candidate Message
                        </label>

                        <textarea
                            name="message"
                            rows="4"
                            class="form-control @error('message') is-invalid @enderror"
                        >{{ old('message', $candidate->message) }}</textarea>

                        @error('message')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Status --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-control @error('status') is-invalid @enderror"
                            required
                        >

                            <option value="pending"
                                {{ old('status', $candidate->status) == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="confirmed"
                                {{ old('status', $candidate->status) == 'confirmed' ? 'selected' : '' }}>
                                Confirmed
                            </option>

                            <option value="completed"
                                {{ old('status', $candidate->status) == 'completed' ? 'selected' : '' }}>
                                Completed
                            </option>

                            <option value="cancelled"
                                {{ old('status', $candidate->status) == 'cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Admin Notes --}}
                <div class="row mb-3">

                    <div class="col-md-12">

                        <label class="form-label">
                            Admin Notes
                        </label>

                        <textarea
                            name="admin_notes"
                            rows="4"
                            class="form-control @error('admin_notes') is-invalid @enderror"
                            placeholder="Internal admin notes"
                        >{{ old('admin_notes', $candidate->admin_notes) }}</textarea>

                        @error('admin_notes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- BUTTONS --}}
                <div class="text-end">

                    <a
                        href="{{ route('admin.interview_candidates.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Candidate
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection