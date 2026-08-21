@extends('layouts.app')

@section('content')

<div class="container">

    {{-- HEADER --}}
    <div class="row mb-3 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">
                Visitor Record
            </h1>
        </div>

        <div class="col-md-6 text-end">

            <a
                href="{{ route('admin.visitor_records.edit', $visitor->id) }}"
                class="btn btn-primary"
            >
                <i class="fa fa-edit"></i>
                Edit
            </a>

            <a
                href="{{ route('admin.visitor_records.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>

        </div>

    </div>


    <div class="card">

        <div class="card-header">
            <strong>Visitor Information</strong>
        </div>

        <div class="card-body">

            <div class="row">

                {{-- Visitor Name --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Visitor Name</label>
                    <div class="form-control bg-light">
                        {{ $visitor->visitor_name }}
                    </div>
                </div>


                {{-- Mobile --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Mobile</label>
                    <div class="form-control bg-light">
                        {{ $visitor->mobile }}
                    </div>
                </div>


                {{-- Email --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Email</label>
                    <div class="form-control bg-light">
                        {{ $visitor->email ?? '-' }}
                    </div>
                </div>


                {{-- Organization --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Organization</label>
                    <div class="form-control bg-light">
                        {{ $visitor->organization ?? '-' }}
                    </div>
                </div>


                {{-- Purpose --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Purpose of Visit</label>
                    <div class="form-control bg-light">
                        {{ $visitor->purpose }}
                    </div>
                </div>


                {{-- Person to Meet --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Person to Meet</label>
                    <div class="form-control bg-light">
                        {{ $visitor->person_to_meet ?? '-' }}
                    </div>
                </div>


                {{-- Visit Date --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Visit Date</label>
                    <div class="form-control bg-light">
                        @if($visitor->visit_date)
                            {{ $visitor->visit_date->format('d M Y') }}
                        @else
                            -
                        @endif
                    </div>
                </div>


                {{-- Visit Time --}}
                <div class="col-md-6 mb-3">
                    <label class="fw-bold">Visit Time</label>
                    <div class="form-control bg-light">
                        @if($visitor->visit_time)
                            {{ \Carbon\Carbon::parse($visitor->visit_time)->format('h:i A') }}
                        @else
                            -
                        @endif
                    </div>
                </div>


                {{-- Status --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Status
                    </label>

                    <div>
                        @if($visitor->status === 'pending')

                            <span class="badge bg-warning text-dark">
                                Pending
                            </span>

                        @elseif($visitor->status === 'confirmed')

                            <span class="badge bg-primary">
                                Confirmed
                            </span>

                        @elseif($visitor->status === 'visited')

                            <span class="badge bg-success">
                                Visited
                            </span>

                        @elseif($visitor->status === 'cancelled')

                            <span class="badge bg-danger">
                                Cancelled
                            </span>

                        @else

                            <span class="badge bg-secondary">
                                {{ $visitor->status }}
                            </span>

                        @endif
                    </div>

                </div>


                {{-- Submitted At --}}
                <div class="col-md-6 mb-3">

                    <label class="fw-bold">
                        Submitted At
                    </label>

                    <div class="form-control bg-light">
                        {{ $visitor->created_at
                            ? $visitor->created_at->format('d M Y h:i A')
                            : '-' }}
                    </div>

                </div>


                {{-- Message --}}
                <div class="col-md-12 mb-3">

                    <label class="fw-bold">
                        Message
                    </label>

                    <div
                        class="form-control bg-light"
                        style="min-height: 100px; height: auto;"
                    >
                        {!! nl2br(e($visitor->message ?? '-')) !!}
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
                        {!! nl2br(e($visitor->admin_notes ?? '-')) !!}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection