@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="fw-bold mb-4">
        Student Details — {{ $student->student_name }}
    </h3>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">

            <div class="row">

                {{-- LEFT COLUMN --}}
                <div class="col-md-6 mb-3">
                    <h5 class="fw-bold">Basic Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Name:</strong> {{ $student->student_name }}</li>
                        <li class="list-group-item"><strong>Father Name:</strong> {{ $student->f_name ?? '-' }}</li>
                        <li class="list-group-item"><strong>Mobile:</strong> {{ $student->contact }}</li>
                        <li class="list-group-item"><strong>Email:</strong> {{ $student->email_id }}</li>
                        <li class="list-group-item"><strong>Gender:</strong> {{ $student->gender ?? '-' }}</li>
                        <li class="list-group-item"><strong>Session:</strong> {{ $student->session }}</li>
                    </ul>
                </div>

                {{-- RIGHT COLUMN --}}
                <div class="col-md-6 mb-3">
                    <h5 class="fw-bold">Academic Details</h5>
                    <ul class="list-group">
                        <li class="list-group-item"><strong>College:</strong> {{ $student->collegeData->FullName }}</li>
                        <li class="list-group-item"><strong>Course / Technology:</strong> {{ $student->technology ?? '-' }}</li>
                        <li class="list-group-item"><strong>Batch:</strong> {{ $student->batch_id ?? '-' }}</li>
                        <li class="list-group-item"><strong>Status:</strong> {{ $student->status ?? '-' }}</li>
                        <li class="list-group-item"><strong>Created At:</strong> {{ $student->created_at->format('d M Y') }}</li>
                    </ul>
                </div>

            </div>

            {{-- FEES INFORMATION --}}
            <h5 class="fw-bold mt-4">Fee Information</h5>
            <ul class="list-group mb-3">
                <li class="list-group-item"><strong>Total Fees:</strong> {{ $student->total_fees ?? '-' }}</li>
                <li class="list-group-item"><strong>Paid Fees:</strong> {{ $student->paid_fees ?? '-' }}</li>
                <li class="list-group-item"><strong>Pending Fees:</strong> {{ $student->pending_fees ?? '-' }}</li>
                <li class="list-group-item"><strong>Next Due Date:</strong> 
                    {{ $student->next_due_date ? \Carbon\Carbon::parse($student->next_due_date)->format('d M Y') : '-' }}
                </li>
            </ul>

            {{-- CERTIFICATE --}}
            <h5 class="fw-bold">Certificate</h5>
            <ul class="list-group mb-4">
                <li class="list-group-item text-capitalize">
                    <strong>Status:</strong>
                    {{ $student->certificate_status == 1 ? 'Issued' : 'Not Issued' }}
                </li>
            </ul>

            {{-- BACK BUTTON --}}
           <!--  <a href="{{ route('students.index') }}" class="btn btn-secondary">
                ← Back to Students List
            </a> -->

        </div>
    </div>

</div>

@endsection
