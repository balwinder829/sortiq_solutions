@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-question-circle text-primary me-2"></i>
                Add Question to: <span class="text-primary">{{ $test->title }}</span>
            </h4>
            <a href="{{ route('admin.tests.show', $test->id) }}" 
               class="btn text-white" style="background-color: #593bdb;">
               <i class="fas fa-arrow-left me-1"></i> Back to Questions
            </a>
        </div>

        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.questions.store', $test->id) }}">
                @csrf

                <!-- Question Field -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Question</label>
                    <textarea name="question" class="form-control" rows="3" required placeholder="Enter your question..."></textarea>
                </div>

                <!-- Options -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Options</label>
                    @for($i = 0; $i < 4; $i++)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio" name="correct_option" value="{{ $i }}" required>
                            </div>
                            <input type="text" name="options[]" class="form-control" 
                                   placeholder="Option {{ $i + 1 }}" required>
                        </div>
                    @endfor
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Select the correct answer using the radio button.
                    </small>
                </div>

                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn text-white px-4 py-2" style="background-color: #593bdb;">
                        <i class="fas fa-plus-circle me-1"></i> Add Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
