@extends('layouts.app')

@section('content')

<div class="container my-5">

<div class="card shadow-sm border-0 rounded-4">

<div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">

<h4 class="mb-0 fw-semibold text-dark">

<i class="fas fa-edit text-primary me-2"></i>

Edit Question —
<span class="text-primary">{{ $office_test->title }}</span>

</h4>

<a href="{{ route('admin.office-tests.office-questions.index',$office_test->id) }}"
   class="btn text-white"
   style="background-color:#593bdb;">

<i class="fas fa-arrow-left me-1"></i>
Back to Questions

</a>

</div>


<div class="card-body p-4">

@if(session('success'))
<div class="alert alert-success">
{{ session('success') }}
</div>
@endif


<form method="POST"
      action="{{ route('admin.office-tests.office-questions.update',[$office_test->id,$office_question->id]) }}">

@csrf
@method('PUT')


{{-- Question --}}

<div class="mb-4">

<label class="form-label fw-semibold">

Question

</label>

<textarea name="question"
          class="form-control"
          rows="4"
          required>{{ old('question',$office_question->question) }}</textarea>

</div>


{{-- Marks --}}

<div class="mb-4">

<label class="form-label fw-semibold">

Marks

</label>

<input type="number"
       name="marks"
       class="form-control"
       min="0"
       value="{{ old('marks',$office_question->marks) }}">

</div>


{{-- Question Order --}}

<div class="mb-4">

<label class="form-label fw-semibold">

Question Order

</label>

<input type="number"
       name="question_order"
       class="form-control"
       min="1"
       value="{{ old('question_order',$office_question->question_order) }}">

</div>


<div class="text-end">

<button type="submit"
        class="btn text-white px-4 py-2"
        style="background-color:#593bdb;">

<i class="fas fa-save me-1"></i>

Update Question

</button>

</div>


</form>

</div>

</div>

</div>

@endsection