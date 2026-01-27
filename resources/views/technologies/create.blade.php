@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Technology</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('technologies.store') }}">
@csrf

<div class="row">

    <div class="form-group col-md-6">
        <label>Technology Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name') }}"
               placeholder="PHP / Laravel / MySQL"
               required>
    </div>

    <div class="form-group col-md-6">
        <label>Category</label>
        <select name="category" class="form-control" required>
            <option value="">Select Category</option>
            <option value="language">Language</option>
            <option value="framework">Framework</option>
            <option value="database">Database</option>
            <option value="concept">Concept</option>
            <option value="aptitude">Aptitude</option>
            <option value="general">General</option>
        </select>
    </div>

</div>

<button class="btn btn-primary mt-3">Save</button>
<a href="{{ route('technologies.index') }}" class="btn btn-secondary mt-3">Back</a>

</form>
</div>
@endsection
