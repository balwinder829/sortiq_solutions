@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Reference</h2>

    <form action="{{ route('references.update', $reference) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="{{ $reference->name }}" required>
        </div>
         
        <button class="btn btn-success">Update</button>
        <a href="{{ route('references.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
