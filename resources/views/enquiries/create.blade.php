@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card">
        
        <div class="card-header">
            <h4>Add Enquiry</h4>
        </div>
        @if(session('import_errors'))
            <div class="alert alert-warning">
                <strong>Import completed with some issues:</strong>
                <ul class="mb-0">
                    @foreach(session('import_errors') as $err)
                        <li>Row {{ $err['row'] }} → {{ $err['error'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card-body">

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


            <form action="{{ route('enquiries.store') }}" method="POST">
                @csrf

                <div class="form-row">

                    {{-- Name --}}
                    <div class="form-group col-md-6">
                        <label><strong>Name</strong></label>
                        <input type="text"
                               name="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}"
                               placeholder="Enter student name"
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
                               value="{{ old('mobile') }}"
                               placeholder="Enter mobile number">
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
                               value="{{ old('email') }}"
                               placeholder="Enter email address">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- College --}}
                    <div class="form-group col-md-6">
                        <label><strong>College</strong></label>
                        <select name="college"
                                class="form-control @error('college') is-invalid @enderror">

                            <option value="">Select College</option>

                            @foreach($colleges as $clg)
                                <option value="{{ $clg->id }}"
                                    {{ old('college') == $clg->id ? 'selected' : '' }}>
                                    {{ $clg->college_name }}
                                </option>
                            @endforeach

                        </select>

                        @error('college')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>


                    {{-- Study --}}
                    <div class="form-group col-md-6">
                        <label><strong>Study</strong></label>
                        <input type="text"
                               name="study"
                               class="form-control @error('study') is-invalid @enderror"
                               value="{{ old('study') }}"
                               placeholder="Enter study/program">
                        @error('study')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Semester --}}
                    <div class="form-group col-md-6">
                        <label><strong>Semester</strong></label>
                        <input type="text"
                               name="semester"
                               class="form-control @error('semester') is-invalid @enderror"
                               value="{{ old('semester') }}"
                               placeholder="Enter semester">
                        @error('semester')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>

                <button type="submit" class="btn btn-primary mt-2">
                    Save
                </button>

                <a href="{{ route('enquiries.index') }}" class="btn btn-secondary mt-2">
                    Cancel
                </a>

            </form>

        </div>
    </div>
</div>

@endsection
