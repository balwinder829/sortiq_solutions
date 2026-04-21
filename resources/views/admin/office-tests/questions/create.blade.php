@extends('layouts.app')

@section('content')
<div class="container my-5">
<div class="card shadow-sm border-0 rounded-4">

<!-- <div class="card-header bg-light d-flex justify-content-between align-items-center">
    <h4>Add Questions to: {{ $office_test->title }}</h4>

    <a href="{{ route('admin.office-tests.office-questions.index',$office_test->id) }}"
       class="btn text-white" style="background:#593bdb;">
        ← Back
    </a>
</div> -->

     <div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">
    
    <h4 class="mb-0 fw-semibold text-dark">
        <i class="fas fa-question-circle text-primary me-2"></i>
        Add Questions to: <span class="text-primary">{{ $office_test->title }}</span>
    </h4>

    <a href="{{ route('admin.office-tests.office-questions.index',$office_test->id) }}" 
       class="btn text-white" 
       style="background-color: #593bdb;">
        <i class="fas fa-arrow-left me-1"></i> Back to Tests
    </a>

</div>

<div class="card-body">

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form id="questionForm" method="POST"
      action="{{ route('admin.office-tests.office-questions.store',$office_test->id) }}">
@csrf

<div id="questions-wrapper">

@php $oldQuestions = old('questions', [[]]); @endphp

@foreach($oldQuestions as $qIndex => $question)
<div class="question-block border p-3 mb-3">

    <h6>Question #{{ $loop->iteration }}</h6>

    <textarea name="questions[{{ $qIndex }}][question]"
              class="form-control mb-2 question-input"
              placeholder="Enter question...">{{ $question['question'] ?? '' }}</textarea>

    <div class="text-danger small error-question"></div>

    <input type="number"
           name="questions[{{ $qIndex }}][question_order]"
           class="form-control mb-2"
           placeholder="Order (optional)"
           value="{{ $question['question_order'] ?? '' }}">

    <!-- <input type="number"
           name="questions[{{ $qIndex }}][marks]"
           class="form-control mb-2"
           placeholder="Marks (optional)"
           value="{{ $question['marks'] ?? '' }}"> -->

    <button type="button" class="btn btn-danger btn-sm remove-question">Remove</button>

</div>
@endforeach

</div>

<button type="button" id="add-question" class="btn btn-success mb-3">
    + Add Question
</button>

<div class="text-end">
    <button type="submit" class="btn btn-primary">
        Save All
    </button>
</div>

</form>

</div>
</div>
</div>

<script>
let qIndex = {{ count(old('questions', [[]])) }};

// ADD
document.getElementById('add-question').addEventListener('click', function(){

    let html = `
    <div class="question-block border p-3 mb-3">

        <h6>Question #${qIndex + 1}</h6>

        <textarea name="questions[${qIndex}][question]"
                  class="form-control mb-2 question-input"
                  placeholder="Enter question..."></textarea>

        <div class="text-danger small error-question"></div>

        <input type="number"
               name="questions[${qIndex}][question_order]"
               class="form-control mb-2"
               placeholder="Order (optional)">

        <button type="button" class="btn btn-danger btn-sm remove-question">Remove</button>
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

// VALIDATION
document.getElementById('questionForm').addEventListener('submit', function(e){

    let valid = true;
    let firstError = null;

    document.querySelectorAll('.question-block').forEach(block => {

        let question = block.querySelector('.question-input').value.trim();
        block.querySelector('.error-question').innerText = '';

        if(question === ''){
            valid = false;
            block.querySelector('.error-question').innerText = 'Question required';
            if(!firstError) firstError = block;
        }
    });

    if(!valid){
        e.preventDefault();
        firstError.scrollIntoView({behavior:'smooth', block:'center'});
    }
});
</script>

@endsection