@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Add Electricity Bill</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('office-expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-row">

                    {{-- Expense Date --}}
                    <div class="form-group col-md-6">
                        <label>Date</label>
                        <input type="date"
                               name="expense_date"
                               value="{{ old('expense_date') }}"
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
                               value="{{ old('title') }}"
                               required 
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
                               value="{{ old('amount') }}"
                               required 
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
                    </div>

                    {{-- Description --}}
                    <div class="form-group col-md-12">
                        <label>Description</label>
                        <textarea name="description"
                                  class="form-control @error('description') is-invalid @enderror"
                                  rows="3">{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <button class="btn btn-primary">Save</button>
                <a href="{{ route('office-expenses.index') }}" class="btn btn-secondary">Back</a>
            </form>
        </div>
    </div>
</div>
@endsection
