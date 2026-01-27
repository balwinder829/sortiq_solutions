@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Accepted Letter</h4>

    <form method="POST"
          action="{{ route('accepted-letters.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="row">

            {{-- Name --}}
            <div class="form-group col-md-6">
                <label>Name</label>
                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       required>

                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Name --}}
            <div class="form-group col-md-6">
                <label>Emp Code</label>
                <input type="text"
                       name="emp_code"
                       class="form-control @error('emp_code') is-invalid @enderror"
                       value="{{ old('emp_code') }}"
                       >

                @error('emp_code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required>

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- File --}}
            <div class="form-group col-md-12">
                <label>Upload Accepted Letter (PDF / Image)</label>
                <input type="file"
                       name="file"
                       class="form-control @error('file') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png"
                       required>

                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('accepted-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
