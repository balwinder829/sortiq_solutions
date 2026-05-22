<?php

namespace App\Http\Controllers\ExamFront;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalAttendanceTest;
use App\Models\ExternalAttendanceLink;
use App\Models\ExternalAttendanceSubmission;
use App\Models\StudentCourse;

class AttendanceFormController extends Controller
{
    /* ================= ENTRY ================= */

    public function view($slug)
    {
        $link = ExternalAttendanceLink::where('slug', $slug)->firstOrFail();

        return redirect()->route('form.fill', ['slug' => $slug]);
    }

    // public function view($slug)
    // {
    //     $link = ExternalAttendanceLink::where('slug', $slug)->firstOrFail();

    //     $test = $link->test;

    //     $courses = StudentCourse::whereNotIn('course_name', [
    //         'Not decided', 'n/a', 'na'
    //     ])->get();

    //     return view('student.attendance.external_attendance_form', compact('slug', 'test', 'courses'));
    // }

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
        $request->validate([
            'student_name'   => 'required|string|max:255',
            // 'student_email'  => 'required|email|max:255',
            'student_mobile' => 'required|digits:10',
            'gender'         => 'required|in:male,female',
            'slug'           => 'required|exists:external_attendance_links,slug',
        ], [
            'slug.exists' => 'Invalid or expired link.',
        ]);

        $link = ExternalAttendanceLink::where('slug', $request->slug)->firstOrFail();

        $test = ExternalAttendanceTest::where('id', $link->external_attendance_test_id)
            ->where('status', 'published')
            ->where('is_active', 1)
            ->first();

        if (!$test) {
            abort(404, 'Form is not available');
        }

        $collegeId = $link->college_id;

        /* ================= DUPLICATE CHECK ================= */

        // $exists = ExternalAttendanceSubmission::where(
        //         'external_attendance_test_id',
        //         $test->id
        //     )
        //     ->where('college_id', $collegeId)
        //     ->where(function ($q) use ($request) {
        //         $q->where('student_email', $request->student_email)
        //           ->orWhere('student_mobile', $request->student_mobile);
        //     })
        //     ->exists();


        $exists = ExternalAttendanceSubmission::where(
            'external_attendance_test_id',
            $test->id
        )
        ->where('college_id', $collegeId)
        ->where('student_mobile', $request->student_mobile)
        ->where('course_id', $request->course_id)
        ->exists();

        if ($exists) {
            return redirect()->route('form.already');
        }

        /* ================= SAVE ================= */

        ExternalAttendanceSubmission::create([
            'external_attendance_test_id' => $test->id,
            'college_id'      => $collegeId,
            'student_name'    => $request->student_name,
            'student_email'   => $request->student_email,
            'student_mobile'  => $request->student_mobile,
            'gender'          => $request->gender,
            'class'           => $request->class,
            'course_id'       => $request->course_id,
            'semester'        => $request->semester,
            'session_key'     => session()->getId(),
            'ip_address'      => $request->ip(),
            'exam_submitted_at' => now(),
            'is_finalized'    => 1,
        ]);

        return redirect()->route('form.thankyou');
    }
}