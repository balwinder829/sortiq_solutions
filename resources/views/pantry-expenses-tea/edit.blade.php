@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Tea Pantry Expense</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('tea-pantry-expenses.update', $expense->id) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date"
                               name="expense_date"
                               value="{{ old('expense_date', $expense->expense_date) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               required
                               class="form-control @error('expense_date') is-invalid @enderror">
                        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <input type="text"
                               name="title"
                               value="{{ old('title', $expense->title) }}"
                               required
                               class="form-control @error('title') is-invalid @enderror">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Milk Amount</label>
                        <input type="number"
                               step="0.01"
                               name="milk_amount"
                               value="{{ old('milk_amount', $expense->milk_amount) }}"
                               required
                               class="form-control @error('milk_amount') is-invalid @enderror">
                        @error('milk_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                     <div class="form-group col-md-6">
                        <label>Other Charges</label>
                        <input type="number"
                               step="0.01"
                               name="other_charges"
                               value="{{ old('other_charges', $expense->other_charges) }}"
                               
                               class="form-control @error('other_charges') is-invalid @enderror">
                        @error('other_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description"
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('tea-pantry-expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
