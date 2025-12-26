@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Interview for: {{ $interview->candidate_name }}</h4>

    <form method="POST" action="{{ route('daily-interviews.update', $interview) }}">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- Candidate Name --}}
            <div class="form-group col-md-6 mb-3">
                <label for="candidate_name">Candidate Name *</label>
                <input type="text"
                       name="candidate_name"
                       id="candidate_name"
                       class="form-control @error('candidate_name') is-invalid @enderror"
                       value="{{ old('candidate_name', $interview->candidate_name) }}"
                       required
                       placeholder="Name">
                @error('candidate_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Mobile No --}}
            <div class="form-group col-md-6 mb-3">
                <label for="mobile_no">Mobile Number</label>
                <input type="text"
                       name="mobile_no"
                       id="mobile_no"
                       class="form-control @error('mobile_no') is-invalid @enderror"
                       value="{{ old('mobile_no', $interview->mobile_no) }}"
                        required
                    minlength="10"
                    maxlength="10"
                    pattern="[0-9]{10}"
                    title="Enter a valid 10-digit mobile number">
                @error('mobile_no')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Technology --}}
            <div class="form-group col-md-6 mb-3">
                <label for="technology">Technology/Role</label>
                <input type="text"
                       name="technology"
                       id="technology"
                       class="form-control @error('technology') is-invalid @enderror"
                       value="{{ old('technology', $interview->technology) }}"
                       placeholder="e.g., Laravel Developer, QA Engineer">
                @error('technology')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Interview Type --}}
            <div class="form-group col-md-6 mb-3">
                <label for="interview_type">Interview Type *</label>
                <select name="interview_type"
                        id="interview_type"
                        class="form-control @error('interview_type') is-invalid @enderror"
                        required>
                    @php $currentType = old('interview_type', $interview->interview_type); @endphp
                    <option value="" disabled>-- Select Type --</option>
                    <option value="Screening" {{ $currentType === 'Screening' ? 'selected' : '' }}>Screening</option>
                    <option value="Technical 1" {{ $currentType === 'Technical 1' ? 'selected' : '' }}>Technical 1</option>
                    <option value="Technical 2" {{ $currentType === 'Technical 2' ? 'selected' : '' }}>Technical 2</option>
                    <option value="HR Round" {{ $currentType === 'HR Round' ? 'selected' : '' }}>HR Round</option>
                    <option value="Final Round" {{ $currentType === 'Final Round' ? 'selected' : '' }}>Final Round</option>
                </select>
                @error('interview_type')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <hr class="mt-2 mb-3">

            {{-- Current CTC --}}
            <div class="form-group col-md-4 mb-3">
                <label for="current_ctc">Current CTC</label>
                <input type="text"
                       name="current_ctc"
                       id="current_ctc"
                       class="form-control @error('current_ctc') is-invalid @enderror"
                       value="{{ old('current_ctc', $interview->current_ctc) }}"
                       placeholder="e.g., 6 LPA">
                @error('current_ctc')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Expected CTC --}}
            <div class="form-group col-md-4 mb-3">
                <label for="exp_ctc">Expected CTC</label>
                <input type="text"
                       name="exp_ctc"
                       id="exp_ctc"
                       class="form-control @error('exp_ctc') is-invalid @enderror"
                       value="{{ old('exp_ctc', $interview->exp_ctc) }}"
                       placeholder="e.g., 9 LPA">
                @error('exp_ctc')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Notice Period --}}
            <div class="form-group col-md-4 mb-3">
                <label for="notice_period">Notice Period</label>
                <input type="text"
                       name="notice_period"
                       id="notice_period"
                       class="form-control @error('notice_period') is-invalid @enderror"
                       value="{{ old('notice_period', $interview->notice_period) }}"
                       placeholder="e.g., 30 Days, Immediate">
                @error('notice_period')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <hr class="mt-2 mb-3">

            {{-- Availability Datetime --}}
            <div class="form-group col-md-6 mb-3">
                <label for="availability_datetime">Interview Date/Time</label>
                {{-- Format the date for the datetime-local input --}}
                @php 
                    $datetimeValue = old('availability_datetime', $interview->availability_datetime ? $interview->availability_datetime->format('Y-m-d\TH:i') : ''); 
                @endphp
                <input type="datetime-local"
                       name="availability_datetime"
                       id="availability_datetime"
                       class="form-control @error('availability_datetime') is-invalid @enderror"
                       value="{{ $datetimeValue }}">
                @error('availability_datetime')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
            
            {{-- Joining Date --}}
            <div class="form-group col-md-6 mb-3">
                <label for="joining_date">Tentative Joining Date</label>
                <input type="date"
                       name="joining_date"
                       id="joining_date"
                       class="form-control @error('joining_date') is-invalid @enderror"
                       value="{{ old('joining_date', $interview->joining_date?->format('Y-m-d')) }}">
                @error('joining_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Interviewer Name --}}
            <div class="form-group col-md-6 mb-3">
                <label for="interviewer_name">Interviewer Name</label>
                <input type="text"
                       name="interviewer_name"
                       id="interviewer_name"
                       class="form-control @error('interviewer_name') is-invalid @enderror"
                       value="{{ old('interviewer_name', $interview->interviewer_name) }}"
                       placeholder="e.g., Sarah J. (Technical Lead)">
                @error('interviewer_name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            {{-- Interview Status --}}
            <div class="form-group col-md-6 mb-3">
                <label for="interview_status">Interview Status *</label>
                <select name="interview_status"
                        id="interview_status"
                        class="form-control @error('interview_status') is-invalid @enderror"
                        required>
                    @php $currentStatus = old('interview_status', $interview->interview_status); @endphp
                    <option value="Scheduled" {{ $currentStatus === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="Completed" {{ $currentStatus === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="No Show" {{ $currentStatus === 'No Show' ? 'selected' : '' }}>No Show</option>
                    <option value="Rejected" {{ $currentStatus === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="Offered" {{ $currentStatus === 'Offered' ? 'selected' : '' }}>Offered</option>
                </select>
                @error('interview_status')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

        </div>

        <button type="submit" class="btn btn-primary mt-3">Update Interview</button>
        <a href="{{ route('daily-interviews.index') }}" class="btn btn-secondary mt-3">Back</a>
    </form>
</div>
@endsection