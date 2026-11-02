@extends('layouts.app')

@section('title', 'Import Intern Students')

@section('content')
<div class="container mt-4">
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h1 class="page_heading">Import Intern Students</h1>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('intern-students.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Import Errors:</strong>
            <ul class="mt-2 mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Upload Excel / CSV File</h5>
        </div>

        <div class="card-body">
            <div class="alert alert-info">
                <strong>Before importing:</strong>
                <ul class="mb-0 mt-2">
                    <li>The file must contain a <code>student_name</code> column.</li>
                    <li>The <code>status</code> column should use one of these values: <code>joined</code>, <code>dropout</code>, <code>certificate_only</code>, <code>shift_patiala</code>, or <code>passout</code>.</li>
                    <li>Fee-related columns are not used by the Intern Students module.</li>
                    <li>Use column headings in the first row. Do not include <code>id</code> or <code>source_student_id</code>; these are managed by the system.</li>
                </ul>
            </div>

            <form action="{{ route('intern-students.import') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label for="file" class="form-label">Select Excel / CSV File <span class="text-danger">*</span></label>
                    <input type="file" id="file" name="file" required class="form-control"
                           accept=".csv,.xlsx,.xls">
                    @error('file') <small class="text-danger">{{ $message }}</small> @enderror
                    <small class="text-muted">Accepted formats: CSV, XLSX, XLS.</small>
                </div>

                <button type="submit" class="btn" style="background-color:#6b51df;color:#fff;">
                    Import Intern Students
                </button>
                <a href="{{ route('intern-students.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
