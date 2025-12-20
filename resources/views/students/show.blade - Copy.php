@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container mt-4">
    <h3>Student Details</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>ID</th>
                    <td>{{ $student->id }}</td>
                </tr>
                <tr>
                    <th>Student Name</th>
                    <td>{{ $student->student_name }}</td>
                </tr>
                <tr>
                    <th>Father's Name</th>
                    <td>{{ $student->f_name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>{{ $student->email_id }}</td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td>{{ $student->contact }}</td>
                </tr>
                <tr>
                    <th>College</th>
                    <td>{{ $student->college_name }}</td>
                </tr>
                <tr>
                    <th>Department</th>
                    <td>{{ $student->department }}</td>
                </tr>
                <tr>
                    <th>Technology</th>
                    <td>{{ $student->technology }}</td>
                </tr>
                <tr>
                    <th>Duration</th>
                    <td>{{ $student->duration }}</td>
                </tr>
                <tr>
                    <th>Session</th>
                    <td>{{ $student->session }}</td>
                </tr>
                <tr>
                    <th>Total Fees</th>
                    <td>{{ $student->total_fees }}</td>
                </tr>
                <tr>
                    <th>Registration Fees</th>
                    <td>{{ $student->reg_fees }}</td>
                </tr>
                <tr>
                    <th>Pending Fees</th>
                    <td>{{ $student->pending_fees }}</td>
                </tr>
                <tr>
                    <th>Join Date</th>
                    <td>{{ $student->join_date }}</td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td>{{ $student->start_date }}</td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td>{{ $student->end_date }}</td>
                </tr>
                <tr>
                    <th>Batch Assign</th>
                    <td>{{ $student->batch_assign }}</td>
                </tr>
                <tr>
                    <th>Reference</th>
                    <td>{{ $student->reference }}</td>
                </tr>
                <tr>
                    <th>Due Date</th>
                    <td>{{ $student->due_date }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }}">
                            {{ $student->status }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Email Count (Confirmation)</th>
                    <td>{{ $student->email_count_confirmation }}</td>
                </tr>
                <tr>
                    <th>Email Count (Certificate)</th>
                    <td>{{ $student->email_count_certificate }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('students.index') }}" class="btn btn-secondary">Back</a>
        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning">Edit</a>
    </div>
</div>
@endsection
