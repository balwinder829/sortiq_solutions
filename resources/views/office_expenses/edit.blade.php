@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Electricity Bill</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('office-expenses.update', $expense->id) }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-row">

                    {{-- Expense Date --}}
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date"
                               name="expense_date"
                               value="{{ old('expense_date', $expense->expense_date) }}"
                               max="{{ now()->format('Y-m-d') }}"
                               required 
                               class="form-control @error('expense_date') is-invalid @enderror">

                        @error('expense_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div class="form-group col-md-6">
                        <label>Title</label>
                        <input type="text"
                               name="title"
                               required 
                               value="{{ old('title', $expense->title) }}"
                               class="form-control @error('title') is-invalid @enderror">

                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="form-group col-md-6">
                        <label>Amount</label>
                        <input type="number"
                               step="0.01"
                               name="amount"
                               required 
                               value="{{ old('amount', $expense->amount) }}"
                               class="form-control @error('amount') is-invalid @enderror">

                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Image --}}
                    <div class="form-group col-md-6">
                        <label>Image</label>
                        <input type="file"
                               name="image" 
                               class="form-control @error('image') is-invalid @enderror">

                        @error('image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        @if($expense->image)
                            <img src="{{ asset($expense->image) }}" width="80" class="mt-2">
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description"
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $expense->description ?? '') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('office-expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
