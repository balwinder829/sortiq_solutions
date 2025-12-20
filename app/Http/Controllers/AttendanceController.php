<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use Jenssegers\Agent\Agent;

class AttendanceController extends Controller
{
    // -----------------------------
    // EMPLOYEE PANEL
    // -----------------------------
    public function employeePanel()
    {
        $attendanceToday = Attendance::where('employee_id', auth()->id())
            ->whereDate('login_time', today())
            ->first();

        return view('attendance.employee_index', compact('attendanceToday'));
    }

    public function checkIn(Request $request)
    {
        $exists = Attendance::where('employee_id', auth()->id())
        ->whereDate('login_time', today())
        ->exists();

        if ($exists) {
            return back()->with('error', 'Already checked in today.');
        }

        $agent = new Agent();

        Attendance::create([
            'employee_id' => auth()->id(),
            'login_time'  => now(),

            // 🌐 Network
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),

            // 🖥 Browser / Device
            'browser'         => $agent->browser(),
            'browser_version' => $agent->version($agent->browser()),
            'os'              => $agent->platform(),
            'device'          => $agent->device(),
            'device_type'     => $agent->isMobile() ? 'Mobile' : 'Desktop',

            // 📍 GPS (Field staff)
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
        ]);

        return back()->with('success', 'You have checked in.');
    }

    public function checkIn15dec()
    {
        $exists = Attendance::where('employee_id', auth()->id())
            ->whereDate('login_time', today())
            ->exists();

        if ($exists) {
            return back()->with('error', 'Already checked in today.');
        }

        Attendance::create([
            'employee_id' => auth()->id(),
            'login_time' => now(),
        ]);

        return back()->with('success', 'You have checked in.');
    }

    public function checkOut()
    {
        $attendance = Attendance::where('employee_id', auth()->id())
            ->whereNull('logout_time')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Please check in first.');
        }

        $attendance->update([
            'logout_time' => now(),
        ]);

        return back()->with('success', 'You have checked out.');
    }


    // -----------------------------
    // ADMIN PANEL
    // -----------------------------
    public function employeeList()
    {
        $employees = User::whereIn('role', [2, 3])
                     ->with('attendances')   // load attendance too
                     ->get();

        return view('attendance.admin_index', compact('employees'));
    }

    public function employeeDetail($id)
    {
        $employee = User::findOrFail($id);

        $attendance = Attendance::where('employee_id', $id)
            ->orderBy('login_time', 'desc')
            ->get();

        return view('attendance.admin_list', compact('employee', 'attendance'));
    }

    public function monthlyDetail(Request $request, $employeeId = null)
    {
        // If employee is viewing own detail
        if (auth()->user()->role != 1) {
            $employeeId = auth()->id();
        }

        $month = $request->month ?? now()->format('Y-m'); // e.g. 2025-11
        $employee = User::findOrFail($employeeId);

        // Month range
        $startOfMonth = \Carbon\Carbon::parse($month . '-01')->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth()->endOfDay();

        // Attendance for selected month
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereBetween('login_time', [$startOfMonth, $endOfMonth])
            ->orderBy('login_time', 'asc')
            ->get();

        // ✅ Fetch holidays for the month (PUBLIC / OFFICE)
        $holidays = Holiday::whereBetween('holiday_date', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString()
        ])
        ->pluck('holiday_date')
        ->map(fn($date) => \Carbon\Carbon::parse($date)->format('Y-m-d'))
        ->toArray();

        return view(
            'attendance.monthly_detail',
            compact('employee', 'attendance', 'month', 'holidays')
        );
    }

    public function monthlyDetail15dec(Request $request, $employeeId = null)
    {
        // If employee is viewing own detail
        if (auth()->user()->role != 1) {
            $employeeId = auth()->id();
        }

        $month = $request->month ?? now()->format('Y-m'); // "2025-01"
        $employee = User::findOrFail($employeeId);

        // Get all attendance for selected month
        $attendance = Attendance::where('employee_id', $employeeId)
            ->whereYear('login_time', substr($month, 0, 4))
            ->whereMonth('login_time', substr($month, 5, 2))
            ->orderBy('login_time', 'asc')
            ->get();

        return view('attendance.monthly_detail', compact('employee', 'attendance', 'month'));
    }

}
