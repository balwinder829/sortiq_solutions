@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Add Workshop Expense</h3>

    <form method="POST" action="{{ route('workshop-expenses.store') }}">

        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>Session</label>

                <input type="text"
                       class="form-control"
                       value="{{ ucwords($activeSession->session_name) }}"
                       readonly>
            </div>

            <div class="form-group col-md-6">
                <label>Workshop</label>

                <select name="workshop_id"
                        class="form-select select2"
                        required>

                    <option value="">Select Workshop</option>

                    @foreach($workshops as $workshop)
                        <option value="{{ $workshop->id }}">
                            {{ $workshop->title }}
                            -
                            {{ $workshop->college->FullName ?? '' }}
                        </option>
                    @endforeach

                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Title</label>

                <input type="text"
                       name="title"
                       class="form-control"
                       value="{{ old('title') }}"
                       required>
            </div>

            <div class="form-group col-md-3">
                <label>Travel Expense</label>

                <input type="number"
                       step="0.01"
                       name="expense"
                       class="form-control"
                       value="{{ old('expense') }}"
                       required>
            </div>

            <div class="form-group col-md-3">
                <label>Other Expense</label>

                <input type="number"
                       step="0.01"
                       name="other_expense"
                       class="form-control"
                       value="{{ old('other_expense') }}">
            </div>

            <div class="form-group col-md-12">
                <label>Description</label>

                <textarea name="description"
                          rows="4"
                          class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="form-group col-md-6 mt-3">
                <button type="submit" class="btn btn-primary">
                    Save
                </button>

                <a href="{{ route('workshop-expenses.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </div>

        </div>

    </form>

</div>

@endsection