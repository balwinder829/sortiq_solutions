@extends('layouts.exam_header')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@section('content')

<!-- TIMER -->
<div id="timer" class="exam-timer bg-success text-white fw-bold">
    Time Remaining: --
</div>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">

<div class="head-shape">
<img style="width:100%" src="{{ asset('images/head-shape-test.png') }}">
</div>

<div class="head-main" style="padding-top:50px;">
<div class="inner-container">

<div class="rw-flex">

<div class="apd-6">
<div class="h-logo">
<img src="{{ asset('images/logo-sortiq.png') }}" width="200">
</div>
</div>

<div class="apd-6">
<div class="h-detials">

<p>+91 96465 22110</p>
<p>info@sortiqsolutions.com</p>
<p>www.sortiqsolutions.com</p>

</div>
</div>

</div>

</div>
</div>


<form id="examForm"
method="POST"
action="{{ route('student.office.submit',$test->slug) }}">

@csrf

<div class="certi-body" style="padding-top:60px;">

<div class="apt-body-content">

<div class="apt-body-title">
<div class="inner-container">

<h2><strong>{{ ucwords($test->title) }}</strong></h2>

</div>
</div>


<div class="apt-qs-main">

<div class="inner-container">

@foreach($test->questions as $question)

<div class="apt-question">

<h3>{{ $loop->iteration }}. {{ $question->question }}</h3>

<textarea
class="form-control exam-answer"
rows="5"
name="answers[{{ $question->id }}]"
data-question="{{ $question->id }}"
placeholder="Write your answer here..."
>{{ $answers[$question->id] ?? '' }}</textarea>

</div>

@endforeach


<div class="apt-submit mt-4">

<button class="btn btn-primary">
Submit Exam
</button>

</div>

</div>

</div>

</div>

</div>

</form>


<div class="footer-shape">
<img style="width:100%" src="{{ asset('images/footer-shape-1-test.png') }}">
</div>

</div>


<style>

.exam-timer{
position:fixed;
top:15px;
right:20px;
z-index:2000;
padding:10px 18px;
font-size:16px;
border-radius:30px;
background:#fff;
color:#000;
box-shadow:0 4px 10px rgba(0,0,0,.2);
}

textarea{
resize:vertical;
}

.exam-answer{
width:100%;
min-height:120px;
padding:15px;
font-size:15px;
line-height:1.6;
border-radius:6px;
resize:vertical;
overflow:hidden;
box-sizing:border-box;
}
</style>


<script>

/* TIMER */

let seconds = parseInt({{ $remainingSeconds }},10);

const timer = document.getElementById('timer');
const examForm = document.getElementById('examForm');

let examSubmitted = false;
let lastSaveTime = Date.now();


/* AUTOSAVE */

function autoSave(){

let answers = {};

document.querySelectorAll('.exam-answer').forEach(el=>{

let qid = el.dataset.question;

answers[qid] = el.value;

});

fetch("{{ route('student.office.autosave',$test->slug) }}",{

method:"POST",

headers:{
"X-CSRF-TOKEN":"{{ csrf_token() }}",
"Content-Type":"application/json"
},

body:JSON.stringify({answers})

}).then(()=>{

lastSaveTime = Date.now();

});

}


/* SAVE ON TYPING */

document.querySelectorAll('.exam-answer').forEach(el=>{

el.addEventListener('keyup',()=>{

clearTimeout(el.saveTimer);

el.saveTimer = setTimeout(autoSave,1000);

});

});


/* BACKUP SAVE EVERY 10 SEC */

let autoSaveInterval = setInterval(autoSave,10000);


/* TIMER */

const timerInterval = setInterval(()=>{

seconds--;

if(seconds<0) seconds=0;

const m = Math.floor(seconds/60);
const s = seconds%60;

timer.innerText = `Time Remaining: ${m}:${s.toString().padStart(2,'0')}`;

if(seconds<=60){

timer.classList.remove('bg-success');
timer.classList.add('bg-danger');

}

if(seconds<=0){

clearInterval(timerInterval);
clearInterval(autoSaveInterval);

examSubmitted=true;

examForm.submit();

}

},1000);


/* SUBMIT HANDLER */

examForm.addEventListener('submit',()=>{

examSubmitted=true;
clearInterval(autoSaveInterval);

});


/* REFRESH PROTECTION */

window.onbeforeunload=function(e){

if(!examSubmitted){

autoSave();

e.preventDefault();
e.returnValue='';

return '';

}

};
/* AUTO EXPAND TEXTAREA */

function autoResize(el){
    el.style.height = "auto";
    el.style.height = el.scrollHeight + "px";
}

document.querySelectorAll('.exam-answer').forEach(el => {

    autoResize(el);

    el.addEventListener('input', function(){

        autoResize(this);

    });

});
</script>
<script>
window.addEventListener("pageshow", function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>


@endsection