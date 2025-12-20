@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Upload Certificates (CSV)</h2>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('student_certificates.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="file" class="form-label">Select CSV file</label>
            <input type="file" name="file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Upload</button>
    </form>
</div>
@endsection
