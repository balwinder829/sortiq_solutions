@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Letter</h4>

    <form method="POST" action="{{ route('letters.store') }}">
        @csrf

        <div class="row">

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <select name="letter_type"
                        id="letterType"
                        class="form-control @error('letter_type') is-invalid @enderror"
                        required>
                    <option value="">Select Letter Type</option>
                    <option value="offer" {{ old('letter_type')=='offer'?'selected':'' }}>Offer Letter</option>
                    <option value="experience" {{ old('letter_type')=='experience'?'selected':'' }}>Experience Letter</option>
                    <option value="appointment" {{ old('letter_type')=='appointment'?'selected':'' }}>Appointment Letter</option>
                </select>
                @error('letter_type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Employee Code --}}
            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text"
                       name="emp_code"
                       class="form-control"
                       value="{{ old('emp_code') }}">
            </div>

            {{-- Employee Name --}}
            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text"
                       name="emp_name"
                       class="form-control @error('emp_name') is-invalid @enderror"
                       value="{{ old('emp_name') }}"
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
                       value="{{ old('position') }}"
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
                       value="{{ old('joining_date') }}"
                       required>
                @error('joining_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Relieving Date (Experience only) --}}
            <div class="form-group col-md-6 d-none" id="relievingField">
                <label>Relieving Date</label>
                <input type="date"
                       name="relieving_date"
                       class="form-control @error('relieving_date') is-invalid @enderror"
                       value="{{ old('relieving_date') }}">
                @error('relieving_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date"
                       name="issue_date"
                       class="form-control @error('issue_date') is-invalid @enderror"
                       value="{{ old('issue_date') }}"
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
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Salary (All Letter Types) --}}
            <div class="form-group col-md-6">
                <label>Salary</label>
                <input type="number"
                       name="salary"
                       class="form-control @error('salary') is-invalid @enderror"
                       value="{{ old('salary') }}"
                       required>
                @error('salary')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Probation Period (Appointment only) --}}
            <div class="form-group col-md-6 d-none" id="probationField">
                <label>Probation Period (Months)</label>
                <input type="number"
                       name="probation_period"
                       class="form-control @error('probation_period') is-invalid @enderror"
                       value="{{ old('probation_period') }}">
                @error('probation_period')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Bond Period (Appointment only) --}}
            <div class="form-group col-md-6 d-none" id="bondField">
                <label>Bond Period (Years)</label>
                <input type="number"
                       name="bond_period"
                       class="form-control @error('bond_period') is-invalid @enderror"
                       value="{{ old('bond_period') }}">
                @error('bond_period')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const letterType = document.getElementById('letterType');

    function toggleFields() {
        const isExperience = letterType.value === 'experience';
        const isAppointment = letterType.value === 'appointment';

        document.getElementById('relievingField')
            .classList.toggle('d-none', !isExperience);

        document.getElementById('probationField')
            .classList.toggle('d-none', !isAppointment);

        document.getElementById('bondField')
            .classList.toggle('d-none', !isAppointment);
    }

    letterType.addEventListener('change', toggleFields);
    toggleFields(); // for old() values
});
</script>
@endpush
