@extends('layouts.public')

@section('content')

<div class="container my-5">

<div class="card shadow-sm text-center">

<div class="card-header bg-success text-white">

<h3 class="mb-0">
Exam Submitted Successfully
</h3>

</div>


<div class="card-body">

<h4 class="mb-3">
Thank You!
</h4>

<p>
Thank you <strong>{{ $studentTest->student_name }}</strong>
for completing the exam.
</p>

@if($studentTest->test)
<p class="text-muted">
Exam: <strong>{{ $studentTest->test->title }}</strong>
</p>
@endif

<p>
Your answers have been saved successfully.
</p>

<p class="text-muted">
You may now close this page.
</p>

</div>


<div class="card-footer">

<p class="text-muted mb-0">
Submitted at:
{{ optional($studentTest->exam_submitted_at)->format('d M Y H:i') }}
</p>

</div>

</div>

</div>

@endsection