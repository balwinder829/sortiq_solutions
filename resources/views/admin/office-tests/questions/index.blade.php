@extends('layouts.app')

@section('content')

<div class="container my-5">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2 class="text-dark fw-bold mb-0">

<i class="fas fa-question-circle text-primary me-2"></i>

Questions for
<span class="text-primary">{{ $office_test->title }}</span>

</h2>


<a href="{{ route('admin.office-tests.office-questions.create',$office_test->id) }}"
   class="btn text-white"
   style="background-color:#593bdb;">

<i class="fas fa-plus-circle me-1"></i>
Add New Question

</a>

</div>


@if($questions->count() > 0)

@foreach($questions as $index => $question)

<div class="card mb-4 border-0 shadow-sm rounded-4">

<div class="card-header bg-light border-0 rounded-top-4 py-3 d-flex justify-content-between align-items-center">

<div class="fw-semibold text-secondary">
Q{{ $index + 1 }}
</div>

<div class="ms-3 flex-grow-1 fw-semibold text-dark">
{{ $question->question }}
</div>

<div class="d-flex gap-2">

<a href="{{ route('admin.office-tests.office-questions.edit',[$office_test->id,$question->id]) }}"
   class="btn btn-sm btn-warning">

<i class="fas fa-edit"></i>

</a>


<form action="{{ route('admin.office-tests.office-questions.destroy',[$office_test->id,$question->id]) }}"
      method="POST">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger"
        data-swal-confirm="Delete this question?">

<i class="fas fa-trash"></i>

</button>

</form>

</div>

</div>


<div class="card-body">

<div class="d-flex justify-content-between">

<!-- <div class="text-muted">

<strong>Marks:</strong>
{{ $question->marks ?? 0 }}

</div> -->

<div class="text-muted">

<strong>Order:</strong>
{{ $question->question_order }}

</div>

</div>

</div>

</div>

@endforeach


@else

<div class="alert alert-info text-center mt-4">

<i class="fas fa-info-circle me-1"></i>
No questions added yet for this test.

</div>

@endif


<div class="text-center mt-4">

<a href="{{ route('admin.office-tests.index') }}"
   class="btn text-white px-4 py-2"
   style="background-color:#593bdb;">

<i class="fas fa-arrow-left me-2"></i>
Back to Tests

</a>

</div>

</div>

@endsection