@extends('layouts.app')
@section('content')
<div class="container my-5">
    <h2 class="mb-4 text-center text-primary">{{ $test->title }}</h2>

    <form method="POST" action="{{ route('student.test.submit', $test->id) }}">
        @csrf

        @foreach($test->questions as $question)
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $loop->iteration }}. {{ $question->question }}</h5>

                @foreach($question->options as $option)
                <div class="form-check mt-2">
                    <input class="form-check-input" 
                           type="radio" 
                           name="answers[{{ $question->id }}]" 
                           value="{{ $option->id }}" 
                           id="option{{ $option->id }}" 
                           required>
                    <label class="form-check-label" for="option{{ $option->id }}">
                        {{ $option->option_text }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="text-center">
            <button type="submit" class="btn btn-success btn-lg">Submit Test</button>
        </div>
    </form>
</div>
@endsection
