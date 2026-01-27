@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Import Colleges</h3>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- IMPORT SUMMARY --}}
    @if(session('import_summary'))
        <div class="alert alert-info alert-dismissible fade show">
            <strong>Import Summary</strong><br>
            Added: {{ session('import_summary.created') }}<br>
            Skipped (duplicates): {{ session('import_summary.skipped') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- SKIPPED RECORDS --}}
    @if(session('skipped_colleges'))
        <div class="alert alert-warning">
            <strong>Skipped Colleges</strong>
            <ul class="mb-0">
                @foreach(session('skipped_colleges') as $row)
                    <li>
                        Row {{ $row['row'] }} :
                        {{ $row['college'] }},
                        {{ $row['district'] }},
                        {{ $row['state'] }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- SAMPLE FILE --}}
    <div class="mb-3">
        <a href="{{ asset('sample/colleges_import_sample.xlsx') }}"
           class="btn btn-outline-secondary btn-sm"
           download>
            📥 Download Sample File
        </a>
    </div>

    <form action="{{ route('colleges.import') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Upload Excel / CSV</label>
            <input type="file"
                   name="file"
                   required
                   class="form-control"
                   accept=".csv,.xlsx,.xls">
        </div>

        <button type="submit" class="btn btn-primary">
            Import Colleges
        </button>
    </form>
</div>
@endsection
