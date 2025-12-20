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

    <!-- Edit Form -->
    <form action="{{ route('admin.questions.update', $question->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Question Text -->
        <div class="mb-3">
            <label class="form-label">Question</label>
            <textarea name="question_text" class="form-control" rows="3" required>{{ old('question_text', $question->question_text) }}</textarea>
        </div>

        <!-- Options -->
        <div class="mb-3">
            <label class="form-label">Option A</label>
            <input type="text" name="option_a" class="form-control" 
                   value="{{ old('option_a', $question->option_a) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option B</label>
            <input type="text" name="option_b" class="form-control" 
                   value="{{ old('option_b', $question->option_b) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option C</label>
            <input type="text" name="option_c" class="form-control" 
                   value="{{ old('option_c', $question->option_c) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Option D</label>
            <input type="text" name="option_d" class="form-control" 
                   value="{{ old('option_d', $question->option_d) }}" required>
        </div>

        <!-- Correct Answer -->
        <div class="mb-3">
            <label class="form-label">Correct Answer</label>
            <select name="correct_answer" class="form-select" required>
                <option value="">-- Select Correct Option --</option>
                <option value="A" {{ old('correct_answer', $question->correct_answer) == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ old('correct_answer', $question->correct_answer) == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ old('correct_answer', $question->correct_answer) == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ old('correct_answer', $question->correct_answer) == 'D' ? 'selected' : '' }}>D</option>
            </select>
        </div>

        <!-- Save Button -->
        <button type="submit" class="btn btn-primary" 
                style="background-color: #593bdb; border: none;">Update Question</button>
    </form>
</div>
@endsection
