<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPendingRegistration;
use App\Models\StudentSession;
use App\Models\Student;

class StudentPendingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student_request.view')->only(['index','sendToSession']);
        
        
    }

    public function index()
    {
        $students = StudentPendingRegistration::with([
            'collegeData',
            'courseData'
        ])->latest()->get();

        $sessionsList = StudentSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

        return view('pending_students.index', compact('students', 'sessionsList'));
    }

public function sendToSession(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'session_id' => 'required|exists:student_sessions,id', // ✅ single
        ]);

        $inserted = 0;
        $skipped = 0;

        foreach ($request->ids as $id) {

            $joining = StudentPendingRegistration::find($id);

            $lastSno = Student::orderBy('id', 'desc')->value('sno');
            $newSno = is_numeric($lastSno) ? ((int)$lastSno + 1) : 1;

            if (!$joining) {
                $skipped++;
                continue;
            }

            // 🔒 Prevent duplicate
            $exists = Student::where('source_type', 'pending_registration')
                ->where('source_id', $id)
                // ->where('session', $request->session_id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // ✅ Insert into students_detail
            Student::create([
                'sno'            => $newSno, // ✅ added
                'student_name'   => $joining->student_name,
                'f_name'         => $joining->father_name,
                'college_name'   => $joining->college_id,
                // 'duration'       => $joining->duration,
                // 'technology'     => $joining->course_id,
                'join_date'      => $joining->start_date,
                'start_date'     => $joining->start_date,
                'session'        => $request->session_id,
                'contact'        => $joining->contact,
                'email_id' => $joining->email ?? "",

                // 🔥 tracking
                'status'    => 'joined',
                'source_type'    => 'pending_registration',
                'source_id'      => $joining->id,
            ]);



            // ✅ Update flag
            $joining->update([
                'is_sent_to_detail' => 1,
                'sent_to_detail_atsent_to_detail_at' => now(),
            ]);

            $inserted++;
        }

        return response()->json([
            'status' => true,
            'message' => "$inserted students sent successfully" . ($skipped ? " ($skipped skipped)" : "")
        ]);
    }
    public function sendToSession1(Request $request)
{
    $request->validate([
        'ids' => 'required|array',
        'session_id' => 'required|exists:student_sessions,id', // ✅ single
    ]);

    $inserted = 0;
    $skipped = 0;

    foreach ($request->ids as $id) {

        $pending = StudentPendingRegistration::find($id);
        if (!$pending) {
            $skipped++;
            continue;
        }

        // prevent duplicate
        $exists = Student::where('source_type', 'pending_registration')
            ->where('source_id', $id)
            ->where('session', $request->session_id)
            ->exists();

        if ($exists) {
            $skipped++;
            continue;
        }

        Student::create([
            'student_name' => $pending->student_name,
            'f_name'       => $pending->father_name,

            // ⚠️ better to use relation if possible
            'college_name' => $pending->college_id ?? '',
            'technology'   => $pending->course_id ?? '',

            'start_date'   => $pending->start_date,
            'session'      => $request->session_id,

            'source_type'  => 'pending_registration',
            'source_id'    => $pending->id,
        ]);

        $inserted++;
    }

    return response()->json([
        'status' => true,
        'message' => "$inserted students sent successfully" . ($skipped ? " ($skipped skipped)" : "")
    ]);
}
    public function sendToSessionq(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'session_ids' => 'required', // ✅ MULTI
        ]);

        $inserted = 0;

        foreach ($request->ids as $id) {

            $pending = StudentPendingRegistration::find($id);
            if (!$pending) continue;

            foreach ($request->session_ids as $session_id) {

                // prevent duplicate
                $exists = Student::where('source_type', 'pending_registration')
                    ->where('source_id', $id)
                    ->where('session', $session_id)
                    ->exists();

                if ($exists) continue;

                Student::create([
                    'student_name' => $pending->student_name,
                    'f_name'       => $pending->father_name,
                    'college_name' => $pending->college_id ?? '',
                    'technology'   => $pending->course_id ?? '',
                    'start_date'   => $pending->start_date,
                    'session'      => $session_id,

                    'source_type'  => 'pending_registration',
                    'source_id'    => $pending->id,
                ]);

                $inserted++;
            }
        }

        return response()->json([
            'status' => true,
            'message' => "$inserted records added to session(s)"
        ]);
    }
}