@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Student Accepted Letters</h1>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('student-accepted-letters.create') }}"
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
                <th>#</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>File</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($letters as $letter)
            <tr>
                <td></td>
                <td>{{ ucwords($letter->student->student_name ?? '') }}</td>
                
                <td>{{ $letter->email }}</td>
                <td>
                    <span class="badge bg-success">
                        {{ strtoupper(pathinfo($letter->file_path, PATHINFO_EXTENSION)) }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('student-accepted-letters.edit', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="{{ route('student-accepted-letters.download', $letter) }}" class="btn btn-sm">
                        <i class="fas fa-download"></i>
                    </a>

                     {{-- Delete --}}
                        <form action="{{ route('student-accepted-letters.destroy', $letter) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Letter"  data-swal-confirm="Delete this letter?">
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

@push('scripts')
<script>
$(function () {
   var table =  $('#lettersTable').DataTable({
        columnDefs: [
            {
                targets: 0, // first column
                searchable: false,
                orderable: false
            }
        ]
    });

    table.on('draw.dt', function () {
        var PageInfo = table.page.info();

        table.column(0, { page: 'current' }).nodes().each(function (cell, i) {
            cell.innerHTML = PageInfo.start + i + 1;
        });
    }).draw();
});
</script>
@endpush
