@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Assign Salary Structure</h4>

    {{-- Info if salary already exists --}}
    @if(isset($salaryStructure))
        <div class="alert alert-info">
            Salary is already assigned for this employee.
            Updating will create a new salary record and deactivate the previous one.
        </div>
    @endif

    <form method="POST" action="{{ route('salary-structure.store', $employee) }}">
        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text"
                       class="form-control"
                       value="{{ $employee->emp_code }}"
                       disabled>
            </div>

            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text"
                       class="form-control"
                       value="{{ $employee->emp_name }}"
                       disabled>
            </div>

            <div class="form-group col-md-6">
                <label>Basic Salary</label>
                <input type="number"
                       name="basic_salary"
                       class="form-control"
                       value="{{ old('basic_salary', $salaryStructure->basic_salary ?? '') }}"
                       required>
            </div>

            <div class="form-group col-md-6">
                <label>HRA</label>
                <input type="number"
                       name="hra"
                       class="form-control"
                       value="{{ old('hra', $salaryStructure->hra ?? 0) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Allowance</label>
                <input type="number"
                       name="allowance"
                       class="form-control"
                       value="{{ old('allowance', $salaryStructure->allowance ?? 0) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Deduction</label>
                <input type="number"
                       name="deduction"
                       class="form-control"
                       value="{{ old('deduction', $salaryStructure->deduction ?? 0) }}">
            </div>

             <div class="form-group col-md-6">
                <label>Bank Account Number</label>
                <input type="number"
                       name="account_number"
                       class="form-control"
                       value="{{ old('deduction', $salaryStructure->account_number ?? 0) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Effective From</label>
                <input type="date"
                       name="effective_from"
                       class="form-control"
                       value="{{ old('effective_from', $salaryStructure->effective_from ?? now()->toDateString()) }}"
                       required>
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">
            Save Salary
        </button>

        <a href="{{ route('employees.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>
    </form>
</div>
@endsection
