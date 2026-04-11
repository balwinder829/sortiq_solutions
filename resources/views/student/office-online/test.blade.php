@extends('layouts.exam_header')

@section('content')

<!-- ================= FIXED TIMER ================= -->
<div id="timer" class="exam-timer fw-bold">
    Time Remaining: --
</div>

<div class="wrapper" style="width: 100%; overflow: hidden; background-color: #fff;">
    
    <!-- HEADER -->
    <div class="head-shape">
        <img style="width: 100%;" src="{{ asset('images/head-shape-test.png') }}"/>
    </div>

    <div class="head-main" style="padding-top: 50px;">
        <div class="inner-container">
            <div class="rw-flex">
                <div class="apd-6">
                    <img style="max-width: 200px;" src="{{ asset('images/logo-sortiq.png') }}"/>
                </div>
                <div class="apd-6 text-start">
                    <p>+91 96465 22110</p>
                    <p>info@sortiqsolutions.com</p>
                    <p>www.sortiqsolutions.com</p>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM -->
    <form id="examForm" method="POST" action="{{ route('student.office-online.submit') }}">
        @csrf

        <div class="certi-body" style="padding-top: 60px;">
            <div class="apt-body-title text-center">
                <h2><strong>{{ ucwords($test->title) }}</strong></h2>
            </div>

            <div class="apt-qs-main">
                <div class="inner-container">
                    <div class="apt-rep">

                        {{-- ✅ IMPORTANT CHANGE --}}
                        @foreach($test->questions as $question)

                        <div class="apt-question">
                            <h3>{{ $loop->iteration }}. {{ $question->question }}</h3>

                            <ul class="opt-list">
                                @foreach($question->options as $option)

                                <li class="radio">
                                    <input 
                                        id="q{{ $question->id }}_{{ $option->id }}"
                                        type="radio"
                                        name="answers[{{ $question->id }}]"
                                        value="{{ $option->id }}"
                                        @checked(isset($answers[$question->id]) && $answers[$question->id] == $option->id)
                                    >

                                    <label for="q{{ $question->id }}_{{ $option->id }}">
                                        {{ $option->option_text }}
                                    </label>
                                </li>

                                @endforeach
                            </ul>
                        </div>

                        @endforeach

                    </div>

                    <div class="apt-submit text-center mt-4">
                        <button class="btn btn-primary">Submit Answer</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>

<!-- ================= TIMER STYLE ================= -->
<style>
.exam-timer {
    position: fixed;
    top: 15px;
    right: 20px;
    padding: 10px 18px;
    border-radius: 30px;
    background: #fff;
    color: #000;
    box-shadow: 0 4px 10px rgba(0,0,0,.2);
}
</style>

<!-- ================= SCRIPT ================= -->
<script>
let seconds = parseInt({{ $remainingSeconds }}, 10);
const timer = document.getElementById('timer');
const examForm = document.getElementById('examForm');

let examSubmitted = false;
let lastSaveTime = Date.now();

/* AUTO SAVE */
function autoSave() {
    let answers = {};

    document.querySelectorAll('input[type=radio]:checked').forEach(el => {
        let qid = el.name.match(/\d+/)[0];
        answers[qid] = el.value;
    });

    if (Object.keys(answers).length === 0) return;

    fetch("{{ route('student.office-online.autosave') }}", {
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

/* SAVE ON CHANGE */
document.querySelectorAll('input[type=radio]').forEach(radio => {
    radio.addEventListener('change', autoSave);
});

/* AUTO SAVE INTERVAL */
let autoSaveInterval = setInterval(autoSave, 15000);

/* TIMER */
const timerInterval = setInterval(() => {
    seconds--;

    const m = Math.floor(seconds / 60);
    const s = seconds % 60;

    timer.innerText = `Time Remaining: ${m}:${s.toString().padStart(2,'0')}`;

    if (seconds <= 0) {
        clearInterval(timerInterval);
        clearInterval(autoSaveInterval);
        examSubmitted = true;
        window.onbeforeunload = null;
        examForm.submit();
    }
}, 1000);

/* SUBMIT */
examForm.addEventListener('submit', () => {
    examSubmitted = true;
    clearInterval(autoSaveInterval);
});

/* LEAVE WARNING */
window.onbeforeunload = function () {
    if (!examSubmitted && Date.now() - lastSaveTime > 2000) {
        autoSave();
        return '';
    }
};
</script>

@endsection