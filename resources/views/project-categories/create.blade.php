@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-8">
            <h1 class="page_heading">Add Project Category</h1>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('project-categories.store') }}" method="POST">
        @csrf

        <div class="row">

            {{-- Category Name --}}
            <div class="form-group col-md-6 mb-3">
                <label><strong>Name</strong></label>

                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       placeholder="e.g. Web Development"
                       required>

                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group col-md-6 mb-3">
                <label><strong>Status</strong></label>

                <select name="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required>

                    <option value="active"
                        {{ old('status', 'active') == 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status') == 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

                @error('status')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

        </div>

        <button class="btn btn-success">
            Add
        </button>

        <a href="{{ route('project-categories.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </form>

</div>
@endsection