@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Add College</h1>

    @if($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('colleges.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label><strong>College Name</strong></label>
            <input type="text" name="college_name" class="form-control" value="{{ old('college_name') }}" required>
        </div>

        <div class="mb-3">
            <label><strong>College Display Name</strong></label>
            <input type="text" name="college_display_name" class="form-control" value="{{ old('college_display_name') }}" required>
        </div>

        <div class="mb-3">
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

        <div class="mb-3">
            <label><strong>District</strong></label>
            <select name="district_id" id="district" class="form-control" required disabled>
                <option value="">-- Select District --</option>
            </select>
        </div>

        <button class="btn btn-success">Add</button>
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
