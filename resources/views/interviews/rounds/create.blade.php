@extends('layouts.app')

@section('content')
<div class="container">
<h4>Add Interview Round</h4>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST"
      action="{{ route('interviews.rounds.store', $interview) }}">
@csrf

<div class="row">

{{-- ROUND TYPE --}}
<div class="form-group col-md-6">
    <label>Round Type</label>
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

<div class="col-md-6">
    <label>Round Date</label>
    <input type="date"
           name="round_date"
           class="form-control">
</div>


{{-- OVERALL RATING --}}
<div class="form-group col-md-6">
    <label>Overall Rating (out of 10)</label>
    <input type="number"
           name="rounds[general][rating]"
           class="form-control"
           min="0" max="10">
</div>

{{-- REMARKS --}}
<div class="form-group col-md-12 mt-2">
    <label>Remarks</label>
    <textarea name="rounds[general][remarks]"
              class="form-control"
              rows="3"></textarea>
</div>

{{-- TECHNOLOGY SELECT --}}
<div class="form-group col-md-6 mt-3 d-none" id="technologyBlock">
    <label>Technologies</label>
    <select id="techSelect" class="form-control" multiple>
        @foreach($technologies as $tech)
            <option value="{{ $tech->id }}">{{ $tech->name }}</option>
        @endforeach
    </select>
</div>

{{-- TECHNOLOGY RATINGS --}}
<div class="col-md-12 mt-3 d-none" id="techRatings"></div>

</div>

<button class="btn btn-primary mt-3">Save Round</button>
<a href="{{ route('interviews.show', $interview) }}"
   class="btn btn-secondary mt-3">Back</a>

</form>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const roundType = document.getElementById('round_type');
    const techBlock = document.getElementById('technologyBlock');
    const techSelect = document.getElementById('techSelect');
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

        Array.from(this.selectedOptions).forEach(opt => {
            techRatings.innerHTML += `
                <div class="row mb-2">
                    <div class="col-md-4">
                        <strong>${opt.text}</strong>
                        <input type="hidden"
                               name="rounds[technical][technologies][${opt.value}][selected]"
                               value="1">
                    </div>
                    <div class="col-md-4">
                        <input type="number"
                               name="rounds[technical][technologies][${opt.value}][rating]"
                               class="form-control"
                               min="0" max="10"
                               placeholder="Rating out of 10">
                    </div>
                </div>
            `;
        });
    });

});
</script>
@endsection
