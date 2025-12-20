@extends('layouts.app')

@section('content')
<style>
     table.dataTable td {
    text-transform: capitalize;
}
 </style>
<div class="container">
    <a href="{{ route('courses.create') }}" class="btn mb-3" style="background-color: #6b51df; color: #fff;">Add Course</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="table-responsive">
    <table id="course_table" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course Name</th>
                <!-- <th>Created At</th> -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($courses as $course)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $course->course_name }}</td>
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
@endsection
@push('scripts')
<script>
    $(document).ready(function () {
        $('#course_table').DataTable({
            "pageLength": 50,
            "lengthMenu": [5, 10, 25, 50, 100],
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
@endpush