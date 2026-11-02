@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Edit Category</h1>
        </div>

        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.attendance-test-options.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <form method="POST"
                  action="{{ route('admin.attendance-test-options.categories.update', $category) }}">
                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Category Name</label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', $category->name) }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Description</label>

                        <input type="text"
                               name="description"
                               class="form-control"
                               value="{{ old('description', $category->description) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Display Order</label>

                        <input type="number"
                               name="sort_order"
                               class="form-control"
                               min="0"
                               value="{{ old('sort_order', $category->sort_order) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="fw-bold">Status</label>

                        <select name="is_active" class="form-select" required>
                            <option value="1"
                                {{ old('is_active', $category->is_active) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('is_active', $category->is_active) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.attendance-test-options.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn"
                            style="background-color:#6b51df;color:#fff;">
                        Update Category
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection