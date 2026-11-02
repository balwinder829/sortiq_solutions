<?php

namespace App\Http\Controllers\ExamFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceLink;
use App\Models\ExternalAttendanceSubmission;
use App\Models\StudentCourse;
use App\Rules\NotBlockedNumber;
use App\Models\ExternalAttendanceTestOption;
use App\Models\Test;
use App\Models\TestLink;
use App\Models\StudentTest;
use Illuminate\Support\Facades\DB;

class AttendanceFormController extends Controller
{
    /* ================= ENTRY ================= */

    public function view($slug)
    {
        $link = ExternalAttendanceLink::where('slug', $slug)->firstOrFail();

        return redirect()->route('form.fill', ['slug' => $slug]);
    }

    /* ================= SHOW FORM ================= */

    public function showForm($slug)
    {
        $link = ExternalAttendanceLink::where('slug', $slug)->firstOrFail();
        $courses = StudentCourse::whereNotIn('course_name', [
            'Not decided', 'n/a', 'na'
        ])->get();

        $test = ExternalAttendanceTest::where('id', $link->external_attendance_test_id)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->firstOrFail();

        return view('student.attendance.external_attendance_form', [
            'slug' => $slug,
            'test' => $test,
            'courses' => $courses,
            'collegeId' => $link->college_id
        ]);
    }

    /* ================= SUBMIT ================= */

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'student_name'   => 'required|string|max:255',
            'student_mobile' => ['required', 'digits:10', new NotBlockedNumber],
            'course_type'    => 'nullable|in:Degree,Diploma',
            'student_branch' => 'nullable|string|max:150',
            'semester'       => 'nullable|integer|min:1|max:8',
            'slug'           => 'required|exists:external_attendance_links,slug',
            'student_email'  => 'required|email|max:255',
            'gender'         => 'required|in:male,female',
        ], [
            'slug.exists' => 'Invalid or expired link.',
        ]);

        /*
        |--------------------------------------------------------------------------
        | GLOBAL EXAM CHECK
        |--------------------------------------------------------------------------
        | One mobile number can have only one exam attempt across all colleges/tests.
        */
        
        $existingAttempt = StudentTest::where(
            'student_mobile',
            $validated['student_mobile']
        )
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->exam_locked) {
                return redirect()->route('student.already.submitted');
            }

            return $this->resumeExistingAttempt($existingAttempt);
        }


        $link = ExternalAttendanceLink::where('slug', $validated['slug'])
            ->firstOrFail();

        $test = ExternalAttendanceTest::where(
            'id',
            $link->external_attendance_test_id
        )
            ->where('status', 'published')
            ->where('is_active', 1)
            ->firstOrFail();

        $collegeId = $link->college_id;

        // Prevent duplicate attendance submission for this form/college/mobile today.
        $existingSubmission = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
            ->where('college_id', $collegeId)
            ->where('student_mobile', $validated['student_mobile'])
            ->whereDate('created_at', now()->toDateString())
            ->first();

        if ($existingSubmission) {
            session([
                'external_attendance_submission_id' => $existingSubmission->id,
            ]);

            return redirect()->route('form.choose');
        }


        $submission = ExternalAttendanceSubmission::create([
            'external_attendance_test_id' => $test->id,
            'college_id'                  => $collegeId,
            'student_name'                => $validated['student_name'],
            'student_mobile'              => $validated['student_mobile'],
            'student_email'               => $validated['student_email'],
            'gender'                      => $validated['gender'],
            'course_type'                 => $validated['course_type'] ?? null,
            'student_branch'              => $validated['student_branch'] ?? null,
            'semester'                    => $validated['semester'] ?? null,
            'session_key'                 => session()->getId(),
            'ip_address'                  => $request->ip(),
            'exam_submitted_at'           => now(),
            'is_finalized'                => 1,
        ]);

        session([
            'external_attendance_submission_id' => $submission->id,
        ]);

        return redirect()->route('form.choose');
    }



    /* ================= TEST SELECTION ================= */
    public function chooseTest()
    {
        $submissionId = session('external_attendance_submission_id');

        if (!$submissionId) {
            return redirect()->route('form.already');
        }

        $submission = ExternalAttendanceSubmission::where('id', $submissionId)
            ->where('session_key', session()->getId())
            ->first();

        if (!$submission) {
            session()->forget('external_attendance_submission_id');

            return redirect()->route('form.already');
        }

        // A mobile number can have only one attempt globally.
        
        $existingAttempt = StudentTest::where(
            'student_mobile',
            $submission->student_mobile
        )
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->exam_locked) {
                return redirect()->route('student.already.submitted');
            }

            return $this->resumeExistingAttempt($existingAttempt);
        }


        $options = DB::table('external_attendance_test_options as o')
            ->join('attendance_test_categories as c', 'c.id', '=', 'o.category_id')
            ->join('tests as t', 't.id', '=', 'o.test_id')
            ->where('o.external_attendance_test_id', $submission->external_attendance_test_id)
            ->where('o.is_active', 1)
            ->where('c.is_active', 1)
            ->where('t.is_active', 1)
            ->where('t.status', 'published')
            ->select(
                'o.id',
                'o.test_type',
                'o.display_name',
                'o.is_default',
                'o.sort_order',
                'c.name as category_name',
                't.title as test_title'
            )
            ->orderBy('o.sort_order')
            ->orderBy('c.sort_order')
            ->orderBy('c.name')
            ->get();

        if ($options->isEmpty()) {
            return redirect()->route('form.thankyou');
        }

        return view('student.attendance.choose_test', compact(
            'submission',
            'options'
        ));
    }
    

    /* ================= START SELECTED TEST ================= */

    public function startSelectedTest(Request $request)
    {
        $request->validate([
            'option_id' => 'required|integer|exists:external_attendance_test_options,id',
        ]);

        $submissionId = session('external_attendance_submission_id');

        if (!$submissionId) {
            return redirect()->route('form.already');
        }

        $submission = ExternalAttendanceSubmission::where('id', $submissionId)
            ->where('session_key', session()->getId())
            ->first();

        if (!$submission) {
            session()->forget('external_attendance_submission_id');

            return redirect()->route('form.already');
        }

        
        $existingAttempt = StudentTest::where(
            'student_mobile',
            $submission->student_mobile
        )
            ->whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->exam_locked) {
                return redirect()->route('student.already.submitted');
            }

            return $this->resumeExistingAttempt($existingAttempt);
        }


        // Only allow an active option assigned to this attendance test.
        $option = DB::table('external_attendance_test_options as o')
            ->join('attendance_test_categories as c', 'c.id', '=', 'o.category_id')
            ->join('tests as t', 't.id', '=', 'o.test_id')
            ->where('o.id', $request->option_id)
            ->where('o.external_attendance_test_id', $submission->external_attendance_test_id)
            ->where('o.is_active', 1)
            ->where('c.is_active', 1)
            ->where('t.is_active', 1)
            ->where('t.status', 'published')
            ->select('o.*')
            ->first();

        if (!$option) {
            return back()->withErrors([
                'option_id' => 'This test option is no longer available.',
            ]);
        }
 
        $test = Test::where('id', $option->test_id)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->first();

        if (!$test) {
            return redirect()->route('student.test.unavailable');
        }

        // Find the college-specific KeyTest link.
        $testLink = TestLink::where('test_id', $test->id)
            ->where('college_id', $submission->college_id)
            ->first();

        if (!$testLink) {
            return back()->withErrors([
                'option_id' => 'The selected test is not linked to your college. Please contact the administrator.',
            ]);
        }

        $studentTest = StudentTest::create([
            'test_id'         => $test->id,
            'college_id'      => $submission->college_id,
            'student_name'    => $submission->student_name,
            'student_email'   => $submission->student_email,
            'gender'          => $submission->gender,
            'student_mobile'  => $submission->student_mobile,
            'course_type'     => $submission->course_type,
            'class'           => $submission->class,
            'branch'          => $submission->student_branch,
            'semester'        => $submission->semester,
            'score'           => 0,
            'session_key'     => session()->getId(),
            'exam_started_at' => null,
            'ip_address'      => request()->ip(),
        ]);

        // Reuse the existing KeyTest session keys.
        session([
            'current_test_id'         => $test->id,
            'current_student_test_id' => $studentTest->id,
        ]);

        return redirect()->route('student.test.show', $testLink->slug);
    }

    private function resumeExistingAttempt(StudentTest $studentTest)
    {
        if ($studentTest->exam_locked) {
            return redirect()->route('student.already.submitted');
        }

        $test = Test::find($studentTest->test_id);

        if (!$test) {
            return redirect()->route('form.already');
        }

        // Find the college-specific link for this existing exam.
        $testLink = TestLink::where('test_id', $studentTest->test_id)
            ->where('college_id', $studentTest->college_id)
            ->first();

        $slug = $testLink?->slug ?? $test->slug;

        session([
            'current_test_id'         => $studentTest->test_id,
            'current_student_test_id' => $studentTest->id,
        ]);

        return redirect()->route('student.test.show', $slug);
    }

    public function thankYou()
    {
        $submissionId = session('external_attendance_submission_id');

        if (!$submissionId) {
            return redirect()->route('form.already');
        }

        $submission = ExternalAttendanceSubmission::where('id', $submissionId)
            ->where('session_key', session()->getId())
            ->first();

        if (!$submission) {
            session()->forget('external_attendance_submission_id');

            return redirect()->route('form.already');
        }

        return view('student.attendance.form_thank_you', compact('submission'));
    }
}