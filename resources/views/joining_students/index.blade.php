@extends('layouts.app')

@section('content')

<style>
    table.dataTable td {
        vertical-align: middle;
        text-transform: capitalize;
    }

    thead th {
        background-color: #f8f9fa !important;
        font-weight: 600;
        border-bottom: 1px solid #dee2e6 !important;
    }

    table.table-bordered > :not(caption) > * > * {
        border-color: #dee2e6;
    }

    .badge {
        font-size: 12px;
        padding: 5px 8px;
    }

    .no-wrap {
        white-space: nowrap;
    }
</style>

<div class="container">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Joined Students</h4>
    </div>

    {{-- ================= FLASH MESSAGE ================= --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ================= TABLE ================= --}}
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="studentsTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>College</th>
                    <th>Duration</th>
                    <th>Technology</th>
                    <th>Date of Joining</th>
                    <th class="no-wrap">Joined At</th>
                    <th class="no-wrap">Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $student->student_name }}</td>
                        <td>{{ $student->father_name }}</td>
                        <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                        <td>{{ $student->courseData->course_name ?? '-' }}</td>
                        <td>{{ $student->durationData->name ?? '-' }}</td>
                        <td class="no-wrap">
                            {{ \Carbon\Carbon::parse($student->date_of_joining)->format('d M Y') }}
                        </td>
                        <td class="no-wrap text-muted">
                            {{ $student->created_at->format('d M Y h:i A') }}
                        </td>
                        <td class="no-wrap text-center">
                            <a href="{{ route('joined_students.edit', $student->id) }}"
                               class="btn btn-sm"
                                data-bs-toggle="tooltip"
                               title="Edit">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('joined_students.destroy', $student->id) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this student?')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="btn btn-sm"
                                        data-bs-toggle="tooltip"
                                        title="Delete">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>

                    </tr>
               @endforeach
            </tbody>

        </table>
    </div>

</div>

@endsection

@push('scripts')
<script>
$(document).ready(function () {
    $('#studentsTable').DataTable({
        paging: true,
        info: true,
        ordering: false,
        searching: true
    });
});
</script>
@endpush
