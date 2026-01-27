@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Salary Slip</h4>

    <form method="POST" action="{{ route('salary-slips.store') }}">
        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text" name="emp_name" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text" name="emp_code" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label>Designation</label>
                <input type="text" name="designation" class="form-control">
            </div>

            <div class="form-group col-md-3">
                <label>Month</label>
                <input type="text" name="month" class="form-control" placeholder="e.g. March" required>
            </div>

            <div class="form-group col-md-3">
                <label>Year</label>
                <input type="number" name="year" class="form-control" required>
            </div>

            <div class="form-group col-md-4">
                <label>Basic Salary</label>
                <input type="number" name="basic_salary" class="form-control" required>
            </div>

            <div class="form-group col-md-4">
                <label>Allowances</label>
                <input type="number" name="allowances" class="form-control">
            </div>

            <div class="form-group col-md-4">
                <label>Deductions</label>
                <input type="number" name="deductions" class="form-control">
            </div>

            <div class="form-group col-md-6">
                <label>Net Salary</label>
                <input type="number" name="net_salary" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date" name="issue_date" class="form-control" required>
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('salary-slips.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
