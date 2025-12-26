@extends('layouts.exam')

@section('content')

<!-- ================= FIXED TIMER (TOP RIGHT) ================= -->
<div id="timer" class="exam-timer bg-success text-white fw-bold">
    Time Remaining: --
</div>

<!-- ================= SCROLLABLE CONTENT ================= -->
<div class="container-fluid exam-content">

<form id="examForm" method="POST" action="{{ route('student.test.submit', $test->id) }}">
@csrf

@foreach($test->questions as $question)
<div class="card m-3">
    <div class="card-body">
        <h5>{{ $loop->iteration }}. {{ $question->question }}</h5>

        @foreach($question->options as $option)
        <div class="form-check">
            <input class="form-check-input"
                type="radio"
                name="answers[{{ $question->id }}]"
                value="{{ $option->id }}"
                @checked(isset($answers[$question->id]) && $answers[$question->id] == $option->id)>
            <label class="form-check-label">
                {{ $option->option_text }}
            </label>
        </div>
        @endforeach
    </div>
</div>
@endforeach

<div class="text-center mb-5">
    <button class="btn btn-success btn-lg">Submit Test</button>
</div>

</form>
</div>

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
