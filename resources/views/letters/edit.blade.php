@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Letter</h4>

    <form method="POST" action="{{ route('letters.update', $letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Letter Type (readonly) --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <input type="text"
                       class="form-control"
                       value="{{ ucfirst($letter->letter_type) }}"
                       readonly>
            </div>

            {{-- Employee Code --}}
            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text"
                       name="emp_code"
                       class="form-control"
                       value="{{ old('emp_code', $letter->emp_code) }}">
            </div>

            {{-- Employee Name --}}
            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text"
                       name="emp_name"
                       class="form-control @error('emp_name') is-invalid @enderror"
                       value="{{ old('emp_name', $letter->emp_name) }}"
                       required>
                @error('emp_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Position --}}
            <div class="form-group col-md-6">
                <label>Position</label>
                <input type="text"
                       name="position"
                       class="form-control @error('position') is-invalid @enderror"
                       value="{{ old('position', $letter->position) }}"
                       required>
                @error('position')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Joining Date --}}
            <div class="form-group col-md-6">
                <label>Joining Date</label>
                <input type="date"
                       name="joining_date"
                       class="form-control @error('joining_date') is-invalid @enderror"
                       value="{{ old('joining_date', $letter->joining_date) }}"
                       required>
                @error('joining_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Relieving Date (experience only) --}}
            @if($letter->letter_type === 'experience')
            <div class="form-group col-md-6">
                <label>Relieving Date</label>
                <input type="date"
                       name="relieving_date"
                       class="form-control @error('relieving_date') is-invalid @enderror"
                       value="{{ old('relieving_date', $letter->relieving_date) }}"
                       required>
                @error('relieving_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            @endif

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date"
                       name="issue_date"
                       class="form-control @error('issue_date') is-invalid @enderror"
                       value="{{ old('issue_date', $letter->issue_date) }}"
                       required>
                @error('issue_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $letter->email) }}"
                       required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
