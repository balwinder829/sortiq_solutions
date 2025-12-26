@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card shadow-sm col-md-6 mx-auto">
        @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <div class="card-body">

            <h4 class="mb-3">Add Student</h4>

            <form method="POST"
                  action="{{ route('admin.offline.tests.store.student', $test->id) }}">
                @csrf

                <div class="mb-2">
                    <label>Name</label>
                    <input class="form-control" name="student_name" required>
                </div>

                <div class="mb-2">
                    <label>Email</label>
                    <input class="form-control" name="student_email">
                </div>

                <div class="mb-2">
                    <label>Mobile</label>
                    <input class="form-control" name="student_mobile" pattern="[0-9]{10}"
                        title="Enter a valid 10-digit mobile number"
                        maxlength="10"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="10 digit number">
                </div>

                <div class="mb-3">
                    <label>Score</label>
                    <input class="form-control" name="score" required>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
                    <button class="btn btn-success">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
