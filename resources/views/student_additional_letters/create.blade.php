@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Generate Student Additional Letter</h4>

    <form method="POST" action="{{ route('student-additional-letters.store') }}">
        @csrf

        <div class="row">

            {{-- Student Name --}}
           <!--  <div class="form-group col-md-12">
                <label>Student Name</label>
                <input type="text"
                       name="student_name"
                       class="form-control @error('student_name') is-invalid @enderror"
                       value="{{ old('student_name') }}"
                       id="student_name"
                       required>
                @error('student_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> -->

             {{-- Student Name --}}
            <div class="form-group col-md-12">
                <label>Select Student</label>
                <select name="student_id" id="student_select" class="form-control" required>
                    <option value="">Select Student</option>

                    @foreach($students as $student)

                        @php
                            $gender = strtolower($student->gender ?? '');
                            $isMarried = $student->is_married ?? 0;

                            if ($gender === 'female') {
                                $relation = $isMarried ? 'W/O' : 'D/O';
                            } else {
                                $relation = 'S/O';
                            }

                            $collegeOrPlace = $student->collegeData->FullName ?? $student->place ?? 'N/A';
                        @endphp

                        <option value="{{ $student->id }}"
                            data-name="{{ $student->student_name }}"
                            {{ old('student_id') == $student->id ? 'selected' : '' }}>

                            {{ $student->student_name }}
                            {{ $relation }}
                            {{ $student->f_name ?: 'NA' }}
                            from {{ $collegeOrPlace }}

                        </option>

                    @endforeach
                </select>
            </div>


            <!-- {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div> -->

            {{-- Internship Type --}}
           

            {{-- Letter Type --}}
            <div class="form-group col-md-6">
                <label>Letter Type</label>
                <select name="internship_type" id="internship_type" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="free" {{ old('internship_type') == 'free' ? 'selected' : '' }} >Free Internship Letter</option>
                    <option value="internship" {{ old('internship_type') == 'internship' ? 'selected' : '' }} >Internship Letter</option>
                    <option value="internship_with_package" {{ old('internship_type') == 'internship_with_package' ? 'selected' : '' }} >Internship With Package Letter</option>
                    <option value="stipend" {{ old('internship_type') == 'stipend' ? 'selected' : '' }} >Stipend Internship Letter</option>
                    <option value="offer" {{ old('internship_type') == 'offer' ? 'selected' : '' }} >Offer Letter</option>
                    <option value="custom" {{ old('internship_type') == 'custom' ? 'selected' : '' }} >Custom Type Letter</option>
                    <option value="noc" {{ old('internship_type') == 'noc' ? 'selected' : '' }} >Non-Consent Letter</option>
                    <option value="mutual_consent" {{ old('internship_type') == 'mutual_consent' ? 'selected' : '' }} >Mutual Consent Letter</option>
                    <option value="training_consent" {{ old('internship_type') == 'training_consent' ? 'selected' : '' }} >Training Consent Letter</option>
                    <option value="placement" {{ old('internship_type') == 'placement' ? 'selected' : '' }} >Student Placement Letter</option>
                </select>
                @error('internship_type')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Subject --}}
            <div class="form-group col-md-6 d-none" id="subject_field">
                <label>Subject</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject') }}">
                @error('subject')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- College --}}
            <!-- <div class="form-group col-md-6">
                <label>College</label>
                <select name="college_id" class="form-control" required>
                    <option value="">Select College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ old('college_id') == $college->id ? 'selected' : '' }}>{{ $college->FullName }}</option>
                    @endforeach
                </select>
            </div> -->

            {{-- Letter Content --}}
            <div class="form-group col-md-12 d-none" id="letter_content_field">
                <label>Letter Content</label>
                <textarea name="letter_content"
                          id="editor"
                          class="form-control @error('letter_content') is-invalid @enderror"
                          rows="8">{{ old('letter_content') }}</textarea>

                @error('letter_content')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('student-additional-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection

@push('scripts')
<!-- <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script> -->
 <script src="{{ asset('ckeditor/ckeditor.js') }}"></script>

<script>
    CKEDITOR.replace('editor');
</script>
<script>
// document.getElementById('internship_type').addEventListener('change', function () {
//     document.getElementById('subject_field').style.display =
//         this.value === 'custom' ? 'block' : 'none';
// });

// document.addEventListener('DOMContentLoaded', function () {
//     const letterType = document.getElementById('internship_type');
// console.log(letterType);
    // function toggleFields() {
    //     // const isExperience = letterType.value === 'experience';
    //     const isContent = letterType.value === 'stipend' || letterType.value === 'custom';
    //     const iscustom = letterType.value === 'custom';

    
    //     document.getElementById('editor').classList.toggle('d-none', !isContent);
    //     document.getElementById('subject_field').classList.toggle('d-none', !iscustom);
        
    // }

    // letterType.addEventListener('change', toggleFields);
    // toggleFields();
// });
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const letterType = document.getElementById('internship_type');
    const contentField = document.getElementById('letter_content_field');
    const subjectField = document.getElementById('subject_field');

    function toggleFields() {
        const isContent = letterType.value === 'stipend' || letterType.value === 'custom';
        const isCustom  = letterType.value === 'custom';

        contentField.classList.toggle('d-none', !isContent);
        subjectField.classList.toggle('d-none', !isCustom);
    }

    letterType.addEventListener('change', toggleFields);
    toggleFields(); // run on page load (old values)
});
</script>

@endpush
