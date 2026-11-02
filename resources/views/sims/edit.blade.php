@extends('layouts.app')

@section('content')

<div class="container">

    <h4>
        Edit SIM #{{ $sim->id }}
    </h4>


    <form method="POST"
          action="{{ route('sims.update', $sim) }}">

        @csrf

        @method('PUT')

        <div class="row">

            {{-- Mobile Number --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    Mobile Number *
                </label>

                <input type="text"
                       name="mobile_number"
                       class="form-control @error('mobile_number') is-invalid @enderror"
                       value="{{ old('mobile_number', $sim->mobile_number) }}"
                       required
                       maxlength="20"
                       placeholder="Mobile number">

                @error('mobile_number')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            {{-- Operator --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    Operator *
                </label>

                <input type="text"
                       name="operator"
                       class="form-control @error('operator') is-invalid @enderror"
                       value="{{ old('operator', $sim->operator) }}"
                       required
                       maxlength="100"
                       placeholder="Airtel / Jio / Vi / BSNL">

                @error('operator')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            {{-- Name --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    Name
                </label>

                <input type="text"
                       name="name"
                       class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $sim->name) }}"
                       placeholder="Person using SIM">

                @error('name')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            {{-- Status --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    Status *
                </label>

                @php
                    $status = old('status', $sim->status ?? 'active');
                @endphp

                <select name="status"
                        class="form-control @error('status') is-invalid @enderror"
                        required>

                    <option value="active"
                        {{ $status === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="deactive"
                        {{ $status === 'deactive' ? 'selected' : '' }}>
                        Deactive
                    </option>

                </select>

                @error('status')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            {{-- Issued Date --}}
            <div class="form-group col-md-6 mb-3">

                <label>
                    Issued Date
                </label>

                <input type="date"
                       name="issued_at"
                       class="form-control @error('issued_at') is-invalid @enderror"
                       value="{{ old(
                           'issued_at',
                           $sim->issued_at
                               ? $sim->issued_at->format('Y-m-d')
                               : ''
                       ) }}">

                @error('issued_at')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>


            {{-- Notes --}}
            <div class="form-group col-12 mb-3">

                <label>
                    Notes
                </label>

                <textarea name="notes"
                          class="form-control @error('notes') is-invalid @enderror"
                          rows="3"
                          placeholder="Optional notes...">{{ old('notes', $sim->notes) }}</textarea>

                @error('notes')
                    <small class="text-danger">
                        {{ $message }}
                    </small>
                @enderror

            </div>

        </div>


        <button class="btn btn-primary mt-3">
            Update
        </button>

        <a href="{{ route('sims.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>

</div>

@endsection