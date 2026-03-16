@extends('layouts.app')

@section('content')

<div class="container">

    <h1 class="page_heading mb-4">Edit Category</h1>

    <form action="{{ route('admin.helpdesk.categories.update',$category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="form-group col-md-6 mb-3">
                <label>Name</label>
                <input type="text" name="name" value="{{ $category->name }}" class="form-control @error('name') is-invalid @enderror">
                @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
            </div>
        </div>

        <button class="btn btn-primary">Update</button>
        <a href="{{ route('admin.helpdesk.categories.index') }}" class="btn btn-success">Back</a>
    </form>

</div>

@endsection
