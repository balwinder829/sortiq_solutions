@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Block Number</h2>

    <form method="POST" action="{{ route('admin.blocked-numbers.store') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Number</label>
            <input type="text"
                   name="number"
                   class="form-control"
                   required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
            @error('number')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-danger">Block Number</button>
        <a href="{{ route('admin.blocked-numbers.index') }}"
           class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
