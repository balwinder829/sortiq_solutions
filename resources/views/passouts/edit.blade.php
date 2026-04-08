@extends('layouts.app')

@section('content')

<div class="container">

<div class="card">
    
    <div class="card-header">
        <h4>Edit Passout</h4>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-body">

        <form action="{{ route('passouts.update', $enquiry->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-row">

                {{-- Name --}}
                <div class="form-group col-md-6">
                    <label><strong>Name</strong></label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $enquiry->name) }}"
                           required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Mobile --}}
                <div class="form-group col-md-6">
                    <label><strong>Mobile</strong></label>
                    <input type="text"
                           name="mobile"
                           class="form-control @error('mobile') is-invalid @enderror"
                           value="{{ old('mobile', $enquiry->mobile) }}"
                           required
                           minlength="10"
                           maxlength="10"
                           pattern="[0-9]{10}"
                           title="Enter a valid 10-digit mobile number">
                    @error('mobile')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="form-group col-md-6">
                    <label><strong>Email</strong></label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $enquiry->email) }}">
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Study --}}
                <div class="form-group col-md-6">
                    <label><strong>Study</strong></label>
                    <input type="text"
                           name="study"
                           class="form-control @error('study') is-invalid @enderror"
                           value="{{ old('study', $enquiry->study) }}">
                    @error('study')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>
                 {{-- Study --}}
                <div class="form-group col-md-6">
                    <label><strong>Gap</strong></label>
                    <input type="text"
                           name="gap"
                           class="form-control @error('gap') is-invalid @enderror"
                           value="{{ old('gap', $enquiry->gap) }}">
                    @error('gap')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Assign To --}}
                <div class="form-group col-md-6">
                    <label><strong>Assign To</strong></label>
                    <select name="assigned_to"
                            class="form-control @error('assigned_to') is-invalid @enderror">

                        <option value="">Select</option>

                        @foreach ($sales as $user)
                            <option value="{{ $user->id }}"
                                {{ old('assigned_to', $enquiry->assigned_to) == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach

                    </select>

                    @error('assigned_to')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

            </div>

            <button type="submit" class="btn btn-primary mt-2">
                Update
            </button>

            <a href="{{ route('passouts.index') }}" class="btn btn-secondary mt-2">
                Cancel
            </a>

        </form>

    </div>
</div>

</div>

@endsection
