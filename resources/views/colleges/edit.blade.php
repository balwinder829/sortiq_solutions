@extends('layouts.app')
@section('content')

<div class="container">
    <div class="row mb-2">
        <div class="col-md-8">
            <h1 class="page_heading">Edit College/Place</h1>
        </div>
    </div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('colleges.update', $college) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row">

        <div class="form-group col-md-6">
            <label>Name</label>
            <input type="text"
                   name="college_name"
                   class="form-control @error('college_name') is-invalid @enderror"
                   value="{{ old('college_name', $college->college_name) }}"
                   required>

            @error('college_name')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group col-md-6">
            <label>Display Name</label>
            <input type="text"
                   name="college_display_name"
                   class="form-control"
                   value="{{ old('college_display_name', $college->college_display_name) }}"
                   required>
        </div>

        <div class="form-group col-md-6">
            <label>Short Name</label>
            <input type="text"
                   name="college_short_name"
                   class="form-control"
                   value="{{ old('college_short_name', $college->college_short_name) }}"
                   required>
        </div>

        <div class="form-group col-md-6">
            <label>State</label>
            <select name="state_id" id="state" class="form-control" required>
                <option value="">-- Select State --</option>

                @foreach($states as $state)
                    <option value="{{ $state->id }}"
                        {{ old('state_id', $college->state_id) == $state->id ? 'selected' : '' }}>
                        {{ $state->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>District</label>
            <select name="district_id" id="district" class="form-control" required>
                <option value="">-- Select District --</option>

                @foreach($districts as $d)
                    <option value="{{ $d->id }}"
                        {{ old('district_id', $college->district_id) == $d->id ? 'selected' : '' }}>
                        {{ $d->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group col-md-6">
            <label>Offer Training</label>

            <select name="offer_training" class="form-control">
                <option value="0"
                    {{ old('offer_training', $college->offer_training ?? '') == 0 ? 'selected' : '' }}>
                    No
                </option>

                <option value="1"
                    {{ old('offer_training', $college->offer_training ?? '') == 1 ? 'selected' : '' }}>
                    Yes
                </option>
            </select>
        </div>

        {{-- Training In --}}
        <div class="form-group col-md-6">
            <label>Training In</label>

            <select name="training_in"
                    class="form-control @error('training_in') is-invalid @enderror">

                <option value="">-- Select --</option>

                <option value="Degree"
                    {{ old('training_in', $college->training_in ?? '') == 'Degree' ? 'selected' : '' }}>
                    Degree
                </option>

                <option value="Diploma"
                    {{ old('training_in', $college->training_in ?? '') == 'Diploma' ? 'selected' : '' }}>
                    Diploma
                </option>

                <option value="Both"
                    {{ old('training_in', $college->training_in ?? '') == 'Both' ? 'selected' : '' }}>
                    Both
                </option>

            </select>

            @error('training_in')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Training Months --}}
        <div class="form-group col-md-6">
            <label>Training Months</label>

            <input type="text"
                   name="training_months"
                   class="form-control @error('training_months') is-invalid @enderror"
                   value="{{ old('training_months', $college->training_months ?? '') }}"
                   placeholder="e.g. Jan, Mar, Jun, Aug, Nov">

            @error('training_months')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>
        {{-- Training Times in Year --}}
        <!-- <div class="form-group col-md-6">
            <label>No. of training Times in Year</label>

            <select name="training_in_year" class="form-control">
                @foreach(range(0, 5) as $year)
                    <option value="{{ $year }}"
                        {{ old('training_in_year', $college->training_in_year ?? '') == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div> -->


        <div class="form-group col-md-6">
            <label>College Type</label>

            <select name="college_type"  id="college_type" class="form-control">
                <option value="">Select College Type</option>

                @foreach(\App\Models\College::TYPES as $key => $value)
                    <option value="{{ $key }}"
                        {{ old('college_type', $college->college_type) == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </div>
@php
    $selectedDepartments = old(
        'departments',
        $college->departments ?? []
    );
@endphp
        {{-- Departments --}}
<div class="form-group col-md-12 mb-3">
    <label>Departments</label>

    <select name="departments[]"
            id="departments"
            class="form-control select2"
            multiple>

        @foreach($collegeDepartments as $department)
            <option
                value="{{ $department->name }}"
                data-type="{{ $department->type }}"
                {{ in_array($department->name, $selectedDepartments) ? 'selected' : '' }}
            >
                {{ $department->name }}
            </option>
        @endforeach

    </select>

    <small class="text-muted">
        Departments are shown according to College Type.
    </small>

    @error('departments')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    @error('departments.*')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>

        {{-- Training Duration & Frequency --}}
        <div class="form-group col-md-12 mb-3">
            <label>
                <strong>Training Duration & Frequency</strong>
            </label>

            @php
                $trainingSchedule = $college->training_schedule ?? [];
            @endphp

            <div class="row">

                {{-- 21 Days --}}
                <div class="col-md-4">
                    <label>21 Days</label>

                    <select name="training_schedule[21_days]" class="form-control">
                        @for($i = 0; $i <= 8; $i++)
                            <option value="{{ $i }}"
                                {{ old(
                                    'training_schedule.21_days',
                                    $trainingSchedule['21_days'] ?? 0
                                ) == $i ? 'selected' : '' }}>
                                {{ $i }} Times / Year
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- 45 Days --}}
                <div class="col-md-4">
                    <label>45 Days</label>

                    <select name="training_schedule[45_days]" class="form-control">
                        @for($i = 0; $i <= 8; $i++)
                            <option value="{{ $i }}"
                                {{ old(
                                    'training_schedule.45_days',
                                    $trainingSchedule['45_days'] ?? 0
                                ) == $i ? 'selected' : '' }}>
                                {{ $i }} Times / Year
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- 6 Months --}}
                <div class="col-md-4">
                    <label>6 Months</label>

                    <select name="training_schedule[6_months]" class="form-control">
                        @for($i = 0; $i <= 8; $i++)
                            <option value="{{ $i }}"
                                {{ old(
                                    'training_schedule.6_months',
                                    $trainingSchedule['6_months'] ?? 0
                                ) == $i ? 'selected' : '' }}>
                                {{ $i }} Times / Year
                            </option>
                        @endfor
                    </select>
                </div>

            </div>
        </div>

        {{-- Seminar Count --}}
        <div class="form-group col-md-6">
            <label>Seminar</label>

            <input type="number"
                   name="seminar_count"
                   class="form-control @error('seminar_count') is-invalid @enderror"
                   value="{{ old('seminar_count', $college->seminar_count ?? 0) }}"
                   min="0">

            @error('seminar_count')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Placement Count --}}
        <div class="form-group col-md-6">
            <label>Placement</label>

            <input type="number"
                   name="placement_count"
                   class="form-control @error('placement_count') is-invalid @enderror"
                   value="{{ old('placement_count', $college->placement_count ?? 0) }}"
                   min="0">

            @error('placement_count')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Whom to Connect --}}
        <div class="form-group col-md-6">
            <label>Whom to Connect</label>

            <select name="connected_to"
                    class="form-control @error('connected_to') is-invalid @enderror">

                <option value="">-- Select --</option>

                <option value="HOD"
                    {{ old('connected_to', $college->connected_to ?? '') == 'HOD' ? 'selected' : '' }}>
                    HOD
                </option>

                <option value="TPO"
                    {{ old('connected_to', $college->connected_to ?? '') == 'TPO' ? 'selected' : '' }}>
                    TPO
                </option>

                <option value="Principal"
                    {{ old('connected_to', $college->connected_to ?? '') == 'Principal' ? 'selected' : '' }}>
                    Principal
                </option>

                <option value="other"
                    {{ old('connected_to', $college->connected_to ?? '') == 'other' ? 'selected' : '' }}>
                    Some Other
                </option>

                <option value="Both"
                    {{ old('connected_to', $college->connected_to ?? '') == 'Both' ? 'selected' : '' }}>
                    Both (HOD + TPO)
                </option>

            </select>

            @error('connected_to')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        {{-- Reference By --}}
        <div class="form-group col-md-6">
            <label>Reference By</label>

            <input type="text"
                   name="reference_by"
                   class="form-control @error('reference_by') is-invalid @enderror"
                   value="{{ old('reference_by', $college->reference_by ?? '') }}"
                   maxlength="255"
                   placeholder="Who referred / found this college?">

            @error('reference_by')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Contact Person --}}
        <div class="form-group col-md-6">
            <label>Contact Person</label>

            <input type="text"
                   name="contact_person"
                   class="form-control @error('contact_person') is-invalid @enderror"
                   value="{{ old('contact_person', $college->contact_person ?? '') }}"
                   maxlength="255"
                   placeholder="Person you usually speak with">

            @error('contact_person')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        {{-- Important College --}}
        <div class="form-group col-md-6">
            <label>Important College</label>

            <select name="is_important" class="form-control">
                <option value="0"
                    {{ old('is_important', $college->is_important) == 0 ? 'selected' : '' }}>
                    No
                </option>

                <option value="1"
                    {{ old('is_important', $college->is_important) == 1 ? 'selected' : '' }}>
                    Yes
                </option>
            </select>
        </div>

        {{-- Government / Private --}}
        <div class="form-group col-md-6">
            <label>Ownership</label>

            <select name="ownership_type" class="form-control">
                <option value="0"
                    {{ old('ownership_type', $college->ownership_type) == 0 ? 'selected' : '' }}>
                    Private
                </option>

                <option value="1"
                    {{ old('ownership_type', $college->ownership_type) == 1 ? 'selected' : '' }}>
                    Government
                </option>
            </select>
        </div>

        {{-- Connection Type --}}
        <div class="form-group col-md-6">
            <label>Connection Type</label>

            <select name="connection_type" class="form-control">
                <option value="0"
                    {{ old('connection_type', $college->connection_type) == 0 ? 'selected' : '' }}>
                    New Connection
                </option>

                <option value="1"
                    {{ old('connection_type', $college->connection_type) == 1 ? 'selected' : '' }}>
                    Old Connection
                </option>
            </select>
        </div>

        

    </div>

    <button class="btn btn-primary">Update</button>

    <a href="{{ route('colleges.index') }}"
       class="btn btn-secondary">
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
            districtSelect.innerHTML =
                '<option value="">-- Select District --</option>';
            return;
        }

        fetch(`/districts/by-state/${stateId}`)
            .then(res => res.json())
            .then(data => {

                districtSelect.innerHTML =
                    '<option value="">-- Select District --</option>';

                data.forEach(d => {
                    districtSelect.innerHTML +=
                        `<option value="${d.id}">${d.name}</option>`;
                });

                districtSelect.disabled = false;
            })
            .catch(err => {

                console.error(err);

                districtSelect.innerHTML =
                    '<option value="">-- Error loading --</option>';
            });
    });
});
</script>
<script>
    $(document).ready(function () {

        const $collegeType = $('#college_type');
        const $departments = $('#departments');

        // Store all departments from database
        const allDepartments = [];

        $departments.find('option').each(function () {
            allDepartments.push({
                value: $(this).val(),
                text: $(this).text().trim(),
                type: $(this).data('type')
            });
        });

        // IMPORTANT:
        // Keep selected departments separately.
        // This prevents selections from being lost when College Type changes.
        let selectedDepartments = [];

        $departments.find('option:selected').each(function () {
            selectedDepartments.push($(this).val());
        });


        function getAllowedTypes() {

            const collegeType = $collegeType.val();

            if (collegeType == '0') {
                // Degree
                return ['degree'];
            }

            if (collegeType == '1') {
                // Diploma
                return ['diploma'];
            }

            if (collegeType == '2' || collegeType == '3') {
                // Both / Unknown
                return ['degree', 'diploma'];
            }

            // No College Type selected
            return ['degree', 'diploma'];
        }


        function filterDepartments() {

            const allowedTypes = getAllowedTypes();

            // Remove existing options
            $departments.empty();

            // Add Degree first, then Diploma
            allowedTypes.forEach(function (type) {

                allDepartments.forEach(function (department) {

                    if (department.type === type) {

                        const isSelected =
                            selectedDepartments.includes(department.value);

                        const option = new Option(
                            department.text,
                            department.value,
                            false,
                            isSelected
                        );

                        $departments.append(option);
                    }
                });
            });

            // Set remembered selections that are currently available
            const visibleSelected = selectedDepartments.filter(function (value) {

                return $departments.find(
                    'option[value="' + CSS.escape(value) + '"]'
                ).length > 0;

            });

            $departments.val(visibleSelected);

            // Refresh Select2
            if ($departments.hasClass('select2-hidden-accessible')) {
                $departments.trigger('change.select2');
            }
        }


        // IMPORTANT:
        // Capture manual selection/deselection by the user.
        $departments.on('change', function () {

            const visibleSelected = $(this).val() || [];

            const allowedTypes = getAllowedTypes();

            // Get departments currently visible in this College Type
            const visibleValues = allDepartments
                .filter(function (department) {
                    return allowedTypes.includes(department.type);
                })
                .map(function (department) {
                    return department.value;
                });


            // Remove only the currently visible departments
            // from our remembered selection.
            selectedDepartments = selectedDepartments.filter(function (value) {
                return !visibleValues.includes(value);
            });


            // Add the currently selected visible departments
            visibleSelected.forEach(function (value) {

                if (!selectedDepartments.includes(value)) {
                    selectedDepartments.push(value);
                }

            });

        });


        // College Type changed
        $collegeType.on('change', function () {

            filterDepartments();

        });


        // Initial load
        filterDepartments();

    });
</script>
@endsection
