@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Student Accepted Letter</h4>

    <form method="POST"
          action="{{ route('student-accepted-letters.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="row">

            {{-- Student Name --}}
            <div class="form-group col-md-6">
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

            

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email') }}">

                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- File --}}
            <div class="form-group col-md-12">
                <label>Upload Accepted Letter (PDF / Image)</label>
                <input type="file"
                       name="file"
                       class="form-control @error('file') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png"
                       required>

                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('student-accepted-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
