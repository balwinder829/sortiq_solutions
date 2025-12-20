@php
    $isEdit = isset($student_certificate);
@endphp

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>{{ $isEdit ? 'Edit Certificate' : 'Add Certificate' }}</h2>

    <form action="{{ $isEdit ? route('student_certificates.update', $student_certificate->id) : route('student_certificates.store') }}" method="POST">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>SNO</label>
            <input type="number" name="sno" value="{{ old('sno', $student_certificate->sno ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $student_certificate->first_name ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $student_certificate->last_name ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>College</label>
            <input type="text" name="colleage" value="{{ old('colleage', $student_certificate->colleage ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Duration</label>
            <input type="text" name="duration" value="{{ old('duration', $student_certificate->duration ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Technology</label>
            <input type="text" name="technology" value="{{ old('technology', $student_certificate->technology ?? '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Semester</label>
            <input type="text" name="semester" value="{{ old('semester', $student_certificate->semester ?? '') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Stream</label>
            <input type="text" name="stream" value="{{ old('stream', $student_certificate->stream ?? '') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Branch</label>
            <input type="text" name="branch" value="{{ old('branch', $student_certificate->branch ?? '') }}" class="form-control">
        </div>
        <div class="mb-3">
            <label>Start Date</label>
            <input type="date" name="start_date" value="{{ old('start_date', isset($student_certificate->start_date) ? $student_certificate->start_date->format('Y-m-d') : '') }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>End Date</label>
            <input type="date" name="end_date" value="{{ old('end_date', isset($student_certificate->end_date) ? $student_certificate->end_date->format('Y-m-d') : '') }}" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">{{ $isEdit ? 'Update' : 'Add' }}</button>
    </form>
</div>
@endsection
