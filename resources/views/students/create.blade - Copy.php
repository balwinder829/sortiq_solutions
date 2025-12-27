@extends('layouts.app')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">          
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Student Detail</h4>
                    </div>
                    
                    <div class="card-body">
                        <div class="basic-form scrollable-form">
                            <form method="POST" action="{{ route('students.store') }}">
                                @csrf

                                <div class="form-row">
                                    
                                    <div class="form-group col-md-6">
                                        <label>Student Name</label>
                                        <input type="text" maxlength="55" required class="form-control" 
                                               name="student_name" value="{{ old('student_name') }}">
                                        @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Father Name</label>
                                        <input type="text" maxlength="55" required class="form-control" 
                                               name="f_name" value="{{ old('f_name') }}">
                                        @error('f_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Serial No.</label>
                                        <input type="text" maxlength="55" required class="form-control" 
                                               name="sno" value="{{ old('sno') }}">
                                        @error('sno') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Gender</label>
                                        <select name="gender" class="form-control" required>
                                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>--Select--</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Session</label>
                                        <select name="session" required class="form-control">
                                            <option value="" disabled {{ old('session') ? '' : 'selected' }}>Choose...</option>
                                            @foreach($sessions as $session)
                                                <option value="{{ $session->session_name }}" 
                                                    {{ old('session') == $session->session_name ? 'selected' : '' }}>
                                                    {{ $session->session_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('session') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>College Name</label>
                                        <select name="college_name" required class="form-control">
                                            <option value="" disabled {{ old('college_name') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($colleges as $college)
                                                <option value="{{ $college->college_name }}" 
                                                    {{ old('college_name') == $college->college_name ? 'selected' : '' }}>
                                                    {{ $college->FullName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('college_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Contact No</label>
                                        <input type="number" required class="form-control" 
                                               name="contact" value="{{ old('contact') }}">
                                        @error('contact') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Email</label>
                                        <input type="email" required name="email_id" class="form-control"
                                               value="{{ old('email_id') }}">
                                        @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Status</label>
                                        <select name="status" required class="form-control">
                                            <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choose one</option>
                                            <option value="joined" {{ old('status') == 'joined' ? 'selected' : '' }}>Joined</option>
                                            <option value="dropout" {{ old('status') == 'dropout' ? 'selected' : '' }}>Dropout</option>
                                            <option value="certificate_only" {{ old('status') == 'certificate_only' ? 'selected' : '' }}>Certificate only</option>
                                            <option value="shift_patiala" {{ old('status') == 'shift_patiala' ? 'selected' : '' }}>Shift to Patiala</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Technology</label>
                                        <select name="technology" required class="form-control">
                                            <option value="" disabled {{ old('technology') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->course_name }}" 
                                                    {{ old('technology') == $course->course_name ? 'selected' : '' }}>
                                                    {{ $course->course_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('technology') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Total Fees</label>
                                        <input type="text" name="total_fees" required class="form-control"
                                               value="{{ old('total_fees') }}">
                                        @error('total_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Reg Fees</label>
                                        <input type="text" name="reg_fees" required class="form-control"
                                               value="{{ old('reg_fees') }}">
                                        @error('reg_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Total Pending Fees</label>
                                        <input type="text" name="pending_fees" class="form-control"
                                               value="{{ old('pending_fees') }}">
                                        @error('pending_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Registration Pending Amount</label>
                                        <input type="text" name="reg_due_amount" class="form-control"
                                               value="{{ old('reg_due_amount') }}">
                                        @error('reg_due_amount') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Next Due Date</label>
                                        <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>Department</label>
                                        <select name="department" class="form-control" required>
                                            <option value="" disabled {{ old('department') ? '' : 'selected' }}>--Select--</option>
                                            @foreach($department as $departments)
                                                <option value="{{ $departments->name }}" 
                                                    {{ old('department') == $departments->name ? 'selected' : '' }}>
                                                    {{ $departments->name }}
                                                </option>
                                            @endforeach    
                                        </select>
                                        @error('department') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Date of Joining</label>
                                        <input type="date" name="join_date" class="form-control"
                                               value="{{ old('join_date') }}">
                                        @error('join_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Duration</label>
                                        <select name="duration" class="form-control" required>
                                            <option value="" disabled {{ old('duration') ? '' : 'selected' }}>--Select--</option>
                                            <option value="20"  {{ old('duration') == '20'  ? 'selected' : '' }}>21 Days</option>
                                            <option value="13"  {{ old('duration') == '13'  ? 'selected' : '' }}>2 Weeks</option>
                                            <option value="29"  {{ old('duration') == '29'  ? 'selected' : '' }}>4 Weeks</option>
                                            <option value="44"  {{ old('duration') == '44'  ? 'selected' : '' }}>6 Weeks</option>
                                            <option value="59"  {{ old('duration') == '59'  ? 'selected' : '' }}>8 Weeks</option>
                                            <option value="89"  {{ old('duration') == '89'  ? 'selected' : '' }}>3 Months</option>
                                            <option value="119" {{ old('duration') == '119' ? 'selected' : '' }}>4 Months</option>
                                            <option value="179" {{ old('duration') == '179' ? 'selected' : '' }}>6 Months</option>
                                            <option value="269" {{ old('duration') == '269' ? 'selected' : '' }}>9 Months</option>
                                            <option value="364" {{ old('duration') == '364' ? 'selected' : '' }}>1 Year</option>
                                        </select>
                                        @error('duration') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control"
                                               value="{{ old('start_date') }}">
                                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control"
                                               value="{{ old('end_date') }}">
                                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Batch Assign</label>
                                        <select name="batch_assign" required class="form-control">
                                            <option value="" disabled {{ old('batch_assign') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($batches as $batch)
                                                <option value="{{ $batch->batch_name }}" 
                                                    {{ old('batch_assign') == $batch->batch_name ? 'selected' : '' }}>
                                                    {{ $batch->batch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('batch_assign') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Reference</label>
                                        <select name="reference" class="form-control">
                                            <option value="" disabled {{ old('reference') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($references as $reference)
                                                <option value="{{ $reference->name }}" 
                                                    {{ old('reference') == $reference->name ? 'selected' : '' }}>
                                                    {{ $reference->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('reference') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group" style="margin-top: 3%; margin-left: 1%;">
                                        <button type="submit" class="btn" style="background-color: #6b51df; color: #fff; margin-left: 8px;">Save</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
