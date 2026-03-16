@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Generate Letter</h4>

     @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


    <form method="POST" action="{{ route('letters.store') }}">
        @csrf

        <div class="row">

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
                    <option value="intern" {{ old('letter_type')=='intern'?'selected':'' }}>Intern Letter</option>
                    <option value="intern_custom" {{ old('letter_type')=='intern_custom'?'selected':'' }}>Intern Custom Letter</option>
                    <option value="offer" {{ old('letter_type')=='offer'?'selected':'' }}>Offer Letter</option>
                    <option value="experience" {{ old('letter_type')=='experience'?'selected':'' }}>Experience Letter</option>
                    <option value="relieving" {{ old('letter_type')=='relieving'?'selected':'' }}>Relieving Letter</option>
                    <option value="appointment" {{ old('letter_type')=='appointment'?'selected':'' }}>Appointment Letter</option>
                    <option value="appointment_with_bond" {{ old('letter_type')=='appointment_with_bond'?'selected':'' }}>Appointment With Bond Letter</option>
                    <option value="increment" {{ old('letter_type')=='increment'?'selected':'' }}>Increment Letter</option>
                    <option value="bond" {{ old('letter_type')=='bond'?'selected':'' }}>Employment Bond Letter</option>
                    <option value="custom_bond" {{ old('letter_type')=='custom_bond'?'selected':'' }}>Custom Bond Letter</option>
                    <option value="noc" {{ old('letter_type')=='noc'?'selected':'' }}>NOC Letter</option>
                    <option value="custom" {{ old('letter_type')=='custom'?'selected':'' }}>Custom Office Letter</option>
                </select>
                @error('letter_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Employee --}}
            <div class="form-group col-md-6">
                <label>Select Employee</label>
                <select
                    name="employee_id"
                    id="employeeSelect"
                    class="form-control @error('employee_id') is-invalid @enderror"
                    required
                >
                    <option value="">Select Employee</option>
                    @foreach($employees as $employee)
                        <option
                            value="{{ $employee->id }}"
                            data-salary="{{ optional($employee->salaryStructure)->total_salary }}"
                            {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                        >
                            {{ $employee->emp_code }} - {{ $employee->emp_name }}
                        </option>
                    @endforeach
                </select>
                @error('employee_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Relieving Date --}}
            <div class="form-group col-md-6 d-none" id="relievingField">
                <label>Relieving Date</label>
                <input
                    type="date"
                    name="relieving_date"
                    class="form-control @error('relieving_date') is-invalid @enderror"
                    value="{{ old('relieving_date') }}"
                >
                @error('relieving_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input
                    type="date"
                    name="issue_date"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', now()->toDateString()) }}"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    required
                >
                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Probation --}}
            <div class="form-group col-md-6 d-none" id="probationField">
                <label>Probation Period (Months)</label>
                <input
                    type="number"
                    name="probation_period"
                    class="form-control @error('probation_period') is-invalid @enderror"
                    value="{{ old('probation_period') }}"
                >
                @error('probation_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Bond Period --}}
            <div class="form-group col-md-6 d-none" id="bondField">
                <label>Bond Period (Years.Months)</label>
                <input
                    type="text"
                    name="bond_period"
                    id="bondPeriod"
                    class="form-control @error('bond_period') is-invalid @enderror"
                    value="{{ old('bond_period') }}"
                    placeholder="e.g. 2.10 (2 years 10 months)"
                >
                @error('bond_period')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Increment Salary --}}
            <div class="form-group col-md-6 d-none" id="incrementOldSalary">
                <label>Current Salary</label>
                <input type="text" id="currentSalary" class="form-control" readonly>
            </div>

            <div class="form-group col-md-6 d-none" id="incrementNewSalary">
                <label>New Salary</label>
                <input
                    type="number"
                    name="new_salary"
                    class="form-control @error('new_salary') is-invalid @enderror"
                    value="{{ old('new_salary') }}"
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
                    value="{{ old('effective_date') }}"
                >
                @error('effective_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Bond Dates --}}
            <div class="form-group col-md-6 d-none" id="bondStartDate">
                <label>Bond Start Date</label>
                <input
                    type="date"
                    name="bond_start_date"
                    id="bondStartDateInput"
                    class="form-control @error('bond_start_date') is-invalid @enderror"
                    value="{{ old('bond_start_date') }}"
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
                    value="{{ old('bond_end_date') }}"
                >
                @error('bond_end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="bondAmount">
                <label id="bondAmountLabel">Bond Amount</label>
                <input
                    type="number"
                    name="bond_amount"
                    class="form-control @error('bond_amount') is-invalid @enderror"
                    value="{{ old('bond_amount') }}"
                >
                @error('bond_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group col-md-6 d-none" id="bondcheckNo">
                <label>Check Number</label>
                <input
                    type="number"
                    name="check_number"
                    class="form-control @error('check_number') is-invalid @enderror"
                    value="{{ old('check_number') }}"
                >
                @error('check_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-12 d-none" id="bondTerms">
                <label id="bondlabel">Bond Terms</label>
                <textarea
                    name="bond_terms"
                    id="bond_terms"
                    class="form-control @error('bond_terms') is-invalid @enderror"
                >{{ old('bond_terms') }}</textarea>
                @error('bond_terms')
                    <div class="invalid-feedback">{{ $message }}</div>
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
        // const isExperience = letterType.value === 'experience';
        const isExperience = letterType.value === 'experience' || letterType.value === 'relieving';

        const isAppointment = letterType.value === 'appointment';
        const isAppointmentWithBond = letterType.value === 'appointment_with_bond';
        const isIncrement = letterType.value === 'increment';
        const isintern = letterType.value === 'intern';
        const isinternCustom = letterType.value === 'intern_custom';
        const isBond = letterType.value === 'bond'  || letterType.value === 'custom_bond';
        const isBondField = letterType.value === 'bond';
        const isCustomBond = letterType.value === 'custom_bond' || letterType.value === 'custom';

        console.log(isinternCustom);
        document.getElementById('relievingField').classList.toggle('d-none', !isExperience);
        // document.getElementById('probationField').classList.toggle('d-none', !isAppointment);
        // document.getElementById('bondField').classList.toggle('d-none', !isAppointment);
         document.getElementById('bondField')
        .classList
        .toggle('d-none', !(isBond || isAppointmentWithBond));

        document.getElementById('incrementOldSalary').classList.toggle('d-none', !isIncrement);
        document.getElementById('incrementNewSalary').classList.toggle('d-none', !isIncrement);
        document.getElementById('incrementEffectiveDate').classList.toggle('d-none', !isIncrement);
        // document.getElementById('incrementPercentage').classList.toggle('d-none', !isIncrement);

        document.getElementById('bondStartDate').classList.toggle('d-none', !(isBond || isAppointmentWithBond));
        document.getElementById('bondEndDate').classList.toggle('d-none', !(isBond || isAppointmentWithBond));
        document.getElementById('bondAmount').classList.toggle('d-none', !(isBond || isintern));
        document.getElementById('bondAmountLabel').textContent = isintern ? 'Stipend' : 'Bond Amount';
        document.getElementById('bondcheckNo').classList.toggle('d-none', !isBond);
        document.getElementById('bondTerms').classList.toggle('d-none', !(isCustomBond || isinternCustom));
        document.getElementById('bondlabel').textContent = isinternCustom ? 'Content' : 'Bond Terms';

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
        if (!bondPeriodInput.value || !bondStartInput.value) {
            bondEndInput.value = '';
            return;
        }

        const period = bondPeriodInput.value.toString();

        // Split Year.Month
        const parts = period.split('.');
        const years = parseInt(parts[0], 10);
        const months = parts[1] ? parseInt(parts[1], 10) : 0;

        // ❌ Invalid months check
        if (months > 11) {
            alert('Months must be between 0 and 11');
            bondPeriodInput.value = '';
            bondEndInput.value = '';
            return;
        }

        const startDate = new Date(bondStartInput.value);

        // Add years & months
        startDate.setFullYear(startDate.getFullYear() + years);
        startDate.setMonth(startDate.getMonth() + months);

        // Format YYYY-MM-DD
        const yyyy = startDate.getFullYear();
        const mm = String(startDate.getMonth() + 1).padStart(2, '0');
        const dd = String(startDate.getDate()).padStart(2, '0');

        bondEndInput.value = `${yyyy}-${mm}-${dd}`;
    }

    bondPeriodInput.addEventListener('input', calculateBondEndDate);
    bondStartInput.addEventListener('change', calculateBondEndDate);
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const employeeSelect = document.getElementById('employeeSelect');
    const letterType     = document.getElementById('letterType');
    const salaryWrapper  = document.getElementById('incrementOldSalary');
    const salaryInput    = document.getElementById('currentSalary');

    // ❌ If required elements are missing, DO NOTHING (prevents crash)
    if (!employeeSelect || !letterType || !salaryWrapper || !salaryInput) {
        return;
    }

    function updateSalaryVisibility() {
        const isIncrement = letterType.value === 'increment';
        const selectedOption = employeeSelect.options[employeeSelect.selectedIndex];

        if (
            isIncrement &&
            selectedOption &&
            selectedOption.dataset.salary !== undefined &&
            selectedOption.dataset.salary !== ''
        ) {
            salaryInput.value = selectedOption.dataset.salary;
            salaryWrapper.classList.remove('d-none');
        } else {
            salaryInput.value = '';
            salaryWrapper.classList.add('d-none');
        }
    }

    employeeSelect.addEventListener('change', updateSalaryVisibility);
    letterType.addEventListener('change', updateSalaryVisibility);

    // initial call
    updateSalaryVisibility();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    const letterType     = document.getElementById('letterType');
    const employeeSelect = document.getElementById('employeeSelect');

    if (!letterType || !employeeSelect) return;

    function toggleEmployeeRequired() {
        const isCustom = letterType.value === 'custom';

        // if (isCustom) {
        //     employeeSelect.removeAttribute('required');
        // } else {
        //     employeeSelect.setAttribute('required', 'required');
        // }
    }

    letterType.addEventListener('change', toggleEmployeeRequired);

    // run once on page load (important for old input / edit)
    // toggleEmployeeRequired();
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('bondPeriod');
    if (!input) return;

    input.addEventListener('input', function () {
        const value = input.value;
        if (!value.includes('.')) return;

        const parts = value.split('.');
        const months = parseInt(parts[1] || 0, 10);

        if (months > 11) {
            input.setCustomValidity('Months must be between 0 and 11');
        } else {
            input.setCustomValidity('');
        }
    });
});
</script>

@endpush
