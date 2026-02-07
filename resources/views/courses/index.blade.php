@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
/* Highlight clickable student count */
.student-count {
    color: #0d6efd;              /* Bootstrap primary blue */
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    transition: all 0.2s ease-in-out;
}

.student-count:hover {
    color: #084298;              /* Darker blue on hover */
    text-decoration: none;
    transform: scale(1.05);
}

/* Optional badge look */
.student-count.badge-style {
    background-color: #e7f1ff;
    padding: 4px 10px;
    border-radius: 12px;
    text-decoration: none;
}

 </style>
<div class="container">
    <div class="row mb-2">
        <div class="col-md-2">
            <h1 class="page_heading">Technologies</h1>
        </div>
         
        <div class="col-md-10">
            <div class="d-flex justify-content-end">
                <a href="{{ route('courses.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add Technology</a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
    <table id="course_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Technology Name</th>
                <th>Students</th>
                <!-- <th>Created At</th> -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $course->course_name }}</td>
                    {{-- Student Count --}}
                    <!-- <td class="text-center">
                        <span class="student-count view-students badge-style"
                              data-course-id="{{ $course->id }}"
                              data-course-name="{{ $course->course_name }}">
                            {{ $course->students_count }}
                        </span>
                    </td> -->
                    <td>
                        <a href="{{ route('common_filtered_student', [
                            'technology' => $course->id
                        ]) }}"
                           class="text-decoration-none">
                            <span class="badge bg-success">
                                {{ $course->students_count }}
                            </span>
                        </a>
                    </td>



                    <!-- <td>{{ optional($course->created_at)->format('Y-m-d') }}</td> -->
                    <td class="text-center">
                        <div class="mb-2">
                        <a href="{{ route('courses.edit', $course->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="fa fa-edit"></i></a>

                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                        </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
  
</div>

{{-- ================= STUDENTS MODAL ================= --}}
<div class="modal fade" id="studentsModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    Students – <span id="modalCourseName"></span>
                </h5>

                <a href="#" id="downloadExcel"
                   class="btn btn-sm btn-success ms-3">
                    <i class="fa fa-file-excel"></i> Download Excel
                </a>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>SNO</th>
                                <th>Session ID</th>
                                <th>Session Name</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    Click student count to load data
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#course_table').DataTable({
            "pageLength": 10,
            "lengthMenu": [10, 15, 20, 25, 50, 100],
             // "scrollX": true // <-- Add this
        });
    });
</script>
<script>
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
</script>
<script>
$(document).ready(function () {

     

    $(document).on('click', '.view-students', function () {

        let courseId   = $(this).data('course-id');
        let courseName = $(this).data('course-name');

        $('#modalCourseName').text(courseName);
        $('#studentsTableBody').html(
            '<tr><td colspan="5" class="text-center">Loading...</td></tr>'
        );

        $('#downloadExcel').attr(
            'href',
            `/courses/${courseId}/students/export-excel`
        );

        $.ajax({
            url: `/courses/${courseId}/students`,
            type: 'GET',
            success: function (students) {

                let rows = '';

                if (students.length === 0) {
                    rows = `<tr>
                                <td colspan="5" class="text-center">
                                    No students found
                                </td>
                            </tr>`;
                } else {
                    students.forEach((student, index) => {
                        rows += `<tr>
                            <td>${index + 1}</td>
                            <td>${student.student_name}</td>
                            <td>${student.sno ?? '-'}</td>
                            <td>${student.session_id ?? '-'}</td>
                            <td>${student.session_name ?? '-'}</td>
                        </tr>`;
                    });
                }

                $('#studentsTableBody').html(rows);
                $('#studentsModal').modal('show');
            }
        });
    });
});
</script>

@endpush