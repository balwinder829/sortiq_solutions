<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentLeaveRequest;
use Carbon\Carbon;
use Mail;
use App\Mail\StudentLeaveAppliedMail;

class StudentLeaveController extends Controller
{
    public function create()
    {
        return view('student_leave_apply');
    }

    public function store(Request $request)
    {
        // 🛡️ Honeypot
        if ($request->filled('website')) {
            abort(403);
        }

        // ✅ Validation
        $request->validate([
            'sno'          => 'required',
            'student_name' => 'required|string',
            'from_date'    => 'required|date|after_or_equal:today',
            'to_date'      => 'required|date|after_or_equal:from_date',
            'reason'       => 'nullable|string',
        ]);

        // 🔍 Find student
        $student = Student::where('sno', $request->sno)
            ->whereNull('deleted_at')
            ->first();

        if (!$student) {
            return back()->with('error', 'Invalid student');
        }

        // 🚫 Duplicate check
        $exists = StudentLeaveRequest::where('student_id', $student->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('from_date', [$request->from_date, $request->to_date])
                  ->orWhereBetween('to_date', [$request->from_date, $request->to_date]);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Leave already applied for these dates');
        }

        // 📅 Days
        $days = Carbon::parse($request->from_date)
            ->diffInDays($request->to_date) + 1;

        // 💾 Save
        $leave = StudentLeaveRequest::create([
            'student_id'   => $student->id,
            'sno'          => $student->sno,
            'student_name' => $student->student_name,
            'contact'      => $student->contact,
            'email'        => $student->email_id,
            'session_id'        => $student->session,
            'from_date'    => $request->from_date,
            'to_date'      => $request->to_date,
            'total_days'   => $days,
            'reason'       => $request->reason,
            'ip_address'   => $request->ip(),
        ]);

        // 📧 Mail
        try {
            Mail::to('mehlakrish07@gmail.com')
                ->cc('krish.mehla87@gmail.com')
                ->send(new StudentLeaveAppliedMail($leave));
        } catch (\Exception $e) {}

        return back()->with('success', 'Leave applied successfully');
    }
}