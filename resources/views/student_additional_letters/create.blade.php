@extends('layouts.app')

@section('content')
<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Generate {{ ucfirst($letterFor) }} Additional Letter</h4>

        <a href="{{ route('student-additional-letters.index', ['letter_for' => $letterFor]) }}"
           class="btn btn-secondary">
            Back
        </a>
    </div>

    <form method="POST" action="{{ route('student-additional-letters.store') }}">
        @csrf

        <input type="hidden" name="letter_for" value="{{ $letterFor }}">

        <div class="row">

            {{-- Student / Intern Selection --}}
            <div class="form-group col-md-12">
                <label>
                    Select {{ $letterFor === 'intern' ? 'Interns' : 'Students' }}
                    <span class="text-danger">*</span>
                </label>

                <select
                    name="{{ $letterFor === 'intern' ? 'intern_student_id[]' : 'student_id[]' }}"
                    id="person_select"
                    class="form-control select2"
                    multiple
                    required
                >

                    @if($letterFor === 'student')

                        @foreach($students as $student)
                            <option value="{{ $student->id }}">
                                {{ $student->student_name }}
                                | {{ $student->father_name_with_title ?? $student->f_name ?? '' }}
                                | {{ $student->collegeData->FullName ?? $student->place ?? 'N/A' }}
                            </option>
                        @endforeach

                    @else

                        @foreach($internStudents as $intern)
                            <option value="{{ $intern->id }}">
                                {{ $intern->student_name }}
                                | Father: {{ $intern->f_name ?? 'N/A' }}
                                | {{ $intern->collegeData->FullName ?? 'N/A' }}
                            </option>
                        @endforeach

                    @endif

                </select>

                @error('student_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror

                @error('intern_student_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type <span class="text-danger">*</span></label>

                <select name="internship_type"
                        id="internship_type"
                        class="form-control"
                        required>
                    <option value="">Select Letter Type</option>

                    @foreach($letterTypes as $value => $label)
                        <option value="{{ $value }}"
                            {{ old('internship_type') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @error('internship_type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Issue Date --}}
            <div class="form-group col-md-6">
                <label>Issue Date</label>
                <input type="date"
                       name="issue_date"
                       class="form-control"
                       value="{{ old('issue_date', date('Y-m-d')) }}"
                       max="{{ date('Y-m-d') }}">
                @error('issue_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Normal + Strict Offer Shared Fields --}}
            <div class="col-md-12" id="offer_common_fields" style="display:none;">
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Offer Letter Details</strong>
                    </div>

                    <div class="card-body">
                        <div class="row">

                            <div class="form-group col-md-6">
                                <label>Training / Internship Start Date</label>
                                <input type="date"
                                       name="offer_training_start_date"
                                       class="form-control offer-common-input"
                                       value="{{ old('offer_training_start_date') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label>Training / Internship Duration (Months)</label>
                                <input type="number"
                                       name="offer_training_duration_months"
                                       class="form-control offer-common-input"
                                       min="1"
                                       value="{{ old('offer_training_duration_months') }}">
                            </div>

                            {{-- Normal Offer Only --}}
                            <div class="form-group col-md-6 normal-offer-field">
                                <label>Internship Type</label>
                                <input type="text"
                                       name="offer_internship_type"
                                       class="form-control normal-offer-input"
                                       value="{{ old('offer_internship_type') }}">
                            </div>

                            <div class="form-group col-md-6 normal-offer-field">
                                <label>PPO Evaluation Period (Months)</label>
                                <input type="number"
                                       name="offer_ppo_evaluation_months"
                                       class="form-control normal-offer-input"
                                       min="1"
                                       value="{{ old('offer_ppo_evaluation_months') }}">
                            </div>

                            {{-- Strict Offer Only --}}
                            <div class="form-group col-md-6 strict-offer-field">
                                <label>Probation Duration (Months)</label>
                                <input type="number"
                                       name="offer_probation_duration_months"
                                       class="form-control strict-offer-input"
                                       min="1"
                                       value="{{ old('offer_probation_duration_months') }}">
                            </div>

                            <div class="form-group col-md-6 strict-offer-field">
                                <label>Working Hours</label>
                                <input type="text"
                                       name="offer_working_hours"
                                       class="form-control strict-offer-input"
                                       value="{{ old('offer_working_hours') }}">
                            </div>

                            <div class="form-group col-md-6 strict-offer-field">
                                <label>Service Agreement Duration</label>
                                <input type="text"
                                       name="offer_service_agreement_duration"
                                       class="form-control strict-offer-input"
                                       value="{{ old('offer_service_agreement_duration') }}">
                            </div>

                            <div class="form-group col-md-6 strict-offer-field">
                                <label>Annual Salary / Package</label>
                                <input type="number"
                                       name="offer_annual_salary"
                                       class="form-control strict-offer-input"
                                       min="0"
                                       step="0.01"
                                       value="{{ old('offer_annual_salary') }}">
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            {{-- Subject --}}
            <div class="form-group col-md-12" id="subject_field" style="display:none;">
                <label>Subject <span class="text-danger">*</span></label>
                <input type="text"
                       name="subject"
                       class="form-control"
                       value="{{ old('subject') }}">
                @error('subject')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Roll Number --}}
            <div class="form-group col-md-6" id="roll_number_field" style="display:none;">
                <label>Roll Number <span class="text-danger">*</span></label>
                <input type="text"
                       name="roll_number"
                       class="form-control"
                       value="{{ old('roll_number') }}">
            </div>

            {{-- Semester / Batch --}}
            <div class="form-group col-md-6" id="semester_field" style="display:none;">
                <label id="semester_label">Semester</label>
                <input type="text"
                       name="semester"
                       class="form-control"
                       value="{{ old('semester') }}">
            </div>

            {{-- Letter Content --}}
            <div class="form-group col-md-12" id="letter_content_field" style="display:none;">
                <label>Letter Content <span class="text-danger">*</span></label>
                <textarea name="letter_content"
                          id="letter_content"
                          class="form-control"
                          rows="8">{{ old('letter_content') }}</textarea>

                @error('letter_content')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="col-md-12 mt-3">
                <button type="submit" class="btn btn-primary">
                    Generate Letter
                </button>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    $('#person_select').select2({
        placeholder: 'Select {{ $letterFor === "intern" ? "Interns" : "Students" }}',
        width: '100%',
        allowClear: true
    });

    const templates = @json($templates);
    const letterFor = @json($letterFor);

    function setFieldsEnabled(container, enabled) {
        $(container).find('input, textarea, select').each(function () {
            $(this).prop('disabled', !enabled);

            if (!enabled) {
                $(this).prop('required', false);
            }
        });
    }

    function toggleLetterFields() {

        const type = $('#internship_type').val();

        // Hide all conditional fields first.
        $('#offer_common_fields').hide();
        $('#subject_field').hide();
        $('#roll_number_field').hide();
        $('#semester_field').hide();
        $('#letter_content_field').hide();

        $('.normal-offer-field').hide();
        $('.strict-offer-field').hide();

        setFieldsEnabled('#offer_common_fields', false);
        setFieldsEnabled('#subject_field', false);
        setFieldsEnabled('#roll_number_field', false);
        setFieldsEnabled('#semester_field', false);
        setFieldsEnabled('#letter_content_field', false);

        // Custom letter subject.
        if (type === 'custom') {
            $('#subject_field').show();
            setFieldsEnabled('#subject_field', true);
            $('#subject_field input').prop('required', true);
        }

        // Roll number / semester fields are Student-only.
        const rollTypes = [
            'with_roll_number',
            'with_roll_number_internship',
            'confirmation_letter'
        ];

        if (letterFor === 'student' && rollTypes.includes(type)) {
            $('#roll_number_field, #semester_field').show();

            setFieldsEnabled('#roll_number_field', true);
            setFieldsEnabled('#semester_field', true);

            $('#roll_number_field input, #semester_field input').prop('required', true);

            $('#semester_label').text(
                type === 'confirmation_letter' ? 'Batch' : 'Semester'
            );
        }

        // Content fields.
        const contentTypes = [
            'stipend',
            'custom',
            'student_responsibility',
            'placed_student_responsibility'
        ];

        if (contentTypes.includes(type)) {
            $('#letter_content_field').show();
            setFieldsEnabled('#letter_content_field', true);

            if (type === 'stipend' || type === 'custom') {
                $('#letter_content').prop('required', true);
            }
        }

        // Offer and Strict Offer.
        if (type === 'offer' || type === 'strict_offer_letter') {

            $('#offer_common_fields').show();
            setFieldsEnabled('#offer_common_fields', true);

            $('#offer_common_fields .offer-common-input').prop('required', true);

            if (type === 'offer') {
                $('.normal-offer-field').show();
                $('.normal-offer-input').prop('disabled', false).prop('required', true);
            }

            if (type === 'strict_offer_letter') {
                $('.strict-offer-field').show();
                $('.strict-offer-input').prop('disabled', false).prop('required', true);
            }
        }

        // Load responsibility templates.
        if (
            type === 'student_responsibility' ||
            type === 'placed_student_responsibility'
        ) {
            const content = templates[type] || '';

            if (window.letterContentEditor) {
                window.letterContentEditor.setData(content);
            } else {
                $('#letter_content').val(content);
            }
        }
    }

    $('#internship_type').on('change', toggleLetterFields);

    toggleLetterFields();

    // CKEditor support, if available in the existing layout.
    if (window.ClassicEditor) {
        ClassicEditor.create(document.querySelector('#letter_content'))
            .then(editor => {
                window.letterContentEditor = editor;

                const type = $('#internship_type').val();

                if (
                    type === 'student_responsibility' ||
                    type === 'placed_student_responsibility'
                ) {
                    editor.setData(templates[type] || '');
                }
            })
            .catch(error => console.error(error));
    }

});
</script>
@endpush