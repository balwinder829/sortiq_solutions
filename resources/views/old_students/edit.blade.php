
@extends('layouts.app')

@section('title', 'Edit Old Student')

@section('content')
@php
    $selectedTechnologies = old(
        'technology',
        !empty($student->technology)
            ? array_map('trim', explode(',', $student->technology))
            : []
    );
@endphp

<div class="container-fluid mt-4">

    <div class="row">
        <div class="col-12">

            <form method="POST"
                  action="{{ route('old-students.update', $student->id) }}">

                @csrf
                @method('PUT')

                <div class="card">

                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="mb-0">
                            Edit Old Student - S.No. {{ $student->sno ?? '-' }}
                        </h4>

                        <a href="{{ route('old-students.index') }}"
                           class="btn btn-secondary btn-sm">
                            Back to Old Students
                        </a>
                    </div>

                    <div class="card-body">

                        {{-- Validation Errors --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <strong>Please correct the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row g-3">

                            {{-- Student Name --}}
                            <div class="col-md-6">
                                <label class="form-label">Student Name</label>
                                <input type="text"
                                       name="student_name"
                                       class="form-control"
                                       maxlength="255"
                                       value="{{ old('student_name', $student->student_name) }}">
                                @error('student_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Father Name --}}
                            <div class="col-md-6">
                                <label class="form-label">Father Name</label>
                                <input type="text"
                                       name="f_name"
                                       class="form-control"
                                       maxlength="255"
                                       value="{{ old('f_name', $student->f_name) }}">
                                @error('f_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Serial Number --}}
                            <div class="col-md-6">
                                <label class="form-label">Serial No.</label>
                                <input type="text"
                                       class="form-control"
                                       value="{{ $student->sno }}"
                                       readonly>
                            </div>

                            {{-- Gender --}}
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">-- Select Gender --</option>
                                    <option value="male"
                                        {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>
                                        Male
                                    </option>
                                    <option value="female"
                                        {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>
                                        Female
                                    </option>
                                </select>
                                @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Contact --}}
                            <div class="col-md-6">
                                <label class="form-label">Contact</label>
                                <input type="text"
                                       name="contact"
                                       class="form-control"
                                       maxlength="20"
                                       value="{{ old('contact', $student->contact) }}">
                                @error('contact')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Alternative Phone --}}
                            <div class="col-md-6">
                                <label class="form-label">Alternative Phone</label>
                                <input type="text"
                                       name="alternative_phone"
                                       class="form-control"
                                       maxlength="20"
                                       value="{{ old('alternative_phone', $student->alternative_phone) }}">
                                @error('alternative_phone')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email"
                                       name="email_id"
                                       class="form-control"
                                       value="{{ old('email_id', $student->email_id) }}">
                                @error('email_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                           

{{-- From Session --}}
<div class="col-md-6">
    <label class="form-label">From Session</label>

    <input type="text"
           class="form-control"
           value="{{ $sessionMap->get($student->from_session)?->session_display_name
                ?? $sessionMap->get($student->from_session)?->session_name
                ?? $student->from_session }}"
           readonly>

    {{-- Submit original session without allowing user to change it --}}
    <input type="hidden"
           name="from_session"
           value="{{ $student->from_session }}">

    <small class="text-muted">
        Original session is retained and cannot be changed.
    </small>
</div>

                           {{-- Destination Session --}}
<div class="col-md-6">
    <label class="form-label">
        Destination Session <span class="text-danger">*</span>
    </label>

    <select name="session" class="form-control" required>
        <option value="">-- Select Destination Session --</option>

        @foreach($sessions as $session)
            <option value="{{ $session->id }}"
                {{ (string)old('session', $student->session) === (string)$session->id ? 'selected' : '' }}>
                {{ $session->session_name }}
            </option>
        @endforeach
    </select>

    @error('session')
        <small class="text-danger">{{ $message }}</small>
    @enderror
</div>

                            {{-- College --}}
                            <div class="col-md-6">
                                <label class="form-label">College</label>
                                <select name="college_name"
                                        class="form-control select2">
                                    <option value="">-- Select College --</option>

                                    @foreach($colleges as $college)
                                        <option value="{{ $college->id }}"
                                            {{ (string)old('college_name', $student->college_name) === (string)$college->id ? 'selected' : '' }}>
                                            {{ $college->FullName ?? $college->college_name ?? 'College '.$college->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('college_name')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Technology --}}
                            <div class="col-md-6">
                                <label class="form-label">Technology</label>
                                <select name="technology[]"
                                        class="form-control select2"
                                        multiple>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ in_array((string)$course->id, array_map('strval', $selectedTechnologies), true) ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    You can select multiple technologies.
                                </small>
                                @error('technology')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                                @error('technology.*')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Department --}}
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <input type="text"
                                       name="department"
                                       class="form-control"
                                       value="{{ old('department', $student->department) }}">
                                @error('department')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="col-md-6">
                                <label class="form-label">Status</label>
                                <input type="text"
                                       name="status"
                                       class="form-control"
                                       value="{{ old('status', $student->status) }}">
                                @error('status')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Study Mode --}}
                            <div class="col-md-6">
                                <label class="form-label">Study Mode</label>
                                <select name="is_online" class="form-control">
                                    <option value="0"
                                        {{ (string)old('is_online', $student->is_online) === '0' ? 'selected' : '' }}>
                                        Offline
                                    </option>
                                    <option value="1"
                                        {{ (string)old('is_online', $student->is_online) === '1' ? 'selected' : '' }}>
                                        Online
                                    </option>
                                </select>
                                @error('is_online')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Joining Date --}}
                            <div class="col-md-6">
                                <label class="form-label">Joining Date</label>
                                <input type="date"
                                       name="join_date"
                                       class="form-control"
                                       value="{{ old('join_date', $student->join_date ? \Carbon\Carbon::parse($student->join_date)->format('Y-m-d') : '') }}">
                                @error('join_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-6">
                                <label class="form-label">Start Date</label>
                                <input type="date"
                                       name="start_date"
                                       class="form-control"
                                       value="{{ old('start_date', $student->start_date ? \Carbon\Carbon::parse($student->start_date)->format('Y-m-d') : '') }}">
                                @error('start_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-6">
                                <label class="form-label">End Date</label>
                                <input type="date"
                                       name="end_date"
                                       class="form-control"
                                       value="{{ old('end_date', $student->end_date ? \Carbon\Carbon::parse($student->end_date)->format('Y-m-d') : '') }}">
                                @error('end_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-md-12">
                                <label class="form-label">Address</label>
                                <textarea name="address"
                                          class="form-control"
                                          rows="3">{{ old('address', $student->address) }}</textarea>
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn btn-primary">
                                Update Old Student
                            </button>

                            <a href="{{ route('old-students.index') }}"
                               class="btn btn-secondary">
                                Cancel
                            </a>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>

</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
      rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select option',
        allowClear: true
    });
});
</script>
@endpush
