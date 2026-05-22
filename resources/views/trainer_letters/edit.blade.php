@extends('layouts.app')

@section('content')

<div class="container">

    <h4>Edit Trainer Letter</h4>

    <form method="POST"
          action="{{ route('trainer-letters.update', $letter) }}">

        @csrf
        @method('PUT')

        <div class="row">

            <div class="form-group col-md-6">
                <label>Trainer</label>

                <select name="trainer_id"
                        class="form-control"
                        required>

                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ $letter->trainer_id == $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select name="letter_type"
                        class="form-control">

                    <option value="trainer_consent" selected>
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
                    value="{{ $letter->issue_date }}"
                    required
                >
            </div>

        </div>

        <button class="btn btn-primary mt-3">
            Update
        </button>

        <a href="{{ route('trainer-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection