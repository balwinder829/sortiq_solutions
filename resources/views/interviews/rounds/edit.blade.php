@extends('layouts.app')

@section('content')
<div class="container">
<h4>Edit {{ strtoupper($round->round_type) }} Round</h4>

<form method="POST" action="{{ route('interviews.rounds.update', $round) }}">
@csrf
@method('PUT')

@php
    $techRatings = $round->technologies->keyBy('id');
@endphp

<div class="row">

{{-- ROUND TYPE --}}
<div class="col-md-6">
    <label>Round Type</label>
    <select name="round_type" id="roundType" class="form-control">
        <option value="hr" {{ $round->round_type=='hr'?'selected':'' }}>HR</option>
        <option value="technical" {{ $round->round_type=='technical'?'selected':'' }}>Technical</option>
        <option value="machine" {{ $round->round_type=='machine'?'selected':'' }}>Machine</option>
    </select>
</div>

<div class="col-md-6">
    <label>Round Date</label>
    <input type="date"
           name="round_date"
           class="form-control"
           value="{{ $round->round_date }}">
</div>


{{-- OVERALL RATING --}}
<div class="col-md-6">
    <label>Overall Rating</label>
    <input type="number"
           name="rating"
           class="form-control"
           min="0" max="10"
           value="{{ $round->rating }}">
</div>

{{-- REMARKS --}}
<div class="col-md-12 mt-2">
    <label>Remarks</label>
    <textarea name="remarks"
              class="form-control"
              rows="3">{{ $round->remarks }}</textarea>
</div>

{{-- TECHNOLOGY SELECT --}}
<div class="col-md-6 mt-3"
     id="technologyBlock"
     style="{{ in_array($round->round_type,['technical','machine']) ? '' : 'display:none;' }}">
    <label>Technologies</label>
    <select id="techSelect"
            class="form-control"
            multiple>
        @foreach($technologies as $tech)
            <option value="{{ $tech->id }}"
                {{ $techRatings->has($tech->id) ? 'selected' : '' }}>
                {{ $tech->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- TECHNOLOGY RATINGS --}}
<div class="col-md-12 mt-3"
     id="techRatings"
     style="{{ in_array($round->round_type,['technical','machine']) ? '' : 'display:none;' }}">

@foreach($technologies as $tech)
@php $pivot = $techRatings->get($tech->id)?->pivot; @endphp
<div class="row mb-2 tech-row"
     data-tech-id="{{ $tech->id }}"
     style="{{ $pivot ? '' : 'display:none;' }}">

    <div class="col-md-4">
        <strong>{{ $tech->name }}</strong>
        <input type="hidden"
               class="tech-selected"
               name="technologies[{{ $tech->id }}][selected]"
               value="{{ $pivot ? 1 : '' }}">
    </div>

    <div class="col-md-4">
        <input type="number"
               name="technologies[{{ $tech->id }}][rating]"
               class="form-control"
               min="0" max="10"
               value="{{ $pivot->rating ?? '' }}"
               placeholder="Rating out of 10">
    </div>
</div>
@endforeach

</div>

</div>

<button class="btn btn-primary mt-3">Update Round</button>
<a href="{{ route('interviews.show', $round->interview_id) }}"
   class="btn btn-secondary mt-3">Back</a>

</form>
</div>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const roundType = document.getElementById('roundType');
    const techBlock = document.getElementById('technologyBlock');
    const techRatings = document.getElementById('techRatings');
    const techSelect = document.getElementById('techSelect');

    roundType.addEventListener('change', function () {
        if (this.value === 'technical' || this.value === 'machine') {
            techBlock.style.display = '';
            techRatings.style.display = '';
        } else {
            techBlock.style.display = 'none';
            techRatings.style.display = 'none';
        }
    });

    techSelect.addEventListener('change', function () {
        const selectedIds = Array.from(this.selectedOptions).map(o => o.value);

        document.querySelectorAll('.tech-row').forEach(row => {
            const techId = row.dataset.techId;
            const flag = row.querySelector('.tech-selected');

            if (selectedIds.includes(techId)) {
                row.style.display = '';
                flag.value = 1;
            } else {
                row.style.display = 'none';
                flag.value = '';
            }
        });
    });

});
</script>
@endsection
