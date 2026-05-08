@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Verify Students</h1>
        </div>
    </div>

    <table id="studentsTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>S. No.</th>
                <th>Name</th>
                <th>Email</th>
                <th>Course</th>
                <th>Starting Date</th>
                <th>End Date</th>
                <!-- <th>Status</th> -->
            </tr>
        </thead>

        <tbody>
            @foreach($students as $student)
                <tr>
                    <td></td>
                    <td>{{ $student->sno ?? '-' }}</td>
                    <td>{{ $student->student_name }}</td>
                    <td>{{ $student->email_id }}</td>
                    <td>{{ $student->course_name ?? '-' }}</td>
                    <td>
                        {{ $student->start_date
                            ? \Carbon\Carbon::parse($student->start_date)->format('d M Y')
                            : '-' }}
                    </td>
                    <td>
                        {{ $student->end_date
                            ? \Carbon\Carbon::parse($student->end_date)->format('d M Y')
                            : '-' }}
                    </td>
                   <!--  <td>
                        @if($student->is_verified ?? false)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td> -->
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
   var table = $('#studentsTable').DataTable({
        paging: true,
        info: true,
        ordering: false,
        searching: true,
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
