@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Technology</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('technologies.update', $technology) }}">
@csrf
@method('PUT')

<div class="row">

    <div class="form-group col-md-6">
        <label>Technology Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', $technology->name) }}"
               required>
    </div>

    <div class="form-group col-md-6">
        <label>Category</label>
        <select name="category" class="form-control" required>
            @foreach(['language','framework','database','concept','aptitude','general'] as $c)
                <option value="{{ $c }}"
                    {{ $technology->category == $c ? 'selected' : '' }}>
                    {{ ucfirst($c) }}
                </option>
            @endforeach
        </select>
    </div>

</div>

<button class="btn btn-primary mt-3">Update</button>
<a href="{{ route('technologies.index') }}" class="btn btn-secondary mt-3">Back</a>

</form>
</div>
@endsection
