@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Generate Trainer Letter</h4>

    <form method="POST" action="{{ route('trainer-letters.store') }}">
        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>Select Trainer</label>

                <select name="trainer_id" class="form-control" required>
                    <option value="">Select Trainer</option>

                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}">
                            {{ $trainer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select name="letter_type" class="form-control" required>
                    <option value="trainer_consent">
                        Trainer Consent Letter
                    </option>
                </select>
            </div>

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

        <a href="{{ route('trainer-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection