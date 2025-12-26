@extends('layouts.exam_header')

@section('content')

<!-- ================= FIXED TIMER (TOP RIGHT) ================= -->
<div id="timer" class="exam-timer bg-success text-white fw-bold">
    Time Remaining: --
</div>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">
    <div class="head-shape">
        <img style="width: 100%; display: block;" src="{{ asset('images/head-shape-test.png') }}"/>
    </div>
    <div class="head-main" style="padding-top: 50px;">
        <div class="inner-container">
            <div class="rw-flex">
                <div class="apd-6">
                    <div class="h-logo">
                        <img style="width: 100%; max-width: 200px;" src="{{ asset('images/logo-sortiq.png') }}" width="200"/>
                    </div>
                </div>
                <div class="apd-6">
                    <div class="h-detials">
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ asset('certificate_images/cl.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">+91 96465 22110</span></p>
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%;font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ asset('certificate_images/email.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">info@sortiqsolutions.com</span></p>
                            <p style="margin: 0; font-size: 14px; line-height: normal; display: inline-block; margin-top: 2px; width: 100%; font-family: 'Inter', sans-serif; text-align:left;"><img src="{{ asset('certificate_images/globe.png') }}" style="width:15px; margin-top:0px;"/>&nbsp;&nbsp;<span style="color: #2c2e35; font-size: 15px; margin-top: 0px; line-height: 14px; position: relative; top: -2px;">www.sortiqsolutions.com</span></p>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <form id="examForm" method="POST" action="{{ route('student.test.submit', $test->id) }}">
    @csrf
     <div class="certi-body" style="padding-top: 60px;">
            <div class="apt-body-content">
                <div class="apt-body-title">
                    <div class="inner-container">
                        <h2><strong>{{ ucwords($test->title) }}</strong></h2>
                    </div>
                </div>

                <div class="apt-qs-main">
                    <div class="inner-container">
                        <div class="apt-rep">
                       @foreach($test->questions as $question)
                        <div class="apt-question">
                            <h3>{{ $loop->iteration }}. {{ $question->question }}</h3>
                            <div class="apt-options">
                                <ul class="opt-list">
                                     @foreach($question->options as $option)
                                    <li class="radio">
                                        <input id="first_{{ $question->id }}_{{ $option->id }}" type="radio" name="answers[{{ $question->id }}]"
                value="{{ $option->id }}" class="form-check-input" 
                @checked(isset($answers[$question->id]) && $answers[$question->id] == $option->id)>  
                                        <label for="first_{{ $question->id }}_{{ $option->id }}">{{ $option->option_text }}</label>     
                                    </li>
                                     
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endforeach
                        </div>
                        <div class="apt-submit">
                            <button>Submit Answer</button>
                        </div>
                    </div>
                </div>
             
        </div>
    </div>
    </form>
    <div class="footer-shape">
        <img style="width: 100%; display: block;" src="{{ asset('images/footer-shape-1-test.png') }}"/>
    </div>
</div>
<!-- ================= SCROLLABLE CONTENT ================= -->
 

<!-- ================= STYLES ================= -->
<style>
.exam-timer {
    position: fixed;
    top: 15px;
    right: 20px;
    z-index: 2000;
    padding: 10px 18px;
    font-size: 16px;
    border-radius: 30px;
    box-shadow: 0 4px 10px rgba(0,0,0,.2);
    transition: background-color 0.4s ease;
     background: #ffffff !important; /* FORCE white background */
    color: #000 !important;  
}

.exam-content {
    margin-top: 20px;
}
</style>

<!-- ================= SCRIPTS ================= -->
<script>
/* ================== STATE ================== */
let seconds = parseInt({{ $remainingSeconds }}, 10);
const timer = document.getElementById('timer');
const examForm = document.getElementById('examForm');

let examSubmitted = false;
let lastSaveTime = Date.now();

/* ================== AUTO SAVE ================== */
function autoSave() {
    let answers = {};

    document.querySelectorAll('input[type=radio]:checked').forEach(el => {
        let qid = el.name.match(/\d+/)[0];
        answers[qid] = el.value;
    });

    if (Object.keys(answers).length === 0) return;

    fetch("{{ route('student.test.autosave', $test->id) }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({ answers })
    }).then(() => {
        lastSaveTime = Date.now();
    });
}

/* ================== SAVE ON OPTION CHANGE ================== */
document.querySelectorAll('input[type=radio]').forEach(radio => {
    radio.addEventListener('change', autoSave);
});

/* ================== BACKUP AUTO SAVE ================== */
let autoSaveInterval = setInterval(autoSave, 15000);

/* ================== TIMER ================== */
const timerInterval = setInterval(() => {
    seconds--;
    if (seconds < 0) seconds = 0;

    const m = Math.floor(seconds / 60);
    const s = seconds % 60;

    // Update text
    timer.innerText =
        `Time Remaining: ${m}:${s.toString().padStart(2,'0')}`;

    // Color changes
    if (seconds <= 60) {
        timer.classList.remove('bg-success', 'bg-warning');
        timer.classList.add('bg-danger');
    } 
    // else if (seconds <= 300) {
    //     timer.classList.remove('bg-success', 'bg-danger');
    //     timer.classList.add('bg-warning', 'text-dark');
    // } 
    else {
        timer.classList.remove('bg-warning', 'bg-danger', 'text-dark');
        timer.classList.add('bg-success');
    }

    if (seconds <= 0) {
        clearInterval(timerInterval);
        clearInterval(autoSaveInterval);
        examSubmitted = true;
        window.onbeforeunload = null;
        examForm.submit();
    }
}, 1000);

/* ================== SUBMIT HANDLER ================== */
examForm.addEventListener('submit', () => {
    examSubmitted = true;
    window.onbeforeunload = null;
    clearInterval(autoSaveInterval);
});

/* ================== REFRESH / LEAVE WARNING ================== */
window.onbeforeunload = function (e) {
    if (!examSubmitted && Date.now() - lastSaveTime > 2000) {
        autoSave();
        e.preventDefault();
        e.returnValue = '';
        return '';
    }
};
</script>

@endsection
