<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentSession;
use App\Models\EmailCount;
use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateIssuedMail;
use App\Mail\StudentConfirmationMail;
use App\Models\College;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Department;
use App\Models\Reference;
use App\Models\Duration;
use App\Models\StudentStatus;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Imports\StudentsImport;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class CloseStudenController extends Controller
{
// Show all students
public function index(Request $request)
{
    $query = Student::query();

    // Always filter students with 0.00 pending fees
    $query->where('pending_fees', 0.00);

    // Optional search filters
    if ($request->filled('student_name')) {
        $query->where('student_name', 'like', '%' . $request->student_name . '%');
    }

    if ($request->filled('f_name')) {
        $query->where('f_name', 'like', '%' . $request->f_name . '%');
    }

    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    if ($request->filled('session')) {
        $query->where('session_id', $request->session);
    }

    if ($request->filled('college_name')) {
        $query->where('college_name', $request->college_name);
    }

    if ($request->filled('email_id')) {
        $query->where('email_id', 'like', '%' . $request->email_id . '%');
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('technology')) {
        $query->where('technology', $request->technology);
    }

    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    if (auth()->user()->role == 1) {
         $activeSessionId = session('admin_session_id');
        $query->where('session', $activeSessionId);
    }
    // $query->where('certificate_status', 2);
    $query->where('send_to_close', 1);
    $query->whereIn('certificate_status', [1, 2]);

    $students    = $query->latest()->paginate(10);
    $sessions    = StudentSession::all();
    $colleges    = \App\Models\College::all();
    $courses     = \App\Models\Course::all();
    $batches     = \App\Models\Batch::all();
    $users       = \App\Models\User::all();
    $departments = \App\Models\Department::all();
    $student_status = StudentStatus::all();

    return view('close_student.index', compact(
        'students',
        'sessions',
        'colleges',
        'batches',
        'courses',
        'departments',
        'users',
        'student_status'
    ));
}


    // Show a single student (for view/edit)
    public function edit(Student $student)
    {
         $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        // $department = Department::all();
        $references = Reference::all();
        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();
        return view('close_student.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status'));
    }

    // Update student data
    public function update(Request $request, Student $student)
    {
         // dd($request->all());
        $validates = $request->validate([
            'student_name'   => 'required|string|max:255',
            'f_name'         => 'required|string|max:255',
            'sno'            => 'required|string|max:255',
            'email_id'       => 'required|email|unique:students_detail,email_id,'.$student->id,
            'contact'        => 'nullable|string|max:15',
            'gender'         => 'required|string',
            'college_name'   => 'required|string',   // not college_id
            'session'        => 'required|string',   // not session_id
            'technology'     => 'required|string',   // not technology_id
            'batch_assign'   => 'required|string',   // not batch_id
            'reference'      => 'string',   // not reference_user
            'status'         => 'required|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'pending_fees'   => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'reg_due_amount' => 'required|string',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',
            'send_to_close'         => 'required|boolean',
            'is_placed'         => 'required|boolean',
        ]);
        // dd('Passed validation', $validates);

        if ($validates['send_to_close'] == 1) {
            $validates['certificate_status'] = 3;
        } else {
            $validates['certificate_status'] = 2;
        }
        $student->update($validates);
        return redirect()->route('close_student.index')
                         ->with('success', 'Student data updated successfully');
    }
}
