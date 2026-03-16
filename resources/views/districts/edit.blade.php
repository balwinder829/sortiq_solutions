@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit District</h3>

    <form method="POST" action="{{ route('districts.update',$district->id) }}">
        <div class="row">
        @csrf
        @method('PUT')

       <div class="form-group col-md-6">
            <label>Select State</label>
            <select name="state_id" 
                    class="form-control @error('state_id') is-invalid @enderror" 
                    required>
                <option value="" disabled>Select State</option>

                @foreach($states as $state)
                    <option value="{{ $state->id }}"
                        {{ old('state_id', $district->state_id) == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>

            @error('state_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    
        
 
        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text" 
                   name="name" 
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $district->name) }}"
                   placeholder="Name" 
                   required>

            @error('name')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

      

        <div class="form-group col-md-6">
            <button type="submit" class="btn btn-primary mt-2">Update</button>
            <a href="{{ route('districts.index') }}" class="btn btn-secondary mt-1 ml-2">Back</a>
        </div>

        </div>
    </form>
</div>
@endsection
