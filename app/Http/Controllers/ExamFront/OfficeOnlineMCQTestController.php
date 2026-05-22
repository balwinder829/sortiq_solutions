<?php

namespace App\Http\Controllers\ExamFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OfficeOnlineTest;
use App\Models\StudentOfficeOnlineTest;
use App\Models\StudentOfficeOnlineAnswer;
use App\Models\OfficeOnlineOption;

class OfficeOnlineMCQTestController extends Controller
{

    /* ---------------- ENTRY ---------------- */

    public function enter($slug)
    {
        $test = OfficeOnlineTest::where('slug', $slug)->firstOrFail();

        return view('student.office-online.enter_key', compact('test'));
    }

    /* ---------------- ACCESS TEST ---------------- */

    public function accessTest(Request $request)
    {
        $request->validate([
            'student_sno'   => 'required|string',
            'student_name'   => 'required|string',
            'student_email'  => 'required|email',
            'student_mobile' => 'required|digits:10',
            'slug'           => 'required|exists:office_online_tests,slug',
        ]);

        $test = OfficeOnlineTest::where('slug', $request->slug)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->first();
        // dd($test);
        if (!$test) {
            return redirect()->route('student.office-online.unavailable', $test->slug);
        }

        // Timing check
        if (!$test->exam_start_at || !$test->exam_end_at) {
            return redirect()->route('student.office-online.exam.closed', $test->slug);
        }

        if (now()->greaterThanOrEqualTo($test->exam_end_at)) {
            return redirect()->route('student.office-online.exam.closed', $test->slug);
        }

        // Existing attempt
        $studentTest = StudentOfficeOnlineTest::where('office_online_test_id', $test->id)
            ->where('student_mobile', $request->student_mobile)
            ->where('student_sno', $request->student_sno)
            ->first();

        // dd($studentTest);
        if ($studentTest && $studentTest->exam_locked) {
            return redirect()->route('student.office-online.already.submitted', $test->slug);
        }

        if ($studentTest && !$studentTest->exam_locked) {
            session([
                'office_test_id' => $test->id,
                'office_student_test_id' => $studentTest->id,
            ]);

            return redirect()->route('student.office-online.exam', $test->slug);
        }

        // New attempt
        $studentTest = StudentOfficeOnlineTest::create([
            'office_online_test_id' => $test->id,
            'student_name'    => $request->student_name,
            'student_sno'    => $request->student_sno,
            'student_email'   => $request->student_email,
            'student_mobile'  => $request->student_mobile,
            'score'           => 0,
            'session_key'     => session()->getId(),
            'exam_started_at' => null,
            'ip_address'      => $request->ip(),
        ]);

        session([
            'office_test_id' => $test->id,
            'office_student_test_id' => $studentTest->id,
        ]);

        return redirect()->route('student.office-online.exam', $test->slug);
    }

    /* ---------------- SHOW TEST ---------------- */

    public function showTest($slug)
    {
        $test = OfficeOnlineTest::where('slug', $slug)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->with('questions.options')
            ->firstOrFail();

        if (session('office_test_id') != $test->id) {
            abort(403);
        }

         if (!$test->questions()->exists()) {
            // return redirect()->route('student.office-online.unavailable');
            return redirect()->route('student.office-online.unavailable', $test->slug);
        }

        $studentTest = StudentOfficeOnlineTest::findOrFail(session('office_student_test_id'));

        if ($studentTest->exam_locked) {
            return redirect()->route('student.already.submitted', $test->slug);
        }

        if (now()->lessThan($test->exam_start_at)) {
            return view('office-online.waiting', compact('test'));
        }

        if (!$studentTest->exam_started_at) {
            $studentTest->update(['exam_started_at' => now()]);
        }

        if (now()->greaterThanOrEqualTo($test->exam_end_at)) {
            return $this->forceSubmit($studentTest, $test);
        }

        $remainingSeconds = max(
            0,
            (int) now()->diffInSeconds($test->exam_end_at)
        );

        $answers = $studentTest->answers
            ->pluck('selected_option_id', 'office_online_question_id')
            ->toArray();

        return view('student.office-online.test', compact(
            'test',
            'studentTest',
            'remainingSeconds',
            'answers'
        ));
    }

    /* ---------------- AUTO SAVE ---------------- */

    public function autoSave(Request $request)
    {
        $studentTest = StudentOfficeOnlineTest::find(session('office_student_test_id'));

        if (!$studentTest || $studentTest->exam_locked) {
            return response()->json(['status' => 'locked']);
        }

        if ($request->answers) {
            foreach ($request->answers as $questionId => $optionId) {
                StudentOfficeOnlineAnswer::updateOrCreate(
                    [
                        'student_office_online_test_id' => $studentTest->id,
                        'office_online_question_id'     => $questionId
                    ],
                    [
                        'selected_option_id' => $optionId
                    ]
                );
            }
        }

        return response()->json(['status' => 'saved']);
    }

    /* ---------------- SUBMIT ---------------- */

    public function submitTest(Request $request)
    {
        $studentTestId = session('office_student_test_id');

        if (!$studentTestId) {
            abort(403);
        }

        $studentTest = StudentOfficeOnlineTest::findOrFail($studentTestId);

        if ($studentTest->exam_locked) {
            return redirect()->route('student.office-online.result');
        }

        if ($request->answers) {
            foreach ($request->answers as $questionId => $optionId) {
                StudentOfficeOnlineAnswer::updateOrCreate(
                    [
                        'student_office_online_test_id' => $studentTest->id,
                        'office_online_question_id'     => $questionId,
                    ],
                    [
                        'selected_option_id' => $optionId,
                    ]
                );
            }
        }

        $score = 0;

        foreach ($studentTest->answers as $answer) {
            if ($answer->option && $answer->option->is_correct) {
                $score++;
            }
        }

        $studentTest->update([
            'score' => $score,
            'exam_locked' => true,
            'exam_submitted_at' => now(),
        ]);
        // dd('here');
        return redirect()->route('student.office-online.result');
    }

    /* ---------------- FORCE SUBMIT ---------------- */

    private function forceSubmit($studentTest, $test)
    {
        if ($studentTest->exam_locked) {
            return redirect()->route('student.office-online.result');
        }

        $score = 0;

        foreach ($studentTest->answers as $answer) {
            if ($answer->option && $answer->option->is_correct) {
                $score++;
            }
        }

        $studentTest->update([
            'score' => $score,
            'exam_locked' => true,
            'exam_submitted_at' => now(),
        ]);
        return redirect()->route('student.office-online.result');
    }

    /* ---------------- RESULT ---------------- */

    public function showResult()
    {
        // dd('here');
        $studentTestId = session('office_student_test_id');

        if (!$studentTestId) {
            abort('403');
            return redirect()->route('student.office-online.enter');
        }

        $studentTest = StudentOfficeOnlineTest::findOrFail($studentTestId);

        session()->forget(['office_test_id', 'office_student_test_id']);

        return view('student.office-online.result', compact('studentTest'));
    }
}