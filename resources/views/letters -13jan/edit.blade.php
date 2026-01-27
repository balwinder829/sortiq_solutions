@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Letter</h4>

    
    <form method="POST" action="{{ route('letters.update', $letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Employee (READ ONLY) --}}
            <div class="form-group col-md-6">
                <label>Employee</label>
                <input
                    type="text"
                    class="form-control"
                    value="{{ $letter->employee->emp_code }} - {{ $letter->employee->emp_name }}"
                    readonly
                >
            </div>

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <select
                    name="letter_type"
                    id="letterType"
                    class="form-control @error('letter_type') is-invalid @enderror"
                    required
                >
                    <option value="">Select Letter Type</option>
                    <option value="offer" {{ $letter->letter_type === 'offer' ? 'selected' : '' }}>Offer Letter</option>
                    <option value="experience" {{ $letter->letter_type === 'experience' ? 'selected' : '' }}>Experience Letter</option>
                    <option value="relieving" {{ $letter->letter_type === 'relieving' ? 'selected' : '' }}>Relieving Letter</option>
                    <option value="appointment" {{ $letter->letter_type === 'appointment' ? 'selected' : '' }}>Appointment Letter</option>
                    <option value="increment" {{ $letter->letter_type === 'increment' ? 'selected' : '' }}>Increment Letter</option>
                    <option value="bond" {{ $letter->letter_type === 'bond' ? 'selected' : '' }}>Employment Bond Letter</option>
                    <option value="custom_bond" {{ $letter->letter_type === 'custom_bond' ? 'selected' : '' }}>Custom Bond Letter</option>
                </select>
                @error('letter_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input
                    type="date"
                    name="issue_date"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', $letter->issue_date) }}"
                    required
                >
                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Experience / Relieving --}}
            <div class="form-group col-md-6 d-none" id="relievingField">
                <label>Relieving Date</label>
                <input
                    type="date"
                    name="relieving_date"
                    class="form-control @error('relieving_date') is-invalid @enderror"
                    value="{{ old('relieving_date', $letter->relieving_date) }}"
                >
                @error('relieving_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Appointment --}}
            <div class="form-group col-md-6 d-none" id="probationField">
                <label>Probation Period (Months)</label>
                <input
                    type="number"
                    name="probation_period"
                    class="form-control @error('probation_period') is-invalid @enderror"
                    value="{{ old('probation_period', $letter->probation_period) }}"
                >
                @error('probation_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="bondField">
                <label>Bond Period (Years.Months)</label>
                <input
                    type="number"
                    step="0.1"
                    min="0"
                    id="bondPeriod"
                    name="bond_period"
                    class="form-control @error('bond_period') is-invalid @enderror"
                    value="{{ old('bond_period', $letter->bond_period) }}"
                    placeholder="e.g. 1.2 (1 year 2 months)"
                >
                @error('bond_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Increment --}}
            <div class="form-group col-md-6 d-none" id="incrementOldSalary">
                <label>Current Salary</label>
                <input
                    type="text"
                    class="form-control"
                    value="{{ optional($letter->employee->salaryStructure)->basic_salary }}"
                    readonly
                >
            </div>

            <div class="form-group col-md-6 d-none" id="incrementNewSalary">
                <label>New Salary</label>
                <input
                    type="number"
                    name="new_salary"
                    class="form-control @error('new_salary') is-invalid @enderror"
                    value="{{ old('new_salary', $letter->new_salary) }}"
                >
                @error('new_salary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="incrementEffectiveDate">
                <label>Effective Date</label>
                <input
                    type="date"
                    name="effective_date"
                    class="form-control @error('effective_date') is-invalid @enderror"
                    value="{{ old('effective_date', $letter->effective_date) }}"
                >
                @error('effective_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="incrementPercentage">
                <label>Increment Percentage (%)</label>
                <input
                    type="number"
                    step="0.01"
                    name="increment_percentage"
                    class="form-control @error('increment_percentage') is-invalid @enderror"
                    value="{{ old('increment_percentage', $letter->increment_percentage) }}"
                >
                @error('increment_percentage')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Bond --}}
            <div class="form-group col-md-6 d-none" id="bondStartDate">
                <label>Bond Start Date</label>
                <input
                    type="date"
                    name="bond_start_date"
                    id="bondStartDateInput"
                    class="form-control @error('bond_start_date') is-invalid @enderror"
                    value="{{ old('bond_start_date', $letter->bond_start_date) }}"
                >
                @error('bond_start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="bondEndDate">
                <label>Bond End Date</label>
                <input
                    type="date"
                    name="bond_end_date"
                    id="bondEndDateInput"
                    class="form-control @error('bond_end_date') is-invalid @enderror"
                    value="{{ old('bond_end_date', $letter->bond_end_date) }}"
                >
                @error('bond_end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="bondAmount">
                <label>Bond Amount</label>
                <input
                    type="number"
                    name="bond_amount"
                    class="form-control @error('bond_amount') is-invalid @enderror"
                    value="{{ old('bond_amount', $letter->bond_amount) }}"
                >
                @error('bond_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-12 d-none" id="bondTerms">
                <label>Bond Terms</label>
                <textarea
                    name="bond_terms"
                    id="bond_terms"
                    class="form-control @error('bond_terms') is-invalid @enderror"
                >{{ old('bond_terms', $letter->bond_terms) }}</textarea>
                @error('bond_terms')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const letterType = document.getElementById('letterType');

    function toggleFields() {
        const isExperience = ['experience', 'relieving'].includes(letterType.value);
        const isAppointment = letterType.value === 'appointment';
        const isIncrement = letterType.value === 'increment';
        const isBond = ['bond', 'custom_bond'].includes(letterType.value);
        const isCustomBond = letterType.value === 'custom_bond';

        document.getElementById('relievingField').classList.toggle('d-none', !isExperience);
        document.getElementById('probationField').classList.toggle('d-none', !isAppointment);

        document.getElementById('bondField')
            .classList.toggle('d-none', !(isAppointment || isBond));

        document.getElementById('incrementOldSalary').classList.toggle('d-none', !isIncrement);
        document.getElementById('incrementNewSalary').classList.toggle('d-none', !isIncrement);
        document.getElementById('incrementEffectiveDate').classList.toggle('d-none', !isIncrement);
        document.getElementById('incrementPercentage').classList.toggle('d-none', !isIncrement);

        document.getElementById('bondStartDate').classList.toggle('d-none', !(isAppointment || isBond));
        document.getElementById('bondEndDate').classList.toggle('d-none', !(isAppointment || isBond));
        document.getElementById('bondAmount').classList.toggle('d-none', !isBond);
        document.getElementById('bondTerms').classList.toggle('d-none', !isCustomBond);
    }

    letterType.addEventListener('change', toggleFields);
    toggleFields();
});
</script>

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>
<script>
    CKEDITOR.replace('bond_terms');
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const bondPeriodInput = document.getElementById('bondPeriod');
    const bondStartInput  = document.getElementById('bondStartDateInput');
    const bondEndInput    = document.getElementById('bondEndDateInput');

    function calculateBondEndDate() {
        if (!bondPeriodInput || !bondStartInput || !bondEndInput) return;
        if (!bondPeriodInput.value || !bondStartInput.value) return;

        const parts = bondPeriodInput.value.toString().split('.');
        const years = parseInt(parts[0], 10);
        const months = parts[1] ? parseInt(parts[1], 10) : 0;

        if (months > 11) {
            alert('Months must be between 0 and 11');
            bondPeriodInput.value = '';
            bondEndInput.value = '';
            return;
        }

        const startDate = new Date(bondStartInput.value);
        startDate.setFullYear(startDate.getFullYear() + years);
        startDate.setMonth(startDate.getMonth() + months);

        const yyyy = startDate.getFullYear();
        const mm = String(startDate.getMonth() + 1).padStart(2, '0');
        const dd = String(startDate.getDate()).padStart(2, '0');

        bondEndInput.value = `${yyyy}-${mm}-${dd}`;
    }

    bondPeriodInput?.addEventListener('input', calculateBondEndDate);
    bondStartInput?.addEventListener('change', calculateBondEndDate);

    calculateBondEndDate();
});
</script>
@endpush
