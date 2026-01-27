@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Salary Slip</h4>

    <form method="POST" action="{{ route('salary-slips.update', $salarySlip) }}">
        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text" name="emp_name"
                       class="form-control"
                       value="{{ old('emp_name', $salarySlip->emp_name) }}"
                       required>
            </div>

            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text" name="emp_code"
                       class="form-control"
                       value="{{ old('emp_code', $salarySlip->emp_code) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Designation</label>
                <input type="text" name="designation"
                       class="form-control"
                       value="{{ old('designation', $salarySlip->designation) }}">
            </div>

            <div class="form-group col-md-3">
                <label>Month</label>
                <input type="text" name="month"
                       class="form-control"
                       value="{{ old('month', $salarySlip->month) }}"
                       required>
            </div>

            <div class="form-group col-md-3">
                <label>Year</label>
                <input type="number" name="year"
                       class="form-control"
                       value="{{ old('year', $salarySlip->year) }}"
                       required>
            </div>

            <div class="form-group col-md-4">
                <label>Basic Salary</label>
                <input type="number" name="basic_salary"
                       class="form-control"
                       value="{{ old('basic_salary', $salarySlip->basic_salary) }}"
                       required>
            </div>

            <div class="form-group col-md-4">
                <label>Allowances</label>
                <input type="number" name="allowances"
                       class="form-control"
                       value="{{ old('allowances', $salarySlip->allowances) }}">
            </div>

            <div class="form-group col-md-4">
                <label>Deductions</label>
                <input type="number" name="deductions"
                       class="form-control"
                       value="{{ old('deductions', $salarySlip->deductions) }}">
            </div>

            <div class="form-group col-md-6">
                <label>Net Salary</label>
                <input type="number" name="net_salary"
                       class="form-control"
                       value="{{ old('net_salary', $salarySlip->net_salary) }}"
                       required>
            </div>

            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date" name="issue_date"
                       class="form-control"
                       value="{{ old('issue_date', $salarySlip->issue_date) }}"
                       required>
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ old('email', $salarySlip->email) }}"
                       required>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('salary-slips.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
