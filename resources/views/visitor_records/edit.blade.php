@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3 align-items-center">

        <div class="col-md-6">
            <h1 class="page_heading">Edit Visitor Record</h1>
        </div>

        <div class="col-md-6 text-end">
            <a
                href="{{ route('admin.visitor_records.index') }}"
                class="btn btn-secondary"
            >
                Back
            </a>
        </div>

    </div>


    <div class="card">

        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form
                method="POST"
                action="{{ route('admin.visitor_records.update', $visitor->id) }}"
            >

                @csrf
                @method('PUT')


                {{-- Visitor Name / Mobile --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Visitor Name
                        </label>

                        <input
                            type="text"
                            name="visitor_name"
                            class="form-control @error('visitor_name') is-invalid @enderror"
                            value="{{ old('visitor_name', $visitor->visitor_name) }}"
                            required
                        >

                        @error('visitor_name')
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
                            value="{{ old('mobile', $visitor->mobile) }}"
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


                {{-- Email / Organization --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $visitor->email) }}"
                        >

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Company / College / Organization
                        </label>

                        <input
                            type="text"
                            name="organization"
                            class="form-control @error('organization') is-invalid @enderror"
                            value="{{ old('organization', $visitor->organization) }}"
                        >

                        @error('organization')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Purpose / Person --}}
                <div class="row mb-3">

                    <div class="col-md-6">

                        <label class="form-label">
                            Purpose of Visit
                        </label>

                        <input
                            type="text"
                            name="purpose"
                            class="form-control @error('purpose') is-invalid @enderror"
                            value="{{ old('purpose', $visitor->purpose) }}"
                            required
                        >

                        @error('purpose')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Person to Meet
                        </label>

                        <input
                            type="text"
                            name="person_to_meet"
                            class="form-control @error('person_to_meet') is-invalid @enderror"
                            value="{{ old('person_to_meet', $visitor->person_to_meet) }}"
                        >

                        @error('person_to_meet')
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
                            Visit Date
                        </label>

                        <input
                            type="date"
                            name="visit_date"
                            class="form-control @error('visit_date') is-invalid @enderror"
                            value="{{ old('visit_date', $visitor->visit_date?->format('Y-m-d')) }}"
                            min="{{ date('Y-m-d') }}"
                            required
                        >

                        @error('visit_date')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    <div class="col-md-6">

                        <label class="form-label">
                            Visit Time
                        </label>

                        <input
                            type="time"
                            name="visit_time"
                            class="form-control @error('visit_time') is-invalid @enderror"
                            value="{{ old('visit_time', $visitor->visit_time) }}"
                        >

                        @error('visit_time')
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
                            Message
                        </label>

                        <textarea
                            name="message"
                            rows="4"
                            class="form-control @error('message') is-invalid @enderror"
                        >{{ old('message', $visitor->message) }}</textarea>

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
                                {{ old('status', $visitor->status) == 'pending' ? 'selected' : '' }}>
                                Pending
                            </option>

                            <option value="confirmed"
                                {{ old('status', $visitor->status) == 'confirmed' ? 'selected' : '' }}>
                                Confirmed
                            </option>

                            <option value="visited"
                                {{ old('status', $visitor->status) == 'visited' ? 'selected' : '' }}>
                                Visited
                            </option>

                            <option value="cancelled"
                                {{ old('status', $visitor->status) == 'cancelled' ? 'selected' : '' }}>
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
                        >{{ old('admin_notes', $visitor->admin_notes) }}</textarea>

                        @error('admin_notes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Buttons --}}
                <div class="text-end">

                    <a
                        href="{{ route('admin.visitor_records.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Update Visitor
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection