<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\EmployeeLeaveRequest;
use Carbon\Carbon;
use Mail;
use App\Mail\LeaveAppliedMail;
// use Illuminate\Support\Facades\Mail;

class EmployeeLeaveController extends Controller
{
    /**
     * Show form
     */
    public function create()
    {
        return view('employee_leave_apply');
    }

    /**
     * Store leave
     */

    public function store(Request $request)
{
    // Honeypot
    if ($request->filled('website')) {
        abort(403);
    }

    // Validation
    $request->validate([
        'emp_code'  => 'required|exists:employees,emp_code',
        'email'     => 'required|email',
        'contact'     => 'required',
        'emp_name'  => 'required|string',
        'from_date' => 'required|date|after_or_equal:today',
        'to_date'   => 'nullable|date|after_or_equal:from_date',
        'reason'    => 'nullable|string',
    ]);

    // Find employee
    $employee = Employee::where('emp_code', $request->emp_code)
        ->where('email', $request->email)
        ->where('status', 'active')
        ->first();

    if (!$employee) {
        return back()->with('error', 'Invalid employee details');
    }

    // Handle 1-day leave
    $toDate = $request->to_date ?? $request->from_date;

    // Duplicate check
    $exists = EmployeeLeaveRequest::where('employee_id', $employee->id)
        ->where(function ($q) use ($request, $toDate) {
            $q->whereBetween('from_date', [$request->from_date, $toDate])
              ->orWhereBetween('to_date', [$request->from_date, $toDate]);
        })
        ->exists();

    if ($exists) {
        return back()->with('error', 'Leave already applied for these dates');
    }

    // Daily limit
    $todayCount = EmployeeLeaveRequest::where('employee_id', $employee->id)
        ->whereDate('created_at', today())
        ->count();

    if ($todayCount >= 2) {
        return back()->with('error', 'You already submitted today');
    }

    // Days
    $days = Carbon::parse($request->from_date)
        ->diffInDays($toDate) + 1;

    // Save
    $leave = EmployeeLeaveRequest::create([
        'employee_id' => $employee->id,
        'emp_code'    => $employee->emp_code,
        'emp_name'    => $employee->emp_name,
        'email'       => $request->email,
        'contact'       => $request->contact,
        'from_date'   => $request->from_date,
        'to_date'     => $toDate,
        'total_days'  => $days,
        'reason'      => $request->reason,
        'ip_address'  => $request->ip(),
    ]);

    // Mail
    try {
        Mail::to('mehlakrish07@gmail.com')
            ->cc('krish.mehla87@gmail.com')
            ->send(new LeaveAppliedMail($leave));
    } catch (\Exception $e) {}

    return back()->with('success', 'Leave applied successfully');
}
    public function store13ap(Request $request)
    {
        // ✅ Honeypot (anti-bot)
        if ($request->filled('website')) {
            abort(403);
        }

        // ✅ Validation
        $request->validate([
            'emp_code'  => 'required|exists:employees,emp_code',
            'email'     => 'required|email',
            'emp_name'  => 'required|string',
            'from_date' => 'required|date|after_or_equal:today',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'reason'    => 'nullable|string',
        ]);

        // ✅ Find employee
        $employee = Employee::where('emp_code', $request->emp_code)
            ->where('email', $request->email)
            ->where('status', 'active')
            ->first();

        if (!$employee) {
            return back()->with('error', 'Invalid employee details');
        }

        // ✅ Duplicate check
        $exists = EmployeeLeaveRequest::where('employee_id', $employee->id)
            ->where(function ($q) use ($request) {
                $q->whereBetween('from_date', [$request->from_date, $request->to_date])
                  ->orWhereBetween('to_date', [$request->from_date, $request->to_date]);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'Leave already applied for these dates');
        }

        // ✅ Daily limit
        $todayCount = EmployeeLeaveRequest::where('employee_id', $employee->id)
            ->whereDate('created_at', today())
            ->count();

        if ($todayCount >= 2) {
            return back()->with('error', 'You already submitted today');
        }

        // ✅ Calculate days
        $days = Carbon::parse($request->from_date)
            ->diffInDays($request->to_date) + 1;

        // ✅ Save
        $leave = EmployeeLeaveRequest::create([
            'employee_id' => $employee->id,
            'emp_code'    => $employee->emp_code,
            'emp_name'    => $employee->emp_name, // from DB
            'email'       => $employee->email,
            'from_date'   => $request->from_date,
            'to_date'     => $request->to_date,
            'total_days'  => $days,
            'reason'      => $request->reason,
            'ip_address'  => $request->ip(),
        ]);

        // ✅ Mail (HR + CC)
        try {
            Mail::to('mehlakrish07@gmail.com')
                ->cc('krish.mehla87@gmail.com')
                ->send(new LeaveAppliedMail($leave));
        } catch (\Exception $e) {
            // optional: log error
        }

        return back()->with('success', 'Leave applied successfully');
    }
}