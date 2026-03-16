@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add State</h3>
<!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->

    <form method="POST" action="{{ route('states.store') }}">
        <div class="row">
        @csrf

        {{-- Name --}}
        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name') }}"
                   placeholder="Name" 
                   required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
     


       
         {{-- Code --}}
        <div class="form-group col-md-6">
            <label>State Code</label>
            <input type="text" 
                   name="code"
                   class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code') }}"
                   placeholder="State Code" 
                   required>
            @error('code')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        
         <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary mt-2">Save</button>
         <a href="{{ route('states.index') }}" class="btn btn-secondary mt-1 ml-2">Back</a>
    </div>
</div>
    </form>
</div>
@endsection
