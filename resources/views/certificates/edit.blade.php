@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
<div class="content-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">          
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Student Detail- SNo- {{ $student->sno }}</h4><div class="form-check ms-3">
                            <input type="hidden" name="is_place" value="0">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_place"
                                   value="1"
                                   id="is_place"
                                   {{ old('is_place', $student->is_place) ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_place">
                                Other Place
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="hidden" name="is_married" value="0">

                            <input class="form-check-input"
                                   type="checkbox"
                                   name="is_married"
                                   value="1"
                                   id="is_married"
                                   {{ old('is_married', $student->is_married) ? 'checked' : '' }}>

                            <label class="form-check-label" for="is_married">
                                Married Status
                            </label>
                        </div>
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
                                     <label id="guardianLabel">Father Name</label>
                                    <input type="text" name="f_name" maxlength="55" required class="form-control" 
                                        value="{{ ucwords(old('f_name', preg_match('/^mr\.?/i', $student->f_name) ? $student->f_name : 'Mr. '.$student->f_name) )}}" oninput="handleMrPrefix(this)">
                                </div>
                                <div class="form-group col-md-6 d-none">
                                    <label>Serial No.</label>
                                    <input type="hidden" name="sno" maxlength="55" required class="form-control" 
                                        value="{{ old('sno', $student->sno) }}">
                                </div>
                                <!-- Gender -->
                                <div class="form-group col-md-6">
                                    <label>Gender</label>
                                    <select name="gender" class="form-control" required  id="genderSelect">
                                        <option value="" disabled>--Select--</option>
                                        <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <!-- College -->
                                 <div class="form-group col-md-6" id="collegeWrapper">
                                    <label>College</label>
                                    <select name="college_name" id="college_name" class="form-control select2">
                                        <option value="">--Choose--</option>
                                        @foreach($colleges as $college)
                                            <option value="{{ $college->id }}" 
                                                {{ old('college_name', $student->college_name) == $college->id ? 'selected' : '' }}>
                                                {{ $college->FullName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6 d-none" id="placeWrapper">
                                    <label>Place</label>
                                    <input type="text"
                                           name="place"
                                           id="place"
                                           class="form-control"
                                           value="{{ old('place', $student->place) }}"
                                           placeholder="Enter place">
                                </div>


                                <!-- Contact -->
                                <div class="form-group col-md-6">
                                    <label>Contact No</label>
                                    <input type="text" name="contact" class="form-control" 
                                        value="{{ old('contact', $student->contact) }}"
                                        minlength="10"
                                   
                                   pattern="[0-9]{10}"
                                   title="Enter a valid 10-digit mobile number"
                                     onpaste="handlePaste(event)"
                                    oninput="sanitizeContact(this)"
                                        >
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
                                <!-- <div class="form-group col-md-6">
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
                                </div> -->

                                <div class="form-group col-md-6">
                                    <label>Technology</label>
                                    <select name="technology[]" class="form-control" multiple>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}"
                                                {{ in_array($course->id, old('technology', $student->technology)) ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Fees -->
                                <div class="form-group col-md-6">
                                    <label>Total Fees</label>
                                    <input type="number" name="total_fees" class="form-control" id="total_fees"
                                        value="{{ old('total_fees', $student->total_fees) }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                    <small id="total_fee_warning" class="text-danger d-none">
                                        Total fees cannot exceed 200,000.
                                    </small>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Registration Fees</label>
                                    <input type="number" name="reg_fees" class="form-control"   id="reg_fees"
                                        value="{{ old('reg_fees', $student->reg_fees) }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                                </div>

                                 <div class="form-group col-md-6">
                                    <label>Paid Fees</label>
                                    <input type="text" name="paid_fees" required class="form-control" id="paid_fees" 
                                           value="{{ old('paid_fees', $student->paid_fees) }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
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

                                 

                                <div class="form-group col-md-6" id="pending_next_due_date">
                                    <label>Pending Fees Due Date</label>
                                    <input type="date" name="next_due_date" class="form-control"
                                        value="{{ old('next_due_date', $student->next_due_date) }}">
                                        <small class="text-danger d-none" id="due_date_error">
                                            Next due date must be after the registered date.
                                        </small>
                                </div>

                                


                                <!-- Department -->
                                    <!--  -->

                                <!-- Dates -->
                                <div class="form-group col-md-6">
                                    <label>Registered date</label>
                                    <input type="date" name="join_date" class="form-control" id="join_date" required 
                                        value="{{ old('join_date', $student->join_date) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Start Date</label>
                                    <input type="date" name="start_date" class="form-control"  id="start_date"  required 
                                        value="{{ old('start_date', $student->start_date) }}">
                                        <small id="date_error" class="text-danger d-none">
                                            Start date must be on or after the registered date.
                                        </small>
                                </div>
                                <input type="hidden"
               id="session_end_date"
               value="{{ $activeSession->end_date }}">

                                <div class="form-group col-md-6">
                                    <label>End Date</label>
                                    <input type="date" name="end_date" class="form-control"  id="end_date" 
                                        value="{{ old('end_date', $student->end_date) }}">
                                </div>


                                 <div class="form-group col-md-6">
                                    <label>Duration</label>
                                    <select name="duration" class="form-control" id="duration">

                                        <option value="" disabled 
                                            {{ old('duration', $student->duration ?? '') == '' ? 'selected' : '' }}>
                                            --Select--
                                        </option>

                                        @foreach($course_duration as $d)
                                            <option value="{{ $d->duration }}"
                                                {{ old('duration', $student->duration ?? '') == $d->duration ? 'selected' : '' }}>
                                                {{ $d->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                    @error('duration') 
                                        <small class="text-danger">{{ $message }}</small> 
                                    @enderror
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
        <label>Is Intern?</label>
        <select name="is_intern" class="form-control">
            <option value="0" {{ $student->is_intern == 0 ? 'selected' : '' }}>No</option>
            <option value="1" {{ $student->is_intern == 1 ? 'selected' : '' }}>Yes</option>
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


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

@endsection
@push('scripts')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {

    const genderSelect = document.getElementById("genderSelect");
    const isMarried = document.getElementById("is_married");
    const guardianLabel = document.getElementById("guardianLabel");

    function updateLabel() {
        if (genderSelect.value === "female" && isMarried.checked) {
            guardianLabel.textContent = "Husband Name";
        } else {
            guardianLabel.textContent = "Father Name";
        }
    }

    genderSelect.addEventListener("change", updateLabel);
    isMarried.addEventListener("change", updateLabel);

    updateLabel();
});
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

    const checkbox = document.getElementById("is_place");
    const college = document.getElementById("college_name");
    const place = document.getElementById("place");

    function toggleFields() {

        if (checkbox.checked) {

            // hide college
            $('#collegeWrapper').hide();
            college.removeAttribute("required");
            college.disabled = true;

            // show place
            $('#placeWrapper').removeClass('d-none');
            place.setAttribute("required", true);

        } else {

            // show college
            $('#collegeWrapper').show();
            college.setAttribute("required", true);
            college.disabled = false;

            // hide place
            $('#placeWrapper').addClass('d-none');
            place.removeAttribute("required");
        }
    }

    checkbox.addEventListener("change", toggleFields);

    // important for edit page
    toggleFields();
});
    $(document).ready(function () {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Search college name",
            allowClear: true
        });
    });
</script>
<script>
     function handleMrPrefix(input) {
        const prefix = 'Mr. ';

        // Save cursor position
        let cursorPos = input.selectionStart;

        // Current value
        let original = input.value;

        // Remove all Mr prefixes
        let value = original.replace(/^(mr\.?\s*)+/i, '');

        // Capitalize words
        value = value.replace(/\b\w/g, char => char.toUpperCase());

        // Build final value
        let finalValue = prefix + value;

        // Adjust cursor position
        let diff = finalValue.length - original.length;
        cursorPos += diff;

        // Set value
        input.value = finalValue;

        // Restore cursor safely
        requestAnimationFrame(() => {
            input.setSelectionRange(cursorPos, cursorPos);
        });
    }
</script>

<script>
let endDateManuallyChanged = false;

document.addEventListener('DOMContentLoaded', function () {
    calculateEndDateFromSession();
});

// Detect manual change
document.getElementById('end_date').addEventListener('input', function () {
    endDateManuallyChanged = true;
});

// Recalculate when start date changes (only if user didn't override)
document.getElementById('start_date').addEventListener('change', function () {
    validateDates();
    if (!endDateManuallyChanged) {
        calculateEndDateFromSession();
    }
});
function validateDates() {
    let joinDate  = $('#join_date').val();
    let startDate = $('#start_date').val();

    if (!joinDate || !startDate) return;

    if (new Date(startDate) < new Date(joinDate)) {
        $('#date_error').removeClass('d-none');
        $('#start_date').val('');
        $('#end_date').val('');
    } else {
        $('#date_error').addClass('d-none');
    }
}

function calculateEndDateFromSession() {
    const sessionEnd = document.getElementById('session_end_date').value;
    const startDate  = document.getElementById('start_date').value;

    if (!sessionEnd || !startDate) return;

    let endDate = new Date(sessionEnd);
    let start   = new Date(startDate);
    let sessionStart = new Date("{{ $activeSession->start_date }}");

    // Calculate missed days
    let diffTime = start.getTime() - sessionStart.getTime();
    let missedDays = Math.max(Math.floor(diffTime / (1000 * 60 * 60 * 24)), 0);

    endDate.setDate(endDate.getDate() + missedDays);

    // If Sunday → move to Monday
    if (endDate.getDay() === 0) {
        endDate.setDate(endDate.getDate() + 1);
    }

    setEndDate(endDate);
}

function setEndDate(date) {
    const year  = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day   = String(date.getDate()).padStart(2, '0');

    document.getElementById('end_date').value = `${year}-${month}-${day}`;
}

// Block Sunday on manual change
document.getElementById('end_date').addEventListener('change', function () {
    if (!this.value) return;

    const date = new Date(this.value);
    if (date.getDay() === 0) {
        this.value = '';
        document.getElementById('end_error').classList.remove('d-none');
    } else {
        document.getElementById('end_error').classList.add('d-none');
    }
});

$('#join_date').on('change', validateDates);
</script>


<script>
$(document).ready(function () {

    function calculateFees(changed) {
        let total = parseInt($('#total_fees').val()) || 0;
        let reg   = parseInt($('#reg_fees').val()) || 0;
        let paid  = parseInt($('#paid_fees').val()) || 0;

        /* RULE 1: Total ≤ 200000 */
        if (total > 200000) {
            total = 200000;
            $('#total_fees').val(200000);
            $('#total_fee_warning').removeClass('d-none');
            // alert('Total fees cannot exceed 200,000');
        }else{
            $('#total_fee_warning').addClass('d-none');
        }

        /* RULE 2: Reg + Paid ≤ Total */
        if ((reg + paid) > total) {
            $('#fee_warning').removeClass('d-none');

            if (changed === 'reg') {
                $('#reg_fees').val(0);
                reg = 0;
            }

            if (changed === 'paid') {
                $('#paid_fees').val(0);
                paid = 0;
            }
        } else {
            $('#fee_warning').addClass('d-none');
        }

        /* RULE 3: Pending ALWAYS derived */
        let pending = total - reg - paid;
        if (pending < 0) pending = 0;

        $('#pending_fees').val(pending);
    }

    /* EVENTS */
    $('#total_fees').on('input', function () {
        calculateFees();
    });

    $('#reg_fees').on('input', function () {
        calculateFees('reg');
    });

    $('#paid_fees').on('input', function () {
        calculateFees('paid');
    });

});
</script>

<script>
   
function capitalizeWords(input) {
    const start = input.selectionStart;
    const end = input.selectionEnd;

    let value = input.value.toLowerCase();
    input.value = value.replace(/\b\w/g, char => char.toUpperCase());

    input.setSelectionRange(start, end);
}

function handlePaste(e) {
    e.preventDefault(); // ⛔ stop default paste

    // Get pasted text (works for Excel, WhatsApp, etc.)
    let pasted = (e.clipboardData || window.clipboardData).getData('text');

    // Remove all non-digits
    let digits = pasted.replace(/\D/g, '');

    // Limit to 10 digits
    digits = digits.slice(0, 10);

    // Insert clean value
    e.target.value = digits;
}

function sanitizeContact(el) {
    // Safety net (mobile / autofill)
    let digits = el.value.replace(/\D/g, '').slice(0, 10);
    el.value = digits;
}

 
function blockSunday(input, errorId) {
    input.addEventListener('change', function () {
        if (!this.value) return;

        const day = new Date(this.value).getDay(); // 0 = Sunday

        if (day === 0) {
            this.value = '';
            document.getElementById(errorId).classList.remove('d-none');
        } else {
            document.getElementById(errorId).classList.add('d-none');
        }
    });
}

blockSunday(document.getElementById('start_date'), 'start_error');
blockSunday(document.getElementById('end_date'), 'end_error');


    function toggleDueDate() {
        let total = parseInt($('#total_fees').val()) || 0;
        let reg   = parseInt($('#reg_fees').val()) || 0;
        let paid  = parseInt($('#paid_fees').val()) || 0;

        let pending = total - (reg + paid);

        if (pending < 0) pending = 0;

        $('#pending_fees').val(pending);

        if (pending === 0) {
            $('#pending_next_due_date').hide();
        } else {
            $('#pending_next_due_date').show();
        }
    }

    // Run on page load (important for edit page)
    toggleDueDate();

    // Run whenever fees change
    $('#total_fees, #reg_fees, #paid_fees').on('input', function () {
        toggleDueDate();
    });
 
</script>
<script>
function validateDueDate() {
    let joinDate = $('#join_date').val();
    let dueDate  = $('input[name="next_due_date"]').val();

    if (!joinDate || !dueDate) return;

    if (new Date(dueDate) <= new Date(joinDate)) {
        $('#due_date_error').removeClass('d-none');
        $('input[name="next_due_date"]').val('');
    } else {
        $('#due_date_error').addClass('d-none');
    }
}

// trigger when dates change
$('#join_date, input[name="next_due_date"]').on('change', function () {
    validateDueDate();
});
</script>
<script>
    // function calculatePendingFees() {
    //     let totalFees = parseFloat(document.getElementById('total_fees').value) || 0;
    //     let regFees   = parseFloat(document.getElementById('reg_fees').value) || 0;
    //     let paidFees   = parseFloat(document.getElementById('paid_fees').value) || 0;

    //     let warningEl = document.getElementById('fee_warning');

    //     // Check validation
    //     if ((regFees + paidFees) > totalFees) {
    //         warningEl.classList.remove('d-none');

    //         // Optional: auto-fix by resetting last input
    //         document.getElementById('paid_fees').value = '';
    //         paidFees = 0;
    //     } else {
    //         warningEl.classList.add('d-none');
    //     }
    //     let pending = totalFees - regFees - paidFees;

    //     // Prevent negative value
    //     if (pending < 0) pending = 0;

    //     document.getElementById('pending_fees').value = pending.toFixed(2);
    // }

    // document.getElementById('total_fees').addEventListener('input', calculatePendingFees);
    // document.getElementById('reg_fees').addEventListener('input', calculatePendingFees);
    // document.getElementById('paid_fees').addEventListener('input', calculatePendingFees);

    // function capitalizeWords(input) {
    //     const start = input.selectionStart;
    //     const end = input.selectionEnd;

    //     let value = input.value.toLowerCase();
    //     input.value = value.replace(/\b\w/g, char => char.toUpperCase());

    //     input.setSelectionRange(start, end);
    // }
</script>
<!-- <script>
$(document).ready(function () {

    function calculateFees(changed) {
        let total = parseInt($('#total_fees').val()) || 0;
        let reg   = parseInt($('#reg_fees').val()) || 0;
        let paid  = parseInt($('#paid_fees').val()) || 0;

        /* RULE 1: Total ≤ 200000 */
        if (total > 200000) {
            total = 200000;
            $('#total_fees').val(200000);
            $('#total_fee_warning').removeClass('d-none');
            // alert('Total fees cannot exceed 200,000');
        }else{
            $('#total_fee_warning').addClass('d-none');
        }

        /* RULE 2: Reg + Paid ≤ Total */
        if ((reg + paid) > total) {
            $('#fee_warning').removeClass('d-none');

            if (changed === 'reg') {
                $('#reg_fees').val(0);
                reg = 0;
            }

            if (changed === 'paid') {
                $('#paid_fees').val(0);
                paid = 0;
            }
        } else {
            $('#fee_warning').addClass('d-none');
        }

        /* RULE 3: Pending ALWAYS derived */
        let pending = total - reg - paid;
        if (pending < 0) pending = 0;

        $('#pending_fees').val(pending);
    }

    /* EVENTS */
    $('#total_fees').on('input', function () {
        calculateFees();
    });

    $('#reg_fees').on('input', function () {
        calculateFees('reg');
    });

    $('#paid_fees').on('input', function () {
        calculateFees('paid');
    });

});
</script>


<script>
$(document).ready(function () {

    function toggleDueDate() {
        let pending = parseInt($('#pending_fees').val()) || 0;

        if (pending === 0) {
            $('#pending_next_due_date').hide();
        } else {
            $('#pending_next_due_date').show();
        }
    }

    // Run on page load (important for edit page)
    toggleDueDate();

    // Run whenever fees change
    $('#total_fees, #reg_fees, #paid_fees').on('input', function () {
        toggleDueDate();
    });

});
</script> -->
<!-- <script>
$(document).ready(function () {

    function validateDates() {
        let joinDate  = $('#join_date').val();
        let startDate = $('#start_date').val();

        if (!joinDate || !startDate) return;

        if (new Date(startDate) <= new Date(joinDate)) {
            $('#date_error').removeClass('d-none');
            $('#start_date').val('');
        } else {
            $('#date_error').addClass('d-none');
        }
    }

    $('#join_date, #start_date').on('change', validateDates);

});
</script> -->
@endpush
