@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add District</h3>
<!-- @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif -->

    <form method="POST" action="{{ route('districts.store') }}">
        <div class="row">
        @csrf

        <div class="form-group col-md-6">
        <label>Select State</label>
        <select name="state_id" 
                class="form-control @error('state_id') is-invalid @enderror" 
                required>
            <option value="" disabled>Select State</option>
            @foreach($states as $state)
                <option value="{{ $state->id }}"
                    {{ old('state_id') == $state->id ? 'selected' : '' }}>
                    {{ $state->name }}
                </option>
            @endforeach
        </select>

        @error('state_id')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

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
     

 

        
         <div class="form-group col-md-6">
        <button type="submit" class="btn btn-primary mt-2">Save</button>
         <a href="{{ route('districts.index') }}" class="btn btn-secondary mt-2 ml-2">Back</a>
    </div>
</div>
    </form>
</div>
@endsection
