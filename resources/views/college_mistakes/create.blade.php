@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Add College Mistake</h3>

    <form method="POST"
          action="{{ route('college-mistakes.store') }}">

        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>College Name</label>

                <input type="text"
                       name="college_name"
                       class="form-control"
                       value="{{ old('college_name') }}"
                       required>
            </div>

            <div class="form-group col-md-6">
                <label>Contact Person</label>

                <input type="text"
                       name="contact_person"
                       class="form-control"
                       value="{{ old('contact_person') }}">
            </div>

            <div class="form-group col-md-6">
                <label>Location</label>

                <input type="text"
                       name="location"
                       class="form-control"
                       value="{{ old('location') }}">
            </div>

            <div class="form-group col-md-6">
                <label>Website</label>

                <input type="text"
                       name="website"
                       class="form-control"
                       value="{{ old('website') }}">
            </div>

            <div class="form-group col-md-12">
                <label>Description</label>

                <textarea name="description"
                          rows="5"
                          class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="form-group col-md-6 mt-3">

                <button type="submit"
                        class="btn btn-primary">
                    Save
                </button>

                <a href="{{ route('college-mistakes.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>

            </div>

        </div>

    </form>

</div>

@endsection