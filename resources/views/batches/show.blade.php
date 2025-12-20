@extends('layouts.app')

@section('content')

<div class="container">

    <h3 class="mb-4 fw-bold">Batch Details</h3>

    {{-- BATCH SUMMARY CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $batch->batch_name }}</h5>
        </div>

        <div class="card-body">

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Session:</strong>
                    <div>{{ $batch->sessionData->session_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Technology:</strong>
                    <div>{{ $batch->courseData->course_name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Duration:</strong>
                    <div>{{ $batch->durationData->name ?? '-' }}</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <strong>Start Time:</strong>
                    <div>{{ \Carbon\Carbon::parse($batch->start_time)->format('h:i A') }}</div>
                </div>

                <div class="col-md-4">
                    <strong>End Time:</strong>
                    <div>{{ \Carbon\Carbon::parse($batch->end_time)->format('h:i A') }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Batch Mode:</strong>
                    <div>{{ ucfirst($batch->batch_mode) }}</div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <strong>Trainer:</strong>
                    <div>{{ $batch->trainerData->user->name ?? '-' }}</div>
                </div>

                <div class="col-md-4">
                    <strong>Status:</strong>
                    <div>
                        <span class="badge bg-info">{{ ucfirst($batch->status) }}</span>
                    </div>
                </div>

                <div class="col-md-4">
                    <strong>Total Students:</strong>
                    <div>{{ $batch->students->count() }}</div>
                </div>
            </div>

        </div>
    </div>


    {{-- STUDENT LIST CARD --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Students in this Batch</h5>
        </div>

        <div class="card-body">

            @if($batch->students->count() > 0)

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>College</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->students as $index => $student)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $student->student_name }}</td>
                                <td>{{ $student->email_id }}</td>
                                <td>{{ $student->college }}</td>
                                <td>{{ ucfirst($student->status ?? '-') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            @else
                <div class="text-center text-muted">No students assigned yet.</div>
            @endif

        </div>
    </div>

    <a href="{{ route('batches.index') }}" class="btn btn-secondary">Back to Batches</a>

</div>

@endsection
