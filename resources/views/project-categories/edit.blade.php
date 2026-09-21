@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-2">
        <div class="col-md-8">
            <h1 class="page_heading">Edit Project Category</h1>
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

    <form action="{{ route('project-categories.update', $projectCategory) }}"
          method="POST">

        @csrf
        @method('PUT')

        <div class="row">

            {{-- Category Name --}}
            <div class="form-group col-md-6 mb-3">

                <label><strong>Name</strong></label>

                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $projectCategory->name) }}"
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
                        {{ old('status', $projectCategory->status) == 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $projectCategory->status) == 'inactive' ? 'selected' : '' }}>
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
            Update
        </button>

        <a href="{{ route('project-categories.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </form>

</div>
@endsection