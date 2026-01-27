@extends('layouts.app')

@section('content')
<div class="container">
<h4>Add Interview</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('interviews.store') }}">
@csrf

<div class="row">

{{-- Candidate Info --}}
<div class="form-group col-md-6">
    <label>Candidate Name</label>
    <input type="text"
           name="candidate_name"
           class="form-control"
           required>
</div>

<div class="form-group col-md-6">
    <label>Candidate Experience</label>
    <input type="text"
           name="candidate_experience"
           class="form-control" required>
</div>

<div class="form-group col-md-6">
    <label>Candidate Contact</label>
    <input type="text" name="candidate_contact" class="form-control"  minlength="10"
                       maxlength="10"
                       pattern="[0-9]{10}"
                       title="Enter a valid 10-digit mobile number" required>
</div>

<div class="form-group col-md-6">
    <label>Candidate Email</label>
    <input type="email" name="candidate_email" class="form-control" required>
</div>


<div class="form-group col-md-6">
    <label>Interviewer Name</label>
    <input type="text"
           name="interviewer_name"
           class="form-control"
           required>
</div>

<div class="form-group col-md-6">
    <label>Interview Date</label>
    <input type="date"
           name="interview_date"
           class="form-control">
</div>

{{-- ROUND TYPE --}}
<div class="form-group col-md-6 mt-3">
    <label>Interview Round</label>
    <select name="round_type"
            id="round_type"
            class="form-control"
            required>
        <option value="">Select Round</option>
        <option value="hr">HR</option>
        <option value="technical">Technical</option>
        <option value="machine">Machine</option>
    </select>
</div>

{{-- OVERALL ROUND RATING --}}
<div class="form-group col-md-6 mt-3">
    <label>Overall Rating (out of 10)</label>
    <input type="number"
           name="rounds[general][rating]"
           class="form-control"
           min="0" max="10">
</div>

{{-- OVERALL ROUND REMARKS --}}
<div class="form-group col-md-12 mt-2">
    <label>Round Remarks</label>
    <textarea name="rounds[general][remarks]"
              class="form-control"
              rows="3"
              placeholder="Overall remarks for this round"></textarea>
</div>

{{-- TECHNOLOGY SELECT --}}
<div class="form-group col-md-6 mt-3 d-none" id="technologyBlock">
    <label>Technologies</label>
    <select id="techSelect"
            class="form-control"
            multiple>
        @foreach($technologies as $tech)
            <option value="{{ $tech->id }}">
                {{ $tech->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- TECHNOLOGY RATINGS --}}
<div class="col-md-12 mt-3 d-none" id="techRatings"></div>

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

<button class="btn btn-primary mt-3">Save</button>
<a href="{{ route('interviews.index') }}"
   class="btn btn-secondary mt-3">Back</a>

</form>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const roundType   = document.getElementById('round_type');
    const techBlock   = document.getElementById('technologyBlock');
    const techSelect  = document.getElementById('techSelect');
    const techRatings = document.getElementById('techRatings');

    roundType.addEventListener('change', function () {
        if (this.value === 'technical' || this.value === 'machine') {
            techBlock.classList.remove('d-none');
            techRatings.classList.remove('d-none');
        } else {
            techBlock.classList.add('d-none');
            techRatings.classList.add('d-none');
            techRatings.innerHTML = '';
        }
    });

    techSelect.addEventListener('change', function () {
        techRatings.innerHTML = '';

        Array.from(this.selectedOptions).forEach(option => {
            techRatings.innerHTML += `
                <div class="row mb-2 align-items-center">
                    <div class="col-md-4">
                        <strong>${option.text}</strong>
                        <input type="hidden"
                               name="rounds[technical][technologies][${option.value}][selected]"
                               value="1">
                    </div>
                    <div class="col-md-4">
                        <input type="number"
                               name="rounds[technical][technologies][${option.value}][rating]"
                               class="form-control"
                               placeholder="Rating out of 10"
                               min="0" max="10">
                    </div>
                </div>
            `;
        });
    });

});
</script>
@endsection
