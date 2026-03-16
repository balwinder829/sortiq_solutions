@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit State</h3>

    <form method="POST" action="{{ route('states.update',$state->id) }}">
        <div class="row">
        @csrf
        @method('PUT')

        {{-- Title --}}
        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name',$state->name) }}"
                   placeholder="Title" 
                   required>
            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    
        
 

        {{-- Name --}}
        <div class="form-group col-md-6">
            <label>State Code</label>
            <input type="text" 
                   name="code"
                   class="form-control @error('code') is-invalid @enderror"
                   value="{{ old('code',$state->code) }}"
                   placeholder="State Code" 
                   required>
            @error('code')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

      

        <div class="form-group col-md-6">
            <button type="submit" class="btn btn-primary mt-2">Update</button>
            <a href="{{ route('states.index') }}" class="btn btn-secondary mt-1 ml-2">Back</a>
        </div>

        </div>
    </form>
</div>
@endsection
