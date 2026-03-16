@extends('layouts.app')

@section('content')
<div class="container my-5">

    <h2 class="mb-4">Edit Question</h2>

    <!-- Back Button -->
    <a href="{{ route('admin.tests.show', $question->test_id) }}" 
       class="btn btn-secondary mb-3">← Back to Test</a>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Question -->
        <div class="mb-3">
            <label class="form-label">Question</label>
            <textarea name="question" class="form-control" rows="3" required>{{ old('question', $question->question) }}</textarea>
        </div>

        <hr>

        <h5>Options</h5>

        @foreach($question->options as $index => $option)

        <div class="mb-3 p-3 border rounded">

            <label class="form-label">
                Option {{ chr(65+$index) }}
            </label>

            <input type="text"
                   name="options[{{ $option->id }}][text]"
                   class="form-control mb-2"
                   value="{{ old('options.'.$option->id.'.text', $option->option_text) }}"
                   required>

            <div class="form-check">
                <input class="form-check-input"
                       type="radio"
                       name="correct_answer"
                       value="{{ $option->id }}"
                       {{ $option->is_correct ? 'checked' : '' }}>

                <label class="form-check-label">
                    Correct Answer
                </label>
            </div>

        </div>

        @endforeach


        <button type="submit" class="btn btn-primary"
                style="background:#593bdb;border:none;">
            Update Question
        </button>

    </form>

</div>
@endsection