<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JoiningStudent;
use App\Models\Student;
use App\Models\StudentSession;
use App\Exports\JoiningStudentsExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class JoiningStudentAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:joined_students.view')
            ->only([
                'index',
                'show',
                'paymentProof',
                'export',
                'sendToSession',
            ]);

        $this->middleware('permission:joined_students.edit')
            ->only([
                'verifyPayment',
                'rejectPayment',
            ]);

        $this->middleware('permission:joined_students.delete')
            ->only([
                'destroy',
                'bulkDestroy',
            ]);
    }

    /**
     * Admin list
     */
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

        $query = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData',
            'paymentUpiAccount',
        ]);

        if ($status === 'awaiting') {
            $query->where(function ($q) {
                $q->whereIn('payment_status', ['pending', 'submitted'])
                    ->orWhereNull('payment_status');
            });
        } elseif (in_array($status, ['verified', 'rejected'], true)) {
            $query->where('payment_status', $status);
        }

        $students = $query->latest('updated_at')->get();

        $sessionsList = StudentSession::where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->get()
            ->pluck('display_name', 'id');

        $paymentCounts = [
            'all' => JoiningStudent::count(),

            'awaiting' => JoiningStudent::where(function ($q) {
                $q->whereIn('payment_status', ['pending', 'submitted'])
                    ->orWhereNull('payment_status');
            })->count(),

            'verified' => JoiningStudent::where(
                'payment_status',
                'verified'
            )->count(),

            'rejected' => JoiningStudent::where(
                'payment_status',
                'rejected'
            )->count(),
        ];

        return view('joining_students.index', compact(
            'students',
            'sessionsList',
            'status',
            'paymentCounts'
        ));
    }

    /**
     * Student detail
     */
    public function show($id)
    {
        $student = JoiningStudent::with([
            'collegeData',
            'courseData',
            'durationData',
            'paymentUpiAccount',
        ])->findOrFail($id);

        return view('joining_students.show', compact('student'));
    }

    /**
     * Show payment proof from public storage disk
     */
    public function paymentProof($id)
    {
        $student = JoiningStudent::findOrFail($id);

        $path = $student->payment_proof;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Payment screenshot not found.');
        }

        return Storage::disk('public')->response($path);
    }

    /**
     * Verify payment
     */
    public function verifyPayment(Request $request, $id)
    {
        $request->validate([
            'payment_admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $student = JoiningStudent::findOrFail($id);

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

    /**
     * Reject payment
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'payment_admin_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $student = JoiningStudent::findOrFail($id);

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

    /**
     * Single soft delete
     */
    public function destroy($id)
    {
        $student = JoiningStudent::findOrFail($id);
        $student->delete();

        return redirect()
            ->route('admin.joining_students.index')
            ->with('success', 'Joining student moved to trash.');
    }

    /**
     * Bulk soft delete
     */
    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
        ]);

        $students = JoiningStudent::whereIn('id', $validated['ids'])->get();

        if ($students->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No joining students found.',
            ], 404);
        }

        foreach ($students as $student) {
            $student->delete();
        }

        return response()->json([
            'success' => true,
            'message' => $students->count() . ' joining student(s) moved to trash.',
            'deleted_count' => $students->count(),
        ]);
    }

    /**
     * Send selected joining students to a session
     *
     * Existing joining-to-student mapping is preserved.
     */
    public function sendToSession(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct'],
            'session_id' => ['required', 'exists:student_sessions,id'],
        ]);

        $inserted = 0;
        $skipped = 0;

        foreach ($validated['ids'] as $id) {
            $joining = JoiningStudent::find($id);

            if (!$joining) {
                $skipped++;
                continue;
            }

            $exists = Student::where('source_type', 'joining_student')
                ->where('source_id', $joining->id)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $lastSno = Student::orderBy('id', 'desc')->value('sno');
            $newSno = is_numeric($lastSno)
                ? ((int) $lastSno + 1)
                : 1;

            Student::create([
                'sno' => $newSno,
                'student_name' => $joining->student_name,
                'f_name' => $joining->father_name,
                'contact' => $joining->contact,
                'email_id' => $joining->email ?? '',
                'college_name' => $joining->college,
                'join_date' => $joining->date_of_joining,
                'start_date' => $joining->date_of_joining,
                'session' => $validated['session_id'],

                'status' => 'joined',
                'source_type' => 'joining_student',
                'source_id' => $joining->id,
            ]);

            $joining->update([
                'is_sent_to_detail' => 1,
                'sent_to_detail_at' => now(),
            ]);

            $inserted++;
        }

        return response()->json([
            'status' => true,
            'message' => "$inserted students sent successfully"
                . ($skipped ? " ($skipped skipped)" : ''),
        ]);
    }

    /**
     * Excel export
     */
    public function export(Request $request)
    {
        $fileName = 'joining-students-' . now()->format('d_F') . '.xlsx';

        return Excel::download(
            new JoiningStudentsExport($request),
            $fileName
        );
    }
}