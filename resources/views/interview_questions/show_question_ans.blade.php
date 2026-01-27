@extends('layouts.app')

@section('content')
<div class="container">

    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="page_heading">Interview Questions & Answers</h1>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-12">
            <form method="GET"
                  action="{{ route('interview-questions.practice') }}"
                  class="row g-2">

                <div class="col-md-3">
                    <select name="round_type" class="form-control">
                        <option value="">All Rounds</option>
                        <option value="hr" {{ request('round_type')=='hr'?'selected':'' }}>
                            HR
                        </option>
                        <option value="technical" {{ request('round_type')=='technical'?'selected':'' }}>
                            Technical
                        </option>
                        <option value="machine" {{ request('round_type')=='machine'?'selected':'' }}>
                            Machine
                        </option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="experience_level" class="form-control">
                        <option value="">All Experience</option>
                        <option value="fresher" {{ request('experience_level')=='fresher'?'selected':'' }}>Fresher</option>
                        <option value="1-3" {{ request('experience_level')=='1-3'?'selected':'' }}>1–3 Years</option>
                        <option value="3-5" {{ request('experience_level')=='3-5'?'selected':'' }}>3–5 Years</option>
                        <option value="5+" {{ request('experience_level')=='5+'?'selected':'' }}>5+ Years</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="technology_id" class="form-control">
                        <option value="">All Technologies</option>
                        @foreach($technologies as $tech)
                            <option value="{{ $tech->id }}"
                                {{ request('technology_id')==$tech->id?'selected':'' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary">Search</button>
                    <a href="{{ route('interview-questions.practice') }}"
                       class="btn btn-secondary">
                        Reset
                    </a>
                </div>

            </form>
        </div>
    </div>

    {{-- QUESTIONS LIST --}}
    @forelse($questions as $q)
        <div class="card mb-3">
            <div class="card-body">

                <div class="mb-2">
                    <span class="badge bg-info">
                        {{ strtoupper($q->round_type) }}
                    </span>

                    @if($q->technology)
                        <span class="badge bg-secondary">
                            {{ $q->technology->name }}
                        </span>
                    @endif

                    <span class="badge bg-success">
                        {{ $q->experience_level }}
                    </span>
                </div>

                <h6 class="fw-bold">
                    Q. {{ $q->question }}
                </h6>

                <div class="mt-2">
                    {!! $q->answer !!}
                </div>

            </div>
        </div>
    @empty
        <div class="alert alert-warning">
            No questions found for selected filters.
        </div>
    @endforelse

</div>
@endsection
