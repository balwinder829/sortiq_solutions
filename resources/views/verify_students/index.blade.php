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
                    <td>{{ $student->sno ?? '-' }}</td>
                    <td>{{ $student->student_name }}</td>
                    <td>{{ $student->email_id }}</td>
                    <td>{{ $student->courseData->course_name ?? '-' }}</td>
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
    $('#studentsTable').DataTable({
        pageLength: 100
    });
});
</script>
@endpush
