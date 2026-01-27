@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Student Evaluation</h4>

    {{-- GLOBAL VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the errors below.</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ route('student-evaluations.update', $student_evaluation) }}">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Student --}}
            <div class="form-group col-md-12">
                <label>Select Student</label>
                <select name="student_id"
                        id="student_select"
                        class="form-control"
                        disabled>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            {{ $student_evaluation->student_id == $student->id ? 'selected' : '' }}>
                            {{ $student->student_name }}
                            {{ in_array(strtolower($student->gender), ['male','m']) ? 's/o' : 'd/o' }}
                            {{ $student->f_name ?: 'NA' }}
                            from {{ $student->collegeData->FullName ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>

                <input type="hidden" name="student_id" value="{{ $student_evaluation->student_id }}">
            </div>

            {{-- Trainer --}}
            <div class="form-group col-md-6">
                <label>Trainer</label>
                <select name="trainer_id"
                        class="form-control @error('trainer_id') is-invalid @enderror"
                        required>
                    <option value="">Select Trainer</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ old('trainer_id', $student_evaluation->trainer_id) == $trainer->id ? 'selected' : '' }}>
                            {{ $trainer->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('trainer_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Attendance --}}
            <div class="form-group col-md-6">
                <label>Attendance %</label>
                <input type="number"
                       name="attendance_percentage"
                       class="form-control @error('attendance_percentage') is-invalid @enderror"
                       value="{{ old('attendance_percentage', $student_evaluation->attendance_percentage) }}"
                       required>
                @error('attendance_percentage')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            @php $ratings=['good'=>'Good','avg'=>'Avg','bad'=>'Bad']; @endphp

            @foreach(['behavior','technical','live_project','soft_skills','github'] as $field)
            <div class="form-group col-md-12">
                <label>{{ ucfirst(str_replace('_',' ',$field)) }}</label><br>

                @foreach($ratings as $k=>$v)
                <label class="me-3">
                    <input type="radio"
                           name="{{ $field }}"
                           value="{{ $k }}"
                           {{ old($field, $student_evaluation->$field) === $k ? 'checked' : '' }}>
                    {{ $v }}
                </label>
                @endforeach

                @error($field)
                    <br><small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            @endforeach

            {{-- Projects --}}
            <div class="form-group col-md-6">
                <label>Projects</label><br>
                @foreach(['completed','partial','pending'] as $v)
                <label class="me-3">
                    <input type="radio"
                           name="projects"
                           value="{{ $v }}"
                           {{ old('projects', $student_evaluation->projects) === $v ? 'checked' : '' }}>
                    {{ ucfirst($v) }}
                </label>
                @endforeach
                @error('projects')
                    <br><small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Assignments --}}
            <div class="form-group col-md-6">
                <label>Assignments</label><br>
                @foreach(['completed','partial','pending'] as $v)
                <label class="me-3">
                    <input type="radio"
                           name="assignments"
                           value="{{ $v }}"
                           {{ old('assignments', $student_evaluation->assignments) === $v ? 'checked' : '' }}>
                    {{ ucfirst($v) }}
                </label>
                @endforeach
                @error('assignments')
                    <br><small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('student-evaluations.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
