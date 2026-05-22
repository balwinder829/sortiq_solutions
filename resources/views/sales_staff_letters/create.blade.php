@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Generate Sales Staff Letter</h4>

    <form method="POST"
          action="{{ route('sales-staff-letters.store') }}">

        @csrf

        <div class="row">

            {{-- Sales Staff --}}
            <div class="form-group col-md-6">
                <label>Select Sales Staff</label>

                <select name="sales_staff_id"
                        class="form-control"
                        required>

                    <option value="">
                        Select Sales Staff
                    </option>

                    @foreach($salesStaff as $staff)
                        <option value="{{ $staff->id }}">
                            {{ $staff->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select name="letter_type"
                        class="form-control"
                        required>

                    <option value="trainer_consent">
                        Sales Staff Consent Letter
                    </option>

                </select>
            </div>

            {{-- Employee ID --}}
            <div class="form-group col-md-6 mt-3">
                <label>Employee ID</label>

                <input
                    type="text"
                    name="emp_id"
                    class="form-control"
                    value="{{ old('emp_id') }}"
                    required
                >
            </div>

            {{-- Month of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Month Of Deduction</label>

                <input
                    type="text"
                    name="month_of_deduction"
                    class="form-control"
                    value="{{ old('month_of_deduction') }}"
                    required
                >
            </div>

            {{-- Year of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Year Of Deduction</label>

                <input
                    type="text"
                    name="year_of_deduction"
                    class="form-control"
                    value="{{ old('year_of_deduction') }}"
                    required
                >
            </div>

            {{-- Sale Target --}}
            <div class="form-group col-md-6 mt-3">
                <label>Sale Target</label>

                <input
                    type="text"
                    name="sale_target"
                    class="form-control"
                    value="{{ old('sale_target') }}"
                    required
                >
            </div>

            {{-- Amount of Deduction --}}
            <div class="form-group col-md-6 mt-3">
                <label>Amount Of Deduction</label>

                <input
                    type="number"
                    step="0.01"
                    name="amount_of_deduction"
                    class="form-control"
                    value="{{ old('amount_of_deduction') }}"
                    required
                >
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6 mt-3">
                <label>Issue Date</label>

                <input
                    type="date"
                    name="issue_date"
                    class="form-control"
                    value="{{ now()->toDateString() }}"
                    required
                >
            </div>

        </div>

        <button class="btn btn-primary mt-3">
            Save
        </button>

        <a href="{{ route('sales-staff-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection