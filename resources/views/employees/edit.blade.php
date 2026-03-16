@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Employee</h4>

    <form method="POST"
          action="{{ route('employees.update', $employee) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Employee Code (readonly) --}}
            <div class="form-group col-md-6">
                <label>Employee Code</label>
                <input type="text" class="form-control" value="{{ $employee->emp_code }}" readonly>
            </div>

            {{-- Employee Name --}}
            <div class="form-group col-md-6">
                <label>Employee Name</label>
                <input type="text"
                       name="emp_name"
                       class="form-control @error('emp_name') is-invalid @enderror"
                       value="{{ old('emp_name', $employee->emp_name) }}"
                       required>
                @error('emp_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Position --}}
            <div class="form-group col-md-6">
                <label>Position</label>
                <input type="text"
                       name="position"
                       class="form-control @error('position') is-invalid @enderror"
                       value="{{ old('position', $employee->position) }}"
                       required>
                @error('position')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

             {{-- Employement type --}}
            <div class="form-group col-md-6">
               <label>Experience Level</label>
                <select name="employment_type" class="form-control" required>
                    <option value="">Select</option>
                    @foreach(['intern','fresher','junior','senior'] as $bg)
                        <option value="{{ $bg }}"
                            {{ old('employment_type', $employee->employment_type) == $bg ? 'selected' : '' }}>
                            {{ ucwords($bg) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Work Mode</label>
                <select name="work_mode" class="form-control" required>
                    @foreach(['offline','online'] as $mode)
                        <option value="{{ $mode }}"
                            {{ old('work_mode', $employee->work_mode ?? 'offline') == $mode ? 'selected' : '' }}>
                            {{ ucfirst($mode) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Employement Type</label>
                <select name="employment_mode" class="form-control" required>
                    @foreach(['normal','intern' ,'freelancer'] as $mode)
                        <option value="{{ $mode }}"
                            {{ old('employment_mode', $employee->employment_mode ?? 'normal') == $mode ? 'selected' : '' }}>
                            {{ ucfirst($mode) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6">
                <label>Job Type</label>
                <select name="job_type" id="jobType" class="form-control" required>
                    @foreach(['full_time','part_time'] as $type)
                        <option value="{{ $type }}"
                            {{ old('job_type', $employee->job_type ?? 'full_time') == $type ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', ucwords($type)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group col-md-6"
                 id="workingHoursField"
                 style="{{ old('job_type', $employee->job_type ?? '') == 'part_time' ? '' : 'display:none;' }}">

                <label>Working Hours Per Day</label>

                <input type="number"
                       name="working_hours_per_day"
                       class="form-control"
                       step="0.5"
                       min="1"
                       value="{{ old('working_hours_per_day', $employee->working_hours_per_day ?? '') }}"
                       placeholder="Enter hours (e.g. 4.5)">

                @error('working_hours_per_day')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Joining Date --}}
            <div class="form-group col-md-6">
                <label>Joining Date</label>
                <input type="date"
                       name="joining_date"
                       class="form-control @error('joining_date') is-invalid @enderror"
                       value="{{ old('joining_date', $employee->joining_date) }}"
                       required>
                @error('joining_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Probation Period (In Months) --}}
            <div class="form-group col-md-6">
                <label>Probation Period (In Months)</label>
                <input type="text"
                       name="probation_period"
                       class="form-control @error('probation_period') is-invalid @enderror"
                       value="{{ old('probation_period', $employee->probation_period) }}"
                       required>
                @error('probation_period')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Role --}}
            <div class="form-group col-md-6">
                <label>Role</label>
                <select name="role"
                        class="form-control @error('role') is-invalid @enderror"
                        required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role', $employee->role) == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                @error('role')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Username --}}
            <div class="form-group col-md-6">
                <label>Username</label>
                <input type="text"
                       name="username"
                       class="form-control @error('username') is-invalid @enderror"
                       value="{{ old('username', $employee->username) }}"
                       required>
                @error('username')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Email --}}
            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $employee->email) }}"
                       required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            {{-- Password --}}
                <div class="form-group col-md-6">
                    <label>Password</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Leave blank to keep current password">
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>


            {{-- Password --}}
            <div class="form-group col-md-6">
                <label>Existing Password</label>

                <div class="input-group">
                    <input type="password"
                           id="emp_pswd"
                           name="emp_pswd"
                           class="form-control @error('emp_pswd') is-invalid @enderror"
                           value="{{ old('emp_pswd', $employee->emp_pswd) }}"
                           readonly>

                    <span class="input-group-text" style="cursor:pointer"
                          onclick="toggleProbation()">
                        👁
                    </span>
                </div>
            </div>


            {{-- Phone --}}
            <div class="form-group col-md-6">
                <label>Phone</label>
                <input type="text"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $employee->phone) }}"
                       required
                       minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}">
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

             {{-- Phone --}}
            <div class="form-group col-md-6">
                <label>Alternative Phone</label>
                <input type="text"
                       name="alternative_phone"
                       class="form-control @error('alternative_phone') is-invalid @enderror"
                       value="{{ old('alternative_phone', $employee->alternative_phone) }}"
                       required
                       minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}">
                @error('alternative_phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- DOB --}}
            <div class="form-group col-md-6">
                <label>Date of Birth</label>
                <input type="date"
                       name="dob"
                       class="form-control"
                       max="{{ date('Y-m-d') }}"
                       value="{{ old('dob', $employee->dob) }}">
            </div>

            {{-- Blood Group --}}
            <div class="form-group col-md-6">
                <label>Blood Group</label>
                <select name="blood_group" class="form-control">
                    <option value="">Select</option>
                    @foreach(['A+','A-','B+','B-','O+','O-','AB+','AB-'] as $bg)
                        <option value="{{ $bg }}"
                            {{ old('blood_group', $employee->blood_group) == $bg ? 'selected' : '' }}>
                            {{ $bg }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Address --}}
            <div class="form-group col-md-12">
                <label>Address</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $employee->address) }}</textarea>
            </div>

            {{-- PHOTO UPLOAD (ID CARD) --}}
            <div class="form-group col-md-6">
                <label>Employee Photo (ID Card)</label>

                <div id="drop-area"
                     style="border:2px dashed #6b51df;
                            padding:20px;
                            text-align:center;
                            cursor:pointer;
                            border-radius:6px;">

                    <p style="margin:0;">Drag & drop image here<br>or click to upload</p>
                    <input type="file"
                           name="photo"
                           id="photoInput"
                           accept="image/*"
                           style="display:none;">
                </div>

                <div id="preview" style="margin-top:10px;">
                    @if($employee->photo)
                        <img id="previewImg"
                             src="{{ asset('images/employee_images/'.$employee->photo) }}"
                             style="max-width:120px;
                                    border:1px solid #ddd;
                                    padding:4px;
                                    border-radius:4px;">
                    @else
                        <img id="previewImg" style="display:none; max-width:120px;">
                    @endif
                    
                </div>

                @error('photo')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Status --}}
            <div class="form-group col-md-6">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="active"   {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
const dropArea = document.getElementById('drop-area');
const fileInput = document.getElementById('photoInput');
const previewImg = document.getElementById('previewImg');

dropArea.addEventListener('click', () => fileInput.click());

dropArea.addEventListener('dragover', e => {
    e.preventDefault();
    dropArea.style.background = '#f5f5ff';
});

dropArea.addEventListener('dragleave', () => {
    dropArea.style.background = '';
});

dropArea.addEventListener('drop', e => {
    e.preventDefault();
    dropArea.style.background = '';
    fileInput.files = e.dataTransfer.files;
    showPreview(fileInput.files[0]);
});

fileInput.addEventListener('change', () => {
    showPreview(fileInput.files[0]);
});

function showPreview(file) {
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => {
        previewImg.src = reader.result;
        previewImg.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

 
function toggleProbation() {
    const input = document.getElementById('emp_pswd');
    input.type = input.type === 'password' ? 'text' : 'password';
} 

</script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const jobType = document.getElementById("jobType");
    const workingHoursField = document.getElementById("workingHoursField");

    function toggleWorkingHours() {
        if (jobType.value === "part_time") {
            workingHoursField.style.display = "block";
        } else {
            workingHoursField.style.display = "none";
        }
    }

    // Run on page load
    toggleWorkingHours();

    // Run when changed
    jobType.addEventListener("change", toggleWorkingHours);
});
</script>
@endpush
