@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row mb-2">
        <div class="col-md-8">
            <h1 class="page_heading">Add College/ Place</h1>
        </div>  
    </div>
    

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('colleges.store') }}" method="POST">
        @csrf

        <div class="row">
        <div class="form-group col-md-6">
            <label><strong>Name</strong></label>
            <input type="text" name="college_name"  class="form-control @error('college_name') is-invalid @enderror" value="{{ old('college_name') }}" required>
             @error('college_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror

        </div>

        <div class="form-group col-md-6">
            <label><strong>Display Name</strong></label>
            <input type="text" name="college_display_name" class="form-control" value="{{ old('college_display_name') }}" required>
        </div>

        <div class="form-group col-md-6">
            <label><strong>State</strong></label>
            <select name="state_id" id="state" class="form-control" required>
                <option value="">-- Select State --</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label><strong>District</strong></label>
            <select name="district_id" id="district" class="form-control" required disabled>
                <option value="">-- Select District --</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>College Type</label>
            <select name="college_type" class="form-control">
                <option value="">Select College Type</option>

                @foreach(\App\Models\College::TYPES as $key => $value)
                    <option value="{{ $key }}">
                        {{ $value }}
                    </option>
                @endforeach

            </select>
        </div>

         <div class="form-group col-md-6">
            <label>Offer Training</label>
            <select name="offer_training" class="form-control">
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>Training Times in Year</label>
            <select name="training_in_year" class="form-control">
                @foreach(range(0, 5) as $year)
                    <option value="{{ $year }}"
                        {{ old('training_in_year', $college->training_in_year ?? '') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Important College --}}
        <div class="form-group col-md-6">
            <label>Important College</label>
            <select name="is_important" class="form-control">
                <option value="0" {{ old('is_important', 0) == 0 ? 'selected' : '' }}>No</option>
                <option value="1" {{ old('is_important') == 1 ? 'selected' : '' }}>Yes</option>
            </select>
        </div>

        {{-- Government / Private --}}
        <div class="form-group col-md-6">
            <label>Ownership</label>
            <select name="ownership_type" class="form-control">
                <option value="0" {{ old('ownership_type', 0) == 0 ? 'selected' : '' }}>Private</option>
                <option value="1" {{ old('ownership_type') == 1 ? 'selected' : '' }}>Government</option>
            </select>
        </div>

        {{-- Old / New Connection --}}
        <div class="form-group col-md-6">
            <label>Connection Type</label>
            <select name="connection_type" class="form-control">
                <option value="0" {{ old('connection_type', 0) == 0 ? 'selected' : '' }}>New Connection</option>
                <option value="1" {{ old('connection_type') == 1 ? 'selected' : '' }}>Old Connection</option>
            </select>
        </div>

        {{-- Departments --}}
        <div class="form-group col-md-6">
            <label>Departments</label>

            @php
                $departmentList = [
                    'CSE',
                    'MBA',
                    'BBA',
                    'Civil',
                    'EC',
                    'Mechanical',
                ];
            @endphp

            <select name="departments[]" class="form-control" multiple>
                @foreach($departmentList as $department)
                    <option value="{{ $department }}"
                        {{ in_array($department, old('departments', [])) ? 'selected' : '' }}>
                        {{ $department }}
                    </option>
                @endforeach
            </select>

            <small class="text-muted">
                Hold Ctrl (Windows) or Cmd (Mac) to select multiple departments.
            </small>
        </div>
    </div>
        <button class="btn btn-success">Add</button>
        <a href="{{ route('colleges.index') }}" class="btn btn-secondary">
            Back
        </a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const stateSelect = document.getElementById('state');
    const districtSelect = document.getElementById('district');

    stateSelect.addEventListener('change', function() {
        const stateId = this.value;
        districtSelect.innerHTML = '<option>Loading...</option>';
        districtSelect.disabled = true;

        if (!stateId) {
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            return;
        }

        fetch(`/districts/by-state/${stateId}`)
            .then(res => res.json())
            .then(data => {
                districtSelect.innerHTML = '<option value="">-- Select District --</option>';
                data.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
                districtSelect.disabled = false;
            })
            .catch(err => {
                console.error(err);
                districtSelect.innerHTML = '<option value="">-- Error loading --</option>';
            });
    });

    // If old value exists (validation failed), load districts and set selected
    const oldState = "{{ old('state_id') }}";
    const oldDistrict = "{{ old('district_id') }}";
    if (oldState) {
        stateSelect.value = oldState;
        stateSelect.dispatchEvent(new Event('change'));
        // after fetch completes, script cannot set selected immediately — handled by server side or extra JS if needed
        // A small delay approach to set selected after data load:
        const interval = setInterval(() => {
            const found = Array.from(districtSelect.options).some(o => o.value == oldDistrict);
            if (found) {
                districtSelect.value = oldDistrict;
                clearInterval(interval);
            }
        }, 200);
    }
});
</script>
@endsection
