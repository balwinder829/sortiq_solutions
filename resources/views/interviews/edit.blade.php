@extends('layouts.app')

@section('content')
<div class="container">
<h4>Edit Candidate</h4>

<form method="POST" action="{{ route('interviews.update', $interview) }}">
@csrf
@method('PUT')

<div class="row">
    <div class="col-md-6">
        <label>Name</label>
        <input type="text" name="candidate_name" class="form-control"
               value="{{ old('candidate_name', $interview->candidate_name) }}" required>
    </div>

    <div class="col-md-6">
        <label>Experience</label>
        <input type="text" name="candidate_experience" class="form-control"
               value="{{ old('candidate_experience', $interview->candidate_experience) }}" required>
    </div>

    <div class="col-md-6">
        <label>Contact</label>
        <input type="text" name="candidate_contact" class="form-control"
               value="{{ old('candidate_contact', $interview->candidate_contact) }}"   minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number" required>
    </div>

    <div class="col-md-6">
        <label>Email</label>
        <input type="email" name="candidate_email" class="form-control"
               value="{{ old('candidate_email', $interview->candidate_email) }}" required>
    </div>

    <div class="col-md-6">
        <label>Interviewer</label>
        <input type="text" name="interviewer_name" class="form-control"
               value="{{ old('interviewer_name', $interview->interviewer_name) }}" required>
    </div>

    <div class="col-md-6">
        <label>Interview Date</label>
        <input type="date" name="interview_date" class="form-control"
               value="{{ old('interview_date', $interview->interview_date) }}">
    </div>

    {{-- FINAL RESULT --}}
        <div class="form-group col-md-6 mt-3">
            <label>Final Result</label>
            <select name="final_result" class="form-control">
                <option value="">Select</option>
                <option value="selected">Selected</option>
                <option value="rejected">Rejected</option>
                <option value="on_hold">On Hold</option>
            </select>
        </div>
</div>

<button class="btn btn-primary mt-3">Update</button>
<a href="{{ route('interviews.show', $interview) }}"
   class="btn btn-secondary mt-3">Back</a>

</form>
</div>
@endsection
