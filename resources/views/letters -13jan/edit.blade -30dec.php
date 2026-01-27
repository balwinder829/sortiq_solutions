@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Letter</h4>

    <form method="POST" action="{{ route('letters.update', $letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <input type="text" class="form-control" value="{{ ucfirst($letter->letter_type) }}" readonly>
            </div>

            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text" name="emp_code" class="form-control"
                       value="{{ old('emp_code', $letter->emp_code) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text" name="emp_name" class="form-control"
                       value="{{ old('emp_name', $letter->emp_name) }}" required>
            </div>

            <div class="form-group col-md-6">
                <label>Position</label>
                <input type="text" name="position" class="form-control"
                       value="{{ old('position', $letter->position) }}" required>
            </div>

            <div class="form-group col-md-6">
                <label>Joining Date</label>
                <input type="date" name="joining_date" class="form-control"
                       value="{{ old('joining_date', $letter->joining_date) }}" required>
            </div>

            @if($letter->letter_type === 'experience')
            <div class="form-group col-md-6">
                <label>Relieving Date</label>
                <input type="date" name="relieving_date" class="form-control"
                       value="{{ old('relieving_date', $letter->relieving_date) }}">
            </div>
            @endif

            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control"
                       value="{{ old('issue_date', $letter->issue_date) }}" required>
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control"
                       value="{{ old('email', $letter->email) }}" required>
            </div>

            <div class="form-group col-md-6">
                <label>Salary</label>
                <input type="number" name="salary" class="form-control"
                       value="{{ old('salary', $letter->salary) }}">
            </div>

            @if($letter->letter_type === 'appointment')
            <div class="form-group col-md-6">
                <label>Probation Period</label>
                <input type="number" name="probation_period" class="form-control"
                       value="{{ old('probation_period', $letter->probation_period) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Bond Period</label>
                <input type="number" name="bond_period" class="form-control"
                       value="{{ old('bond_period', $letter->bond_period) }}">
            </div>
            @endif

            @if($letter->letter_type === 'increment')
                <div class="form-group col-md-6">
                    <label>Old Salary</label>
                    <input type="number" name="old_salary" class="form-control"
                           value="{{ old('old_salary', $letter->old_salary) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>New Salary</label>
                    <input type="number" name="new_salary" class="form-control"
                           value="{{ old('new_salary', $letter->new_salary) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Effective Date</label>
                    <input type="date" name="effective_date" class="form-control"
                           value="{{ old('effective_date', $letter->effective_date) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Increment Percentage</label>
                    <input type="number" step="0.01" name="increment_percentage" class="form-control"
                           value="{{ old('increment_percentage', $letter->increment_percentage) }}">
                </div>
            @endif

            @if($letter->letter_type === 'bond')
                <div class="form-group col-md-6">
                    <label>Bond Start Date</label>
                    <input type="date" name="bond_start_date" class="form-control"
                           value="{{ old('bond_start_date', $letter->bond_start_date) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Bond End Date</label>
                    <input type="date" name="bond_end_date" class="form-control"
                           value="{{ old('bond_end_date', $letter->bond_end_date) }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Bond Amount</label>
                    <input type="number" name="bond_amount" class="form-control"
                           value="{{ old('bond_amount', $letter->bond_amount) }}">
                </div>
                <div class="form-group col-md-12">
                    <label>Bond Terms</label>
                    <textarea name="bond_terms" class="form-control">{{ old('bond_terms', $letter->bond_terms) }}</textarea>
                </div>
            @endif

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('letters.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
