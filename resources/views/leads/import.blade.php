@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Import Leads</h3>

   {{-- SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- SINGLE ERROR --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- MULTIPLE ERRORS --}}
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <strong>Import Errors:</strong>
        <ul class="mt-2 mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif



    <form action="{{ route('leads.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Upload Excel/CSV</label>
            <input type="file" name="file" required class="form-control" accept=".csv,.xlsx,.xls">
        </div>

        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>
@endsection
