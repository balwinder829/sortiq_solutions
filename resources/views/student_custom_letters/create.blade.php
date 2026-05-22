@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Generate Letter</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('student-custom-letters.store') }}">
        @csrf

        <div class="row">

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>

                <select
                    name="letter_type"
                    id="letterType"
                    class="form-control @error('letter_type') is-invalid @enderror"
                    required
                >
                    <option value="">Select Letter Type</option>

                    <option value="part_time_job_opportunity"
                        {{ old('letter_type')=='part_time_job_opportunity' ? 'selected' : '' }}>
                        Part Time Job Opportunity
                    </option>

                    <option value="offer_letter"
                        {{ old('letter_type')=='offer_letter' ? 'selected' : '' }}>
                        Offer Letter
                    </option>

                    <option value="strict_offer_letter"
                        {{ old('letter_type')=='strict_offer_letter' ? 'selected' : '' }}>
                        Strict Offer Letter
                    </option>

                    <option value="strict_consent_letter"
                        {{ old('letter_type')=='strict_consent_letter' ? 'selected' : '' }}>
                        Strict Consent Letter
                    </option>

                    <option value="internship_consent"
                        {{ old('letter_type')=='internship_consent' ? 'selected' : '' }}>
                        Internship Consent
                    </option>

                    <option value="stipend_policy"
                        {{ old('letter_type')=='stipend_policy' ? 'selected' : '' }}>
                        Stipend Policy
                    </option>
                </select>

                @error('letter_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Session --}}
            <div class="form-group col-md-6">
                <label>Session</label>

                <input
                    type="text"
                    class="form-control"
                    value="{{ ucwords($activeSession->session_name) ?? 'No Active Session' }}"
                    readonly
                >

                @if(!$activeSession)
                    <small class="text-danger">
                        Active session not found. Please login again.
                    </small>
                @endif
            </div>

            {{-- Student Name --}}
            <div class="form-group col-md-6">
                <label>Student Name</label>

                <input
                    type="text"
                    name="student_name"
                    value="{{ old('student_name') }}"
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
                    value="{{ old('father_name') }}"
                    class="form-control"
                >
            </div>

            {{-- College --}}
            <div class="form-group col-md-6">
                <label>College Name</label>

                <input
                    type="text"
                    name="college"
                    value="{{ old('college') }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Course / Branch --}}
            <div class="form-group col-md-6 d-none commonDynamicField">
                <label>Course / Branch</label>

                <input
                    type="text"
                    name="course_branch"
                    value="{{ old('course_branch') }}"
                    class="form-control"
                >
            </div>

            {{-- Contact No --}}
            <div class="form-group col-md-6">
                <label>Contact No</label>

                <input
                    type="text"
                    name="contact_no"
                    value="{{ old('contact_no') }}"
                    class="form-control"
                >
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email ID</label>

                <input
                    type="email"
                    name="email_id"
                    value="{{ old('email_id') }}"
                    class="form-control"
                >
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>

                <input
                    type="date"
                    name="issue_date"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', now()->toDateString()) }}"
                    class="form-control"
                    required
                >
            </div>

            {{-- Position --}}
            <div class="form-group col-md-6 d-none" id="positionField">
                <label>Position</label>

                <input
                    type="text"
                    name="position"
                    id="positionInput"
                    value="{{ old('position') }}"
                    class="form-control"
                >
            </div>

            {{-- Training Start Date --}}
            <div class="form-group col-md-6 d-none" id="trainingStartDateField">
                <label>Training Start Date</label>

                <input
                    type="date"
                    name="training_start_date"
                    id="trainingStartDate"
                    value="{{ old('training_start_date') }}"
                    class="form-control"
                >
            </div>

            {{-- Training Duration --}}
            <div class="form-group col-md-6 d-none strictField trainingConsentField stipendField">
                <label>Training / Internship Duration</label>

                <input
                    type="text"
                    name="training_duration"
                    id="trainingDuration"
                    value="{{ old('training_duration') }}"
                    class="form-control"
                >
            </div>

            {{-- Training Domain --}}
            <div class="form-group col-md-6 d-none trainingConsentField stipendField">
                <label>Training / Internship Domain</label>

                <input
                    type="text"
                    name="training_domain"
                    id="trainingDomain"
                    value="{{ old('training_domain') }}"
                    class="form-control"
                >
            </div>

            {{-- Batch Mode --}}
            <div class="form-group col-md-6 d-none trainingConsentField">
                <label>Batch Mode</label>

                <input
                    type="text"
                    name="batch_mode"
                    id="batchMode"
                    value="{{ old('batch_mode') }}"
                    class="form-control"
                >
            </div>

            {{-- Joining Date --}}
            <div class="form-group col-md-6 d-none trainingConsentField stipendField">
                <label>Joining Date</label>

                <input
                    type="date"
                    name="joining_date"
                    id="joiningDate"
                    value="{{ old('joining_date') }}"
                    class="form-control"
                >
            </div>

            {{-- Completion Date --}}
            <div class="form-group col-md-6 d-none internshipConsentField">
                <label>Completion Date</label>

                <input
                    type="date"
                    name="completion_date"
                    id="completionDate"
                    value="{{ old('completion_date') }}"
                    class="form-control"
                >
            </div>

            {{-- Reporting Mentor --}}
            <div class="form-group col-md-6 d-none stipendField">
                <label>Reporting Mentor</label>

                <input
                    type="text"
                    name="reporting_mentor"
                    id="reportingMentor"
                    value="{{ old('reporting_mentor') }}"
                    class="form-control"
                >
            </div>

            {{-- Internship Mode --}}
            <div class="form-group col-md-6 d-none stipendField">
                <label>Internship Mode</label>

                <input
                    type="text"
                    name="internship_mode"
                    id="internshipMode"
                    value="{{ old('internship_mode') }}"
                    class="form-control"
                >
            </div>

            {{-- Probation Period --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Probation Period</label>

                <input
                    type="text"
                    name="probation_period"
                    id="probationPeriod"
                    value="{{ old('probation_period') }}"
                    class="form-control"
                >
            </div>

            {{-- Working Hours --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Working Hours</label>

                <input
                    type="text"
                    name="working_hours"
                    id="workingHours"
                    value="{{ old('working_hours') }}"
                    class="form-control"
                >
            </div>

            {{-- Bond Duration --}}
            <div class="form-group col-md-6 d-none strictField">
                <label>Bond Duration</label>

                <input
                    type="text"
                    name="bond_duration"
                    id="bondDuration"
                    value="{{ old('bond_duration') }}"
                    class="form-control"
                >
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>

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

    const strictFields = document.querySelectorAll('.strictField');

    const positionField = document.getElementById('positionField');
    const positionInput = document.getElementById('positionInput');

    const trainingStartDateField = document.getElementById('trainingStartDateField');

    function hideAllDynamicFields() {

        strictFields.forEach(field => {
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

        document.querySelectorAll('.commonDynamicField').forEach(field => {
            field.classList.add('d-none');
        });

        positionField.classList.add('d-none');
        trainingStartDateField.classList.add('d-none');
    }

    function toggleFields() {

        const value = letterType.value;

        hideAllDynamicFields();

        // STRICT OFFER LETTER
        if (value === 'strict_offer_letter') {

            strictFields.forEach(field => {
                field.classList.remove('d-none');
            });

            positionField.classList.remove('d-none');
            trainingStartDateField.classList.remove('d-none');
        }

        // OFFER LETTER
        else if (value === 'offer_letter') {

            positionField.classList.remove('d-none');
            trainingStartDateField.classList.remove('d-none');

            document.getElementById('trainingDuration')
                .closest('.form-group')
                .classList.remove('d-none');
        }

        // STRICT CONSENT LETTER
        else if (value === 'strict_consent_letter') {

            document.querySelectorAll('.trainingConsentField').forEach(field => {
                field.classList.remove('d-none');
            });

            document.querySelectorAll('.commonDynamicField').forEach(field => {
                field.classList.remove('d-none');
            });
        }

        // INTERNSHIP CONSENT
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

        // STIPEND POLICY
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