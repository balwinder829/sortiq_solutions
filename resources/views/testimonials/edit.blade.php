@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Testimonial</h3>

    <form method="POST" action="{{ route('testimonials.update', $testimonial->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

        {{-- Name --}}
        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text"
                   name="name"
                   class="form-control"
                   value="{{ old('name', $testimonial->name) }}"
                   required>
        </div>

        {{-- Type --}}
        @php $types = ['services','internship']; @endphp

        <div class="form-group col-md-6">
            <label>Type</label>

            <select name="type" class="form-control" required>
                @foreach($types as $type)
                    <option value="{{ $type }}"
                        {{ old('type', $testimonial->type) == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Image --}}
        <div class="form-group col-md-6">
            <label>Image</label>
            <input type="file" name="image" class="form-control">

            @if($testimonial->image)
                <img src="{{ asset($testimonial->image) }}" width="80" class="mt-2">
            @endif
        </div>

        {{-- Status --}}
        <div class="form-group col-md-6">
            <label>Status</label><br>

            <input type="checkbox" name="status" value="1"
                {{ $testimonial->status ? 'checked' : '' }}>
            <span>Active</span>
        </div>

        {{-- Description --}}
        <div class="form-group col-md-12">
            <label>Description</label>

            <textarea name="description"
                      class="form-control"
                      rows="4">{{ old('description', $testimonial->description) }}</textarea>
        </div>

        <div class="form-group col-md-6">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('testimonials.index') }}" class="btn btn-secondary ml-2">Back</a>
        </div>

        </div>
    </form>
</div>
@endsection