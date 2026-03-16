<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Batch;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\StudentProjectAssignment;

class StudentDashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:student');
    }


    public function index()
    {

        $student = Auth::guard('student')->user();

        /*
        |--------------------------------------------------------------------------
        | Student Basic Information
        |--------------------------------------------------------------------------
        */

        $batch = Batch::find($student->batch_assign);


        /*
        |--------------------------------------------------------------------------
        | Future Modules (prepare variables)
        |--------------------------------------------------------------------------
        */

        // $attendancePercentage = 0; // future module
        $pendingFees = $student->pending_fees ?? 0;

        $assignments = []; // future table
        $notifications = []; // future system
        $tests = []; // future exams

        $totalClasses = AttendanceSession::where(
            'batch_id',
            $student->batch_assign
        )->count();

        $present = AttendanceRecord::where(
            'student_id',
            $student->id
        )->whereIn('status',['present','late'])->count();

        $attendancePercentage = $totalClasses > 0
            ? round(($present / $totalClasses) * 100)
            : 0;

        return view('students_dashboard.dashboard', compact(

            'student',
            'batch',
            'attendancePercentage',
            'pendingFees',
            'assignments',
            'notifications',
            'tests'

        ));
    }


    /*
    |--------------------------------------------------------------------------
    | Student Profile
    |--------------------------------------------------------------------------
    */

    public function profile()
    {
        $student = Auth::guard('student')->user();

        return view('students.profile', compact('student'));
    }


    /*
    |--------------------------------------------------------------------------
    | Student Attendance (future module)
    |--------------------------------------------------------------------------
    */

    public function attendancqe()
    {
        $student = Auth::guard('student')->user();

        return view('students.attendance', compact('student'));
    }
    public function attendance()
    {
        $student = Auth::guard('student')->user();

        // $attendance = AttendanceRecord::with('session')
        //     ->where('student_id',$student->id)
        //     ->orderByDesc('created_at')
        //     ->get();

         $attendance = AttendanceRecord::with('session')
        ->where('student_id',$student->id)
        ->join('attendance_sessions','attendance_records.session_id','=','attendance_sessions.id')
        ->orderByDesc('attendance_sessions.session_date')
        ->select('attendance_records.*')
        ->get();

        $present = $attendance
            ->whereIn('status',['present','late'])
            ->count();

        $absent = $attendance
            ->where('status','absent')
            ->count();

        $total = $attendance->count();

        $percentage = $total > 0
            ? round(($present/$total)*100)
            : 0;

        return view(
            'students_dashboard.attendance',
            compact(
                'attendance',
                'present',
                'absent',
                'total',
                'percentage'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Assignments
    |--------------------------------------------------------------------------
    */

    public function assignments()
    {
        $student = Auth::guard('student')->user();

        return view('students.assignments', compact('student'));
    }


    /*
    |--------------------------------------------------------------------------
    | Student Tests
    |--------------------------------------------------------------------------
    */

    public function tests()
    {
        $student = Auth::guard('student')->user();

        return view('students.tests', compact('student'));
    }


    /*
    |--------------------------------------------------------------------------
    | Student Certificates
    |--------------------------------------------------------------------------
    */

    public function certificates()
    {
        $student = Auth::guard('student')->user();

        return view('students.certificates', compact('student'));
    }

    public function projects()
    {
        $student = Auth::guard('student')->user();

        $projects = StudentProjectAssignment::with([
            'project',
            'submission'
        ])
        ->where('student_id',$student->id)
        ->latest()
        ->get();

        return view(
            'students_dashboard.projects',
            compact('projects')
        );
    }

}