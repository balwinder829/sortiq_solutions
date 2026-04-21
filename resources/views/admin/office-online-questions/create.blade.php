@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
    
    <h4 class="mb-0 fw-semibold text-dark">
        <i class="fas fa-question-circle text-primary me-2"></i>
        Add Questions to: <span class="text-primary">{{ $test->title }}</span>
    </h4>

    <a href="{{ route('admin.office-online-tests.index') }}" 
       class="btn text-white" 
       style="background-color: #593bdb;">
        <i class="fas fa-arrow-left me-1"></i> Back to Tests
    </a>

</div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>❌ {{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

            <form id="questionForm" method="POST" action="{{ route('admin.office-online-questions.store') }}">
                @csrf

                <input type="hidden" name="office_online_test_id" value="{{ $test->id }}">

                <div id="questions-wrapper">

                    @php
                        $oldQuestions = old('questions', [ [] ]);
                    @endphp

                    @foreach($oldQuestions as $qIndex => $question)
                    <div class="question-block border rounded p-3 mb-4" data-index="{{ $qIndex }}">

                        <h6 class="mb-3">Question #{{ $loop->iteration }}</h6>

                        <!-- Question -->
                        <div class="mb-3">
                            <label>Question</label>
                            <textarea name="questions[{{ $qIndex }}][question]" class="form-control question-input">{{ $question['question'] ?? '' }}</textarea>
                            <div class="text-danger small error-question"></div>
                        </div>

                        <!-- Options -->
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
                                placeholder="Option {{ $i+1 }}">
                        </div>
                        @endfor

                        <div class="text-danger small error-options"></div>

                        <button type="button" class="btn btn-danger btn-sm mt-2 remove-question">Remove</button>
                    </div>
                    @endforeach

                </div>

                <button type="button" id="add-question" class="btn btn-success mb-3">
                    + Add More Question
                </button>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Submit All</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
let qIndex = {{ count(old('questions', [ [] ])) }};

// ADD QUESTION
document.getElementById('add-question').addEventListener('click', function () {

    let html = `
    <div class="question-block border rounded p-3 mb-4" data-index="${qIndex}">

        <h6>Question #${qIndex + 1}</h6>

        <div class="mb-3">
            <label>Question</label>
            <textarea name="questions[${qIndex}][question]" class="form-control question-input"></textarea>
            <div class="text-danger small error-question"></div>
        </div>

        ${[0,1,2,3].map(i => `
        <div class="input-group mb-2">
            <div class="input-group-text">
                <input type="radio" name="questions[${qIndex}][correct_option]" value="${i}" class="correct-option">
            </div>
            <input type="text" name="questions[${qIndex}][options][]" class="form-control option-input" placeholder="Option ${i+1}">
        </div>
        `).join('')}

        <div class="text-danger small error-options"></div>

        <button type="button" class="btn btn-danger btn-sm mt-2 remove-question">Remove</button>
    </div>`;

    document.getElementById('questions-wrapper').insertAdjacentHTML('beforeend', html);
    qIndex++;
});

// REMOVE
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-question')){
        e.target.closest('.question-block').remove();
    }
});

// FRONTEND VALIDATION
document.getElementById('questionForm').addEventListener('submit', function(e){

    let isValid = true;
    let firstError = null;

    document.querySelectorAll('.question-block').forEach(block => {

        let question = block.querySelector('.question-input').value.trim();
        let options = block.querySelectorAll('.option-input');
        let correct = block.querySelector('.correct-option:checked');

        block.querySelector('.error-question').innerText = '';
        block.querySelector('.error-options').innerText = '';

        // QUESTION CHECK
        if(question === ''){
            isValid = false;
            block.querySelector('.error-question').innerText = 'Question is required';
            if(!firstError) firstError = block;
        }

        // OPTIONS CHECK
        let emptyOption = false;
        options.forEach(opt => {
            if(opt.value.trim() === '') emptyOption = true;
        });

        if(emptyOption){
            isValid = false;
            block.querySelector('.error-options').innerText = 'All 4 options required';
            if(!firstError) firstError = block;
        }

        // CORRECT OPTION CHECK
        if(!correct){
            isValid = false;
            block.querySelector('.error-options').innerText += ' | Select correct answer';
            if(!firstError) firstError = block;
        }

    });

    if(!isValid){
        e.preventDefault();

        // SCROLL TO ERROR
        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

});
</script>

@endsection