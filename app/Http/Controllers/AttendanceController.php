<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Holiday;
use App\Models\Trainer;
use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use App\Traits\CurrentActorTrait;
// use Jenssegers\Agent\Agent;

class AttendanceController extends Controller
{   
    use CurrentActorTrait;

    protected string $permissionPrefix = 'attendance';

    protected array $permissionMap = [
        'employeePanel'        => 'view',
        'employeeDetail'         => 'view',
        'monthlyDetail'         => 'view',
        'trainerDetail'         => 'view',
        'trainerAttendanceDetail'         => 'view',
    ];

    public function __construct()
    {
        $this->middleware('auth');

        // ❌ deny everything by default
        // $this->middleware(function () {
        //     abort(403);
        // });

        // ✅ allow only mapped methods
        foreach ($this->permissionMap as $method => $action) {
            $this->middleware(
                "permission:{$this->permissionPrefix}.{$action}"
            )->only($method);
        }
    }
    // -----------------------------
    // EMPLOYEE PANEL
    // -----------------------------
    public function employeePanel()
    {
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ TRAINER
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('trainer')->check()) {

            $trainerId = Auth::guard('trainer')->id();

            $attendanceToday = Attendance::where('actor_type', 'trainer')
                ->where('actor_id', $trainerId)
                ->whereDate('login_time', today())
                ->first();

            return view('attendance.employee_index', compact('attendanceToday'));
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ EMPLOYEE / USER
        |--------------------------------------------------------------------------
        */
        $userId = Auth::id();

        $attendanceToday = Attendance::where('employee_id', $userId)
            ->whereDate('login_time', today())
            ->first();

        return view('attendance.employee_index', compact('attendanceToday'));
    }
    public function employeePanel27jan()
    {
        $attendanceToday = Attendance::where('employee_id', auth()->id())
            ->whereDate('login_time', today())
            ->first();

        return view('attendance.employee_index', compact('attendanceToday'));
    }

    public function checkIn(Request $request)
{
    if (Auth::guard('trainer')->check()) {
        $actorType = 'trainer';
        $actorId   = Auth::guard('trainer')->id();
        $employeeId = null;
    } else {
        $actorType = 'employee';
        $actorId   = Auth::id();
        $employeeId = Auth::id();
    }

    $exists = Attendance::where('actor_type', $actorType)
        ->where('actor_id', $actorId)
        ->whereDate('login_time', today())
        ->exists();

    if ($exists) {
        return back()->with('error', 'Already checked in today.');
    }

    $agent = new Agent();

    Attendance::create([
        'employee_id' => $employeeId,
        'actor_type'  => $actorType,
        'actor_id'    => $actorId,
        'login_time'  => now(),
        'ip_address'  => $request->ip(),
        'user_agent'  => $request->userAgent(),
        'browser'     => $agent->browser(),
        'browser_version' => $agent->version($agent->browser()),
        'os'          => $agent->platform(),
        'device'      => $agent->device(),
        'device_type' => $agent->isMobile() ? 'Mobile' : 'Desktop',
        'latitude'    => $request->latitude,
        'longitude'   => $request->longitude,
    ]);

    return back()->with('success', 'Checked in');
}

    public function checkIn27jan(Request $request)
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

     

    public function checkOut()
    {
        if (Auth::guard('trainer')->check()) {
            $actorType = 'trainer';
            $actorId   = Auth::guard('trainer')->id();
        } else {
            $actorType = 'employee';
            $actorId   = Auth::id();
        }

        $attendance = Attendance::where('actor_type', $actorType)
            ->where('actor_id', $actorId)
            ->whereNull('logout_time')
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Please check in first.');
        }

        $attendance->update(['logout_time' => now()]);

        return back()->with('success', 'Checked out');
    }

    public function checkOut27jan()
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
    public function employeeList(Request $request)
{
    /*
    |--------------------------------------------------------------------------
    | 1️⃣ EMPLOYEES (USERS TABLE)
    |--------------------------------------------------------------------------
    */
    $employees = User::where('role', '!=', 1)

        ->when($request->name, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        })

        ->when($request->start_date || $request->end_date, function ($q) use ($request) {
            $q->whereHas('attendances', function ($qa) use ($request) {

                if ($request->start_date) {
                    $qa->whereDate('login_time', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $qa->whereDate('logout_time', '<=', $request->end_date);
                }
            });
        })

        ->with(['attendances' => function ($qa) use ($request) {

            if ($request->start_date) {
                $qa->whereDate('login_time', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $qa->whereDate('logout_time', '<=', $request->end_date);
            }
        }])
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 2️⃣ TRAINERS (TRAINERS TABLE)
    |--------------------------------------------------------------------------
    */
    $trainers = Trainer::when($request->name, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        })
        ->with(['attendances' => function ($qa) use ($request) {

            $qa->where('actor_type', 'trainer');

            if ($request->start_date) {
                $qa->whereDate('login_time', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $qa->whereDate('logout_time', '<=', $request->end_date);
            }
        }])
        ->get();

    return view('attendance.admin_index', compact('employees', 'trainers'));
}

    public function employeeList27jan(Request $request)
{
    $employees = User::where('role', '!=', 1)

        // 🔹 filter by name
        ->when($request->name, function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        })

        // 🔹 filter by attendance login/logout
        ->when($request->start_date || $request->end_date, function ($q) use ($request) {
            $q->whereHas('attendances', function ($qa) use ($request) {

                if ($request->start_date) {
                    $qa->whereDate('login_time', '>=', $request->start_date);
                }

                if ($request->end_date) {
                    $qa->whereDate('logout_time', '<=', $request->end_date);
                }
            });
        })

        // 🔹 load filtered attendances
        ->with(['attendances' => function ($qa) use ($request) {

            if ($request->start_date) {
                $qa->whereDate('login_time', '>=', $request->start_date);
            }

            if ($request->end_date) {
                $qa->whereDate('logout_time', '<=', $request->end_date);
            }
        }])

        ->get();

    return view('attendance.admin_index', compact('employees'));
}

    public function employeeList_old()
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

    public function monthlyDetail(Request $request, $actorId = null)
    {
        // dd('here');
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ DETERMINE ACTOR (USER / TRAINER)
        |--------------------------------------------------------------------------
        */
        if (Auth::guard('trainer')->check()) {
            // 🔹 TRAINER VIEWING OWN ATTENDANCE
            $actorType = 'trainer';
            $actorId   = Auth::guard('trainer')->id();
            $actor     = Trainer::findOrFail($actorId);

        } else {
            // 🔹 USER (ADMIN / EMPLOYEE)
            $user = Auth::user();

            if ($user->role != 1) {
                // Employee viewing own
                $actorType = 'user';
                $actorId   = $user->id;
            } else {
                // Admin viewing selected employee
                $actorType = 'user';
                $actorId   = $actorId ?? $user->id;
            }

            $actor = User::findOrFail($actorId);
        }

        /*
        |--------------------------------------------------------------------------
        | 2️⃣ MONTH RANGE
        |--------------------------------------------------------------------------
        */
        $month = $request->month ?? now()->format('Y-m');

        $startOfMonth = Carbon::parse($month . '-01')->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth()->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | 3️⃣ ATTENDANCE QUERY (ACTOR-BASED)
        |--------------------------------------------------------------------------
        */
        $attendanceQuery = Attendance::whereBetween('login_time', [
            $startOfMonth,
            $endOfMonth
        ]);

        if ($actorType === 'trainer') {
            $attendanceQuery
                ->where('actor_type', 'trainer')
                ->where('actor_id', $actorId);
        } else {
            $attendanceQuery
                ->where('employee_id', $actorId);
        }

        $attendance = $attendanceQuery
            ->orderBy('login_time', 'asc')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 4️⃣ HOLIDAYS
        |--------------------------------------------------------------------------
        */
        $holidays = Holiday::whereBetween('holiday_date', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString()
        ])
        ->pluck('holiday_date')
        ->map(fn ($date) => Carbon::parse($date)->format('Y-m-d'))
        ->toArray();

        /*
        |--------------------------------------------------------------------------
        | 5️⃣ VIEW
        |--------------------------------------------------------------------------
        */
        return view(
            'attendance.monthly_detail',
            compact('actor', 'attendance', 'month', 'holidays', 'actorType')
        );
    }


    public function monthlyDetail27jan(Request $request, $employeeId = null)
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

    public function trainerDetail(Trainer $trainer)
    {
        $attendance = $trainer->attendances()
            ->orderBy('login_time', 'desc')
            ->get();

        return view('attendance.trainer_detail', compact('trainer', 'attendance'));
    }

    public function trainerAttendanceDetail(Request $request, Trainer $trainer)
    {

        $month = $request->month ?? now()->format('Y-m');

        $startOfMonth = \Carbon\Carbon::parse($month . '-01')->startOfDay();
        $endOfMonth   = $startOfMonth->copy()->endOfMonth()->endOfDay();

        $attendance = Attendance::where('actor_type', 'trainer')
            ->where('actor_id', $trainer->id)
            ->whereBetween('login_time', [$startOfMonth, $endOfMonth])
            ->orderBy('login_time', 'asc')
            ->get();

        $holidays = Holiday::whereBetween('holiday_date', [
            $startOfMonth->toDateString(),
            $endOfMonth->toDateString()
        ])->pluck('holiday_date')->toArray();

            // 🔹 TRAINER VIEWING OWN ATTENDANCE
            $actorType = 'trainer';
            $actor     = $trainer;
            // dd($actorId, $actorType, $actor);
        return view('attendance.monthly_detail', [
            'employee'  => $trainer,   // reuse same blade
            'attendance'=> $attendance,
            'month'     => $month,
            'holidays'  => $holidays,
            'actorType'  => $actorType,
            'actor'  => $actor,
            'isTrainer' => true
        ]);
    }


}
