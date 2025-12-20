@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Department</h4>
        </div>
        <div class="card-body">
            <!-- Display Validation Errors -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('departments.update', $department->id) }}" method="POST">
                @csrf
                @method('PUT') <!-- Important for PUT request -->

                <div class="form-group">
                    <label for="name">Department Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control" 
                        value="{{ old('name', $department->name) }}" 
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary mt-2">Update Department</button>
                <a href="{{ route('departments.index') }}" class="btn btn-secondary mt-2">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
