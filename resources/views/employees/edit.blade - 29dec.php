@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Employee</h4>

    <form method="POST" action="{{ route('employees.update', $employee) }}">
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

            <div class="form-group col-md-6">
                <label>Role</label>
                <select name="role"
                        class="form-control @error('role') is-invalid @enderror"
                        required>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}"
                            {{ old('role', $employee->user->role) == $role->id ? 'selected' : '' }}>
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
                       value="{{ old('username', $employee->user->username) }}"
                       required>
                @error('username')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Email</label>
                <input type="email"
                       name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $employee->user->email) }}"
                       required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group col-md-6">
                <label>Phone</label>
                <input type="text"
                       name="phone"
                       class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $employee->user->phone) }}"
                      required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
                @error('phone')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div class="form-group col-md-6">
                <label>Date of Birth</label>
                <input type="date" name="dob"
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



            {{-- Status --}}
          <div class="form-group col-md-6">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">Select Status</option>
                    <option value="active"   {{ $employee->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $employee->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                @error('status')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>



        </div>

        <button class="btn btn-primary mt-3">Update</button>
        <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
