<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentPendingRegistration;
use App\Models\StudentSession;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;

class StudentPendingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:student_request.view')->only(['index','sendToSession']);
        
        
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');

        $allowedStatuses = [
            'all',
            'awaiting',
            'verified',
            'rejected',
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'all';
        }

        $query = StudentPendingRegistration::with([
            'collegeData',
            'courseData',
            'paymentUpiAccount',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Payment Status Filter
        |--------------------------------------------------------------------------
        */
        if ($status === 'awaiting') {
            $query->where(function ($q) {
                $q->whereIn('payment_status', [
                    'pending',
                    'submitted',
                ])->orWhereNull('payment_status');
            });
        } elseif (in_array($status, ['verified', 'rejected'], true)) {
            $query->where('payment_status', $status);
        }

        $students = $query
            ->latest('updated_at')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Active Sessions
        |--------------------------------------------------------------------------
        */
        $sessionsList = StudentSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

        /*
        |--------------------------------------------------------------------------
        | Payment Status Counts
        |--------------------------------------------------------------------------
        */
        $paymentCounts = [
            'all' => StudentPendingRegistration::count(),

            'awaiting' => StudentPendingRegistration::where(function ($q) {
                $q->whereIn('payment_status', [
                    'pending',
                    'submitted',
                ])->orWhereNull('payment_status');
            })->count(),

            'verified' => StudentPendingRegistration::where(
                'payment_status',
                'verified'
            )->count(),

            'rejected' => StudentPendingRegistration::where(
                'payment_status',
                'rejected'
            )->count(),
        ];

        return view('pending_students.index', compact(
            'students',
            'sessionsList',
            'status',
            'paymentCounts'
        ));
    }


    public function show($id)
    {
        $student = StudentPendingRegistration::with([
            'collegeData',
            'courseData',
            'paymentUpiAccount',
        ])->findOrFail($id);

        return view('pending_students.show', compact('student'));
    }

    public function paymentProof($id)
    {
        $student = StudentPendingRegistration::findOrFail($id);

        $path = $student->payment_proof;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Payment screenshot not found.');
        }

        return Storage::disk('public')->response($path);
    }

    public function destroy($id)
    {
        $student = StudentPendingRegistration::findOrFail($id);

        $student->delete();

        return redirect()
            ->route('admin.pending_request.index')
            ->with('success', 'Pending registration moved to trash.');
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $students = StudentPendingRegistration::whereIn('id', $validated['ids'])
            ->get();

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No pending registrations found.',
            ], 404);
        }

        foreach ($students as $student) {
            $student->delete(); // Soft delete
        }

        return response()->json([
            'success' => true,
            'message' => $students->count() . ' registration(s) moved to trash.',
            'deleted_count' => $students->count(),
        ]);
    }
    public function index_25sep()
    {
        $students = StudentPendingRegistration::with([
            'collegeData',
            'courseData'
        ])->latest('updated_at')->get();

        $sessionsList = StudentSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

        return view('pending_students.index', compact('students', 'sessionsList'));
    }

    public function verifyPayment(Request $request, $id)
{
    $request->validate([
        'payment_admin_note' => ['nullable', 'string', 'max:5000'],
    ]);

    $student = StudentPendingRegistration::findOrFail($id);

    if ($student->payment_status === 'verified') {
        return redirect()
            ->back()
            ->with('info', 'Payment is already verified.');
    }

    $student->update([
        'payment_status' => 'verified',
        'payment_verified_at' => now(),
        'payment_verified_by' => auth()->id(),
        'payment_admin_note' => $request->filled('payment_admin_note')
            ? trim($request->payment_admin_note)
            : null,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Payment verified successfully.');
}

public function rejectPayment(Request $request, $id)
{
    $request->validate([
        'payment_admin_note' => ['nullable', 'string', 'max:5000'],
    ]);

    $student = StudentPendingRegistration::findOrFail($id);

    if ($student->payment_status === 'verified') {
        return redirect()
            ->back()
            ->with('error', 'A verified payment cannot be rejected.');
    }

    $student->update([
        'payment_status' => 'rejected',
        'payment_verified_at' => null,
        'payment_verified_by' => null,
        'payment_admin_note' => $request->filled('payment_admin_note')
            ? trim($request->payment_admin_note)
            : null,
    ]);

    return redirect()
        ->back()
        ->with('success', 'Payment rejected successfully.');
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