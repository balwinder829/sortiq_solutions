@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Tests</h2>
    <a href="{{ route('admin.tests.create')}}" class="btn btn-primary mb-3" style="background-color: #593bdb; border: none;">Create New Test</a>
        <!-- Filter Form -->
    <form method="GET" action="{{ route('admin.tests.index') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <input type="text" name="title" class="form-control" placeholder="Search Title" value="{{ request('title') }}">
        </div>
        <div class="col-md-3">
            <input type="text" name="course" class="form-control" placeholder="Search Course" value="{{ request('course') }}">
        </div>
        <div class="col-md-3 d-flex">
            <button type="submit" class="btn btn-primary me-2" style="background-color: #593bdb; border: none;">Search</button>
            <a href="{{ route('admin.tests.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered" id="student_test">
        <thead>
            <tr>
                <th>S. No</th>
                <th>Title</th>
                <th>Course</th>
                <th>Access Key</th>
                <th>Actions</th>
                <th>Results</th>
                <th>Student Link</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tests as $test)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $test->title }}</td>
                    <td>{{ $test->studentCourse->course_name }}</td>
                    <td>{{ $test->access_key }}</td>
                    <td>
                        <a href="{{ route('admin.tests.show', $test->id) }}" class="btn btn-sm btn-info">View Questions</a> 
                        <a href="{{ route('admin.questions.create', $test->id) }}" class="btn btn-sm btn-success">Add Questions</a>
                        <a href="{{ route('admin.tests.edit', $test->id) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.tests.destroy', $test->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete?')">Delete</button>
                        </form>
                    </td>
                    <td>
                    <a href="{{ route('admin.tests.results', $test->id) }}" class="btn btn-info btn-sm">View Results</a>
                    </td>
                    <td>
                    @if($test->slug)
                        <a href="{{ route('student.test.slug', $test->slug) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Open Test Link
                        </a>
                    @else
                        <span class="text-danger">No Link</span>
                    @endif
                </td>
                </tr>
            @endforeach
        </tbody>    
    </table>
</div>
@endsection
