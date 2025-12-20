@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Event Cost</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('event-expenses.update', $expense->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date" name="expense_date"
                               value="{{ old('expense_date', $expense->expense_date) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               required class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <input type="text" name="title"
                               value="{{ old('title', $expense->title) }}"
                               required class="form-control">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Amount</label>
                        <input type="number" step="0.01"
                               name="amount"
                               value="{{ old('amount', $expense->amount) }}"
                               required class="form-control">
                    </div>

                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description"
                                  rows="3"
                                  class="form-control">{{ old('description', $expense->description) }}</textarea>
                    </div>

                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('event-expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
