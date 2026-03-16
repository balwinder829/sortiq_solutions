@extends('layouts.exam')

@section('content')

<div class="container text-center mt-5">

<h3 class="text-primary">
{{ $test->title }}
</h3>

<p class="mt-3">
Exam will start at
<strong>
{{ optional($test->exam_start_at)->format('d M Y h:i A') }}
</strong>
</p>

<h4 id="countdown" class="text-danger mt-4"></h4>

<p class="text-muted mt-3">
Please stay on this page.<br>
The exam will start automatically.
</p>

</div>


<script>

const startTime = new Date("{{ optional($test->exam_start_at)->format('Y-m-d H:i:s') }}").getTime();

const countdownEl = document.getElementById('countdown');

const examUrl = "{{ route('student.office.exam.show', $test->slug) }}";

const interval = setInterval(()=>{

const now = new Date().getTime();

let diff = Math.floor((startTime - now) / 1000);

if(diff <= 0){

clearInterval(interval);

/* redirect to exam page */
window.location.href = examUrl;

return;

}

const m = Math.floor(diff / 60);
const s = diff % 60;

countdownEl.innerText =
`Exam starts in ${m}:${s.toString().padStart(2,'0')}`;

},1000);

</script>

@endsection