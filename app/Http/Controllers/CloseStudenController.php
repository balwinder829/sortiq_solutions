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
use Illuminate\Support\Str;

class CloseStudenController extends Controller
{

    protected string $permissionPrefix = 'close_students';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
         

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        // 'bulkDelete'      => 'delete',
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

    // if ($request->filled('technology')) {
    //     $query->where('technology', $request->technology);
    // }

     if ($request->filled('technology')) {
        $query->whereRaw(
            "FIND_IN_SET(?, technology)",
            [$request->technology]
        );
    }

    if ($request->filled('department')) {
        $query->where('department', $request->department);
    }

    if ($request->filled('gender')) {
        $query->where('gender', $request->gender);
    }

    if (auth()->user()->role == 1) {
         $activeSessionId = session('admin_session_id');
        $query->where('session', $activeSessionId);
    }
    // $query->where('certificate_status', 2);
    $query->where('send_to_close', 1);
    $query->whereIn('certificate_status', [1, 2]);

    $students    = $query->latest()->get();
    $sessions    = StudentSession::all();
    $colleges = College::orderBy('college_name')->get();
    $courses = Course::orderBy('course_name')->get();
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
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        // $department = Department::all();
        $references = Reference::all();
        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();
        return view('close_student.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status','activeSession'));
    }

    // Update student data
    public function update(Request $request, Student $student)
    {
         // dd($request->all());
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(
                trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->f_name))
            )
        ]);

        $validates = $request->validate([
            'student_name'   => 'required|string|max:255',
            // 'f_name'         => 'required|string|max:255',
            'f_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    // Remove "Mr." and validate remaining name
                    $nameOnly = trim(preg_replace('/^mr\.?\s*/i', '', $value));

                    if ($nameOnly === '') {
                        $fail('Father name is required.');
                    }
                }
            ],
            'sno'            => 'required|string|max:255',
            'email_id'       => 'nullable|email',
            'contact'        => 'required|string|max:15',
            'gender'         => 'required|string',
            'college_name'   => 'required|string',   // not college_id
            'session'        => 'required|string',   // not session_id
            'technology'     => 'required|string',   // not technology_id
            // 'batch_assign'   => 'required|string',   // not batch_id
            'reference'      => 'string',   // not reference_user
            'status'         => 'nullable|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            // 'paid_fees'       => 'required|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
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

        $validates['student_name'] = Str::of($validates['student_name'])->trim()->lower();
        $validates['f_name']       = Str::of($validates['f_name'])->trim()->lower();

        $activeSessionId = session('admin_session_id');
        if (!empty($validates['contact'])) {
            $contactExists = Student::withTrashed()
                ->where('student_name', $validates['student_name'])
                ->where('f_name', $validates['f_name'])
                ->where('contact', $validates['contact'])
                ->where('session', $activeSessionId)
                ->where('id', '!=', $student->id) // 👈 ignore current record
                ->exists();

            if ($contactExists) {
                return back()
                    ->withErrors([
                        'contact' => 'This student name with this contact already exists in this session'
                    ])
                    ->withInput();
            }
        }

        // Force lowercase before saving
        // $validates['student_name'] = Str::lower($validates['student_name']);
        // $validates['f_name']       = Str::lower($validates['f_name']);

        $validates['paid_fees'] = $validates['paid_fees'] ?? 0;
        $validates['reg_fees'] = $validates['reg_fees'] ?? 0;

        $validates['pending_fees'] = max(
            $validates['total_fees'] - $validates['reg_fees'] - $validates['paid_fees'],
            0
        );

        $student->update($validates);
        return redirect()->route('close_student.index')
                         ->with('success', 'Student data updated successfully');
    }
}
