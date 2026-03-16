@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add Accepted Letter</h4>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form method="POST"
          action="{{ route('accepted-letters.store') }}"
          enctype="multipart/form-data">
        @csrf

        <div class="row">

       {{-- Employee Dropdown --}}
        <div class="form-group col-md-12">
            <label>Select Employee</label>
            <select name="employee_id"
                    class="form-control @error('employee_id') is-invalid @enderror"
                    required>

                <option value="" disabled>-- Select Employee --</option>

                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}"
                        {{ old('employee_id') == $emp->emp_code ? 'selected' : '' }}>
                        {{ ucwords($emp->emp_name) }} ({{ $emp->emp_code }})
                    </option>
                @endforeach

            </select>

            @error('employee_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
 


            {{-- File --}}
            <div class="form-group col-md-12">
                <label>Upload Accepted Letter (PDF / Image)</label>
                <input type="file"
                       name="file"
                       class="form-control @error('file') is-invalid @enderror"
                       accept=".pdf,.jpg,.jpeg,.png"
                       required>

                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

        </div>

        <button class="btn btn-primary mt-3">Save</button>
        <a href="{{ route('accepted-letters.index') }}"
           class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection
