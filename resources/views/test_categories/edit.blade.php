@extends('layouts.app')

@section('content')
<div class="container">

<h3>Edit Test Category</h3>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<a href="{{ route('test-categories.index') }}" class="btn btn-dark mb-3">Back</a>

<form method="POST" action="{{ route('test-categories.update', $test_category->id) }}">
    @csrf @method('PUT')

    <div class="mb-3">
        <label>Category Name</label>
        <input type="text" name="name"
               class="form-control"
               value="{{ $test_category->name }}" required>
    </div>

    <button class="btn" style="background-color:#6b51df;color:white;">
        Update Category
    </button>
</form>

</div>
@endsection
