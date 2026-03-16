@extends('layouts.app')

@section('content')

<div class="container">

    <h1 class="page_heading mb-4">Create Category</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.helpdesk.categories.store') }}" method="POST">
        @csrf

        <div class="form-group col-md-6 mb-3">
            <label>Name</label>

            <input type="text" 
                   name="name" 
                   value="{{ old('name') }}"
                   class="form-control @error('name') is-invalid @enderror">

            @error('name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group col-md-6 mb-3">
            <button class="btn btn-primary">Save</button>
            <a href="{{ route('admin.helpdesk.categories.index') }}" class="btn btn-success">Back</a>
        </div>
    </form>

</div>

@endsection
