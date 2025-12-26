@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Add New CV Record</h4>

    <form method="POST" action="{{ route('cvs.store') }}">
        @csrf

        <div class="row">
            
            {{-- Employee Name --}}
            <div class="form-group col-md-6 mb-3">
                <label for="employee_name">Employee Name *</label>
                <input type="text"
                       name="employee_name"
                       id="employee_name"
                       class="form-control @error('employee_name') is-invalid @enderror"
                       value="{{ old('employee_name') }}"
                       required
                       placeholder="Candidate Full Name">
                @error('employee_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Phone Number (New) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="phone_number">Phone Number</label>
                <input type="text"
                       name="phone_number"
                       id="phone_number"
                       class="form-control @error('phone_number') is-invalid @enderror"
                       value="{{ old('phone_number') }}"
                        required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
                @error('phone_number')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Technology --}}
            <div class="form-group col-md-6 mb-3">
                <label for="technology">Technology/Primary Skill *</label>
                <input type="text"
                       name="technology"
                       id="technology"
                       class="form-control @error('technology') is-invalid @enderror"
                       value="{{ old('technology') }}"
                       required
                       placeholder="e.g., PHP/Laravel, DevOps, UI/UX">
                @error('technology')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Location (New) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="location">Location</label>
                <input type="text"
                       name="location"
                       id="location"
                       class="form-control @error('location') is-invalid @enderror"
                       value="{{ old('location') }}"
                       placeholder="e.g., Mumbai, Remote - India">
                @error('location')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Experience Status (Select) --}}
            <div class="form-group col-md-4 mb-3">
                <label for="experience_status">Experience Status *</label>
                <select name="experience_status"
                        id="experience_status"
                        class="form-control @error('experience_status') is-invalid @enderror"
                        required>
                    @php $currentStatus = old('experience_status'); @endphp
                    <option value="" disabled selected>-- Select Status --</option>
                    <option value="Fresher" {{ $currentStatus === 'Fresher' ? 'selected' : '' }}>Fresher</option>
                    <option value="Experienced" {{ $currentStatus === 'Experienced' ? 'selected' : '' }}>Experienced</option>
                </select>
                @error('experience_status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Experience Years (New) --}}
            <div class="form-group col-md-4 mb-3">
                <label for="experience_years">Experience Years</label>
                <input type="number"
                       name="experience_years"
                       id="experience_years"
                       class="form-control @error('experience_years') is-invalid @enderror"
                       value="{{ old('experience_years') }}"
                       min="0"
                       max="99"
                       placeholder="e.g., 3">
                <small class="form-text text-muted">Set to 0 if Fresher.</small>
                @error('experience_years')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Hiring Status (New) --}}
            <div class="form-group col-md-4 mb-3">
                <label for="hiring_status">Hiring Status *</label>
                <select name="hiring_status"
                        id="hiring_status"
                        class="form-control @error('hiring_status') is-invalid @enderror"
                        required>
                    @php $hiringStatus = old('hiring_status'); @endphp
                    <option value="" disabled selected>-- Select Status --</option>
                    <option value="Looking" {{ $hiringStatus === 'Looking' ? 'selected' : '' }}>Looking</option>
                    <option value="Not Looking" {{ $hiringStatus === 'Not Looking' ? 'selected' : '' }}>Not Looking</option>
                    <option value="Open to Offers" {{ $hiringStatus === 'Open to Offers' ? 'selected' : '' }}>Open to Offers</option>
                </select>
                @error('hiring_status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Current Job Status (New) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="current_job_status">Current Job Status</label>
                <input type="text"
                       name="current_job_status"
                       id="current_job_status"
                       class="form-control @error('current_job_status') is-invalid @enderror"
                       value="{{ old('current_job_status') }}"
                       placeholder="e.g., Working at XYZ Pvt Ltd, Currently Unemployed">
                @error('current_job_status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- CV File Name (New) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="file_name">CV File Name *</label>
                <input type="text"
                       name="file_name"
                       id="file_name"
                       class="form-control @error('file_name') is-invalid @enderror"
                       value="{{ old('file_name') }}"
                       required
                       placeholder="e.g., John_Doe_Laravel_CV.pdf">
                @error('file_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Last Updated At (New) --}}
            <div class="form-group col-md-6 mb-3">
                <label for="last_updated_at">CV Document Last Updated</label>
                <input type="date"
                       name="last_updated_at"
                       id="last_updated_at"
                       class="form-control @error('last_updated_at') is-invalid @enderror"
                       value="{{ old('last_updated_at') }}">
                <small class="form-text text-muted">Date the document itself was last edited.</small>
                @error('last_updated_at')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Google Drive Link --}}
            <div class="form-group col-md-6 mb-3">
                <label for="gdrive_link">Google Drive Link to CV *</label>
                <input type="url"
                       name="gdrive_link"
                       id="gdrive_link"
                       class="form-control @error('gdrive_link') is-invalid @enderror"
                       value="{{ old('gdrive_link') }}"
                       required
                       placeholder="https://drive.google.com/...">
                @error('gdrive_link')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">Save CV Record</button>
        <a href="{{ route('cvs.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection