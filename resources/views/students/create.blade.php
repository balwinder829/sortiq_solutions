@extends('layouts.app')

@section('content')
 
<div class="container">
    <form method="POST" action="{{ route('students.store') }}">
        @csrf

    <div class="row mb-2 align-items-center">
        <div class="col-md-8">
            <h1 class="page_heading">Add Student - SNo- {{ $nextSno }}</h1>
        </div>  

        <div class="col-md-4 text-end">
            <div class="form-check d-inline-block ms-3">
                <input type="hidden" name="is_place" value="0">

                <input class="form-check-input"
                       type="checkbox"
                       name="is_place"
                       value="1"
                       id="is_place">

                <label class="form-check-label" for="is_place">
                    Other Place
                </label>
            </div>
            <div class="form-check d-inline-block">
                <input type="hidden" name="is_married" value="0">
                
                <input class="form-check-input"
                       type="checkbox"
                       name="is_married"
                       value="1"
                       id="is_married"
                       {{ old('is_married') ? 'checked' : '' }}>

                <label class="form-check-label" for="is_married">
                    Married Status
                </label>
            </div>
        </div>
    </div>

    
        <div class="form-row">
            
            <div class="form-group col-md-6">
                <label>Student Name</label>
                <input type="text" maxlength="55" required class="form-control" 
                       name="student_name" value="{{ old('student_name') }}" oninput="capitalizeWords(this)">
                @error('student_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label  id="guardianLabel">Father Name</label>
                <input type="text" maxlength="55" required class="form-control"
                       name="f_name" value="{{ old('f_name', 'Mr. ') }}" oninput="handleMrPrefix(this)">
                @error('f_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6 d-none">
                <label>Serial No.</label>
                <input type="hidden" maxlength="55" class="form-control" 
                       name="sno" value="{{ $nextSno }}" readonly>
                @error('sno') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Gender</label>
                <select name="gender" id="genderSelect" class="form-control" required>
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


            <div class="form-group col-md-6"  id="collegeWrapper">
                <label>College Name</label>
                <select name="college_name" id="college_name"  required class="form-control select2">
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

            <div class="form-group col-md-6 d-none" id="placeWrapper">
                <label>Place</label>
                <input type="text"
                       name="place"
                       id="place"
                       class="form-control"
                       placeholder="Enter place">
            </div>
            
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email" required name="email_id" class="form-control"
                       value="{{ old('email_id') }}">
                @error('email_id') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Contact No</label>
                <input type="text" class="form-control" 
                       name="contact" value="{{ old('contact') }}"
                       minlength="10"
                       
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number"
                       onpaste="handlePaste(event)"
           oninput="sanitizeContact(this)"
                       >
                @error('contact') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Alternate No</label>
                <input type="text" class="form-control" 
                       name="alternative_phone" value="{{ old('alternative_phone') }}"
                       minlength="10"
                       
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number"
                       onpaste="handlePaste(event)"
           oninput="sanitizeContact(this)"
                       >
                @error('alternative_phone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            

           <div class="form-group col-md-6">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="" disabled {{ old('status') ? '' : 'selected' }}>Choose one</option>

                    @foreach($student_status as $s)
                        <option value="{{ $s->status }}" 
                             {{ old('status', $student->status ?? 'joined') == $s->status ? 'selected' : '' }}>
                            {{ $s->name }}
                        </option>
                    @endforeach
                </select>

                @error('status') 
                    <small class="text-danger">{{ $message }}</small> 
                @enderror
            </div>



            <!-- <div class="form-group col-md-6">
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
            </div> -->

            <!-- <div class="form-group col-md-6">
                <label>Technology</label>
                <select name="technology[]" class="form-control" multiple>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}"
                            {{ in_array($course->course_name, old('technology', [])) ? 'selected' : '' }}>
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
                            {{ in_array($course->id, old('technology', [$defaultTechnology])) ? 'selected' : '' }}>
                            {{ $course->course_name }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="form-group col-md-6">
                <label>Total Fees</label>
                <input type="text" name="total_fees" required class="form-control"
                       value="{{ old('total_fees') }}"  id="total_fees" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                @error('total_fees') <small class="text-danger">{{ $message }}</small> @enderror
                <small id="total_fee_warning" class="text-danger d-none">
                    Total fees cannot exceed 200,000.
                </small>
            </div>

            <div class="form-group col-md-6">
                <label>Reg Fees</label>
                <input type="text" name="reg_fees" required class="form-control" id="reg_fees" 
                       value="{{ old('reg_fees') }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                @error('reg_fees') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group col-md-6"  id="paidFeesWrapper">
                <label>Paid Fees</label>
                <input type="text" name="paid_fees" required class="form-control" id="paid_fees" 
                       value="{{ old('paid_fees',0) }}" oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                @error('paid_fees') <small class="text-danger">{{ $message }}</small> @enderror
                 <small id="fee_warning" class="text-danger d-none">
                    Registration fees + Paid fees cannot be greater than Total fees.
                </small>
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

            

            <div class="row col-md-12 border border-warning p-3">
                
                    <div class="form-group col-md-6">
                        <label>Registered Date</label>
                        <input type="date" name="join_date" class="form-control" id="join_date"
                            value="{{ old('join_date') }}" >
                        <small class="text-danger d-none" id="join_error">
                            Sunday is not allowed
                        </small>
                        @error('join_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label>Duration</label>
                        <select name="duration" class="form-control" id="duration" >
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
                        <small class="text-danger d-none" id="start_error">
                            Sunday is not allowed
                        </small>
                        <small id="date_error" class="text-danger d-none">
                            Start date must be on or after the registered date.
                        </small>
                        @error('start_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                <!--  <div class="form-group col-md-6">
                        <label>End Date</label>
                        <input type="date" name="end_date" class="form-control"
                            value="{{ old('end_date') }}" id="end_date" >
                        <small class="text-danger d-none" id="end_error">
                            Sunday is not allowed
                        </small>
                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div> -->
                    <input type="hidden"
                    id="session_end_date"
                    value="{{ $activeSession->end_date }}">

                    <div class="form-group col-md-6">
                        <label>End Date</label>
                        <input type="date"
                            name="end_date"
                            class="form-control"
                            id="end_date">
                        <small class="text-danger d-none" id="end_error">
                            Sunday is not allowed
                        </small>
                        @error('end_date') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="form-group col-md-6" id="pending_next_due_date">
                        <label>Pending Fees Due Date</label>
                        <input type="date" name="next_due_date" class="form-control" value="{{ old('next_due_date') }}">
                        <small class="text-danger d-none" id="due_date_error">
                            Next due date must be after the registered date.
                        </small>
                    </div>
            </div>
            <div class="form-group col-md-6">
                <label>Batch Assign</label>
                <select name="batch_assign" class="form-control">

                    <option value="" disabled {{ old('batch_assign') ? '' : 'selected' }}>Choose one</option>
                     <option value="" {{ old('batch_assign', '') == '' ? 'selected' : '' }}>
                        Not Decided
                    </option>
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
                <label>Study Mode?</label>
                <select name="is_online" class="form-control">
                    <option value="0">Offline</option>
                    <option value="1">Online</option>
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

            <div class="form-group col-md-6">
                <label>Address</label>
                <textarea name="address"
                          class="form-control"
                          rows="3"
                          >{{ old('address') }}</textarea>

                @error('address')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
         <!-- <div class="form-group" style="margin-top: 3%; margin-left: 1%;"> -->
                <button type="submit" class="btn" style="background-color: #6b51df; color: #fff; margin-left: 8px;">Save</button>
            <!-- </div> -->
    </form>    
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />


@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {

    const checkbox = document.getElementById("is_place");
    const college = document.getElementById("college_name");
    const place = document.getElementById("place");

    function toggleFields() {

        if (checkbox.checked) {

            // hide college select + select2 UI
            $('#collegeWrapper').hide();

            // remove validation
            college.removeAttribute("required");
            college.disabled = true;

            // show place field
            $('#placeWrapper').removeClass('d-none');
            place.setAttribute("required", true);

        } else {

            // show college
            $('#collegeWrapper').show();

            // enable validation
            college.setAttribute("required", true);
            college.disabled = false;

            // hide place
            $('#placeWrapper').addClass('d-none');
            place.removeAttribute("required");
            place.value = "";
        }
    }

    checkbox.addEventListener("change", toggleFields);

    // run on page load
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
let endDateManuallyChanged = false;

document.addEventListener('DOMContentLoaded', function () {
    calculateEndDateFromSession();
});

// Detect manual change
document.getElementById('end_date').addEventListener('input', function () {
    //endDateManuallyChanged = true;
});

// Recalculate when start date changes (only if user didn't override)
document.getElementById('start_date').addEventListener('change', function () {
    validateDates();
    if (!endDateManuallyChanged) {
        calculateEndDateFromSession();
    }
});

document.getElementById('duration').addEventListener('change', function () {
    calculateEndDateFromSession();
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
    // console.log(" Executing... ")
    handleFieldsEnableDisable();
    const sessionEnd = document.getElementById('session_end_date').value;
    const startDate  = document.getElementById('start_date').value;
    const duration  = document.getElementById('duration').value || 0;

    if (!sessionEnd || !startDate) return;

    let endDate = new Date(startDate);
    let start   = new Date(startDate);
    // let sessionStart = new Date("{{ $activeSession->start_date }}");
    // console.log({sessionStart, start})
    // Calculate missed days
    // let diffTime = start.getTime() - sessionStart.getTime();
    // let missedDays = Math.max(Math.floor(diffTime / (1000 * 60 * 60 * 24)), 0);
    endDate.setDate(start.getDate() + Number(duration));
    // console.log({start, duration, startDate, endDate})
    // console.log({ endDay: endDate.getDay(), duration, diffTime})
    // If Sunday → move to Monday
    if (endDate.getDay() === 0) {
        endDate.setDate(endDate.getDate() + 1);
    }
    // console.log({ endDate });
    setEndDate(endDate);
}

function handleFieldsEnableDisable(){

    const duration  = document.getElementById('duration').value;
    const startDate  = document.getElementById('start_date').value;
    // console.log({ duration, startDate });
    if(duration == ""){
        document.getElementById('start_date').value = "";
        document.getElementById('end_date').value = "";

        document.getElementById('start_date').disabled = true;
        document.getElementById('end_date').disabled = true;
        return;
    } else {
        document.getElementById('start_date').disabled = false;
    } 
    
    
    if (startDate == ""){
        document.getElementById('end_date').disabled = true;
        return;
    } 
    
    if (startDate !== "" && duration !== ""){
        document.getElementById('start_date').disabled = false;
        document.getElementById('end_date').disabled = false;
    }

}

function setEndDate(date) {
    const year  = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day   = String(date.getDate()).padStart(2, '0');
    // console.log(`${year}-${month}-${day}`);
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

$(document).ready(function () {

    function togglePaidFees() {
        let total = parseInt($('#total_fees').val()) || 0;
        let reg   = parseInt($('#reg_fees').val()) || 0;

        if (total > 0 && reg === total) {
            // Hide paid fees
            $('#paidFeesWrapper').hide();
            $('#paid_fees').val(0);
        } else {
            $('#paidFeesWrapper').show();
        }
    }

    // Run on load (important for edit)
    togglePaidFees();

    // Run when total or reg fees change
    $('#total_fees, #reg_fees').on('input', function () {
        togglePaidFees();
    });

});

   
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
// blockSunday(document.getElementById('join_date'), 'join_error');

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


    function handleMrPrefixold(input) {
        const prefix = 'Mr. ';

        // Remove any existing prefix
        let value = input.value.replace(/^mr\.?\s*/i, '');

        // Capitalize each word
        value = value.replace(/\b\w/g, char => char.toUpperCase());

        // Set final value
        input.value = prefix + value;
    }

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

document.addEventListener("DOMContentLoaded", function () {

    const genderSelect = document.getElementById("genderSelect");
    const isMarried = document.getElementById("is_married");
    const guardianLabel = document.getElementById("guardianLabel");

    function updateLabel() {
        if (!guardianLabel) return;

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
@endpush
