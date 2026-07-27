@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Student Additional Letter</h4>

    <form method="POST"
          action="{{ route('student-additional-letters.update', $letter) }}">
        @csrf
        @method('PUT')

        <div class="row">

           <!--  <div class="form-group col-md-6">
                <label>Student Name</label>
                <input type="text" name="student_name"
                       class="form-control"
                       value="{{ old('student_name', $letter->student_name) }}"
                       required>
            </div>
 -->
            <!-- <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" name="email"
                       class="form-control"
                       value="{{ old('email', $letter->email) }}"
                       required>
            </div> -->
             {{-- Student Name --}}
            <div class="form-group col-md-12">
                <label>Select Student</label>
                <select name="student_id" id="student_select" class="form-control" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            data-name="{{ $student->student_name }}"
                            {{ $letter->student_id == $student->id ? 'selected' : '' }}>
                            {{ $student->student_name }}
                            {{ in_array(strtolower($student->gender), ['male','m']) ? 's/o' : 'd/o' }}
                            {{ $student->f_name ?: 'NA' }}
                            from {{ $student->collegeData->FullName ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Internship Type</label>
                <select name="internship_type"  id="internship_type"
                        class="form-control" required>
                    <option value="free"
                        {{ $letter->internship_type == 'free' ? 'selected' : '' }}>
                        Free Internship Letter
                    </option>
                    <option value="internship"
                        {{ $letter->internship_type == 'internship' ? 'selected' : '' }}>
                        Internship Letter
                    </option>
                    <option value="internship_with_package"
                        {{ $letter->internship_type == 'internship_with_package' ? 'selected' : '' }}>
                        Internship With Package Letter
                    </option>
                    <option value="stipend"
                        {{ $letter->internship_type == 'stipend' ? 'selected' : '' }}>
                        Stipend Internship Letter
                    </option>

                    <option value="offer"
                        {{ $letter->internship_type == 'offer' ? 'selected' : '' }}>
                        Offer Letter
                    </option>

                    <option value="custom"
                        {{ $letter->internship_type == 'custom' ? 'selected' : '' }}>
                        Custom Type Letter
                    </option>
                    <option value="noc"
                        {{ $letter->internship_type == 'noc' ? 'selected' : '' }}>
                        Non-Consent Letter
                    </option>
                    <option value="mutual_consent"
                        {{ $letter->internship_type == 'mutual_consent' ? 'selected' : '' }}>
                       Mutual Consent Letter
                    </option>
                    <option value="training_consent"
                        {{ $letter->internship_type == 'training_consent' ? 'selected' : '' }}>
                       Training Consent Letter
                    </option>
                    <option value="placement"
                        {{ $letter->internship_type == 'placement' ? 'selected' : '' }}>
                       Student Placement Letter
                    </option>
                    <option value="strict_offer_letter"
                        {{ $letter->internship_type == 'strict_offer_letter' ? 'selected' : '' }}>
                       Strict Offer Letter
                    </option>
                    <option value="strict_consent_letter"
                        {{ $letter->internship_type == 'strict_consent_letter' ? 'selected' : '' }}>
                       Strict Consent Letter
                    </option>
                    <option value="stipend_policy"
                        {{ $letter->internship_type == 'stipend_policy' ? 'selected' : '' }}>
                       Stipend Policy Letter
                    </option>
                    <option value="internship_consent"
                        {{ $letter->internship_type == 'internship_consent' ? 'selected' : '' }}>
                       Internship Consent Letter
                    </option>
                    <option value="part_time_job_opportunity"
                        {{ $letter->internship_type == 'part_time_job_opportunity' ? 'selected' : '' }}>
                       Part Time Job Opportunity Letter
                    </option>
                    <option value="with_roll_number"
                        {{ $letter->internship_type == 'with_roll_number' ? 'selected' : '' }}>
                       With Roll Number Letter
                    </option>
                    <option value="with_roll_number_internship"
                        {{ $letter->internship_type == 'with_roll_number_internship' ? 'selected' : '' }}>
                       With Roll Number Internship Letter
                    </option>
                    <option value="confirmation_letter"
                        {{ $letter->internship_type == 'confirmation_letter' ? 'selected' : '' }}>
                       Confirmation Letter
                    </option>
                    <option value="student_responsibility"
                        {{ $letter->internship_type == 'student_responsibility' ? 'selected' : '' }}>
                       Student Responsibility Letter
                    </option>
                    <option value="placed_student_responsibility"
                        {{ $letter->internship_type == 'placed_student_responsibility' ? 'selected' : '' }}>
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
                <input
                    type="date"
                    name="issue_date"
                    class="form-control @error('issue_date') is-invalid @enderror"
                    max="{{ now()->toDateString() }}"
                    value="{{ old('issue_date', $letter->issue_date) }}"
                    required
                >
                @error('issue_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- Subject --}}
            <div class="form-group col-md-6 d-none" id="subject_field">
                <label>Subject</label>
                <input type="text" name="subject"
                       value="{{ old('subject', $letter->subject) }}"
                       class="form-control">
                       @error('subject')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Roll Number --}}
            <div class="form-group col-md-6 d-none" id="roll_number_field">
                <label>Roll Number</label>
                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $letter->roll_number) }}">
                @error('roll_number')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Semester --}}
            <div class="form-group col-md-6 d-none" id="semester_field">
                <label id="semester_field_label">{{ $letter->internship_type == 'confirmation_letter' ? 'Batch' : 'Semester' }}</label>
                <input type="text" name="semester" class="form-control" value="{{ old('semester', $letter->semester) }}">
                @error('semester')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group col-md-12 d-none" id="letter_content_field">
                <label>Letter Content</label>
                <textarea name="letter_content"
                          id="editor"
                          class="form-control"
                          rows="8">{{ old('letter_content', $letter->letter_content) }}</textarea>
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
const savedContent = @json(old('letter_content', $letter->letter_content));
</script>
<!-- <script>
document.getElementById('internship_type').addEventListener('change', function () {
    document.getElementById('subject_field').style.display =
        this.value === 'custom' ? 'block' : 'none';
});
</script> -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const letterType = document.getElementById('internship_type');
    const contentField = document.getElementById('letter_content_field');
    const subjectField = document.getElementById('subject_field');
    const rollNumberField = document.getElementById('roll_number_field');
    const semesterField = document.getElementById('semester_field');
    const semesterFieldLabel = document.getElementById('semester_field_label');
    function toggleFields() {
        const isContent = letterType.value === 'stipend' || letterType.value === 'custom' || letterType.value === 'student_responsibility' || letterType.value === 'placed_student_responsibility';
        const isCustom  = letterType.value === 'custom';
        const isWithRollNumber = (letterType.value === 'with_roll_number' || letterType.value === 'with_roll_number_internship' || letterType.value === 'confirmation_letter');

        contentField.classList.toggle('d-none', !isContent);
        subjectField.classList.toggle('d-none', !isCustom);
        rollNumberField.classList.toggle('d-none', !isWithRollNumber);
        semesterField.classList.toggle('d-none', !isWithRollNumber);
        if(letterType.value === 'confirmation_letter'){
            semesterFieldLabel.innerText = "Batch";
        } else {
            semesterFieldLabel.innerText = "Semester";
        }

        if (CKEDITOR.instances.editor) {

            let content = CKEDITOR.instances.editor.getData().trim();

            if (letterType.value === 'student_responsibility') {

                if (content === '') {
                    CKEDITOR.instances.editor.setData(letterTemplates.student_responsibility ?? '');
                }

            } else if (letterType.value === 'placed_student_responsibility') {

                if (content === '') {
                    CKEDITOR.instances.editor.setData(letterTemplates.placed_student_responsibility ?? '');
                }

            }
        }
    }

    letterType.addEventListener('change', toggleFields);
    toggleFields(); // run on page load (old values)
});
</script>

@endpush
