@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Leave Details</h5>
        </div>

        <div class="card-body">

            {{-- Alerts --}}
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Employee Name:</strong>
                    <p>{{ $leave->emp_name }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Employee Code:</strong>
                    <p>{{ $leave->emp_code }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Email:</strong>
                    <p>{{ $leave->email }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Contact:</strong>
                    <p>{{ $leave->contact ?? 'N/A' }}</p>
                </div>

                <div class="col-md-6">
                    <strong>Status:</strong>
                    <p>
                        @if($leave->status == 'approved')
                            <span class="badge bg-success">Approved</span>
                        @elseif($leave->status == 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>From Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($leave->from_date)->format('j F Y') }}</p>
                </div>

                <div class="col-md-6">
                    <strong>To Date:</strong>
                    <p>{{ \Carbon\Carbon::parse($leave->to_date)->format('j F Y') }}</p>
                </div>
            </div>

            <div class="mb-3">
                <strong>Total Days:</strong>
                <p>{{ $leave->total_days }}</p>
            </div>

            <div class="mb-3">
                <strong>Reason:</strong>
                <p>{{ $leave->reason ?? 'N/A' }}</p>
            </div>

            <div class="mb-3">
                <strong>Applied On:</strong>
                <p>{{ \Carbon\Carbon::parse($leave->created_at)->format('j F Y h:i A') }}</p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="mt-4 d-flex gap-2">

                <a href="{{ route('admin.employee.leave.index') }}" class="btn btn-secondary">
                    Back
                </a>

                @if($leave->status == 'pending')
                    <a href="{{ route('admin.employee.leave.approve', $leave->id) }}"
                       class="btn btn-success">
                        Approve
                    </a>

                    <a href="{{ route('admin.employee.leave.reject', $leave->id) }}"
                       class="btn btn-danger">
                        Reject
                    </a>
                @endif

            </div>

        </div>
    </div>

</div>

@endsection