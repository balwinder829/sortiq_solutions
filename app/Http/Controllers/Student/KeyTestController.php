<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\StudentTest;
use App\Models\StudentAnswer;

class KeyTestController extends Controller
{
    /* ---------------- ENTRY FORM ---------------- */

    public function studentView($slug)
    {
        $test = Test::where('slug', $slug)->firstOrFail();

        // Display the test to the student
       return redirect()->route('student.enter.key', ['slug' => $test->slug]);

    }

    public function showForm(Request $request)
    {
        // dd($request);
        // if (!request('test_uid')) {
        //     abort(404);
        // }
        return view('student.enter_key');
    }

    /* ---------------- ACCESS TEST ---------------- */

    public function accessTest(Request $request)
    {   
        // dd($request->post());
        if ($request->isMethod('get')) {
            // return redirect()->route('student.enter.key');
        }

        // dd('here');
        $request->validate([
            'student_name'   => 'required|string',
            'student_email'  => 'required|email',
            'gender'   => 'required|string',
            'student_mobile' => 'required|digits:10',
            'slug'       => 'required|exists:tests,slug',
        ]);

        // ✅ Fetch test by SLUG + ACTIVE + PUBLISHED

        $test = Test::where('slug', $request->slug)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->first();

        if (!$test) {
            return redirect()->route('student.test.unavailable');
        }

        
        // ❌ Exam timing missing
        if (!$test->exam_start_at || !$test->exam_end_at) {
            return redirect()->route('student.exam.closed', $test->slug);
        }

        // ❌ Exam already ended
        if (now()->greaterThanOrEqualTo($test->exam_end_at)) {
            return redirect()->route('student.exam.closed', $test->slug);
        }

        // 🔍 Check existing attempt
        $studentTest = StudentTest::where('test_id', $test->id)
            ->where('student_email', $request->student_email)
            ->first();

        // ✅ Already submitted
        if ($studentTest && $studentTest->exam_locked) {
            return redirect()->route('student.already.submitted', $test->slug);
        }

        // 🔁 Resume attempt
        if ($studentTest && !$studentTest->exam_locked) {
            session([
                'current_test_id'         => $test->id,
                'current_student_test_id' => $studentTest->id,
            ]);

            return redirect()->route('student.test.show', $test->slug);
        }
        // dd($request->post());
        // 🆕 Fresh attempt
        $studentTest = StudentTest::create([
            'test_id'         => $test->id,
            'student_name'    => $request->student_name,
            'student_email'   => $request->student_email,
            'gender'           => $request->gender,
            'student_mobile'  => $request->student_mobile,
            'score'           => 0,
            'session_key'     => session()->getId(),
            'exam_started_at' => null,
            'ip_address'      => $request->ip(),
        ]);

        session([
            'current_test_id'         => $test->id,
            'current_student_test_id' => $studentTest->id,
        ]);

        return redirect()->route('student.test.show', $test->slug);
    }

    /* ---------------- SHOW TEST ---------------- */

    public function showTest($slug)
    {
        $test = Test::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->with('questions.options')
            ->firstOrFail();

        if (session('current_test_id') != $test->id) {
            abort(403, 'Unauthorized');
        }

        $studentTest = StudentTest::findOrFail(session('current_student_test_id'));

        // ✅ Already submitted
        if ($studentTest->exam_locked) {
            return redirect()->route('student.already.submitted', $test->slug);
        }

        // ⏳ Waiting room
        if (now()->lessThan($test->exam_start_at)) {
            return view('student.waiting', compact('test'));
        }

        // 🟢 Mark exam start once
        if (!$studentTest->exam_started_at) {
            $studentTest->update(['exam_started_at' => now()]);
        }

        // ⛔ Exam ended → auto submit
        if (now()->greaterThanOrEqualTo($test->exam_end_at)) {
            return $this->forceSubmit($studentTest, $test);
        }

        $remainingSeconds = max(
            0,
            (int) now()->diffInSeconds($test->exam_end_at)
        );

        $answers = $studentTest->answers
            ->pluck('option_id', 'question_id')
            ->toArray();

        return view('student.test', compact(
            'test',
            'studentTest',
            'remainingSeconds',
            'answers'
        ));
    }

    /* ---------------- AUTO SAVE ---------------- */

    public function autoSave(Request $request, $slug)
    {
        $studentTest = StudentTest::find(session('current_student_test_id'));

        if (!$studentTest || $studentTest->exam_locked) {
            return response()->json(['status' => 'locked']);
        }

        if ($request->answers) {
            foreach ($request->answers as $questionId => $optionId) {
                StudentAnswer::updateOrCreate(
                    [
                        'student_test_id' => $studentTest->id,
                        'question_id'     => $questionId
                    ],
                    [
                        'option_id' => $optionId
                    ]
                );
            }
        }

        return response()->json(['status' => 'saved']);
    }

    /* ---------------- SUBMIT TEST ---------------- */

    public function submitTest(Request $request, $slug)
    {
        $studentTestId = session('current_student_test_id');

        if (!$studentTestId) {
            abort(403, 'Session expired');
        }

        $studentTest = StudentTest::findOrFail($studentTestId);
        $test = Test::findOrFail($studentTest->test_id);

        if ($studentTest->exam_locked) {
            return redirect()->route('student.result.show', $studentTest->id);
        }

        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $optionId) {
                StudentAnswer::updateOrCreate(
                    [
                        'student_test_id' => $studentTest->id,
                        'question_id'     => $questionId,
                    ],
                    [
                        'option_id' => $optionId,
                    ]
                );
            }
        }

        $correctCount = 0;
        foreach ($studentTest->answers as $answer) {
            if ($answer->option && $answer->option->is_correct) {
                $correctCount++;
            }
        }

        $studentTest->update([
            'score'             => $correctCount,
            'exam_locked'       => true,
            'exam_submitted_at' => now(),
        ]);

        // session()->forget(['current_test_id', 'current_student_test_id']);

        return redirect()->route('student.result.show');
    }

    /* ---------------- FORCE SUBMIT ---------------- */

    private function forceSubmit(StudentTest $studentTest, Test $test)
    {
        if ($studentTest->exam_locked) {
            return redirect()->route('student.result.show', $studentTest->id);
        }

        $correctCount = 0;
        foreach ($studentTest->answers as $answer) {
            if ($answer->option && $answer->option->is_correct) {
                $correctCount++;
            }
        }

        $studentTest->update([
            'score'             => $correctCount,
            'exam_locked'       => true,
            'exam_submitted_at' => now(),
        ]);

        // session()->forget(['current_test_id', 'current_student_test_id']);

        return redirect()->route('student.result.show');
    }

    public function showResult()
    {
        $studentTestId = session('current_student_test_id');
        session()->forget(['current_test_id', 'current_student_test_id']);
        // dd($studentTestId);
        if (!$studentTestId) {
            abort(403, 'Unauthorized');
        }

        $studentTest = StudentTest::findOrFail($studentTestId);

        // Extra safety: session binding
        if ($studentTest->session_key !== session()->getId()) {
            abort(403);
        }

        return view('student.result', compact('studentTest'));
    }

}
