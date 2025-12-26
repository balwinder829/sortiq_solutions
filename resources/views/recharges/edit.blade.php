@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Recharge #{{ $recharge->id }}</h4>

    <form method="POST" action="{{ route('recharges.update', $recharge) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Employee (name) --}}
            <div class="form-group col-md-6 mb-3">
                <label>Employee (name)</label>
                <input type="text"
                       name="employee_name"
                       class="form-control @error('employee_name') is-invalid @enderror"
                       value="{{ old('employee_name', $recharge->employee_name ?? '') }}"
                       placeholder="Employee name">
                @error('employee_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Mobile Number --}}
            <div class="form-group col-md-6 mb-3">
                <label>Mobile Number *</label>
                <input type="text"
                       name="mobile_number"
                       class="form-control @error('mobile_number') is-invalid @enderror"
                       value="{{ old('mobile_number', $recharge->mobile_number ?? '') }}"
                        required
                       minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Enter a valid mobile number">
                @error('mobile_number')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Operator --}}
            <div class="form-group col-md-6 mb-3">
                <label>Operator</label>
                <input type="text"
                       name="operator"
                       class="form-control @error('operator') is-invalid @enderror"
                       value="{{ old('operator', $recharge->operator ?? '') }}"
                       placeholder="Operator name">
                @error('operator')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Amount --}}
            <div class="form-group col-md-6 mb-3">
                <label>Amount *</label>
                <input type="number"
                       name="amount"
                       class="form-control @error('amount') is-invalid @enderror"
                       value="{{ old('amount', $recharge->amount ?? '') }}"
                       required
                       step="0.01"
                       min="0.01"
                       placeholder="e.g., 100.00">
                @error('amount')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Reference --}}
            <div class="form-group col-md-6 mb-3">
                <label>Reference</label>
                <input type="text"
                       name="reference"
                       class="form-control @error('reference') is-invalid @enderror"
                       value="{{ old('reference', $recharge->reference ?? '') }}"
                       placeholder="Transaction / reference ID (optional)">
                @error('reference')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group col-md-6 mb-3">
                <label>Status</label>
                @php $st = old('status', $recharge->status ?? 'pending'); @endphp
                <select name="status"
                        class="form-control @error('status') is-invalid @enderror">
                    <option value="pending" {{ $st === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ $st === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ $st === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ $st === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
                @error('status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Recharge Time --}}
            <div class="form-group col-md-6 mb-3">
                <label>Recharge Time</label>
                <input type="datetime-local"
                       name="recharged_at"
                       class="form-control @error('recharged_at') is-invalid @enderror"
                       value="{{ old('recharged_at', isset($recharge->recharged_at) && $recharge->recharged_at ? $recharge->recharged_at->format('Y-m-d\TH:i') : '') }}">
                <small class="form-text text-muted">Leave blank to clear (if status not completed) or set automatically when marking completed.</small>
                @error('recharged_at')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Days --}}
            <div class="form-group col-md-6 mb-3">
                <label>Days</label>
                <input type="number"
                       name="days"
                       class="form-control @error('days') is-invalid @enderror"
                       value="{{ old('days', isset($recharge->days) ? $recharge->days : '') }}"
                       min="0"
                       placeholder="e.g., 30">
                @error('days')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="form-group col-12 mb-3">
                <label>Notes</label>
                <textarea name="notes"
                          class="form-control @error('notes') is-invalid @enderror"
                          rows="3"
                          placeholder="Optional notes...">{{ old('notes', $recharge->notes ?? '') }}</textarea>
                @error('notes')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('recharges.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection