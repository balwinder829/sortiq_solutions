@extends('layouts.app')

@section('content')

<div class="container">

    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">

            <h1 class="page_heading">
                Interview Candidate
            </h1>

        </div>

        <div class="col-md-6 text-end">

            <a
                href="{{ route('admin.interview_candidates.edit', $candidate->id) }}"
                class="btn btn-primary"
            >
                <i class="fa fa-edit"></i>
                Edit
            </a>

            <a
                href="{{ route('admin.interview_candidates.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>

        </div>

    </div>


    <div class="card">

        <div class="card-header">
            <strong>Candidate Information</strong>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- Candidate Name --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Candidate Name
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->candidate_name }}
                    </div>

                </div>


                {{-- Mobile --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Mobile
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->mobile }}
                    </div>

                </div>


                {{-- Email --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Email
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->email ?? '-' }}
                    </div>

                </div>


                {{-- Current Location --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Current Location
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->current_location ?? '-' }}
                    </div>

                </div>


                {{-- Current Company --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Current Company
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->current_company ?? '-' }}
                    </div>

                </div>


                {{-- Position --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Position Applied For
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->position_applied }}
                    </div>

                </div>


                {{-- Qualification --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Qualification
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->qualification ?? '-' }}
                    </div>

                </div>


                {{-- Experience --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Experience
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->experience ?? '-' }}
                    </div>

                </div>


                {{-- Technology --}}
                <div class="col-md-12 mb-3">

                    <label class="fw-bold">
                        Technology Known
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->technology_known ?? '-' }}
                    </div>

                </div>


                {{-- Interview Date --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Preferred Interview Date
                    </label>

                    <div class="form-control bg-light">

                        @if($candidate->preferred_date)

                            {{ $candidate->preferred_date->format('d M Y') }}

                        @else

                            -

                        @endif

                    </div>

                </div>


                {{-- Interview Time --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Preferred Interview Time
                    </label>

                    <div class="form-control bg-light">

                        @if($candidate->preferred_time)

                            {{ \Carbon\Carbon::parse($candidate->preferred_time)->format('h:i A') }}

                        @else

                            -

                        @endif

                    </div>

                </div>


                {{-- Resume --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Resume
                    </label>

                    <div>

                        @if($candidate->resume)

                            <a
                                href="{{ asset($candidate->resume) }}"
                                target="_blank"
                                class="btn btn-outline-primary"
                            >
                                <i class="fa fa-file"></i>
                                View / Download Resume
                            </a>

                        @else

                            <span class="text-muted">
                                No resume uploaded.
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Status
                    </label>

                    <div>

                        @if($candidate->status === 'pending')

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                        @elseif($candidate->status === 'confirmed')

                            <span class="badge bg-primary">
                                Confirmed
                            </span>

                        @elseif($candidate->status === 'completed')

                            <span class="badge bg-success">
                                Completed
                            </span>

                        @elseif($candidate->status === 'cancelled')

                            <span class="badge bg-danger">
                                Cancelled
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ $candidate->status }}
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Message --}}
                <div class="col-md-12 mb-3">

                    <label class="fw-bold">
                        Candidate Message
                    </label>

                    <div
                        class="form-control bg-light"
                        style="min-height: 100px; height: auto;"
                    >
                        {!! nl2br(e($candidate->message ?? '-')) !!}
                    </div>

                </div>


                {{-- Admin Notes --}}
                <div class="col-md-12 mb-3">

                    <label class="fw-bold">
                        Admin Notes
                    </label>

                    <div
                        class="form-control bg-light"
                        style="min-height: 100px; height: auto;"
                    >
                        {!! nl2br(e($candidate->admin_notes ?? '-')) !!}
                    </div>

                </div>


                {{-- Submitted At --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Submitted At
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->created_at
                            ? $candidate->created_at->format('d M Y h:i A')
                            : '-' }}
                    </div>

                </div>


                {{-- Updated At --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Last Updated
                    </label>

                    <div class="form-control bg-light">
                        {{ $candidate->updated_at
                            ? $candidate->updated_at->format('d M Y h:i A')
                            : '-' }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection