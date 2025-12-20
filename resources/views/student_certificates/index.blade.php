@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">
    <h2>Students Certificates</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('student_certificates.create') }}" class="btn" style="background-color: #6b51df; color: #fff;">Add New Certificate</a>
        <a href="{{ route('student_certificates.upload_form') }}" class="btn btn-success">Upload CSV</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- 🔍 Filter Form --}}
    <form method="GET" action="{{ route('student_certificates.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="first_name" class="form-control" placeholder="First Name" value="{{ request('first_name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="last_name" class="form-control" placeholder="Last Name" value="{{ request('last_name') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="colleage" class="form-control" placeholder="College" value="{{ request('colleage') }}">
            </div>
            <div class="col-md-3">
                <input type="text" name="duration" class="form-control" placeholder="Duration" value="{{ request('duration') }}">
            </div>
            <div class="col-md-3 mt-2">
                <input type="text" name="technology" class="form-control" placeholder="Technology" value="{{ request('technology') }}">
            </div>
            <div class="col-md-3 mt-2">
                <input type="text" name="semester" class="form-control" placeholder="Semester" value="{{ request('semester') }}">
            </div>
            <div class="col-md-3 mt-2">
                <input type="text" name="stream" class="form-control" placeholder="Stream" value="{{ request('stream') }}">
            </div>
            <div class="col-md-3 mt-2">
                <input type="text" name="branch" class="form-control" placeholder="Branch" value="{{ request('branch') }}">
            </div>
            <div class="col-md-12 mt-2">
                <button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Filter</button>
                <a href="{{ route('student_certificates.index') }}" class="btn btn-secondary">Reset</a>
            </div>
        </div>
    </form>

    {{-- 📋 DataTable --}}
    <table id="student_certificates" class="table table-bordered table-striped w-100">
        <thead  class="table-light">
            <tr>
                <th>SNO</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>College</th>
                <th>Duration</th>
                <th>Technology</th>
                <th>Semester</th>
                <th>Stream</th>
                <th>Branch</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($certificates as $cert)
                <tr>
                    <td>{{ $cert->sno }}</td>
                    <td>{{ $cert->first_name }}</td>
                    <td>{{ $cert->last_name }}</td>
                    <td>{{ $cert->colleage }}</td>
                    <td>{{ $cert->duration }}</td>
                    <td>{{ $cert->technology }}</td>
                    <td>{{ $cert->semester }}</td>
                    <td>{{ $cert->stream }}</td>
                    <td>{{ $cert->branch }}</td>
                    <td>{{ \Carbon\Carbon::parse($cert->start_date)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($cert->end_date)->format('d-m-Y') }}</td>
                    <td>
                        <a href="{{ route('student_certificates.edit', $cert->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" title="Edit">
                            <i class="fa fa-edit"></i>
                        </a>
                        <form action="{{ route('student_certificates.destroy', $cert->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" title="Delete" onclick="return confirm('Delete this student?')">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endsection

@section('scripts')


<script>
$(document).ready(function () {
    // ✅ Initialize DataTable after DOM is ready
    $('#student_certificates').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true,
        "order": [[0, "desc"]]
    });

    // ✅ Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>
@endsection
