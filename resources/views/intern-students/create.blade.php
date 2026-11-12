@extends('layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('intern-students.store') }}">
        @csrf

        <div class="row mb-2 align-items-center">
            <div class="col-md-8">
                <h1 class="page_heading">Add Intern Student</h1>
            </div>
            <!-- <div class="col-md-4 text-end">
                <div class="form-check d-inline-block ms-3">
                    <input type="hidden" name="is_place" value="0">
                    <input class="form-check-input" type="checkbox" name="is_place" value="1" id="is_place" {{ old('is_place') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_place">Other Place</label>
                </div>
                <div class="form-check d-inline-block">
                    <input type="hidden" name="is_married" value="0">
                    <input class="form-check-input" type="checkbox" name="is_married" value="1" id="is_married" {{ old('is_married') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_married">Married Status</label>
                </div>
            </div> -->
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Student Name</label>
                <input type="text" maxlength="55" required class="form-control" name="student_name" value="{{ old('student_name') }}" oninput="capitalizeWords(this)">
                @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label id="guardianLabel">Father Name</label>
                <input type="text" maxlength="55" required class="form-control" name="f_name" value="{{ old('f_name', 'Mr. ') }}" oninput="handleMrPrefix(this)">
                @error('f_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 d-none">
                <label>Serial No.</label>
                <input type="hidden" name="sno" value="{{ old('sno', $nextSno) }}" readonly>
                @error('sno') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Gender</label>
                <select name="gender" id="genderSelect" class="form-control" required>
                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>--Select--</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Session</label>
                <input type="text" class="form-control" value="{{ $activeSession ? ucwords($activeSession->session_name) : 'No Active Session' }}" readonly>
                <input type="hidden" name="session" value="{{ old('session', $activeSession ? $activeSession->id : session('admin_session_id')) }}">
                @if(!$activeSession)<small class="text-danger">Active session not found. Please login again.</small>@endif
                @error('session') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6" id="collegeWrapper">
                <label>College Name</label>
                <select name="college_name" id="college_name" class="form-control select2">
                    <option value="" disabled {{ old('college_name') ? '' : 'selected' }}>Choose one</option>
                    @foreach($colleges as $college)
                        @php
                            $originalName = trim($college->college_name ?? '');
                            $displayName = trim($college->FullName ?? $college->college_display_name ?? '');
                            $normalizedOriginal = trim(preg_replace('/\s*\([^)]*\)/', '', $originalName));
                            $normalizedDisplay = trim(preg_replace('/\s*\([^)]*\)/', '', $displayName));
                        @endphp
                        <option value="{{ $college->id }}" {{ (string) old('college_name') === (string) $college->id ? 'selected' : '' }}>
                            {{ $normalizedOriginal === $normalizedDisplay ? ($displayName ?: '--') : (($originalName ?: '--') . ' -- ' . ($displayName ?: '--')) }}
                        </option>
                    @endforeach
                </select>
                @error('college_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 d-none" id="placeWrapper">
                <label>Place</label>
                <input type="text" name="place" id="place" class="form-control" value="{{ old('place') }}" placeholder="Enter place">
                @error('place') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email_id" class="form-control" value="{{ old('email_id') }}">
                @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Contact No</label>
                <input type="text" class="form-control" name="contact" value="{{ old('contact') }}" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Enter a valid 10-digit mobile number" onpaste="handlePaste(event)" oninput="sanitizeContact(this)">
                @error('contact') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Alternate No</label>
                <input type="text" class="form-control" name="alternative_phone" value="{{ old('alternative_phone') }}" minlength="10" maxlength="10" pattern="[0-9]{10}" title="Enter a valid 10-digit mobile number" onpaste="handlePaste(event)" oninput="sanitizeContact(this)">
                @error('alternative_phone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

             

            <div class="form-group col-md-6">
                <label>Technology</label>
                @php $selectedTechnology = old('technology', [$defaultTechnology]); @endphp
                @if(!is_array($selectedTechnology)) @php $selectedTechnology = [$selectedTechnology]; @endphp @endif
                <select name="technology[]" id="technology" class="form-control select2">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ in_array((string) $course->id, array_map('strval', $selectedTechnology), true) ? 'selected' : '' }}>{{ $course->course_name }}</option>
                    @endforeach
                </select>
                @error('technology') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

             

            <div class="form-group col-md-6">
                <label>Registered Date</label>
                <input type="date" name="join_date" class="form-control" id="join_date" value="{{ old('join_date') }}">
                @error('join_date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Duration</label>
                <select name="duration" class="form-control" id="duration">
                    <option value="">--Select--</option>
                    @foreach($course_duration as $d)
                        <option value="{{ $d->duration }}" {{ old('duration') == $d->duration ? 'selected' : '' }}>{{ $d->name }}</option>
                    @endforeach
                </select>
                @error('duration') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" id="start_date" value="{{ old('start_date') }}">
                <small class="text-danger d-none" id="start_error">Sunday is not allowed</small>
                <small class="text-danger d-none" id="date_error">Start date must be on or after the registered date.</small>
                @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <input type="hidden" id="session_end_date" value="{{ $activeSession ? $activeSession->end_date : '' }}">
            <div class="form-group col-md-6">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" id="end_date" value="{{ old('end_date') }}">
                <small class="text-danger d-none" id="end_error">Sunday is not allowed</small>
                @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Batch Assign</label>
                <select name="batch_assign" class="form-control">
                    <option value="">Not Decided</option>
                    @foreach($batches as $batch)
                        <option value="{{ $batch->id }}" {{ (string) old('batch_assign') === (string) $batch->id ? 'selected' : '' }}>{{ $batch->batch_name }}</option>
                    @endforeach
                </select>
                @error('batch_assign') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Part-Time Offer?</label>
                <select name="part_time_offer" class="form-control">
                    <option value="0" {{ old('part_time_offer', 0) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('part_time_offer') == 1 ? 'selected' : '' }}>Yes</option>
                </select>
                @error('part_time_offer') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Placement Offer?</label>
                <select name="placement_offer" class="form-control">
                    <option value="0" {{ old('placement_offer', 0) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('placement_offer') == 1 ? 'selected' : '' }}>Yes</option>
                </select>
                @error('placement_offer') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>PG Offer?</label>
                <select name="pg_offer" class="form-control">
                    <option value="0" {{ old('pg_offer', 0) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('pg_offer') == 1 ? 'selected' : '' }}>Yes</option>
                </select>
                @error('pg_offer') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <!-- <div class="form-group col-md-6">
                <label>Referred By</label>
                <select name="referred_by" id="referred_by" class="form-control">
                    <option value="">Direct</option>
                    <optgroup label="Reference">
                        @foreach($references as $reference)
                            <option value="{{ $reference->id }}" data-type="reference" {{ old('referred_by') == $reference->id && old('referred_by_type') == 'reference' ? 'selected' : '' }}>{{ $reference->name }}</option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Sales Staff">
                        @foreach($salesStaff as $staff)
                            <option value="{{ $staff->id }}" data-type="staff" {{ old('referred_by') == $staff->id && old('referred_by_type') == 'staff' ? 'selected' : '' }}>{{ $staff->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <input type="hidden" name="referred_by_type" id="referred_by_type" value="{{ old('referred_by_type') }}">
                @error('referred_by') <small class="text-danger">{{ $message }}</small> @enderror
                @error('referred_by_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div> -->

            @php
                $flags = [
                    
                    'is_online' => 'Study Mode Online',
                   
                ];
            @endphp
            @foreach($flags as $field => $label)
                <div class="form-group col-md-6">
                    <label>{{ $label }}</label>
                    <select name="{{ $field }}" class="form-control">
                        <option value="0" {{ old($field, 0) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old($field, 0) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error($field) <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            @endforeach


            {{-- Normal Offer Letter Details --}}
            <div class="col-12 mt-3 mb-2">
                <h5 class="mb-1">Offer Letter Details</h5>
                <small class="text-muted">Required fields for generating the Normal Offer Letter.</small>
                <hr>
            </div>

            <div class="form-group col-md-6">
                <label>Internship Start Date <span class="text-danger">*</span></label>
                <input type="date" name="offer_training_start_date" class="form-control" required
                       value="{{ old('offer_training_start_date') }}">
                @error('offer_training_start_date') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Internship Duration (Months) <span class="text-danger">*</span></label>
                <input type="number" min="1" step="1" name="offer_training_duration_months" class="form-control" required
                       value="{{ old('offer_training_duration_months') }}">
                @error('offer_training_duration_months') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Internship Type <span class="text-danger">*</span></label>
                <input type="text" maxlength="100" name="internship_type" class="form-control" required
                       value="{{ old('internship_type') }}" placeholder="e.g. Free Internship">
                @error('internship_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>PPO Evaluation Period (Months) <span class="text-danger">*</span></label>
                <input type="number" min="1" max="24" step="1" name="offer_ppo_evaluation_months" class="form-control" required
                       value="{{ old('offer_ppo_evaluation_months', 3) }}" placeholder="e.g. 3">
                @error('offer_ppo_evaluation_months') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Probation Duration (Months)</label>
                <input type="number" min="1" step="1" name="offer_probation_duration_months" class="form-control"
                       value="{{ old('offer_probation_duration_months') }}">
                @error('offer_probation_duration_months') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Working Hours</label>
                <input type="text" maxlength="100" name="offer_working_hours" class="form-control"
                       value="{{ old('offer_working_hours') }}"
                       placeholder="e.g. 9:30 AM to 6:30 PM">
                @error('offer_working_hours') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Service Agreement Duration</label>
                <input type="text" maxlength="100" name="offer_service_agreement_duration" class="form-control"
                       value="{{ old('offer_service_agreement_duration') }}"
                       placeholder="e.g. 12 months">
                @error('offer_service_agreement_duration') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Annual Salary (₹)</label>
                <input type="number" min="0" step="0.01" name="offer_annual_salary" class="form-control"
                       value="{{ old('offer_annual_salary') }}"
                       placeholder="e.g. 405000">
                @error('offer_annual_salary') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
        </div>

        <button type="submit" class="btn" style="background-color:#6b51df;color:#fff;margin-left:8px;">Save Intern Student</button>
        <a href="{{ route('intern-students.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    $('.select2').select2({ theme: 'bootstrap-5', width: '100%', allowClear: true });

    const isPlace = document.getElementById('is_place');
    const college = document.getElementById('college_name');
    const place = document.getElementById('place');
    function togglePlaceFields() {
        if (isPlace.checked) {
            $('#collegeWrapper').hide();
            college.required = false;
            college.disabled = true;
            $('#placeWrapper').removeClass('d-none');
            place.required = true;
        } else {
            $('#collegeWrapper').show();
            college.required = true;
            college.disabled = false;
            $('#placeWrapper').addClass('d-none');
            place.required = false;
            place.value = '';
        }
    }
    isPlace.addEventListener('change', togglePlaceFields);
    togglePlaceFields();

    const gender = document.getElementById('genderSelect');
    const married = document.getElementById('is_married');
    const guardianLabel = document.getElementById('guardianLabel');
    function updateGuardianLabel() {
        guardianLabel.textContent = gender.value === 'female' && married.checked ? 'Husband Name' : 'Father Name';
    }
    gender.addEventListener('change', updateGuardianLabel);
    married.addEventListener('change', updateGuardianLabel);
    updateGuardianLabel();

    $('#referred_by').on('change', function () {
        $('#referred_by_type').val($(this).find(':selected').data('type') || '');
    }).trigger('change');

    function blockSunday(input, errorId) {
        if (!input) return;
        input.addEventListener('change', function () {
            if (!this.value) return;
            const date = new Date(this.value + 'T00:00:00');
            if (date.getDay() === 0) {
                this.value = '';
                document.getElementById(errorId).classList.remove('d-none');
            } else {
                document.getElementById(errorId).classList.add('d-none');
            }
        });
    }
    blockSunday(document.getElementById('start_date'), 'start_error');
    blockSunday(document.getElementById('end_date'), 'end_error');

    function validateDates() {
        const joinDate = document.getElementById('join_date').value;
        const startDate = document.getElementById('start_date').value;
        if (!joinDate || !startDate) return;
        if (new Date(startDate) < new Date(joinDate)) {
            document.getElementById('date_error').classList.remove('d-none');
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
        } else {
            document.getElementById('date_error').classList.add('d-none');
        }
    }
    document.getElementById('join_date').addEventListener('change', validateDates);
    document.getElementById('start_date').addEventListener('change', validateDates);

    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const duration = document.getElementById('duration');
    const sessionEnd = document.getElementById('session_end_date').value;
    function calculateEndDate() {
        if (!startDate.value || !duration.value) return;
        const end = new Date(startDate.value + 'T00:00:00');
        end.setDate(end.getDate() + Number(duration.value));
        if (end.getDay() === 0) end.setDate(end.getDate() + 1);
        const y = end.getFullYear();
        const m = String(end.getMonth() + 1).padStart(2, '0');
        const d = String(end.getDate()).padStart(2, '0');
        if (!endDate.value || endDate.dataset.auto === '1') {
            endDate.value = `${y}-${m}-${d}`;
            endDate.dataset.auto = '1';
        }
    }
    endDate.addEventListener('change', function () { this.dataset.auto = '0'; });
    startDate.addEventListener('change', calculateEndDate);
    duration.addEventListener('change', function () {
        if (!startDate.value) startDate.disabled = false;
        calculateEndDate();
    });

    // Keep the session end date available as context; course duration controls the calculated end date.
    if (sessionEnd && !startDate.value) endDate.value = '';
});

function capitalizeWords(input) {
    const start = input.selectionStart, end = input.selectionEnd;
    input.value = input.value.toLowerCase().replace(/\b\w/g, char => char.toUpperCase());
    input.setSelectionRange(start, end);
}
function handlePaste(e) {
    e.preventDefault();
    const pasted = (e.clipboardData || window.clipboardData).getData('text');
    e.target.value = pasted.replace(/\D/g, '').slice(0, 10);
}
function sanitizeContact(input) { input.value = input.value.replace(/\D/g, '').slice(0, 10); }
function handleMrPrefix(input) {
    const prefix = 'Mr. ';
    let value = input.value.replace(/^(mr\.?\s*)+/i, '');
    value = value.replace(/\b\w/g, char => char.toUpperCase());
    input.value = prefix + value;
}
</script>
@endpush
