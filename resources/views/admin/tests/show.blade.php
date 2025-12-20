@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark fw-bold mb-0">
            <i class="fas fa-question-circle text-primary me-2"></i> 
            Questions for <span class="text-primary">{{ $test->title }}</span>
        </h2>
        <a href="{{ route('admin.questions.create', $test->id) }}" 
           class="btn text-white" style="background-color: #593bdb;">
           <i class="fas fa-plus-circle me-1"></i> Add New Question
        </a>
    </div>

    @if($test->questions->count() > 0)
        @foreach($test->questions as $index => $question)
        <div class="card mb-4 border-0 shadow-sm rounded-4">
            <div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
                <div class="fw-semibold text-secondary">
                    Q{{ $index + 1 }}.
                </div>
                <div class="ms-3 flex-grow-1 fw-semibold text-dark">
                    {{ $question->question }}
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($question->options as $option)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   {{ $option->is_correct ? 'checked' : '' }} 
                                   disabled>
                            <span>{{ $option->option_text }}</span>
                        </div>
                        @if($option->is_correct)
                            <span class="badge bg-success px-3 py-2">Correct</span>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endforeach
    @else
        <div class="alert alert-info text-center mt-4">
            <i class="fas fa-info-circle me-1"></i> No questions added yet for this test.
        </div>
    @endif

    <div class="text-center mt-4">
        <a href="{{ $backRoute }}" 
           class="btn text-white px-4 py-2" 
           style="background-color: #593bdb;">
           <i class="fas fa-arrow-left me-2"></i> Back to Tests
        </a>
    </div>
</div>
@endsection
