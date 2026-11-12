
@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Student Additional Letter</h4>

    <form method="POST"
          action="{{ route('student-additional-letters.update', $letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Student Name --}}
            <div class="form-group col-md-12">
                <label>Select Student</label>

                <select name="student_id" id="student_select"
                        class="form-control" required>
                    <option value="">Select Student</option>

                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            data-name="{{ $student->student_name }}"
                            {{ old('student_id', $letter->student_id) == $student->id ? 'selected' : '' }}>

                            {{ $student->student_name }}
                            {{ in_array(strtolower($student->gender ?? ''), ['male', 'm']) ? 'S/O' : 'D/O' }}
                            {{ $student->f_name ?: 'NA' }}
                            from {{ $student->collegeData->FullName ?? 'N/A' }}

                        </option>
                    @endforeach
                </select>

                @error('student_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Internship Type</label>

                <select name="internship_type" id="internship_type"
                        class="form-control" required>

                    <option value="">Select Type</option>

                    <option value="free" {{ old('internship_type', $letter->internship_type) == 'free' ? 'selected' : '' }}>
                        Free Internship Letter
                    </option>

                    <option value="internship" {{ old('internship_type', $letter->internship_type) == 'internship' ? 'selected' : '' }}>
                        Internship Letter
                    </option>

                    <option value="internship_with_package" {{ old('internship_type', $letter->internship_type) == 'internship_with_package' ? 'selected' : '' }}>
                        Internship With Package Letter
                    </option>

                    <option value="stipend" {{ old('internship_type', $letter->internship_type) == 'stipend' ? 'selected' : '' }}>
                        Stipend Internship Letter
                    </option>

                    <option value="offer" {{ old('internship_type', $letter->internship_type) == 'offer' ? 'selected' : '' }}>
                        Offer Letter
                    </option>

                    <option value="custom" {{ old('internship_type', $letter->internship_type) == 'custom' ? 'selected' : '' }}>
                        Custom Type Letter
                    </option>

                    <option value="noc" {{ old('internship_type', $letter->internship_type) == 'noc' ? 'selected' : '' }}>
                        Non-Consent Letter
                    </option>

                    <option value="mutual_consent" {{ old('internship_type', $letter->internship_type) == 'mutual_consent' ? 'selected' : '' }}>
                        Mutual Consent Letter
                    </option>

                    <option value="training_consent" {{ old('internship_type', $letter->internship_type) == 'training_consent' ? 'selected' : '' }}>
                        Training Consent Letter
                    </option>

                    <option value="placement" {{ old('internship_type', $letter->internship_type) == 'placement' ? 'selected' : '' }}>
                        Student Placement Letter
                    </option>

                    <option value="strict_offer_letter" {{ old('internship_type', $letter->internship_type) == 'strict_offer_letter' ? 'selected' : '' }}>
                        Strict Offer Letter
                    </option>

                    <option value="strict_consent_letter" {{ old('internship_type', $letter->internship_type) == 'strict_consent_letter' ? 'selected' : '' }}>
                        Strict Consent Letter
                    </option>

                    <option value="stipend_policy" {{ old('internship_type', $letter->internship_type) == 'stipend_policy' ? 'selected' : '' }}>
                        Stipend Policy Letter
                    </option>

                    <option value="internship_consent" {{ old('internship_type', $letter->internship_type) == 'internship_consent' ? 'selected' : '' }}>
                        Internship Consent Letter
                    </option>

                    <option value="part_time_job_opportunity" {{ old('internship_type', $letter->internship_type) == 'part_time_job_opportunity' ? 'selected' : '' }}>
                        Part Time Job Opportunity Letter
                    </option>

                    <option value="with_roll_number" {{ old('internship_type', $letter->internship_type) == 'with_roll_number' ? 'selected' : '' }}>
                        With Roll Number Letter
                    </option>

                    <option value="with_roll_number_internship" {{ old('internship_type', $letter->internship_type) == 'with_roll_number_internship' ? 'selected' : '' }}>
                        With Roll Number Internship Letter
                    </option>

                    <option value="confirmation_letter" {{ old('internship_type', $letter->internship_type) == 'confirmation_letter' ? 'selected' : '' }}>
                        Confirmation Letter
                    </option>

                    <option value="student_responsibility" {{ old('internship_type', $letter->internship_type) == 'student_responsibility' ? 'selected' : '' }}>
                        Student Responsibility Letter
                    </option>

                    <option value="placed_student_responsibility" {{ old('internship_type', $letter->internship_type) == 'placed_student_responsibility' ? 'selected' : '' }}>
                        Placed Student Responsibility Letter
                    </option>

                </select>

                @error('internship_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>

                <input type="date"
                       name="issue_date"
                       max="{{ now()->toDateString() }}"
                       value="{{ old('issue_date', $letter->issue_date ? \Carbon\Carbon::parse($letter->issue_date)->format('Y-m-d') : '') }}"
                       class="form-control @error('issue_date') is-invalid @enderror"
                       required>

                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>


            {{-- NORMAL OFFER LETTER FIELDS --}}
            <div class="col-md-12 d-none" id="normal_offer_fields">

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Normal Offer Letter Details</strong>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            {{-- Internship Start Date --}}
                            <div class="form-group col-md-6">
                                <label>Internship Start Date</label>

                                <input type="date"
                                       name="offer_training_start_date"
                                       value="{{ old('offer_training_start_date', $letter->offer_training_start_date ? \Carbon\Carbon::parse($letter->offer_training_start_date)->format('Y-m-d') : '') }}"
                                       class="form-control @error('offer_training_start_date') is-invalid @enderror">

                                @error('offer_training_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Internship Duration --}}
                            <div class="form-group col-md-6">
                                <label>Internship Duration (Months)</label>

                                <input type="number"
                                       name="offer_training_duration_months"
                                       min="1"
                                       step="1"
                                       value="{{ old('offer_training_duration_months', $letter->offer_training_duration_months) }}"
                                       placeholder="e.g. 6"
                                       class="form-control @error('offer_training_duration_months') is-invalid @enderror">

                                @error('offer_training_duration_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Internship Type --}}
                            <div class="form-group col-md-6">
                                <label>Internship Type</label>

                                <input type="text"
                                       name="offer_internship_type"
                                       value="{{ old('offer_internship_type', $letter->offer_internship_type) }}"
                                       placeholder="e.g. Free Internship / Paid Internship"
                                       class="form-control @error('offer_internship_type') is-invalid @enderror">

                                @error('offer_internship_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- PPO Evaluation Period --}}
                            <div class="form-group col-md-6">
                                <label>PPO Evaluation Period (Months)</label>

                                <input type="number"
                                       name="offer_ppo_evaluation_months"
                                       min="1"
                                       step="1"
                                       value="{{ old('offer_ppo_evaluation_months', $letter->offer_ppo_evaluation_months) }}"
                                       placeholder="e.g. 3"
                                       class="form-control @error('offer_ppo_evaluation_months') is-invalid @enderror">

                                @error('offer_ppo_evaluation_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            {{-- STRICT OFFER LETTER FIELDS --}}
            <div class="col-md-12 d-none" id="strict_offer_fields">

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Strict Offer Letter Details</strong>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            {{-- Training Start Date --}}
                            <div class="form-group col-md-6">
                                <label>Training Start Date</label>

                                <input type="date"
                                       name="offer_training_start_date"
                                       value="{{ old('offer_training_start_date', $letter->offer_training_start_date ? \Carbon\Carbon::parse($letter->offer_training_start_date)->format('Y-m-d') : '') }}"
                                       class="form-control @error('offer_training_start_date') is-invalid @enderror">

                                @error('offer_training_start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Initial Training Duration --}}
                            <div class="form-group col-md-6">
                                <label>Initial Training Duration (Months)</label>

                                <input type="number"
                                       name="offer_training_duration_months"
                                       min="1"
                                       step="1"
                                       value="{{ old('offer_training_duration_months', $letter->offer_training_duration_months) }}"
                                       class="form-control @error('offer_training_duration_months') is-invalid @enderror">

                                @error('offer_training_duration_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Probation Duration --}}
                            <div class="form-group col-md-6">
                                <label>Probation Duration (Months)</label>

                                <input type="number"
                                       name="offer_probation_duration_months"
                                       min="1"
                                       step="1"
                                       value="{{ old('offer_probation_duration_months', $letter->offer_probation_duration_months) }}"
                                       class="form-control @error('offer_probation_duration_months') is-invalid @enderror">

                                @error('offer_probation_duration_months')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Working Hours --}}
                            <div class="form-group col-md-6">
                                <label>Working Hours</label>

                                <input type="text"
                                       name="offer_working_hours"
                                       value="{{ old('offer_working_hours', $letter->offer_working_hours) }}"
                                       placeholder="e.g. 9:30 AM to 6:30 PM"
                                       class="form-control @error('offer_working_hours') is-invalid @enderror">

                                @error('offer_working_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Service Agreement --}}
                            <div class="form-group col-md-6">
                                <label>Service Agreement Duration</label>

                                <input type="text"
                                       name="offer_service_agreement_duration"
                                       value="{{ old('offer_service_agreement_duration', $letter->offer_service_agreement_duration) }}"
                                       placeholder="e.g. 12 months or 2 years"
                                       class="form-control @error('offer_service_agreement_duration') is-invalid @enderror">

                                @error('offer_service_agreement_duration')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            {{-- Annual Salary --}}
                            <div class="form-group col-md-6">
                                <label>Annual Salary / Package (₹)</label>

                                <input type="number"
                                       name="offer_annual_salary"
                                       min="0"
                                       step="1"
                                       value="{{ old('offer_annual_salary', $letter->offer_annual_salary) }}"
                                       placeholder="e.g. 310000"
                                       class="form-control @error('offer_annual_salary') is-invalid @enderror">

                                @error('offer_annual_salary')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>
            </div>


            {{-- Subject --}}
            <div class="form-group col-md-6 d-none" id="subject_field">
                <label>Subject</label>

                <input type="text"
                       name="subject"
                       value="{{ old('subject', $letter->subject) }}"
                       class="form-control @error('subject') is-invalid @enderror">

                @error('subject')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            {{-- Roll Number --}}
            <div class="form-group col-md-6 d-none" id="roll_number_field">
                <label>Roll Number</label>

                <input type="text"
                       name="roll_number"
                       value="{{ old('roll_number', $letter->roll_number) }}"
                       class="form-control @error('roll_number') is-invalid @enderror">

                @error('roll_number')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            {{-- Semester --}}
            <div class="form-group col-md-6 d-none" id="semester_field">
                <label id="semester_field_label">
                    {{ old('internship_type', $letter->internship_type) == 'confirmation_letter' ? 'Batch' : 'Semester' }}
                </label>

                <input type="text"
                       name="semester"
                       value="{{ old('semester', $letter->semester) }}"
                       class="form-control @error('semester') is-invalid @enderror">

                @error('semester')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


            {{-- Letter Content --}}
            <div class="form-group col-md-12 d-none" id="letter_content_field">
                <label>Letter Content</label>

                <textarea name="letter_content"
                          id="editor"
                          class="form-control @error('letter_content') is-invalid @enderror"
                          rows="8">{{ old('letter_content', $letter->letter_content) }}</textarea>

                @error('letter_content')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>

        <a href="{{ route('student-additional-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>

    </form>
</div>
@endsection


@push('scripts')

<script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('editor');
</script>

<script>
const letterTemplates = @json($templates);
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const letterType = document.getElementById('internship_type');

    const contentField = document.getElementById('letter_content_field');
    const subjectField = document.getElementById('subject_field');
    const rollNumberField = document.getElementById('roll_number_field');
    const semesterField = document.getElementById('semester_field');
    const semesterFieldLabel = document.getElementById('semester_field_label');

    const normalOfferFields = document.getElementById('normal_offer_fields');
    const strictOfferFields = document.getElementById('strict_offer_fields');

    const normalOfferInputs = normalOfferFields.querySelectorAll('input');
    const strictOfferInputs = strictOfferFields.querySelectorAll('input');

    function toggleFields() {

        const type = letterType.value;

        const isContent = [
            'stipend',
            'custom',
            'student_responsibility',
            'placed_student_responsibility'
        ].includes(type);

        const isCustom = type === 'custom';

        const isWithRollNumber = [
            'with_roll_number',
            'with_roll_number_internship',
            'confirmation_letter'
        ].includes(type);

        const isNormalOffer = type === 'offer';
        const isStrictOffer = type === 'strict_offer_letter';

        contentField.classList.toggle('d-none', !isContent);
        subjectField.classList.toggle('d-none', !isCustom);

        rollNumberField.classList.toggle('d-none', !isWithRollNumber);
        semesterField.classList.toggle('d-none', !isWithRollNumber);

        // Normal Offer Letter
        normalOfferFields.classList.toggle('d-none', !isNormalOffer);

        normalOfferInputs.forEach(function (input) {
            input.required = isNormalOffer;
            input.disabled = !isNormalOffer;
        });

        // Strict Offer Letter
        strictOfferFields.classList.toggle('d-none', !isStrictOffer);

        strictOfferInputs.forEach(function (input) {
            input.required = isStrictOffer;
            input.disabled = !isStrictOffer;
        });

        if (type === 'confirmation_letter') {
            semesterFieldLabel.innerText = 'Batch';
        } else {
            semesterFieldLabel.innerText = 'Semester';
        }

        // CKEditor template handling
        if (CKEDITOR.instances.editor) {

            const currentContent = CKEDITOR.instances.editor.getData().trim();

            if (type === 'student_responsibility' && currentContent === '') {

                CKEDITOR.instances.editor.setData(
                    letterTemplates.student_responsibility ?? ''
                );

            } else if (type === 'placed_student_responsibility' && currentContent === '') {

                CKEDITOR.instances.editor.setData(
                    letterTemplates.placed_student_responsibility ?? ''
                );

            }
        }
    }

    letterType.addEventListener('change', toggleFields);

    // Show the correct fields for the saved letter type
    toggleFields();

});

$(document).ready(function () {

    $('#student_select').select2({
        placeholder: 'Search student name',
        width: '100%'
    });

});
</script>

@endpush
