@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Letter</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('student-custom-letters.update', $letter->id) }}">

        @csrf
        @method('PUT')

        <div class="row">

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select
                    name="letter_type"
                    id="letterType"
                    class="form-control"
                    required
                >
                    <option value="">Select Letter Type</option>

                    <option value="part_time_job_opportunity"
                        {{ old('letter_type', $letter->letter_type) == 'part_time_job_opportunity' ? 'selected' : '' }}>
                        Part Time Job Opportunity
                    </option>

                    <option value="offer_letter"
                        {{ old('letter_type', $letter->letter_type) == 'offer_letter' ? 'selected' : '' }}>
                        Offer Letter
                    </option>

                    <option value="strict_offer_letter"
                        {{ old('letter_type', $letter->letter_type) == 'strict_offer_letter' ? 'selected' : '' }}>
                        Strict Offer Letter
                    </option>

                    <option value="strict_consent_letter"
                        {{ old('letter_type', $letter->letter_type) == 'strict_consent_letter' ? 'selected' : '' }}>
                        Strict Consent Letter
                    </option>

                    <option value="internship_consent"
                        {{ old('letter_type', $letter->letter_type) == 'internship_consent' ? 'selected' : '' }}>
                        Internship Consent
                    </option>

                    <option value="stipend_policy"
                        {{ old('letter_type', $letter->letter_type) == 'stipend_policy' ? 'selected' : '' }}>
                        Stipend Policy
                    </option>
                </select>
            </div>

            {{-- Session --}}
            <div class="form-group col-md-6">
                <label>Session</label>

                <input type="text"
                       class="form-control"
                       value="{{ ucwords($activeSession->session_name) ?? 'No Active Session' }}"
                       readonly>
            </div>

            {{-- Student Name --}}
            <div class="form-group col-md-6">
                <label>Student Name</label>

                <input
                    type="text"
                    name="student_name"
                    value="{{ old('student_name', $letter->student_name) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- College --}}
            <div class="form-group col-md-6">
                <label>College Name</label>

                <input
                    type="text"
                    name="college"
                    value="{{ old('college', $letter->college) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>

                <input
                    type="date"
                    name="issue_date"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', $letter->issue_date) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Father Name --}}
            <div class="form-group col-md-6 d-none commonDynamicField">
                <label>Father Name</label>

                <input
                    type="text"
                    name="father_name"
                    value="{{ old('father_name', $letter->father_name) }}"
                    class="form-control"
                >
            </div>

            {{-- Course Branch --}}
            <div class="form-group col-md-6 d-none commonDynamicField">
                <label>Course / Branch</label>

                <input
                    type="text"
                    name="course_branch"
                    value="{{ old('course_branch', $letter->course_branch) }}"
                    class="form-control"
                >
            </div>

            {{-- Contact No --}}
            <div class="form-group col-md-6 d-none commonDynamicField">
                <label>Contact No</label>

                <input
                    type="text"
                    name="contact_no"
                    value="{{ old('contact_no', $letter->contact_no) }}"
                    class="form-control"
                >
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6 d-none commonDynamicField">
                <label>Email ID</label>

                <input
                    type="email"
                    name="email_id"
                    value="{{ old('email_id', $letter->email_id) }}"
                    class="form-control"
                >
            </div>

            {{-- Position --}}
            <div class="form-group col-md-6 d-none commonDynamicField" id="positionField">
                <label>Position</label>

                <input
                    type="text"
                    name="position"
                    id="positionInput"
                    value="{{ old('position', $letter->position) }}"
                    class="form-control"
                >
            </div>

            {{-- Training Start Date --}}
            <div class="form-group col-md-6 d-none commonDynamicField"
                 id="trainingStartDateField">

                <label>Training Start Date</label>

                <input
                    type="date"
                    name="training_start_date"
                    id="trainingStartDate"
                    value="{{ old('training_start_date', $letter->training_start_date) }}"
                    class="form-control"
                >
            </div>

            {{-- Training Duration --}}
            <div class="form-group col-md-6 d-none commonDynamicField strictField trainingConsentField stipendField">
                <label>Training / Internship Duration</label>

                <input
                    type="text"
                    name="training_duration"
                    id="trainingDuration"
                    value="{{ old('training_duration', $letter->training_duration) }}"
                    class="form-control"
                >
            </div>

            {{-- Training Domain --}}
            <div class="form-group col-md-6 d-none commonDynamicField trainingConsentField stipendField">
                <label>Training / Internship Domain</label>

                <input
                    type="text"
                    name="training_domain"
                    value="{{ old('training_domain', $letter->training_domain) }}"
                    class="form-control"
                >
            </div>

            {{-- Batch Mode --}}
            <div class="form-group col-md-6 d-none commonDynamicField trainingConsentField">
                <label>Batch Mode</label>

                <input
                    type="text"
                    name="batch_mode"
                    value="{{ old('batch_mode', $letter->batch_mode) }}"
                    class="form-control"
                >
            </div>

            {{-- Joining Date --}}
            <div class="form-group col-md-6 d-none commonDynamicField trainingConsentField stipendField">
                <label>Joining Date</label>

                <input
                    type="date"
                    name="joining_date"
                    value="{{ old('joining_date', $letter->joining_date) }}"
                    class="form-control"
                >
            </div>

            {{-- Completion Date --}}
            <div class="form-group col-md-6 d-none commonDynamicField internshipConsentField">
                <label>Completion Date</label>

                <input
                    type="date"
                    name="completion_date"
                    value="{{ old('completion_date', $letter->completion_date) }}"
                    class="form-control"
                >
            </div>

            {{-- Reporting Mentor --}}
            <div class="form-group col-md-6 d-none commonDynamicField stipendField">
                <label>Reporting Mentor</label>

                <input
                    type="text"
                    name="reporting_mentor"
                    value="{{ old('reporting_mentor', $letter->reporting_mentor) }}"
                    class="form-control"
                >
            </div>

            {{-- Internship Mode --}}
            <div class="form-group col-md-6 d-none commonDynamicField stipendField">
                <label>Internship Mode</label>

                <input
                    type="text"
                    name="internship_mode"
                    value="{{ old('internship_mode', $letter->internship_mode) }}"
                    class="form-control"
                >
            </div>

            {{-- Probation Period --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Probation Period</label>

                <input
                    type="text"
                    name="probation_period"
                    value="{{ old('probation_period', $letter->probation_period) }}"
                    class="form-control"
                >
            </div>

            {{-- Working Hours --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Working Hours</label>

                <input
                    type="text"
                    name="working_hours"
                    value="{{ old('working_hours', $letter->working_hours) }}"
                    class="form-control"
                >
            </div>

            {{-- Bond Duration --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Bond Duration</label>

                <input
                    type="text"
                    name="bond_duration"
                    value="{{ old('bond_duration', $letter->bond_duration) }}"
                    class="form-control"
                >
            </div>

        </div>

        <button class="btn btn-primary mt-3">
            Update
        </button>

        <a href="{{ route('student-custom-letters.index') }}"
           class="btn btn-secondary mt-3">
            Back
        </a>

    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const letterType = document.getElementById('letterType');

    function hideAllDynamicFields() {

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.add('d-none');
        });

        document.querySelectorAll('.strictField').forEach(field => {
            field.classList.add('d-none');
        });

        document.querySelectorAll('.trainingConsentField').forEach(field => {
            field.classList.add('d-none');
        });

        document.querySelectorAll('.internshipConsentField').forEach(field => {
            field.classList.add('d-none');
        });

        document.querySelectorAll('.stipendField').forEach(field => {
            field.classList.add('d-none');
        });
    }

    function showCommonFields() {

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.remove('d-none');
        });
    }

    function toggleFields() {

    const value = letterType.value;

    // HIDE EVERYTHING
    document.querySelectorAll(
        '.commonDynamicField, .strictField, .trainingConsentField, .internshipConsentField, .stipendField'
    ).forEach(field => {
        field.classList.add('d-none');
    });

    // hide separate fields
    positionField.classList.add('d-none');
    trainingStartDateField.classList.add('d-none');

    // STOP if nothing selected
    if (!value) {
        return;
    }

    // =========================
    // OFFER LETTER
    // =========================
    if (value === 'offer_letter') {

        positionField.classList.remove('d-none');

        trainingStartDateField.classList.remove('d-none');

        document.getElementById('trainingDuration')
            .closest('.form-group')
            .classList.remove('d-none');
    }

    // =========================
    // STRICT OFFER LETTER
    // =========================
    else if (value === 'strict_offer_letter') {

        document.querySelectorAll('.strictField').forEach(field => {
            field.classList.remove('d-none');
        });

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.remove('d-none');
        });

        positionField.classList.remove('d-none');

        trainingStartDateField.classList.remove('d-none');
    }

    // =========================
    // STRICT CONSENT LETTER
    // =========================
    else if (value === 'strict_consent_letter') {

        document.querySelectorAll('.trainingConsentField').forEach(field => {
            field.classList.remove('d-none');
        });

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.remove('d-none');
        });
    }

    // =========================
    // INTERNSHIP CONSENT
    // =========================
    else if (value === 'internship_consent') {

        document.querySelectorAll('.trainingConsentField').forEach(field => {
            field.classList.remove('d-none');
        });

        document.querySelectorAll('.internshipConsentField').forEach(field => {
            field.classList.remove('d-none');
        });

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.remove('d-none');
        });
    }

    // =========================
    // STIPEND POLICY
    // =========================
    else if (value === 'stipend_policy') {

        document.querySelectorAll('.stipendField').forEach(field => {
            field.classList.remove('d-none');
        });

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.remove('d-none');
        });
    }
}

    letterType.addEventListener('change', toggleFields);

    toggleFields();

});
</script>
@endpush