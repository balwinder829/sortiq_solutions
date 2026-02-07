@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Student Evaluation</h4>

    <form method="POST" action="{{ route('student-evaluations.store') }}">
        @csrf

        <div class="row">

            {{-- Student --}}
            <div class="form-group col-md-12">
                <label>Select Student</label>
                <select name="student_id" id="student_select" class="form-control" required>
                    <option value="">Select Student</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}"
                            data-name="{{ $student->student_name }}"
                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->student_name }}
                            {{ in_array(strtolower($student->gender), ['male','m']) ? 's/o' : 'd/o' }}
                            {{ $student->f_name ?: 'NA' }}
                            from {{ $student->collegeData->FullName ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Trainer --}}
            <div class="form-group col-md-6">
                <label>Trainer</label>
                <select name="trainer_id" class="form-control" required>
                    <option value="">Select Trainer</option>
                    @foreach($trainers as $trainer)
                        <option value="{{ $trainer->id }}"
                            {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                            {{ ucwords($trainer->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Attendance --}}
            <div class="form-group col-md-6">
                <label>Attendance %</label>
                <input type="number"
                       name="attendance_percentage"
                       class="form-control"
                       value="{{ old('attendance_percentage') }}"
                       required>
                 @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

             {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ old('email') }}">
                     @error('email')
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
                           {{ old($field) === $k ? 'checked' : '' }}
                           required>
                    {{ $v }}
                </label>
                @endforeach
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
                           {{ old('projects') === $v ? 'checked' : '' }}
                           required>
                    {{ ucfirst($v) }}
                </label>
                @endforeach
            </div>

            {{-- Assignments --}}
            <div class="form-group col-md-6">
                <label>Assignments</label><br>
                @foreach(['completed','partial','pending'] as $v)
                <label class="me-3">
                    <input type="radio"
                           name="assignments"
                           value="{{ $v }}"
                           {{ old('assignments') === $v ? 'checked' : '' }}
                           required>
                    {{ ucfirst($v) }}
                </label>
                @endforeach
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('student-evaluations.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
