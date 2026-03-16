@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Add Paper Expense</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('office-paper-expenses.store') }}" method="POST">
                @csrf

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date"
                               name="expense_date"
                               value="{{ old('expense_date') }}"
                               max="{{ now()->format('Y-m-d') }}"
                               required
                               class="form-control @error('expense_date') is-invalid @enderror">
                        @error('expense_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <input type="text"
                               name="title"
                               value="{{ old('title') }}"
                               required
                               class="form-control @error('title') is-invalid @enderror">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Quantity</label>
                        <input type="text"
                               name="quantity"
                               value="{{ old('quantity') }}"
                               required
                               class="form-control @error('quantity') is-invalid @enderror">
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Total Amount</label>
                        <input type="number"
                               step="0.01"
                               name="total_amount"
                               value="{{ old('total_amount') }}"
                               required
                               class="form-control @error('total_amount') is-invalid @enderror">
                        @error('total_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Other Charges</label>
                        <input type="number"
                               step="0.01"
                               name="other_charges"
                               value="{{ old('other_charges') }}"
                               
                               class="form-control @error('other_charges') is-invalid @enderror">
                        @error('other_charges')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description"
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                <button class="btn btn-primary">Save</button>
                <a href="{{ route('office-paper-expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
