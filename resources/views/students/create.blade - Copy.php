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
                                        <input type="text" maxlength="55" class="form-control" 
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
                                        <input type="text"
                                               class="form-control"
                                               value="{{ ucwords($activeSession->session_name) ?? 'No Active Session' }}"
                                               readonly>

                                        @if(!$activeSession)
                                            <small class="text-danger">
                                                Active session not found. Please login again.
                                            </small>
                                        @endif
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>College Name</label>
                                        <select name="college_name" required class="form-control">
                                            <option value="" disabled {{ old('college_name') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($colleges as $college)
                                                <option value="{{ $college->id }}" 
                                                    {{ old('college_name') == $college->id ? 'selected' : '' }}>
                                                    {{ $college->FullName }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('college_name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" 
                                               name="contact" value="{{ old('contact') }}"
                                               minlength="10"
                                               maxlength="10"
                                               pattern="[0-9]{10}"
                                               title="Enter a valid 10-digit mobile number">
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
    <select name="status" class="form-control">
        <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choose one</option>

        @foreach($student_status as $s)
            <option value="{{ $s->status }}" 
                {{ old('status') == $s->status ? 'selected' : '' }}>
                {{ $s->name }}
            </option>
        @endforeach
    </select>

    @error('status') 
        <small class="text-danger">{{ $message }}</small> 
    @enderror
</div>



                                    <div class="form-group col-md-6">
                                        <label>Technology</label>
                                        <select name="technology" class="form-control">
                                            <option value="" disabled {{ old('technology') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}" 
                                                    {{ old('technology') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->course_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('technology') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Total Fees</label>
                                        <input type="text" name="total_fees" required class="form-control"
                                               value="{{ old('total_fees') }}"  id="total_fees">
                                        @error('total_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Reg Fees</label>
                                        <input type="text" name="reg_fees" required class="form-control" id="reg_fees" 
                                               value="{{ old('reg_fees') }}">
                                        @error('reg_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Total Pending Fees</label>
                                        <input type="text" name="pending_fees" class="form-control" id="pending_fees" 
                                               value="{{ old('pending_fees') }}">
                                        @error('pending_fees') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- <div class="form-group col-md-6">
                                        <label>Registration Pending Amount</label>
                                        <input type="text" name="reg_due_amount" class="form-control"
                                               value="{{ old('reg_due_amount') }}">
                                        @error('reg_due_amount') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div> -->

                                    <div class="form-group col-md-6">
                                        <label>Pending Fees Due Date</label>
                                        <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>Registered Date</label>
                                        <input type="date" name="join_date" class="form-control"
                                               value="{{ old('join_date') }}" >
                                        @error('join_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                   <div class="form-group col-md-6">
                                        <label>Duration</label>
                                        <select name="duration" class="form-control" required id="duration" >
                                            <option value="" disabled {{ old('duration') ? '' : 'selected' }}>--Select--</option>

                                            @foreach($course_duration as $d)
                                                <option value="{{ $d->duration }}" 
                                                    {{ old('duration') == $d->duration ? 'selected' : '' }}>
                                                    {{ $d->name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('duration') 
                                            <small class="text-danger">{{ $message }}</small> 
                                        @enderror
                                    </div>


                                    <div class="form-group col-md-6">
                                        <label>Start Date</label>
                                        <input type="date" name="start_date" class="form-control"
                                               value="{{ old('start_date') }}"  id="start_date" required>
                                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>End Date</label>
                                        <input type="date" name="end_date" class="form-control"
                                               value="{{ old('end_date') }}" id="end_date" >
                                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label>Batch Assign</label>
                                        <select name="batch_assign" class="form-control">
                                            <option value="" disabled {{ old('batch_assign') ? '' : 'selected' }}>Choose one</option>
                                            @foreach($batches as $batch)
                                                <option value="{{ $batch->id }}" 
                                                    {{ old('batch_assign') == $batch->id ? 'selected' : '' }}>
                                                    {{ $batch->batch_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('batch_assign') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>



    <div class="form-group col-md-6">
        <label>Part-Time Offer?</label>
        <select name="part_time_offer" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>Placement Offer?</label>
        <select name="placement_offer" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <div class="form-group col-md-6">
        <label>PG Offer?</label>
        <select name="pg_offer" class="form-control">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
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
@push('scripts')
<script>
    function calculateEndDate() {
        let startDate = document.getElementById('start_date').value;
        let duration  = document.getElementById('duration').value;

        if (startDate && duration) {
            let date = new Date(startDate);
            date.setDate(date.getDate() + parseInt(duration));

            let year  = date.getFullYear();
            let month = String(date.getMonth() + 1).padStart(2, '0');
            let day   = String(date.getDate()).padStart(2, '0');

            document.getElementById('end_date').value = `${year}-${month}-${day}`;
        }
    }

    document.getElementById('start_date').addEventListener('change', calculateEndDate);
    document.getElementById('duration').addEventListener('change', calculateEndDate);
 
    function calculatePendingFees() {
        let totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
        let regFees   = parseFloat(document.getElementById('reg_fees').value) || 0;

        let pending = totalFees - regFees;

        // Prevent negative value
        if (pending < 0) pending = 0;

        document.getElementById('pending_fees').value = pending.toFixed(2);
    }

    document.getElementById('total_fees').addEventListener('input', calculatePendingFees);
    document.getElementById('reg_fees').addEventListener('input', calculatePendingFees);
</script>


@endpush
