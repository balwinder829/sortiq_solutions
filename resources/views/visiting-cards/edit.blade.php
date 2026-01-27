@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Visiting Card</h4>

    <form method="POST"
          action="{{ route('visiting-cards.update', $visiting_card) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Name --}}
            <div class="form-group col-md-6">
                <label>Name</label>
                <input type="text" name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $visiting_card->name) }}" required>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Designation --}}
            <div class="form-group col-md-6">
                <label>Designation</label>
                <input type="text" name="designation"
                       class="form-control @error('designation') is-invalid @enderror"
                       value="{{ old('designation', $visiting_card->designation) }}" required>
                @error('designation') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Company --}}
            <div class="form-group col-md-6">
                <label>Company Name</label>
                <input type="text" name="company_name"
                       class="form-control @error('company_name') is-invalid @enderror"
                       value="{{ old('company_name', $visiting_card->company_name) }}" required>
                @error('company_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Primary Phone --}}
            <div class="form-group col-md-6">
                <label>Primary Phone</label>
                <input type="text" name="phone_primary"
                       class="form-control @error('phone_primary') is-invalid @enderror"
                       value="{{ old('phone_primary', $visiting_card->phone_primary) }}"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number"
                       required>
                @error('phone_primary') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Secondary Phone --}}
            <div class="form-group col-md-6">
                <label>Secondary Phone</label>
                <input type="text" name="phone_secondary"
                       class="form-control @error('phone_secondary') is-invalid @enderror"
                       value="{{ old('phone_secondary', $visiting_card->phone_secondary) }}"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number">
                @error('phone_secondary') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $visiting_card->email) }}">
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Address --}}
            <div class="form-group col-md-12">
                <label>Address</label>
                <textarea name="address"
                          class="form-control @error('address') is-invalid @enderror">{{ old('address', $visiting_card->address) }}</textarea>
                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- CURRENT IMAGES --}}
            <div class="form-group col-md-6">
                <label class="fw-semibold">Current Front Image</label><br>
                @if($visiting_card->card_front && file_exists(public_path($visiting_card->card_front)))
                    <img src="{{ asset($visiting_card->card_front) }}"
                         class="img-fluid rounded border mb-2"
                         style="max-height:180px;">
                @else
                    <p class="text-muted">No front image uploaded</p>
                @endif
            </div>

            <div class="form-group col-md-6">
                <label class="fw-semibold">Current Back Image</label><br>
                @if($visiting_card->card_back && file_exists(public_path($visiting_card->card_back)))
                    <img src="{{ asset($visiting_card->card_back) }}"
                         class="img-fluid rounded border mb-2"
                         style="max-height:180px;">
                @else
                    <p class="text-muted">No back image uploaded</p>
                @endif
            </div>

            {{-- REPLACE IMAGES --}}
            <div class="form-group col-md-6">
                <label>Replace Front Image</label>
                <input type="file" name="card_front"
                       class="form-control @error('card_front') is-invalid @enderror">
                @error('card_front') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Replace Back Image</label>
                <input type="file" name="card_back"
                       class="form-control @error('card_back') is-invalid @enderror">
                @error('card_back') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('visiting-cards.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
