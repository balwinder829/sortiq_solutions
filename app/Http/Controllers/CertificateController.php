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

class CertificateController extends Controller
{
// Show all students
public function index(Request $request)
{   

    $notificationMode = $request->notification ?? null;

    $query = Student::query();

    if ($notificationMode === 'bin_ready') {
        $query->where('pending_fees', 0);
        $query->where('certificate_status', 2);
        $query->where('email_count_certificate','>' ,0);
    }else if($notificationMode === 'pending_fee'){
        $query->where('pending_fees', '>', 0);
        $query->whereDate('next_due_date', '<=', now());
        $query->where('certificate_status', 1);
    }else{
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

        if ($request->filled('part_time_offer')) {
            $query->where('part_time_offer', $request->part_time_offer);
        }

        if ($request->filled('placement_offer')) {
            $query->where('placement_offer', $request->placement_offer);
        }

        if ($request->filled('pg_offer')) {
            $query->where('pg_offer', $request->pg_offer);
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }
        $query->whereIn('certificate_status', [1, 2]);
    }

    // Always filter students with 0.00 pending fees
    // $query->where('pending_fees', 0.00);
    $activeSessionId = session('admin_session_id');
   
    
    if (auth()->user()->role == 1) {
         
        $query->where('session', $activeSessionId);
    }
    // $query->where('certificate_status', 1);
    $query->where('send_to_close', 0);
    

    // $query->where(function ($q) {
    //     // Always show certificate_status = 1
    //     $q->where('certificate_status', 1)

    //       // Show certificate_status = 2 only if student_consent is pending
    //       ->orWhere(function ($q2) {
    //           $q2->where('certificate_status', 2)
    //              ->where(function ($r) {
    //                  $r->whereNull('student_consent')
    //                    ->orWhere('student_consent', 0);
    //              });
    //       });
    // });


    $students    = $query->latest()->paginate(10);
    $sessions    = StudentSession::all();
    $colleges    = \App\Models\College::all();
    $courses     = \App\Models\Course::all();
    $batches     = \App\Models\Batch::all();
    $users       = \App\Models\User::all();
    $departments = \App\Models\Department::all();
    $student_status = StudentStatus::all();

    $dismissed = session('dismiss_pending_fee');
    $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionId)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();

    return view('certificates.index', compact(
        'students',
        'sessions',
        'colleges',
        'batches',
        'courses',
        'departments',
        'users',
        'pendingStudents',
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

        return view('certificates.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status'));
        // return view('certificates.edit', compact('student'));
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
         /**
     * 🔴 BUSINESS RULE CHECK
     * Only when send_to_close = 1
     */
    if ($validates['send_to_close'] == 1) {

        if (
            ($student->email_count_confirmation ?? 0) <= 0 ||
            ($student->email_count_certificate ?? 0) <= 0 ||
            ($student->count_receipt_download ?? 0) <= 0 ||
            ($student->pending_fees ?? 0) > 0
        ) {
            return back()
                ->withInput()
                ->with('error', 'Student cannot be sent to close. Please ensure:
                    • Confirmation email sent
                    • Certificate email sent
                    • Receipt downloaded
                    • No pending fees');
        }

        // All checks passed
        $validates['certificate_status'] = 3;

    } else {
        $validates['certificate_status'] = 2;
    }


        // if ($validates['send_to_close'] == 1) {
        //     $validates['certificate_status'] = 3;
        // } else {
        //     $validates['certificate_status'] = 2;
        // }
        $student->update($validates);

        return redirect()->route('certificates.index')
                        ->with('success','Student updated successfully');
    }

    public function updateold(Request $request, Student $student)
    {
        $request->validate([
            'student_name'   => 'required|string|max:255',
            'father_name'    => 'required|string|max:255',
            'email'          => 'required|email|unique:students,email,' . $student->id,
            'contact_no'     => 'nullable|string|max:15',
            'department'     => 'required|string',
            'session_name'   => 'required|string',
            'pending_fees'   => 'nullable|numeric',
        ]);

        $student->update($request->only([
            'student_name',
            'father_name',
            'email',
            'contact_no',
            'department',
            'session_name',
            'pending_fees'
        ]));

        return redirect()->route('certificates.index')
                         ->with('success', 'Student data updated successfully');
    }
}
