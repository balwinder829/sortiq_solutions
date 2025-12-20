@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Import Trainer</h3>

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
@if(session('warnings') && count(session('warnings')) > 0)
    <div class="alert alert-warning mt-3">
        <strong>⚠ {{ count(session('warnings')) }} rows were skipped:</strong>

        <div class="mt-2">
            <a href="{{ route('trainers.skipped.download', 'txt') }}" class="btn btn-sm btn-secondary">Download TXT</a>
            <a href="{{ route('trainers.skipped.download', 'csv') }}" class="btn btn-sm btn-primary">Download CSV</a>
            <a href="{{ route('trainers.skipped.download', 'xlsx') }}" class="btn btn-sm btn-success">Download Excel</a>
        </div>

        <table class="table table-bordered table-sm mt-3">
            <thead>
                <tr>
                    <th>Row</th>
                    <th>Reason</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @foreach(session('warnings') as $warn)
                    <tr>
                        <td>{{ $warn['row'] }}</td>
                        <td>{{ $warn['reason'] }}</td>
                        <td>{{ $warn['value'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif




    <form action="{{ route('trainers.import') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Upload Excel/CSV</label>
            <input type="file" name="file" required class="form-control" accept=".csv,.xlsx,.xls">
        </div>

        <button type="submit" class="btn btn-primary">Import</button>
    </form>
</div>
@endsection
