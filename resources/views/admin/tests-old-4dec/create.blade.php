@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Create Test</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.tests.store') }}">
        @csrf

        {{-- TEST TITLE --}}
        <div class="mb-3">
            <label class="fw-bold">Test Title</label>
            <input type="text" name="title" id="titleInput" class="form-control" required>
        </div>

        {{-- ACCESS KEY --}}
        <div class="mb-3">
            <label class="fw-bold">Access Key</label>
            <input type="text" name="access_key" id="accessKeyInput" class="form-control"
                   value="{{ Str::random(8) }}" readonly>
        </div>

        {{-- SLUG --}}
        <div class="mb-3">
            <label class="fw-bold">Slug</label>
            <input type="text" name="slug" id="slugInput" class="form-control" readonly>
        </div>

        {{-- COLLEGE --}}
        <div class="mb-3">
            <label class="fw-bold">Select College</label>
            <select name="college_id" class="form-control" required>
                <option value="">Select a College</option>
                @foreach ($colleges as $college)
                    <option value="{{ $college->id }}">{{ $college->college_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- COURSE --}}
        <div class="mb-3">
            <label class="fw-bold">Select Course</label>
            <select name="student_course_id" class="form-control" required>
                <option value="">Select a Course</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- SEMESTER --}}
        <div class="mb-3">
            <label class="fw-bold">Select Semester</label>
            <select name="semester_id" class="form-control" required>
                <option value="">Select Semester</option>
                @foreach ($semesters as $sem)
                    <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- BRANCH --}}
        <div class="mb-3">
            <label class="fw-bold">Select Branch</label>
            <select name="branch_id" class="form-control" required>
                <option value="">Select Branch</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->branch_name }}</option>
                @endforeach
            </select>
        </div>

        {{-- DESCRIPTION --}}
        <div class="mb-3">
            <label class="fw-bold">Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>

        <button class="btn btn-success">Create Test</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Generate slug automatically from title
document.getElementById('titleInput').addEventListener('keyup', function () {
    let title = this.value;
    let slug = title.toLowerCase().replace(/[^a-z0-9]+/g, '-');
    document.getElementById('slugInput').value = slug;
});
</script>
@endpush
