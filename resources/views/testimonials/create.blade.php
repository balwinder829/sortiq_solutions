@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Add Testimonial</h3>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    <form method="POST" action="{{ route('testimonials.store') }}" enctype="multipart/form-data">
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

        {{-- Type --}}
        @php $types = ['services','internship']; @endphp

        <div class="form-group col-md-6">
            <label>Type</label>

            <select name="type"
                    class="form-control @error('type') is-invalid @enderror"
                    required>

                <option value="" disabled {{ old('type') ? '' : 'selected' }}>--Select--</option>

                @foreach($types as $type)
                    <option value="{{ $type }}"
                        {{ old('type') == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach

            </select>

            @error('type')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Image --}}
        <div class="form-group col-md-6">
            <label>Image</label>
            <input type="file"
                   name="image"
                   class="form-control @error('image') is-invalid @enderror">

            @error('image')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6">
            <label>Status</label><br>

            <input type="checkbox" name="status" value="1" checked>
            <span>Active</span>
        </div>

        {{-- Description --}}
        <div class="form-group col-md-12">
            <label>Description</label>

            <textarea name="description"
                      rows="4"
                      class="form-control @error('description') is-invalid @enderror"
                      placeholder="Enter testimonial...">{{ old('description') }}</textarea>

            @error('description')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="form-group col-md-6">
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary ml-2">Back</a>
        </div>

        </div>
    </form>
</div>
@endsection