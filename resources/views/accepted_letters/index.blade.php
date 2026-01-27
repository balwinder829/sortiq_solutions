@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Accepted Letters</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('accepted-letters.create') }}"
               class="btn" style="background:#6b51df;color:#fff;">
                Generate Accepted Letter
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table id="lettersTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Emp Code</th>
                <th>Email</th>
                <th>File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td>{{ $letter->name }}</td>
                <td>{{ $letter->emp_code ?? '-' }}</td>
                <td>{{ $letter->email }}</td>
                <td>
                    <span class="badge bg-success">
                        {{ strtoupper(pathinfo($letter->file_path, PATHINFO_EXTENSION)) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('accepted-letters.edit', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('accepted-letters.download', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

@push('scripts')
<script>
$(function () {
    $('#lettersTable').DataTable();
});
</script>
@endpush
