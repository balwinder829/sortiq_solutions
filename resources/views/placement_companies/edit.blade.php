@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <h4>Edit Placement Company</h4>
        </div>

        <div class="card-body">
            <form method="POST"
                  action="{{ route('placement-companies.update', $company->id) }}">
                @csrf
                @method('PUT')

                <div class="form-row">

                    {{-- NAME --}}
                    <div class="form-group col-md-6">
                        <label>Company Name</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $company->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- CONTACT PERSON --}}
                    <div class="form-group col-md-6">
                        <label>Contact Person</label>
                        <input type="text"
                               name="contact_person"
                               value="{{ old('contact_person', $company->contact_person) }}"
                               class="form-control @error('contact_person') is-invalid @enderror">
                        @error('contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- PHONE --}}
                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone', $company->phone) }}"
                               class="form-control @error('phone') is-invalid @enderror"
                                pattern="[0-9]{10}"
                        title="Enter a valid 10-digit mobile number"
                        maxlength="10"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- WEBSITE --}}
                    <div class="form-group col-md-6">
                        <label>Website</label>
                        <input type="url"
                               name="website"
                               value="{{ old('website', $company->website) }}"
                               class="form-control @error('website') is-invalid @enderror">
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- ADDRESS --}}
                    <div class="form-group col-md-12">
                        <label>Address</label>
                        <textarea name="address"
                                  rows="2"
                                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $company->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- REMARKS --}}
                    <div class="form-group col-md-12">
                        <label>Remarks</label>
                        <textarea name="remarks"
                                  rows="2"
                                  class="form-control @error('remarks') is-invalid @enderror">{{ old('remarks', $company->remarks) }}</textarea>
                        @error('remarks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- STATUS --}}
                    <div class="form-group col-md-6">
                        <label>Status</label>
                        <select name="status"
                                class="form-control @error('status') is-invalid @enderror">
                            <option value="active"
                                {{ old('status', $company->status) == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive"
                                {{ old('status', $company->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

                <button class="btn btn-primary">Update</button>
                <a href="{{ route('placement-companies.index') }}"
                   class="btn btn-secondary">
                    Back
                </a>

            </form>
        </div>
    </div>
</div>
@endsection
