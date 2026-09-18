@extends('layouts.exam_header')

@section('content')

<!-- ================= FIXED TIMER (TOP RIGHT) ================= -->
<!-- <div id="timer" class="exam-timer bg-success text-white fw-bold">
    Time Remaining: --
</div> -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div id="exam-security-overlay">
    <div class="security-message">
        Please return to the exam screen.
    </div>
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
        <img style="width: 100%; display: block;" src="{{ asset('images/confirmation_images/footer-shape-1.png') }}"/>
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

#exam-security-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: #fff;
    z-index: 999999;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
}

#exam-security-overlay .security-message {
    font-size: 18px;
    font-weight: 600;
}
</style>

<!-- ================= SCRIPTS ================= -->
<script>
const examForm = document.getElementById('examForm');

let examSubmitted = false;
let inactivityTimer = null;
let savePromise = null;

/* ================= STORAGE ================= */

const attemptId = '{{ session('current_student_test_id') }}';

const answersStorageKey = 'exam_answers_' + attemptId;
const pendingStorageKey = 'exam_pending_' + attemptId;
const examSubmittedKey = 'exam_submitted_test_{{ $test->slug }}';

function getStoredAnswers() {
    try {
        return JSON.parse(localStorage.getItem(answersStorageKey)) || {};
    } catch (e) {
        return {};
    }
}

function saveStoredAnswers(answers) {
    try {
        localStorage.setItem(
            answersStorageKey,
            JSON.stringify(answers)
        );
    } catch (e) {
        console.log('Local storage unavailable.');
    }
}

function getPendingAnswers() {
    try {
        return JSON.parse(localStorage.getItem(pendingStorageKey)) || {};
    } catch (e) {
        return {};
    }
}

function savePendingAnswersToStorage(answers) {
    try {
        localStorage.setItem(
            pendingStorageKey,
            JSON.stringify(answers)
        );
    } catch (e) {
        console.log('Local storage unavailable.');
    }
}

/* ================= RESTORE ANSWERS ================= */

function restoreAnswers() {

    const storedAnswers = getStoredAnswers();

    Object.keys(storedAnswers).forEach(function(questionId) {

        const optionId = storedAnswers[questionId];

        const radio = document.querySelector(
            'input[name="answers[' + questionId + ']"][value="' + optionId + '"]'
        );

        if (radio) {
            radio.checked = true;
        }
    });
}

/* ================= SEND ONE BATCH ================= */

function sendPendingOnce() {

    const pending = getPendingAnswers();

    if (Object.keys(pending).length === 0) {
        return Promise.resolve(true);
    }

    if (savePromise) {
        return savePromise;
    }

    const answersBeingSent = {
        ...pending
    };

    savePromise = fetch(
        "{{ route('student.test.autosave', $test->id) }}",
        {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Content-Type": "application/json",
                "Accept": "application/json"
            },
            body: JSON.stringify({
                answers: answersBeingSent
            })
        }
    )
    .then(function(response) {

        if (!response.ok) {
            throw new Error('Save failed: ' + response.status);
        }

        return response.json();
    })
    .then(function(data) {

        if (data.status !== 'saved') {
            return false;
        }

        const currentPending = getPendingAnswers();

        Object.keys(answersBeingSent).forEach(function(questionId) {

            if (
                currentPending[questionId] ===
                answersBeingSent[questionId]
            ) {
                delete currentPending[questionId];
            }

        });

        savePendingAnswersToStorage(currentPending);

        return true;
    })
    .catch(function(error) {

        console.log(
            'Autosave failed. Answers kept in localStorage.',
            error
        );

        return false;
    })
    .finally(function() {

        savePromise = null;
    });

    return savePromise;
}

/* ================= FLUSH ALL PENDING ================= */

async function flushPendingAnswers() {

    while (Object.keys(getPendingAnswers()).length > 0) {

        const saved = await sendPendingOnce();

        if (!saved) {
            break;
        }
    }

    return Object.keys(getPendingAnswers()).length === 0;
}

/* =========================================================
   AUTO SUBMIT AFTER 3 VIOLATIONS
   ========================================================= */

async function autoSubmitExam() {

    if (examSubmitted) {
        return;
    }

    examSubmitted = true;

    localStorage.setItem(examSubmittedKey, '1');

    clearTimeout(inactivityTimer);

    /*
     * Get latest answers from localStorage
     */
    const currentAnswers = getStoredAnswers();

    /*
     * Get latest answers directly from the form
     */
    document.querySelectorAll(
        'input[type="radio"]:checked'
    ).forEach(function (radio) {

        const match = radio.name.match(/\d+/);

        if (match) {
            currentAnswers[match[0]] = radio.value;
        }

    });

    /*
     * Save latest answers locally
     */
    saveStoredAnswers(currentAnswers);

    /*
     * Put all current answers into pending queue
     */
    const pending = getPendingAnswers();

    Object.keys(currentAnswers).forEach(function (questionId) {

        pending[questionId] = currentAnswers[questionId];

    });

    savePendingAnswersToStorage(pending);

    /*
     * Try to save pending answers to server.
     * Do NOT wait for it because mobile browsers
     * can suspend the page after it becomes hidden.
     */
    flushPendingAnswers();

    /*
     * Remove browser leave warning
     */
    window.onbeforeunload = null;

    /*
     * Submit the existing Laravel form
     */
    examForm.submit();
}
/* ================= ANSWER CHANGE ================= */

document.querySelectorAll('input[type="radio"]').forEach(function(radio) {

    radio.addEventListener('change', function() {

        const match = this.name.match(/\d+/);

        if (!match) {
            return;
        }

        const questionId = match[0];
        const optionId = this.value;

        /* Save immediately in browser */

        const storedAnswers = getStoredAnswers();

        storedAnswers[questionId] = optionId;

        saveStoredAnswers(storedAnswers);

        /* Add to server pending queue */

        const pending = getPendingAnswers();

        pending[questionId] = optionId;

        savePendingAnswersToStorage(pending);

        /* Send when 10 different answers are pending */

        if (Object.keys(pending).length >= 10) {

            sendPendingOnce().then(function() {

                /*
                 * If more answers were changed while the
                 * previous AJAX request was running, send
                 * another batch when 10 are waiting.
                 */

                if (Object.keys(getPendingAnswers()).length >= 10) {
                    sendPendingOnce();
                }

            });
        }

        resetInactivityTimer();
    });

});

/* ================= 4 MINUTE BACKUP ================= */

function resetInactivityTimer() {

    clearTimeout(inactivityTimer);

    inactivityTimer = setTimeout(function() {

        if (!examSubmitted) {
            sendPendingOnce();
        }

    }, 4 * 60 * 1000);
}

/* ================= PAGE LOAD ================= */
if (localStorage.getItem(examSubmittedKey) === '1') {

    window.location.replace(
        "{{ route('student.already.submitted', $test->slug) }}"
    );

} else {

    restoreAnswers();
    resetInactivityTimer();

}
// restoreAnswers();
// resetInactivityTimer();

/* ================= FINAL SUBMIT ================= */

examForm.addEventListener('submit', async function(e) {

    e.preventDefault();

    if (examSubmitted) {
        return;
    }

    clearTimeout(inactivityTimer);

    /*
     * Get the latest answers directly from the form.
     */

    const currentAnswers = getStoredAnswers();

    document.querySelectorAll(
        'input[type="radio"]:checked'
    ).forEach(function(radio) {

        const match = radio.name.match(/\d+/);

        if (match) {
            currentAnswers[match[0]] = radio.value;
        }

    });

    /*
     * Save latest answers locally.
     */

    saveStoredAnswers(currentAnswers);

    /*
     * Put ALL current answers into pending queue.
     */

    const pending = getPendingAnswers();

    Object.keys(currentAnswers).forEach(function(questionId) {

        pending[questionId] = currentAnswers[questionId];

    });

    savePendingAnswersToStorage(pending);

    /*
     * Keep sending until there is nothing pending.
     */

    await flushPendingAnswers();

    /*
     * Now use the existing Laravel form submission.
     */

    examSubmitted = true;

    localStorage.setItem(examSubmittedKey, '1');

    window.onbeforeunload = null;

    examForm.submit();

});

/* ================= REFRESH / LEAVE WARNING ================= */

if (!examSubmitted) {

    // Browser refresh / close / direct navigation
    window.addEventListener('beforeunload', function(e) {

        if (!examSubmitted) {

            // Get all currently selected answers
            const answers = {};

            document.querySelectorAll('input[name^="answers["]:checked')
                .forEach(function(radio) {

                    const match = radio.name.match(/^answers\[(\d+)\]$/);

                    if (match) {
                        answers[match[1]] = radio.value;
                    }
                });

            // Send answers to Laravel
            if (Object.keys(answers).length > 0) {

                const formData = new FormData();

                formData.append('_token', '{{ csrf_token() }}');

                Object.keys(answers).forEach(function(questionId) {
                    formData.append(
                        `answers[${questionId}]`,
                        answers[questionId]
                    );
                });

                navigator.sendBeacon(
                    '{{ route("student.test.autosave", $test->id) }}',
                    formData
                );
            }

            // Show browser warning
            e.preventDefault();
            e.returnValue = '';
        }

    });


    // Browser Back button
    history.pushState(null, '', location.href);

    window.addEventListener('popstate', function() {

        if (!examSubmitted) {

            const leaveExam = confirm(
                'Your exam is still in progress. Are you sure you want to leave this page?'
            );

            if (leaveExam) {

                examSubmitted = true;
                history.back();

            } else {

                history.pushState(null, '', location.href);

            }
        }

    });

}
// if (!examSubmitted) {

//     // Browser refresh / close / direct navigation
//     window.addEventListener('beforeunload', function(e) {

//         if (!examSubmitted) {

//             // Try normal form submission
//             examForm.submit();

//             // Show browser's native warning
//             e.preventDefault();
//             e.returnValue = '';
//             return '';
//         }

//     });


//     // Browser Back button
//     history.pushState(null, '', location.href);

//     window.addEventListener('popstate', function() {

//         if (!examSubmitted) {

//             const leaveExam = confirm(
//                 'Your exam is still in progress.\n\n' +
//                 'Please submit your exam before leaving this page.\n\n' +
//                 'Click OK to submit the exam, or Cancel to continue the exam.'
//             );

//             if (leaveExam) {

//                 examSubmitted = true;

//                 // Normal form submit — same as clicking Submit Answer
//                 examForm.submit();

//             } else {

//                 history.pushState(null, '', location.href);

//             }
//         }

//     });

// }
// if (!examSubmitted) {

//     // Browser refresh / close / direct navigation
//     window.addEventListener('beforeunload', function(e) {

//         if (!examSubmitted) {
//             e.preventDefault();
//             e.returnValue = '';
//             return '';
//         }

//     });

//     // Browser Back button
//     history.pushState(null, '', location.href);

//     window.addEventListener('popstate', function() {

//         if (!examSubmitted) {

//             const leaveExam = confirm(
//                 'Your exam is still in progress. Are you sure you want to leave this page?'
//             );

//             if (leaveExam) {

//                 examSubmitted = true;
//                 history.back();

//             } else {

//                 history.pushState(null, '', location.href);

//             }
//         }

//     });

// }



/* =========================================================
   SILENT EXAM BROWSER RESTRICTIONS
   ========================================================= */

/* ---------- RIGHT CLICK ---------- */

document.addEventListener('contextmenu', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* ---------- TEXT SELECTION ---------- */

document.addEventListener('selectstart', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* ---------- COPY ---------- */

document.addEventListener('copy', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* ---------- CUT ---------- */

document.addEventListener('cut', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* ---------- PASTE ---------- */

document.addEventListener('paste', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* ---------- DRAG ---------- */

document.addEventListener('dragstart', function (e) {
    if (!examSubmitted) {
        e.preventDefault();
    }
});


/* =========================================================
   BLOCK COMMON BROWSER / DEVTOOLS SHORTCUTS
   ========================================================= */

document.addEventListener('keydown', function (e) {

    if (examSubmitted) {
        return;
    }

    const key = e.key.toLowerCase();


    /* F12 */

    if (e.key === 'F12') {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + SHIFT + I */

    if (
        e.ctrlKey &&
        e.shiftKey &&
        key === 'i'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + SHIFT + J */

    if (
        e.ctrlKey &&
        e.shiftKey &&
        key === 'j'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + SHIFT + C */

    if (
        e.ctrlKey &&
        e.shiftKey &&
        key === 'c'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + U */

    if (
        e.ctrlKey &&
        key === 'u'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + P */

    if (
        e.ctrlKey &&
        key === 'p'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + S */

    if (
        e.ctrlKey &&
        key === 's'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* CTRL + SHIFT + S */

    if (
        e.ctrlKey &&
        e.shiftKey &&
        key === 's'
    ) {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }


    /* PRINT SCREEN */

    if (e.key === 'PrintScreen') {
        e.preventDefault();
        e.stopImmediatePropagation();
        return false;
    }

});


/* =========================================================
   BLOCK MIDDLE MOUSE BUTTON
   ========================================================= */

document.addEventListener('auxclick', function (e) {

    if (
        !examSubmitted &&
        e.button === 1
    ) {
        e.preventDefault();
    }

});


/* =========================================================
   BLOCK LINKS FROM LEAVING EXAM
   ========================================================= */

document.addEventListener('click', function (e) {

    if (examSubmitted) {
        return;
    }

    const link = e.target.closest('a');

    if (link) {
        e.preventDefault();
        e.stopPropagation();
    }

}, true);


/* =========================================================
   BLOCK DRAGGING LINKS / IMAGES
   ========================================================= */

document.addEventListener('dragstart', function (e) {

    if (!examSubmitted) {
        e.preventDefault();
    }

}, true);


/* =========================================================
   DISABLE WINDOW.OPEN DURING EXAM
   ========================================================= */

const originalWindowOpen = window.open;

window.open = function () {

    if (!examSubmitted) {
        return null;
    }

    return originalWindowOpen.apply(
        window,
        arguments
    );

};


/* =========================================================
   BLOCK JAVASCRIPT DIALOGS CREATED BY PAGE
   ========================================================= */

/*
 * Do NOT override alert/confirm globally.
 *
 * Your existing Back-button confirm needs to work.
 */


/* =========================================================
   FULLSCREEN
   ========================================================= */

async function startExamFullscreen() {

    if (
        examSubmitted ||
        document.fullscreenElement
    ) {
        return;
    }

    try {

        await document.documentElement.requestFullscreen();

    } catch (error) {

        /*
         * Browser may reject fullscreen if there
         * is no user gesture.
         */

    }

}


/* =========================================================
   START FULLSCREEN WHEN ANSWER IS SELECTED
   ========================================================= */

document.querySelectorAll(
    '#examForm input[type="radio"]'
).forEach(function (radio) {

    radio.addEventListener('change', function () {

        if (!examSubmitted) {
            startExamFullscreen();
        }

    });

});
// Try to enter fullscreen when exam page opens
window.addEventListener('load', function () {
    startExamFullscreen();
});

// If browser blocks automatic fullscreen,
// enter fullscreen on student's first tap/click
document.addEventListener('click', function startFullscreenOnFirstClick() {

    if (examSubmitted) return;

    if (!document.fullscreenElement) {
        startExamFullscreen();
    }

}, { once: true });

/* =========================================================
   FULLSCREEN EXIT
   ========================================================= */

document.addEventListener(
    'fullscreenchange',
    function () {

        if (
            !document.fullscreenElement &&
            !examSubmitted
        ) {

            /*
             * Browser may require another user gesture.
             * No popup is shown.
             */

        }

    }
);


/* =========================================================
   TAB SWITCH DETECTION — SILENT
   ========================================================= */

/* =========================================================
   EXAM SECURITY
   1st violation = WARNING
   2nd violation = FINAL WARNING
   3rd violation = AUTO SUBMIT
   ========================================================= */

let securityViolationCount = 0;
let lastViolationTime = 0;

function handleExamViolation(reason) {

    if (examSubmitted) {
        return;
    }

    const now = Date.now();

    /*
     * visibilitychange and blur can happen together
     * for the same action.
     *
     * Ignore duplicate events within 1.5 seconds.
     */
    if (now - lastViolationTime < 1500) {
        return;
    }

    lastViolationTime = now;

    securityViolationCount++;

    console.log(
        'Exam violation:',
        reason,
        'Violation:',
        securityViolationCount
    );


    /* ==============================
       1ST VIOLATION
       ============================== */

    if (securityViolationCount === 1) {

        setTimeout(function () {

            if (!examSubmitted) {

                Swal.fire({
                icon: 'warning',
                title: 'Warning 1',
                html:
                    '<div style="font-size:16px;line-height:1.6;">' +
                    'You have left the exam screen.<br><br>' +
                    '<strong>Please stay on the exam screen.</strong><br>' +
                    'Do not switch tabs or open another app during the exam.' +
                    '</div>',
                confirmButtonText: 'I Understand',
                allowOutsideClick: false,
                allowEscapeKey: false
            });

            }

        }, 300);

    }


    /* ==============================
       2ND VIOLATION
       ============================== */

    else if (securityViolationCount === 2) {

        setTimeout(function () {

            if (!examSubmitted) {

                Swal.fire({
                icon: 'warning',
                title: 'Final Warning',
                html:
                    '<div style="font-size:16px;line-height:1.6;">' +
                    'You have left the exam screen again.<br><br>' +
                    '<strong>This is your final warning.</strong><br><br>' +
                    'If you leave the exam screen one more time, ' +
                    'your exam will be submitted automatically.' +
                    '</div>',
                confirmButtonText: 'I Understand',
                allowOutsideClick: false,
                allowEscapeKey: false
            });

            }

        }, 300);

    }


    /* ==============================
       3RD VIOLATION
       ============================== */

    else if (securityViolationCount >= 3) {

        autoSubmitExam();

    }

}


/* =========================================================
   MOBILE + DESKTOP
   TAB / APP SWITCH DETECTION
   ========================================================= */

// document.addEventListener(
//     'visibilitychange',
//     function () {

//         if (examSubmitted) {
//             return;
//         }

//         if (document.hidden) {
//             // Immediately cover the exam
//             showSecurityOverlay();
            
//             handleExamViolation(
//                 'Tab switched / another app opened / browser hidden'
//             );

//         }

//     }
// );

document.addEventListener(
    'visibilitychange',
    function () {

        if (examSubmitted) {
            return;
        }

        if (document.hidden) {

            // Student left the exam
            // showSecurityOverlay();

            handleExamViolation(
                'Tab switched / another app opened / browser hidden'
            );

        } else {

            // Student returned to the exam
            // hideSecurityOverlay();

        }

    }
);


/* =========================================================
   DESKTOP WINDOW BLUR
   ========================================================= */

window.addEventListener(
    'blur',
    function () {

        if (examSubmitted) {
            return;
        }

        /*
         * Do not count blur separately on mobile.
         * Mobile browsers can generate blur for normal
         * browser behaviour.
         */
        if (window.innerWidth <= 768) {
            return;
        }

        handleExamViolation(
            'Another window received focus / browser minimized'
        );

    }
);

// document.addEventListener(
//     'visibilitychange',
//     function () {

//         if (
//             document.hidden ||
//             examSubmitted
//         ) {
//             return;
//         }

//         /*
//          * No popup.
//          * No AJAX.
//          * No answer deletion.
//          */

//     }
// );


// /* =========================================================
//    WINDOW BLUR — SILENT
//    ========================================================= */

// window.addEventListener('blur', function () {

//     if (examSubmitted) {
//         return;
//     }

//     /*
//      * Do nothing visibly.
//      * We cannot prevent the operating system/browser
//      * from changing window focus.
//      */

// });


/* =========================================================
   DISABLE TEXT SELECT THROUGH CSS
   ========================================================= */

const securityStyle =
    document.createElement('style');

securityStyle.innerHTML = `
    #examForm,
    #examForm * {
        user-select: none !important;
        -webkit-user-select: none !important;
        -moz-user-select: none !important;
        -ms-user-select: none !important;
    }

    #examForm img {
        -webkit-user-drag: none !important;
        user-drag: none !important;
    }
`;

document.head.appendChild(securityStyle);


/* =========================================================
   PREVENT WINDOW RESIZE ATTEMPTS FROM BEING USED
   ========================================================= */

window.addEventListener('resize', function () {

    if (examSubmitted) {
        return;
    }

    /*
     * Nothing is shown.
     */

});


/* =========================================================
   BLOCK BEFORE PRINT
   ========================================================= */

window.addEventListener('beforeprint', function () {

    if (!examSubmitted) {
        document.body.style.display = 'none';
    }

});


window.addEventListener('afterprint', function () {

    if (!examSubmitted) {
        document.body.style.display = '';
    }

});

const examSecurityOverlay = document.getElementById('exam-security-overlay');

function showSecurityOverlay() {
    if (examSecurityOverlay) {
        examSecurityOverlay.style.display = 'flex';
    }
}

function hideSecurityOverlay() {
    if (examSecurityOverlay) {
        examSecurityOverlay.style.display = 'none';
    }
}
</script>

@endsection
