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

<div class="row mb-2 align-items-center">
    <div class="col-md-6">
        <h1 class="page_heading">Joined Students</h1>
    </div>

    <div class="col-md-6 text-end">
        <button id="sendSelected" class="btn btn-primary">
            Send to Session
        </button>
    </div>
</div>

{{-- FLASH MESSAGE --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- TABLE --}}
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="studentsTable">
        <thead>
            <tr>
                <th width="30">
                    <input type="checkbox" id="checkAll">
                </th>
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
                    <td>
                        @if(!$student->is_sent_to_detail)
                            <input type="checkbox" class="record_checkbox" value="{{ $student->id }}">
                        @else
                            <span class="badge bg-success">Sent</span>
                        @endif
                    </td>

                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $student->student_name }}</td>
                    <td>{{ $student->father_name }}</td>
                    <td>{{ $student->collegeData->FullName ?? '-' }}</td>
                    <td>{{ $student->durationData->name ?? '-' }}</td>
                    <td>{{ $student->courseData->course_name ?? '-' }}</td>

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
                              data-swal-confirm="Are you sure you want to delete this student?">
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

let selectedIds = new Set();

// DataTable
$(document).ready(function () {
    $('#studentsTable').DataTable({
        paging: true,
        info: true,
        ordering: false,
        searching: true
    });
});

// Select single
$(document).on('change', '.record_checkbox', function () {
    let id = $(this).val();

    if ($(this).is(':checked')) {
        selectedIds.add(id);
    } else {
        selectedIds.delete(id);
    }
});

// Select all
$('#checkAll').on('change', function () {
    let checked = this.checked;

    $('.record_checkbox').each(function () {
        let id = $(this).val();

        if (checked) {
            selectedIds.add(id);
        } else {
            selectedIds.delete(id);
        }

        $(this).prop('checked', checked);
    });
});

// Send to session
$('#sendSelected').click(function () {

    if (selectedIds.size === 0) {
        Swal.fire('No selection', 'Select at least one student', 'warning');
        return;
    }

    let sessionOptions = @json($sessionsList);
    let optionsHtml = '';

    Object.keys(sessionOptions).forEach(function(key) {
        optionsHtml += `<option value="${key}">${sessionOptions[key]}</option>`;
    });

    Swal.fire({
        title: 'Select Session',
        html: `<select id="session_id" class="form-control">${optionsHtml}</select>`,
        showCancelButton: true,
        confirmButtonText: 'Send'
    }).then((result) => {

        if (!result.isConfirmed) return;

        let session_id = $('#session_id').val();

        $.ajax({
            url: "{{ route('joined_students.sendToSession') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                ids: Array.from(selectedIds),
                session_id: session_id
            },
            success: function (res) {
                Swal.fire('Success', res.message, 'success');
                location.reload();
            },
            error: function () {
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        });

    });
});

</script>

@endpush
