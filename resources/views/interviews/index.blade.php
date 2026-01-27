@extends('layouts.app')

@section('content')
<div class="container">

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page_heading">Interviews</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('interviews.create') }}"
           class="btn"
           style="background-color:#6b51df;color:#fff;">
            Add Candidate
        </a>
    </div>
</div>

{{-- FILTERS --}}
<form method="GET" action="{{ route('interviews.index') }}">
<div class="row mb-3">

    <div class="col-md-3">
        <input type="text"
               name="experience"
               class="form-control"
               placeholder="Experience (e.g. 2, 3-5)"
               value="{{ request('experience') }}">
    </div>

    <div class="col-md-3">
        <select name="technology_id" class="form-control">
            <option value="">All Technologies</option>
            @foreach($technologies as $tech)
                <option value="{{ $tech->id }}"
                    {{ request('technology_id') == $tech->id ? 'selected' : '' }}>
                    {{ $tech->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <select name="rating" class="form-control">
            <option value="">Min Rating</option>
            @for($i=1;$i<=10;$i++)
                <option value="{{ $i }}"
                    {{ request('rating') == $i ? 'selected' : '' }}>
                    {{ $i }}+
                </option>
            @endfor
        </select>
    </div>

    <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-primary">Filter</button>
        <a href="{{ route('interviews.index') }}" class="btn btn-secondary">
            Reset
        </a>
    </div>

</div>
</form>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Candidate</th>
    <th>Experience</th>
    <th>Contact</th>
    <th>Interviewer</th>
    <th>Rounds</th>
    <th>Final Result</th>
    <th width="160">Actions</th>
</tr>
</thead>
<tbody>
@forelse($interviews as $interview)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $interview->candidate_name }}</td>
    <td>{{ $interview->candidate_experience ?? '-' }}</td>
    <td>{{ $interview->candidate_contact ?? '-' }}</td>
    <td>{{ $interview->interviewer_name }}</td>

    <td>
        <span class="badge bg-info">
            {{ $interview->rounds->count() }} Round(s)
        </span>
    </td>

    <td>
        {{ $interview->final_result
            ? ucfirst($interview->final_result)
            : '-' }}
    </td>

    <td>
        <a href="{{ route('interviews.show', $interview) }}"
           class="btn btn-sm" title="View">
            <i class="fas fa-eye"></i>
        </a>

        <a href="{{ route('interviews.edit', $interview) }}"
           class="btn btn-sm" title="Edit">
            <i class="fas fa-edit"></i>
        </a>

        <a href="{{ route('interviews.rounds.create', $interview) }}"
           class="btn btn-sm" title="Add Round">
            <i class="fas fa-plus-circle"></i>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="8" class="text-center text-muted">
        No matching candidates found.
    </td>
</tr>
@endforelse
</tbody>
</table>

</div>
@endsection
