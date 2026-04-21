@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-4">

        <!-- HEADER -->
        <div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 fw-semibold text-dark">
                <i class="fas fa-question-circle text-primary me-2"></i>
                Add Questions to: <span class="text-primary">{{ $test->title }}</span>
            </h4>

            <a href="{{ route('admin.tests.show', $test->id) }}" 
               class="btn text-white" style="background-color: #593bdb;">
                <i class="fas fa-arrow-left me-1"></i> Back to Tests
            </a>
        </div>

        <div class="card-body">

            <!-- GLOBAL ERRORS -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>❌ {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="questionForm" method="POST" action="{{ route('admin.questions.store', $test->id) }}">
                @csrf

                <div id="questions-wrapper">

                    @php
                        $oldQuestions = old('questions', [ [] ]);
                    @endphp

                    @foreach($oldQuestions as $qIndex => $question)
                    <div class="question-block border rounded p-3 mb-4">

                        <h6 class="fw-semibold">Question #{{ $loop->iteration }}</h6>

                        <!-- QUESTION -->
                        <textarea name="questions[{{ $qIndex }}][question]" 
                                  class="form-control mb-2 question-input"
                                  placeholder="Enter question...">{{ $question['question'] ?? '' }}</textarea>
                        <div class="text-danger small error-question"></div>

                        <!-- OPTIONS -->
                        @for($i = 0; $i < 4; $i++)
                        <div class="input-group mb-2">
                            <div class="input-group-text">
                                <input type="radio"
                                    name="questions[{{ $qIndex }}][correct_option]"
                                    value="{{ $i }}"
                                    class="correct-option"
                                    {{ (isset($question['correct_option']) && $question['correct_option'] == $i) ? 'checked' : '' }}>
                            </div>

                            <input type="text"
                                name="questions[{{ $qIndex }}][options][]"
                                class="form-control option-input"
                                value="{{ $question['options'][$i] ?? '' }}"
                                placeholder="Option {{ $i + 1 }}">
                        </div>
                        @endfor

                        <div class="text-danger small error-options"></div>

                        <button type="button" class="btn btn-danger btn-sm remove-question">
                            Remove
                        </button>

                    </div>
                    @endforeach

                </div>

                <!-- ADD BUTTON -->
                <button type="button" id="add-question" class="btn btn-success mb-3">
                    + Add Question
                </button>

                <!-- SUBMIT -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        Save All Questions
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- JS -->
<script>
let qIndex = {{ count(old('questions', [[]])) }}; // ✅ FIXED

// ADD QUESTION
document.getElementById('add-question').addEventListener('click', function(){

    let html = `
    <div class="question-block border rounded p-3 mb-4">

        <h6 class="fw-semibold">Question #${qIndex + 1}</h6>

        <textarea name="questions[${qIndex}][question]" 
                  class="form-control mb-2 question-input"
                  placeholder="Enter question..."></textarea>

        <div class="text-danger small error-question"></div>

        ${[0,1,2,3].map(i => `
        <div class="input-group mb-2">
            <div class="input-group-text">
                <input type="radio" name="questions[${qIndex}][correct_option]" value="${i}" class="correct-option">
            </div>
            <input type="text" 
                   name="questions[${qIndex}][options][]" 
                   class="form-control option-input" 
                   placeholder="Option ${i + 1}">
        </div>
        `).join('')}

        <div class="text-danger small error-options"></div>

        <button type="button" class="btn btn-danger btn-sm remove-question">
            Remove
        </button>
    </div>`;

    document.getElementById('questions-wrapper').insertAdjacentHTML('beforeend', html);
    qIndex++;
});

// REMOVE QUESTION
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-question')){
        e.target.closest('.question-block').remove();
    }
});

// FRONTEND VALIDATION
document.getElementById('questionForm').addEventListener('submit', function(e){

    let valid = true;
    let firstError = null;

    document.querySelectorAll('.question-block').forEach(block => {

        let question = block.querySelector('.question-input').value.trim();
        let options = block.querySelectorAll('.option-input');
        let correct = block.querySelector('.correct-option:checked');

        block.querySelector('.error-question').innerText = '';
        block.querySelector('.error-options').innerText = '';

        // QUESTION VALIDATION
        if(question === ''){
            valid = false;
            block.querySelector('.error-question').innerText = 'Question is required';
            if(!firstError) firstError = block;
        }

        // OPTIONS VALIDATION
        let emptyOption = false;
        options.forEach(o => {
            if(o.value.trim() === '') emptyOption = true;
        });

        if(emptyOption){
            valid = false;
            block.querySelector('.error-options').innerText = 'All 4 options are required';
            if(!firstError) firstError = block;
        }

        // CORRECT OPTION VALIDATION
        if(!correct){
            valid = false;
            block.querySelector('.error-options').innerText += ' | Select correct answer';
            if(!firstError) firstError = block;
        }

    });

    if(!valid){
        e.preventDefault();
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>

@endsection