@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Visiting Card</h4>

    <form method="POST"
          action="{{ route('visiting-cards.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="row">

            <div class="form-group col-md-6">
                <label>Name</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}" required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Designation</label>
                <input type="text" name="designation"
                       class="form-control @error('designation') is-invalid @enderror"
                       value="{{ old('designation') }}" required>
                @error('designation')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Company Name</label>
                <input type="text" name="company_name"
                       class="form-control @error('company_name') is-invalid @enderror"
                       value="{{ old('company_name') }}" required>
                @error('company_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Primary Phone</label>
                <input type="text" name="phone_primary"
                       class="form-control @error('phone_primary') is-invalid @enderror"
                       value="{{ old('phone_primary') }}"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number"
                       required>
                @error('phone_primary')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Secondary Phone</label>
                <input type="text" name="phone_secondary"
                       class="form-control @error('phone_secondary') is-invalid @enderror"
                       value="{{ old('phone_secondary') }}"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number">
                @error('phone_secondary')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}">
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-12">
                <label>Address</label>
                <textarea name="address"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address') }}</textarea>
                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Card Front Image</label>
                <input type="file" name="card_front"
                       class="form-control @error('card_front') is-invalid @enderror">
                @error('card_front')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Card Back Image</label>
                <input type="file" name="card_back"
                       class="form-control @error('card_back') is-invalid @enderror">
                @error('card_back')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('visiting-cards.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
