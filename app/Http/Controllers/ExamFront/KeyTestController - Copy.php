<?php

namespace App\Http\Controllers\ExamFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\StudentTest;
use App\Models\StudentAnswer;
use App\Models\TestLink;
use App\Rules\NotBlockedNumber;
use App\Models\ExternalAttendanceSubmission;

class KeyTestController extends Controller
{
    /* ---------------- ENTRY FORM ---------------- */

     

    public function studentView($slug)
    {

        // NEW SYSTEM (college specific link)
        $link = TestLink::where('slug', $slug)->first();
        if ($link) {
            return redirect()->route('student.enter.key', ['slug' => $slug]);
        }else{
            return view('student.test_unavailable');
        }
    }

    public function showForm(Request $request)
    {
        $slug = $request->query('slug');

        $link = TestLink::where('slug', $slug)->first();

        $availabilityMessage = $this->getLinkAvailabilityMessage($link);
        $canStart = $link && $availabilityMessage === null;

        return view('student.enter_key', [
            'slug' => $slug,
            'link' => $link,
            'canStart' => $canStart,
            'availabilityMessage' => $availabilityMessage,
        ]);
    }

    private function getLinkAvailabilityMessage(?TestLink $link): ?string
    {
        if (!$link) {
            return 'This test link is invalid or unavailable.';
        }

        // Admin has manually disabled this college-specific link.
        if (!(bool) $link->is_enabled) {
            return 'This test has been disabled by the management.';
        }

        // No deadline means the admin has not started this test link yet.
        if (!$link->valid_until) {
            return 'Test has not been started by the management yet.';
        }

        // Use the current database deadline on every request.
        if (now()->greaterThanOrEqualTo(
            \Illuminate\Support\Carbon::parse($link->valid_until)
        )) {
            return 'This test link has expired.';
        }

        // If a start date is configured, do not allow an early start.
        if (
            $link->valid_from &&
            now()->lessThan(
                \Illuminate\Support\Carbon::parse($link->valid_from)
            )
        ) {
            return 'Test has not been started by the management yet.';
        }

        $test = Test::find($link->test_id);

        if (
            !$test ||
            $test->status !== 'published' ||
            !(bool) $test->is_active
        ) {
            return 'This test is currently unavailable.';
        }

        return null;
    }

    /* ---------------- ACCESS TEST ---------------- */

    public function accessTest(Request $request)
    {
        $request->validate([
            'student_name'   => 'required|string',
            'student_email'  => 'required|email',
            'gender'         => 'required|string',
            'student_mobile' => ['required', 'digits:10', new NotBlockedNumber],
            'slug'           => 'required|exists:test_links,slug',
            'course_type'    => 'nullable|in:Degree,Diploma',
            'class'          => 'nullable|in:BCA,MCA,BTech,BSc IT,BSc CS,Polytechnic,BSc,B.Tech',
            'semester'       => 'nullable|integer|min:1|max:8',
            'student_branch' => 'nullable',
        ], [
            'student_mobile.required' => 'Mobile number is required',
            'slug.exists' => 'Test link is expired or invalid.',
            'slug.required' => 'Test link is expired or invalid.',
        ]);

        $link = TestLink::where('slug', $request->slug)->first();

        $availabilityMessage = $this->getLinkAvailabilityMessage($link);

        if ($availabilityMessage !== null) {
            return redirect()
                ->route('student.enter.key', ['slug' => $request->slug])
                ->with('availability_message', $availabilityMessage);
        }

        /*
         * Global restriction:
         * One mobile number can have only one attempt across all tests
         * and colleges. Never create a second attempt or reset a lock.
         */
        $existingAttempt = StudentTest::where(
            'student_mobile',
            $request->student_mobile
        )->orderByDesc('id')->first();

        if ($existingAttempt) {
            if ($existingAttempt->exam_locked) {
                return redirect()->route('student.already.submitted');
            }

            $existingTest = Test::find($existingAttempt->test_id);

            if (!$existingTest) {
                return redirect()->route('student.test.unavailable');
            }

            $existingLink = TestLink::where('test_id', $existingAttempt->test_id)
                ->where('college_id', $existingAttempt->college_id)
                ->first();

            // An existing attempt must obey its own college link settings.
            $existingAvailabilityMessage =
                $this->getLinkAvailabilityMessage($existingLink);

            if ($existingAvailabilityMessage !== null) {
                if (
                    $existingLink &&
                    (bool) $existingLink->is_enabled &&
                    $existingLink->valid_until &&
                    now()->greaterThanOrEqualTo(
                        \Illuminate\Support\Carbon::parse($existingLink->valid_until)
                    )
                ) {
                    return $this->forceSubmit($existingAttempt, $existingTest);
                }

                return redirect()
                    ->route('student.enter.key', [
                        'slug' => $existingLink?->slug ?? $existingTest->slug,
                    ])
                    ->with('availability_message', $existingAvailabilityMessage);
            }

            session([
                'current_test_id' => $existingAttempt->test_id,
                'current_student_test_id' => $existingAttempt->id,
            ]);

            return redirect()->route(
                'student.test.show',
                $existingLink ? $existingLink->slug : $existingTest->slug
            );
        }

        // The requested link has already passed availability checks.
        $test = Test::where('id', $link->test_id)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->first();

        if (!$test) {
            return redirect()->route('student.test.unavailable');
        }
 

        $studentTest = StudentTest::create([
            'test_id'         => $test->id,
            'college_id'      => $link->college_id,
            'student_name'    => $request->student_name,
            'student_email'   => $request->student_email,
            'gender'          => $request->gender,
            'student_mobile'  => $request->student_mobile,
            'course_type'     => $request->course_type,
            'class'           => $request->class,
            'branch'          => $request->student_branch,
            'semester'        => $request->semester,
            'score'           => 0,
            'session_key'     => session()->getId(),
            'exam_started_at' => null,
            'ip_address'      => $request->ip(),
        ]);

        session([
            'current_test_id' => $test->id,
            'current_student_test_id' => $studentTest->id,
        ]);

        // Important: use the college-specific link, not the global test slug.
        return redirect()->route('student.test.show', $link->slug);
    }

    /* ---------------- SHOW TEST ---------------- */

    public function showTest($slug)
    {
        $studentTestId = session('current_student_test_id');
        $currentTestId = session('current_test_id');

        if (!$studentTestId || !$currentTestId) {
            return redirect()->route('student.already.submitted');
        }

        $studentTest = StudentTest::find($studentTestId);

        if (
            !$studentTest ||
            (int) $studentTest->test_id !== (int) $currentTestId
        ) {
            return redirect()->route('student.already.submitted');
        }

        if ($studentTest->exam_locked) {
            return redirect()->route('student.already.submitted');
        }

        $test = Test::where('id', $studentTest->test_id)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->with('questions.options')
            ->first();

        if (!$test) {
            return redirect()->route('student.test.unavailable');
        }

        // Resolve the current link for this exact test and college.
        $link = TestLink::where('test_id', $studentTest->test_id)
            ->where('college_id', $studentTest->college_id)
            ->first();

        if (!$link) {
            return redirect()->route('student.test.unavailable');
        }

        // Reject stale URLs after the admin regenerates the link.
        if ($slug !== $link->slug) {
            return redirect()->route('student.test.unavailable');
        }

        $availabilityMessage = $this->getLinkAvailabilityMessage($link);

        if ($availabilityMessage !== null) {

            // A disabled link must never be treated as a normal expiry.
            if (!(bool) $link->is_enabled) {
                return redirect()
                    ->route('student.enter.key', ['slug' => $link->slug])
                    ->with('availability_message', $availabilityMessage);
            }

            // The admin has not started this link yet.
            if (!$link->valid_until) {
                return redirect()
                    ->route('student.enter.key', ['slug' => $link->slug])
                    ->with('availability_message', $availabilityMessage);
            }

            // Do not allow entry before valid_from.
            if (
                $link->valid_from &&
                now()->lessThan(
                    \Illuminate\Support\Carbon::parse($link->valid_from)
                )
            ) {
                return redirect()
                    ->route('student.enter.key', ['slug' => $link->slug])
                    ->with('availability_message', $availabilityMessage);
            }

            // Only an enabled link that has genuinely expired is auto-finalized.
            if (
                now()->greaterThanOrEqualTo(
                    \Illuminate\Support\Carbon::parse($link->valid_until)
                )
            ) {
                return $this->forceSubmit($studentTest, $test);
            }

            // Other unavailable conditions must not finalize the attempt.
            return redirect()
                ->route('student.enter.key', ['slug' => $link->slug])
                ->with('availability_message', $availabilityMessage);
        }

        // Preserve the attendance-submission linking behavior.
        $submissionId = session('external_attendance_submission_id');

        if ($submissionId && empty($studentTest->external_attendance_submission_id)) {
            $submission = ExternalAttendanceSubmission::where('id', $submissionId)
                ->where('student_mobile', $studentTest->student_mobile)
                ->where('session_key', session()->getId())
                ->first();

            if ($submission) {
                $studentTest->update([
                    'external_attendance_submission_id' => $submission->id,
                ]);
            }
        }
 
        if (!$studentTest->exam_started_at) {
            $studentTest->update([
                'exam_started_at' => now(),
            ]);
        }

        $deadline = \Illuminate\Support\Carbon::parse($link->valid_until);

        $deadlineIso = $deadline->toIso8601String();
        $deadlineDisplay = $deadline->format('d M Y, h:i A');

        if (now()->greaterThanOrEqualTo($deadline)) {
            return $this->forceSubmit($studentTest, $test);
        }

        $remainingSeconds = max(
            0,
            (int) now()->diffInSeconds($deadline, false)
        );

        $answers = $studentTest->answers
            ->pluck('option_id', 'question_id')
            ->toArray();

        return view('student.test', compact(
            'test',
            'studentTest',
            'link',
            'remainingSeconds',
            'deadlineIso',
            'deadlineDisplay',
            'answers'
        ));
    }

    /* ---------------- AUTO SAVE ---------------- */

    public function autoSave(Request $request, $slug)
    {
        $studentTest = StudentTest::find(
            session('current_student_test_id')
        );

        if (!$studentTest || $studentTest->exam_locked) {
            return response()->json([
                'status' => 'locked',
                'message' => 'This test attempt is locked.',
            ], 423);
        }

        $test = Test::find($studentTest->test_id);

        if (!$test) {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'Test is unavailable.',
            ], 404);
        }

        $link = TestLink::where('test_id', $studentTest->test_id)
            ->where('college_id', $studentTest->college_id)
            ->first();

        if (!$link || $slug !== $link->slug) {
            return response()->json([
                'status' => 'locked',
                'message' => 'This test link is no longer valid.',
            ], 423);
        }

        $availabilityMessage = $this->getLinkAvailabilityMessage($link);

        // Disabled means blocked immediately; never finalize because of disablement.
        if (!(bool) $link->is_enabled) {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'This test has been disabled by the management.',
            ], 423);
        }

        // A link without a deadline has not been started.
        if (!$link->valid_until) {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'Test has not been started by the management yet.',
            ], 423);
        }

        // Respect the configured start time.
        if (
            $link->valid_from &&
            now()->lessThan(
                \Illuminate\Support\Carbon::parse($link->valid_from)
            )
        ) {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'Test has not been started by the management yet.',
            ], 423);
        }

        // An actual link expiry finalizes only database-saved answers.
        if (
            now()->greaterThanOrEqualTo(
                \Illuminate\Support\Carbon::parse($link->valid_until)
            )
        ) {
            $this->finalizeAttempt($studentTest);

            return response()->json([
                'status' => 'expired',
                'message' => 'The test deadline has passed. Your saved answers have been submitted.',
            ], 423);
        }

        // Reject other unavailable conditions without saving answers or finalizing.
        if ($availabilityMessage !== null) {
            return response()->json([
                'status' => 'unavailable',
                'message' => $availabilityMessage,
            ], 423);
        }
     

        $answers = $request->input('answers', []);

        if (!is_array($answers)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid answers format.',
            ], 422);
        }

        foreach ($answers as $questionId => $optionId) {
            // Only accept questions belonging to this test.
            $questionBelongsToTest = $test->questions()
                ->where('questions.id', $questionId)
                ->exists();

            if (!$questionBelongsToTest) {
                continue;
            }

            // Allow a blank answer to clear a previously selected option.
            if ($optionId === null || $optionId === '') {
                StudentAnswer::where('student_test_id', $studentTest->id)
                    ->where('question_id', $questionId)
                    ->delete();

                continue;
            }

            // Only save options belonging to the relevant question.
            $validOption = \App\Models\Option::where('id', $optionId)
                ->where('question_id', $questionId)
                ->exists();

            if (!$validOption) {
                continue;
            }

            StudentAnswer::updateOrCreate(
                [
                    'student_test_id' => $studentTest->id,
                    'question_id' => $questionId,
                ],
                [
                    'option_id' => $optionId,
                ]
            );
        }

        return response()->json(['status' => 'saved']);
    }

    /* ---------------- SUBMIT TEST ---------------- */
public function submitTest(Request $request, $slug)
{
    $studentTestId = session('current_student_test_id');

    if (!$studentTestId) {
        return redirect()->route('student.already.submitted');
    }

    $studentTest = StudentTest::find($studentTestId);

    if (!$studentTest) {
        return redirect()->route('student.already.submitted');
    }

    $test = Test::find($studentTest->test_id);

    if (!$test) {
        return redirect()->route('student.test.unavailable');
    }

    if ($studentTest->exam_locked) {
        return redirect()->route('student.result.show');
    }

    // Resolve the current link for this test and college.
    $link = TestLink::where('test_id', $studentTest->test_id)
        ->where('college_id', $studentTest->college_id)
        ->first();

    // Reject missing links and old URLs after slug regeneration.
    if (!$link || $slug !== $link->slug) {
        return redirect()->route('student.test.unavailable');
    }

    $availabilityMessage = $this->getLinkAvailabilityMessage($link);

    // 1. A disabled link must never finalize the attempt.
    if (!(bool) $link->is_enabled) {
        return redirect()
            ->route('student.enter.key', ['slug' => $link->slug])
            ->with(
                'availability_message',
                'This test has been disabled by the management.'
            );
    }

    // 2. NULL deadline means the admin has not started this link.
    if (!$link->valid_until) {
        return redirect()
            ->route('student.enter.key', ['slug' => $link->slug])
            ->with(
                'availability_message',
                'Test has not been started by the management yet.'
            );
    }

    // 3. Do not submit or finalize before the configured start time.
    if (
        $link->valid_from &&
        now()->lessThan(
            \Illuminate\Support\Carbon::parse($link->valid_from)
        )
    ) {
        return redirect()
            ->route('student.enter.key', ['slug' => $link->slug])
            ->with(
                'availability_message',
                'Test has not been started by the management yet.'
            );
    }

    // 4. Genuine link expiry: finalize using answers already saved in DB.
    // Do not save answers from this late submission request.
    if (
        now()->greaterThanOrEqualTo(
            \Illuminate\Support\Carbon::parse($link->valid_until)
        )
    ) {
        $this->finalizeAttempt($studentTest);

        return redirect()
            ->route('student.result.show')
            ->with(
                'status',
                'The test deadline has passed. Your saved answers have been submitted.'
            );
    }

    // 5. Other unavailable conditions must not finalize the attempt.
    if ($availabilityMessage !== null) {
        return redirect()
            ->route('student.enter.key', ['slug' => $link->slug])
            ->with('availability_message', $availabilityMessage);
    }

    // 8. Only a currently enabled, available, in-time attempt
    // may save answers supplied by this submission request.
    $answers = $request->input('answers', []);

    if (!is_array($answers)) {
        return redirect()
            ->back()
            ->with('error', 'Invalid answers format.');
    }

    foreach ($answers as $questionId => $optionId) {
        // Accept only questions belonging to this test.
        $questionBelongsToTest = $test->questions()
            ->where('questions.id', $questionId)
            ->exists();

        if (!$questionBelongsToTest) {
            continue;
        }

        // Allow blank answers to clear a previous selection.
        if ($optionId === null || $optionId === '') {
            StudentAnswer::where('student_test_id', $studentTest->id)
                ->where('question_id', $questionId)
                ->delete();

            continue;
        }

        // Accept only options belonging to the relevant question.
        $validOption = \App\Models\Option::where('id', $optionId)
            ->where('question_id', $questionId)
            ->exists();

        if (!$validOption) {
            continue;
        }

        StudentAnswer::updateOrCreate(
            [
                'student_test_id' => $studentTest->id,
                'question_id' => $questionId,
            ],
            [
                'option_id' => $optionId,
            ]
        );
    }

    // Normal submission: finalize after saving the submitted answers.
    $this->finalizeAttempt($studentTest);

    return redirect()->route('student.result.show');
}
 

private function finalizeAttempt(StudentTest $studentTest): void
{
    // Refresh the attempt so we use the latest saved database state.
    $studentTest->refresh();

    if ($studentTest->exam_locked) {
        return;
    }

    $correctCount = 0;

    $savedAnswers = $studentTest->answers()
        ->with('option')
        ->get();

    foreach ($savedAnswers as $answer) {
        if ($answer->option && $answer->option->is_correct) {
            $correctCount++;
        }
    }

    $studentTest->update([
        'score' => $correctCount,
        'exam_locked' => true,
        'exam_submitted_at' => now(),
    ]);
}

 
    /* ---------------- FORCE SUBMIT ---------------- */
    private function forceSubmit(StudentTest $studentTest, Test $test)
    {
        $studentTest->refresh();

        if (!$studentTest->exam_locked) {
            $this->finalizeAttempt($studentTest);
        }

        return redirect()->route('student.result.show');
    }

    public function showResult()
    {
        $studentTestId = session('current_student_test_id');

        if (!$studentTestId) {
            // return redirect()->route('student.enter.key');
            return view('student.result');
        }

        $studentTest = StudentTest::find($studentTestId);

        if (!$studentTest) {
            session()->forget([
                'current_test_id',
                'current_student_test_id',
            ]);
            return view('student.result');
            // return redirect()->route('student.enter.key');
        }

        if (!$studentTest->exam_locked) {
            return redirect()->route('student.test.unavailable');
        }

        session()->forget([
            'current_test_id',
            'current_student_test_id',
        ]);

        return view('student.result');
    }
    
}
