@extends('layouts.app')

@section('content')

<div class="container">

    <div class="row mb-3">

        <div class="col-md-6">
            <h2>Edit Blocked Number</h2>
        </div>

        <div class="col-md-6 text-end">
            <a href="{{ route('admin.blocked-numbers.index') }}"
               class="btn btn-secondary">
                Back
            </a>
        </div>

    </div>

    <form method="POST"
          action="{{ route('admin.blocked-numbers.update',$blocked) }}">

        @csrf
        @method('PUT')

        <div class="mb-3">

            <label class="form-label">
                Number
            </label>

            <input type="text"
                   name="number"
                   class="form-control"
                   value="{{ old('number',$blocked->number) }}"
                   required
                   minlength="10"
                   maxlength="10"
                   pattern="[0-9]{10}"
                   title="Enter a valid 10-digit mobile number">

            @error('number')
                <div class="text-danger">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <button class="btn btn-primary">
            Update Number
        </button>

        <a href="{{ route('admin.blocked-numbers.index') }}"
           class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection