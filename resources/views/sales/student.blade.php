@extends('layouts.app')

@section('title', 'Students')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between mb-3">
        <h3>Students</h3>
        <a href="{{ route('students.create') }}" class="btn" style="background-color: #6b51df; color: #fff;">+ Add Student</a>
    </div>

{{-- Search / Filter Form --}}
<form method="GET" action="{{ route('students.index') }}" class="mb-4">
    <div class="row g-2">
        {{-- Student Name --}}
        <div class="col-md-2">
            <input type="text" name="student_name" class="form-control"
                   placeholder="Student Name" value="{{ request('student_name') }}">
        </div>

        {{-- Father Name --}}
        <div class="col-md-2">
            <input type="text" name="f_name" class="form-control"
                   placeholder="Father Name" value="{{ request('f_name') }}">
        </div>

        {{-- Gender --}}
        <div class="col-md-1">
            <select name="gender" class="form-control">
                <option value="">Gender</option>
                <option value="Male" {{ request('gender')=='Male' ? 'selected' : '' }}>Male</option>
                <option value="Female" {{ request('gender')=='Female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>

        {{-- Session --}}
        <div class="col-md-2">
            <select name="session" class="form-control session" id="ddl_session">
                <option value="">--Session Name--</option>
                @foreach($sessions as $session)
                    <option value="{{ $session->id }}"
                        {{ request('session') == $session->session_name ? 'selected' : '' }}>
                        {{ $session->session_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- College --}}
        <div class="col-md-2">
            <select name="college_name" class="form-control collegeName" id="txtcollege">
                <option value="">--College--</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->college_name }}"
                        {{ request('college_name') == $college->college_name ? 'selected' : '' }}>
                        {{ $college->college_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Email --}}
        <div class="col-md-2">
            <input type="text" name="email_id" class="form-control"
                   placeholder="Email" value="{{ request('email_id') }}">
        </div>
        {{-- Date Range --}}
        <div class="col-md-3">
            <div class="input-group">
                <input type="date" name="start_date" class="form-control" 
                    value="{{ request('start_date') }}" placeholder="Start Date">
                <input type="date" name="end_date" class="form-control" 
                    value="{{ request('end_date') }}" placeholder="End Date">
            </div>
        </div>
        {{-- Status --}}
        <div class="col-md-2">
            <select name="status" class="form-control statusData">
                <option value="" selected>--Status--</option>
                <option value="joined" {{ request('status') == 'joined' ? 'selected' : '' }}>Joined</option>
                <option value="dropout" {{ request('status') == 'dropout' ? 'selected' : '' }}>Dropout</option>
                <option value="certificate_only" {{ request('status') == 'certificate_only' ? 'selected' : '' }}>Certificate only</option>
                <option value="shift_patiala" {{ request('status') == 'shift_patiala' ? 'selected' : '' }}>Shift to Patiala</option>
            </select>
        </div>
        {{-- Technology / Course --}}
        <div class="col-md-2">
            <select name="technology" class="form-control technology" id="txttechnology">
                <option value="">--Technology--</option>
                @foreach($courses as $course)
                    <option value="{{ $course->course_name }}"
                        {{ request('technology') == $course->course_name ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Department --}}
        <div class="col-md-2">
            <select name="department" class="form-control" id="txtdepartment">
                <option value="">--Department--</option>
                @foreach($departments as $department)
                    <option value="{{ $department->name }}"
                        {{ request('department') == $department->name ? 'selected' : '' }}>
                        {{ $department->name }}
                    </option>
                @endforeach
            </select>
        </div>
            <div class="col-md-2">
        <div class="form-check">
            <input type="checkbox" class="form-check-input" name="pending_fees" id="pending_fees"
                value="1" {{ request('pending_fees') == 1 ? 'checked' : '' }}>
            <label class="form-check-label" for="pending_fee">Pending Fee Only</label>
        </div>
    </div>
        {{-- Buttons --}}
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Search</button>
        </div>
        <div class="col-md-1 d-grid">
            <a href="{{ route('students.index') }}" class="btn btn-secondary">Reset</a>
        </div>
    </div>
</form>

    <div class="table-responsive">
        <table id="studentsTable" class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                <th><input type="checkbox" id="checkAll"></th>
                <th class="text-center">ID</th>
                <th class="text-center">Name</th>
                <th class="text-center">Father Name</th>
                <th class="text-center">Gender</th>
                <th class="text-center" width="100px">Session</th>
                <th class="text-center" width="180px">College</th>
                <th class="text-center">Contact</th>
                <th class="text-center">Email</th>
                <th class="text-center">Status</th>
                <th class="text-center">Technology</th>
                <th class="text-center">Total Fees</th>
                <th class="text-center">Reg Fees</th>
                <th class="text-center">Pending Fees</th>
                <th class="text-center">Department</th>
                <th class="text-center" width="100px">Date of Joining</th>
                <th class="text-center">Duration</th>
                <th class="text-center" width="100px">Start Date</th>
                <th class="text-center" width="100px">End Date</th>
                <th width="100px" class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td><input type="checkbox" class="record_checked" value="{{ $student->id }}"></td>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $student->student_name }}</td>
                <td>{{ $student->f_name }}</td>
                <td>{{ $student->gender }}</td>
                <td>{{ $student->session }}</td>
                <td>{{ $student->college_name }}</td>
                <td>{{ $student->contact }}</td>
                <td>{{ $student->email_id }}</td>
                <td><span class="badge bg-{{ $student->status == 'Active' ? 'success' : 'danger' }}">{{ $student->status }}</span></td>
                <td>{{ $student->technology }}</td>
                <td>{{ $student->total_fees }}</td>
                <td>{{ $student->reg_fees }}</td>
                <td>{{ $student->pending_fees }}</td>
                <td>{{ $student->department }}</td>
                <td>{{ \Carbon\Carbon::parse($student->join_date)->format('d-m-Y') }}</td>
                <td>
                    @php
                        $durations = [
                            20 => '21 Days', 13 => '2 Weeks', 29 => '4 Weeks', 44 => '6 Weeks',
                            59 => '8 Weeks', 89 => '3 Months', 119 => '4 Months', 179 => '6 Months',
                            269 => '9 Months', 364 => '1 Year'
                        ];
                    @endphp
                    {{ $durations[$student->duration] ?? $student->duration }}
                </td>
                <td>{{ \Carbon\Carbon::parse($student->start_date)->format('d-m-Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($student->end_date)->format('d-m-Y') }}</td>
            <td class="text-center">
    <div class="mb-2">
                {{-- Edit --}}
        <a href="{{ route('students.edit',$student->id) }}" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Student">
            <i class="fa fa-edit"></i>
        </a>

        {{-- Delete --}}
        <form action="{{ route('students.destroy',$student->id) }}" method="POST" style="display:inline-block;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete Student" onclick="return confirm('Delete this student?')">
                <i class="fa fa-trash"></i>
            </button>
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
    $('#studentsTable').DataTable({
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50, 100],
        "scrollX": true
    });
});
</script>
@endpush
