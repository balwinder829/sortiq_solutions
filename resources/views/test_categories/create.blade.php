@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-6">
            <h1 class="page_heading">Create Test Category</h1>
        </div>
        <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    
                <a href="{{ route('test-categories.index') }}" class="btn mb-3"  style="background-color:#6b51df;color:white;">
                    Back
                </a>
            </div>
        </div>
    </div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif


<form method="POST" action="{{ route('test-categories.store') }}">
    @csrf

    <div class="mb-3">
        <label>Category Name</label>
        <input type="text" name="name" 
               class="form-control" required>
    </div>

    <button class="btn" style="background-color:#6b51df;color:white;">
        Save Category
    </button>
</form>

</div>
@endsection
