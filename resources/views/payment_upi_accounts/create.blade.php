@extends('layouts.app')

@section('content')

<div class="container">

    {{-- ================================
        PAGE HEADER
    ================================= --}}
    <div class="row mb-2">

        <div class="col-md-8">

            <h1 class="page_heading">
                Add Payment UPI / QR
            </h1>

        </div>

    </div>


    {{-- ================================
        VALIDATION ERRORS
    ================================= --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <ul>

                @foreach($errors->all() as $e)

                    <li>{{ $e }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('payment-upi-accounts.store') }}"
          method="POST"
          enctype="multipart/form-data">

        @csrf


        <div class="row">

            {{-- UPI Name --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>UPI Name</strong>
                </label>

                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name') }}"
                       maxlength="150"
                       placeholder="Example: Main Academy UPI"
                       required>

                @error('name')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- Provider --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>UPI Provider</strong>
                </label>

                <select name="provider"
                        class="form-control @error('provider') is-invalid @enderror">

                    <option value="">
                        -- Select Provider --
                    </option>

                    <option value="Google Pay"
                        {{ old('provider') == 'Google Pay' ? 'selected' : '' }}>
                        Google Pay
                    </option>

                    <option value="PhonePe"
                        {{ old('provider') == 'PhonePe' ? 'selected' : '' }}>
                        PhonePe
                    </option>

                    <option value="Paytm"
                        {{ old('provider') == 'Paytm' ? 'selected' : '' }}>
                        Paytm
                    </option>

                    <option value="Amazon Pay"
                        {{ old('provider') == 'Amazon Pay' ? 'selected' : '' }}>
                        Amazon Pay
                    </option>

                    <option value="BHIM"
                        {{ old('provider') == 'BHIM' ? 'selected' : '' }}>
                        BHIM
                    </option>

                    <option value="Other"
                        {{ old('provider') == 'Other' ? 'selected' : '' }}>
                        Other
                    </option>

                </select>

                @error('provider')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- UPI ID --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>UPI ID</strong>
                </label>

                <input type="text"
                       name="upi_id"
                       class="form-control @error('upi_id') is-invalid @enderror"
                       value="{{ old('upi_id') }}"
                       maxlength="255"
                       placeholder="Example: scanner@ybl">

                <small class="text-muted">
                    Optional. QR Code is mandatory.
                </small>

                @error('upi_id')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- QR Image --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>QR Code</strong>
                </label>

                <input type="file"
                       name="qr_image"
                       id="qr_image"
                       class="form-control @error('qr_image') is-invalid @enderror"
                       accept="image/jpeg,image/png,image/jpg,image/webp"
                       required>

                <small class="text-muted">
                    JPG, JPEG, PNG or WEBP. Maximum 5 MB.
                </small>

                @error('qr_image')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>


            {{-- QR Preview --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>QR Preview</strong>
                </label>

                <div>

                    <img id="qrPreview"
                         src=""
                         alt="QR Preview"
                         style="
                            display:none;
                            width:180px;
                            height:180px;
                            object-fit:contain;
                            border:1px solid #ddd;
                            padding:5px;
                            border-radius:6px;
                         ">

                </div>

            </div>


            {{-- Status --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>Status</strong>
                </label>

                <select name="is_active"
                        class="form-control">

                    <option value="1"
                        {{ old('is_active', 1) == 1 ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="0"
                        {{ old('is_active') == 0 ? 'selected' : '' }}>
                        Inactive
                    </option>

                </select>

            </div>


            {{-- Default --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>Default UPI</strong>
                </label>

                <select name="is_default"
                        class="form-control">

                    <option value="0"
                        {{ old('is_default', 0) == 0 ? 'selected' : '' }}>
                        No
                    </option>

                    <option value="1"
                        {{ old('is_default') == 1 ? 'selected' : '' }}>
                        Yes
                    </option>

                </select>

                <small class="text-muted">
                    The first UPI added will automatically become default.
                </small>

            </div>


            {{-- Sort Order --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    <strong>Sort Order</strong>
                </label>

                <input type="number"
                       name="sort_order"
                       class="form-control @error('sort_order') is-invalid @enderror"
                       value="{{ old('sort_order', 0) }}"
                       min="0">

                @error('sort_order')

                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>

                @enderror

            </div>

        </div>


        <button class="btn btn-success">
            Add
        </button>

        <a href="{{ route('payment-upi-accounts.index') }}"
           class="btn btn-secondary">
            Back
        </a>

    </form>

</div>

@endsection


@section('scripts')

@push('scripts')

<script>

    // ==========================================
    // QR IMAGE PREVIEW
    // ==========================================

    document.addEventListener('DOMContentLoaded', function () {

        const qrInput = document.getElementById('qr_image');

        const qrPreview = document.getElementById('qrPreview');


        qrInput.addEventListener('change', function () {

            const file = this.files[0];

            if (!file) {

                qrPreview.src = '';

                qrPreview.style.display = 'none';

                return;

            }

            const reader = new FileReader();

            reader.onload = function (e) {

                qrPreview.src = e.target.result;

                qrPreview.style.display = 'block';

            };

            reader.readAsDataURL(file);

        });

    });

</script>

@endpush

@endsection