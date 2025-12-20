@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">          
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Student Detail</h4>
                    </div>
                    
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('students.update', $student->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <!-- Student Name -->
                                <div class="form-group col-md-6">
                                    <label>Student Name</label>
                                    <input type="text" name="student_name" maxlength="55" required class="form-control" 
                                        value="{{ old('student_name', $student->student_name) }}">
                                </div>

                                <!-- Father Name -->
                                <div class="form-group col-md-6">
                                    <label>Father Name</label>
                                    <input type="text" name="f_name" maxlength="55" required class="form-control" 
                                        value="{{ old('f_name', $student->f_name) }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Serial No.</label>
                                    <input type="text" name="sno" maxlength="55" required class="form-control" 
                                        value="{{ old('sno', $student->sno) }}">
                                </div>
                                <!-- Gender -->
                                <div class="form-group col-md-6">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required>
                                        <option value="" disabled>--Select--</option>
                                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <!-- Session -->
                                <div class="form-group col-md-6">
                                    <label>Session</label>
                                    <select name="session" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($sessions as $session)
                                            <option value="{{ $session->session_name }}" 
                                                {{ old('session', $student->session) == $session->session_name ? 'selected' : '' }}>
                                                {{ $session->session_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- College -->
                                <div class="form-group col-md-6">
                                    <label>College</label>
                                    <select name="college_name" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($colleges as $college)
                                            <option value="{{ $college->college_name }}" 
                                                {{ old('college_name', $student->college_name) == $college->college_name ? 'selected' : '' }}>
                                                {{ $college->college_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Contact -->
                                <div class="form-group col-md-6">
                                    <label>Contact No</label>
                                    <input type="text" name="contact" class="form-control" 
                                        value="{{ old('contact', $student->contact) }}">
                                </div>

                                <!-- Email -->
                                <div class="form-group col-md-6">
                                    <label>Email</label>
                                    <input type="email" name="email_id" class="form-control" 
                                        value="{{ old('email_id', $student->email_id) }}">
                                </div>

                                <!-- Status -->
                                <div class="form-group col-md-6">
                                    <label>Status</label>
                                    <select name="status" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        <option value="joined" {{ old('status', $student->status) == 'joined' ? 'selected' : '' }}>Joined</option>
                                        <option value="dropout" {{ old('status', $student->status) == 'dropout' ? 'selected' : '' }}>Dropout</option>
                                        <option value="certificate_only" {{ old('status', $student->status) == 'certificate_only' ? 'selected' : '' }}>Certificate only</option>
                                        <option value="shift_patiala" {{ old('status', $student->status) == 'shift_patiala' ? 'selected' : '' }}>Shift to Patiala</option>
                                    </select>
                                </div>

                                <!-- Technology -->
                                <div class="form-group col-md-6">
                                    <label>Technology</label>
                                    <select name="technology" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->course_name }}" 
                                                {{ old('technology', $student->technology) == $course->course_name ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fees -->
                                <div class="form-group col-md-6">
                                    <label>Total Fees</label>
                                    <input type="number" name="total_fees" class="form-control" 
                                        value="{{ old('total_fees', $student->total_fees) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Registration Fees</label>
                                    <input type="number" name="reg_fees" class="form-control" 
                                        value="{{ old('reg_fees', $student->reg_fees) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Pending Fees</label>
                                    <input type="number" name="pending_fees" class="form-control" 
                                        value="{{ old('pending_fees', $student->pending_fees) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Reg Due Amount</label>
                                    <input type="number" name="reg_due_amount" class="form-control" 
                                        value="{{ old('reg_due_amount', $student->reg_due_amount) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Next Due Date</label>
                                    <input type="date" name="next_due_date" class="form-control"
                                        value="{{ old('next_due_date', $student->next_due_date) }}">
                                </div>

                                


                                <!-- Department -->
                                <div class="form-group col-md-6">
                                    <label>Department</label>
                                    <select name="department" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($department as $dept)
                                            <option value="{{ $dept->name }}" 
                                                {{ old('department', $student->department) == $dept->name ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Dates -->
                                <div class="form-group col-md-6">
                                    <label>Date of Joining</label>
                                    <input type="date" name="join_date" class="form-control" 
                                        value="{{ old('join_date', $student->join_date) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control" 
                                        value="{{ old('start_date', $student->start_date) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control" 
                                        value="{{ old('end_date', $student->end_date) }}">
                                </div>

                                <!-- Batch -->
                                <div class="form-group col-md-6">
                                    <label>Batch</label>
                                    <select name="batch_assign" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->batch_name }}" 
                                                {{ old('batch_assign', $student->batch_assign) == $batch->batch_name ? 'selected' : '' }}>
                                                {{ $batch->batch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Reference -->
                                <div class="form-group col-md-6">
                                    <label>Reference</label>
                                    <select name="reference" class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($references as $reference)
                                            <option value="{{ $reference->name }}" 
                                                {{ old('reference', $student->reference) == $reference->name ? 'selected' : '' }}>
                                                {{ $reference->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Buttons -->
                            <button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Update</button>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
