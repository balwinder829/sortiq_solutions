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
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('certificates.update', $student->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="form-row">
                                <!-- Student Name -->
                                <div class="form-group col-md-6">
                                    <label>Student Name</label>
                                    <input type="text" name="student_name" maxlength="55" required class="form-control" 
                                        value="{{ old('student_name', $student->student_name) }}"  oninput="capitalizeWords(this)">
                                </div>

                                <!-- Father Name -->
                                <div class="form-group col-md-6">
                                    <label>Father Name</label>
                                    <input type="text" name="f_name" maxlength="55" required class="form-control" 
                                        value="{{ old('f_name', $student->f_name) }}"  oninput="capitalizeWords(this)">
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

                                <!-- College -->
                                <div class="form-group col-md-6">
                                    <label>College</label>
                                    <select name="college_name" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($colleges as $college)
                                            <option value="{{ $college->id }}" 
                                                {{ old('college_name', $student->college_name) == $college->id ? 'selected' : '' }}>
                                                {{ $college->FullName }}
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

                                        @foreach($student_status as $s)
                                            <option value="{{ $s->status }}"
                                                {{ old('status', $student->status) == $s->status ? 'selected' : '' }}>
                                                {{ $s->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>


                                <!-- Technology -->
                                <div class="form-group col-md-6">
                                    <label>Technology</label>
                                    <select name="technology" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                {{ old('technology', $student->technology) == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fees -->
                                <div class="form-group col-md-6">
                                    <label>Total Fees</label>
                                    <input type="number" name="total_fees" class="form-control" id="total_fees"
                                        value="{{ old('total_fees', $student->total_fees) }}" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g,'$1')">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Registration Fees</label>
                                    <input type="number" name="reg_fees" class="form-control"   id="reg_fees"
                                        value="{{ old('reg_fees', $student->reg_fees) }}" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g,'$1')">
                                </div>

                                 <div class="form-group col-md-6">
                                    <label>Paid Fees</label>
                                    <input type="text" name="paid_fees" required class="form-control" id="paid_fees" 
                                           value="{{ old('paid_fees', $student->paid_fees) }}" oninput="this.value=this.value.replace(/[^0-9.]/g,'').replace(/(\..*)\./g,'$1')">
                                    @error('paid_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    <small id="fee_warning" class="text-danger d-none">
                                        Registration fees + Paid fees cannot be greater than Total fees.
                                    </small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Total Pending Fees</label>
                                    <input type="number" name="pending_fees" class="form-control" id="pending_fees" 
                                        value="{{ old('pending_fees', $student->pending_fees) }}">
                                </div>

                                 

                                <div class="form-group col-md-6">
                                    <label>Pending Fees Due Date</label>
                                    <input type="date" name="next_due_date" class="form-control"
                                        value="{{ old('next_due_date', $student->next_due_date) }}">
                                </div>

                                


                                <!-- Department -->
                                    <!--  -->

                                <!-- Dates -->
                                <div class="form-group col-md-6">
                                    <label>Registered date</label>
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
                               <!--  <div class="form-group col-md-6">
                                    <label>Batch</label>
                                    <select name="batch_assign" required class="form-control">
                                        <option value="" disabled>--Choose--</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}" 
                                                {{ old('batch_assign', $student->batch_assign) == $batch->id ? 'selected' : '' }}>
                                                {{ $batch->batch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div> -->

                                 
    <div class="form-group col-md-6">
        <label>Part-Time Offer?</label>
        <select name="part_time_offer" class="form-control">
            <option value="0" {{ $student->part_time_offer == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ $student->part_time_offer == 1 ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Placement Offer?</label>
        <select name="placement_offer" class="form-control">
            <option value="0" {{ $student->placement_offer == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ $student->placement_offer == 1 ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>PG Offer?</label>
        <select name="pg_offer" class="form-control">
            <option value="0" {{ $student->pg_offer == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ $student->pg_offer == 1 ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Send To Close?</label>
        <select name="send_to_close" class="form-control">
            <option value="0" {{ $student->send_to_close == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ $student->send_to_close == 1 ? 'selected' : '' }}>Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Is Placed?</label>
       <select name="is_placed" class="form-control">
            <option value="0" {{ $student->is_placed == 0 ? 'selected' : '' }}>Not Placed</option>
            <option value="1" {{ $student->is_placed == 1 ? 'selected' : '' }}>Placed</option>
        </select>
    </div>

 


                                
                            </div>

                            <!-- Buttons -->
                            <button type="submit" class="btn" style="background-color: #6b51df; color: #fff;">Update</button>
                            <a href="{{ route('certificates.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function calculatePendingFees() {
        let totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
        let regFees   = parseFloat(document.getElementById('reg_fees').value) || 0;
        let paidFees   = parseFloat(document.getElementById('paid_fees').value) || 0;

        let warningEl = document.getElementById('fee_warning');

        // Check validation
        if ((regFees + paidFees) > totalFees) {
            warningEl.classList.remove('d-none');

            // Optional: auto-fix by resetting last input
            document.getElementById('paid_fees').value = '';
            paidFees = 0;
        } else {
            warningEl.classList.add('d-none');
        }
        let pending = totalFees - regFees - paidFees;

        // Prevent negative value
        if (pending < 0) pending = 0;

        document.getElementById('pending_fees').value = pending.toFixed(2);
    }

    document.getElementById('total_fees').addEventListener('input', calculatePendingFees);
    document.getElementById('reg_fees').addEventListener('input', calculatePendingFees);
    document.getElementById('paid_fees').addEventListener('input', calculatePendingFees);
    function capitalizeWords(input) {
        const start = input.selectionStart;
        const end = input.selectionEnd;

        let value = input.value.toLowerCase();
        input.value = value.replace(/\b\w/g, char => char.toUpperCase());

        input.setSelectionRange(start, end);
    }
</script>
@endsection
