@extends('layouts.app')
@section('content')
<div class="container">
    <h1>Edit College</h1>

    <form action="{{ route('colleges.update', $college) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>College Name</label>
            <input type="text" name="college_name" class="form-control" value="{{ old('college_name', $college->college_name) }}" required>
        </div>

        <div class="mb-3">
            <label>State</label>
            <select name="state_id" id="state" class="form-control" required>
                <option value="">-- Select State --</option>
                @foreach($states as $state)
                    <option value="{{ $state->id }}" {{ (old('state_id', $college->state_id) == $state->id) ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>District</label>
            <select name="district_id" id="district" class="form-control" required>
                <option value="">-- Select District --</option>
                @foreach($districts as $d)
                    <option value="{{ $d->id }}" {{ (old('district_id', $college->district_id) == $d->id) ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-primary">Update</button>
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
});
</script>
@endsection
