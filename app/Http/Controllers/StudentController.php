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
use Illuminate\Support\Facades\View;
use ZipArchive;
use Mpdf\Mpdf;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Exports\StudentListExport;
use Illuminate\Support\Str;
use App\Imports\StudentsFeeImport;
use App\Exports\StudentsFeeTemplateExport;
use App\Models\Placement;
use App\Rules\NotBlockedNumber;


class StudentController extends Controller
{   

    protected string $permissionPrefix = 'students';

    protected array $permissionMap = [
        'index'        => 'view',
        'show'         => 'view',
        'confirmStudent'         => 'view',
        'confirmMultiple'         => 'view',
        'importForm'         => 'import',
        'import'         => 'import',
        'exportExcel'         => 'import',

        'create'       => 'create',
        'store'        => 'create',

        'edit'         => 'edit',
        'update'       => 'edit',

        'destroy'      => 'delete',

        'bulkDelete'      => 'bulk_deletes',
        
        'downloadconfirmMultiple'      => 'downloads',
        'downloadMultipleReceipts'      => 'downloads',
        'downloadCertificateMultiple'      => 'downloads',
        'generateIdCard'      => 'downloads',
        'downloadIdStudentCard'      => 'downloads',

        'copyStudents'      => 'copy_to_other_session',

        'moveMultipleToCertificate'      => 'move_to_certificates',
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
    // Constructor to apply auth middleware

    // List students
    public function index(Request $request)
    {   
        $notificationMode = $request->notification ?? null;

        $query = Student::query();
        if ($notificationMode === 'registered_today') {
            $query->whereDate('created_at', today());
        }else{
            // Filters
            if ($request->filled('student_name')) {
                $query->where('student_name', 'like', '%' . $request->student_name . '%');
            }
            if ($request->filled('f_name')) {
                $query->where('f_name', 'like', '%' . $request->f_name . '%');
            }
            if ($request->filled('sno')) {
                $query->where('sno', $request->sno);
            }
            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }
            if ($request->filled('session')) {
                $query->where('session', $request->session);
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
            if ($request->filled('is_intern')) {
                $query->where('is_intern', $request->is_intern);
            }
            if ($request->filled('is_online')) {
                $query->where('is_online', $request->is_online);
            }

            if ($request->filled('registration_fee')) {
                $query->where('reg_fees', $request->registration_fee);
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

            // if ($request->filled('department')) {
            //     $query->where('department', $request->department);
            // }
            if ($request->filled('start_date')) {
                $query->whereDate('start_date', '>=', $request->start_date);
            }
            if ($request->filled('end_date')) {
                $query->whereDate('end_date', '<=', $request->end_date);
            }
            // Filter by pending fee
            if ($request->filled('pending_fees') && $request->pending_fees == 1) {
                $query->where('pending_fees', '>', 0.00);
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

            if ($request->filled('fee_filter')) {
                switch ($request->fee_filter) {

                    case 'completed':
                        $query->where('pending_fees', 0);
                        break;

                    case 'pending':
                        $query->where('pending_fees', '>', 0);
                        break;

                    case 'pending_high':
                        // $query->where('pending_fees', '>', 0)
                              $query->orderBy('pending_fees', 'desc');
                        break;

                    case 'pending_low':
                        // $query->where('pending_fees', '>=', 0)
                              $query->orderBy('pending_fees', 'asc');
                        break;

                    case 'fees_high':
                        $query->orderBy('total_fees', 'desc');
                        break;

                    case 'fees_low':
                        $query->orderBy('total_fees', 'asc');
                        break;
                }
            }

            /* =========================
               AMOUNT SLIDER FILTER
               (ONLY for last 4 options)
               ========================= */

            if (
                $request->filled('fee_filter') &&
                in_array($request->fee_filter, [
                    'pending_high',
                    'pending_low',
                    'fees_high',
                    'fees_low'
                ])
            ) {

                $minAmount = $request->amount_min;
                $maxAmount = $request->amount_max;
                // dd($request->amount_min, $request->amount_max,$request->fee_filter);
                // Decide column
                $amountColumn = in_array($request->fee_filter, ['pending_high', 'pending_low'])
                    ? 'pending_fees'
                    : 'total_fees';

                if ($minAmount !== null && $minAmount !== '') {
                    $query->where($amountColumn, '>=', $minAmount);
                }

                if ($maxAmount !== null && $maxAmount !== '') {
                    $query->where($amountColumn, '<=', $maxAmount);
                }
            }


            if ($request->filled('gender')) {
                $query->where('gender', $request->gender);
            }

            if ($request->filled('next_due_date')) {
                $query->whereDate('next_due_date', $request->next_due_date)
                      ->where('pending_fees', '>', 0);
            }
            
            if (auth()->user()->role == 1) {
                $activeSessionId = session('admin_session_id');
                $query->where('session', $activeSessionId);
            }
            
        }
        
            
            $query->where('certificate_status', 0);
            if (!$request->filled('fee_filter')) {
                // $query->orderBy('id', 'desc');
                 $query->latest('updated_at');
            } 

            if ($request->filled('limit')) {
                $query->limit($request->limit);
            }
            $students = $query->get();
            // dd($students);
            // $students = $query->orderBy('id', 'desc')->get();

            // $students = $query->paginate(10);

        $sessions = StudentSession::all();
        // $colleges = College::all();
        $colleges = College::orderBy('college_name')->get();
        $courses = Course::orderBy('course_name')->get();
        // $courses = Course::all();
        $batches = Batch::all();
        $references = Reference::all();
        $departments = Department::all();
        $users = User::all();
        $student_status = StudentStatus::all();

        //pending fee
        $dismissed = session('dismiss_pending_fee');
        $activeSessionNo = session('admin_session_id');
            
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionNo)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();
        

        return view('students.index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }

    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    // Show create form
    public function create()
    {   
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        // $colleges = College::all();
        // $courses = Course::all();
        // $batches = Batch::all();
        // $department = Department::all();
        // $references = Reference::all();
        $colleges    = College::orderBy('college_display_name', 'asc')->get();
        $courses     = Course::orderBy('course_name', 'asc')->get();
        $batches     = Batch::orderBy('batch_name', 'asc')->get();        
        $references  = Reference::orderBy('name', 'asc')->get();

        // $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();
        $lastStudent = Student::orderBy('id', 'desc')->first();
        $nextSno = $lastStudent ? $lastStudent->sno + 1 : 1;

        return view('students.create', compact('sessions','activeSession','colleges','courses','batches','references','course_duration','student_status','nextSno'));
    }

    // Store student
    public function store(Request $request)
    {   
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(
                trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->f_name))
            )
        ]);

        $validate= $request->validate([
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
            'sno'            => 'nullable|string|max:255',

            // 🔽 removed DB unique, handled manually below
            'email_id'       => 'required|email',
            // 'contact'        => 'nullable|string|max:15',
            'contact' => ['nullable', 'string', new NotBlockedNumber],

            'gender'         => 'required|string',
            // 'college_name'   => 'required|string',
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place'        => 'required_if:is_place,1|nullable|string',
            'is_place'    => 'nullable',
            'status'         => 'nullable',
            // 'technology'     => 'nullable|string',
            'technology'   => 'nullable|array',
            'technology.*' => 'string',

            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'paid_fees'       => 'required|numeric',
            'next_due_date'  => 'nullable|date',
            'join_date'      => 'nullable|date',
            'duration'       => 'nullable',
            'batch_assign'   => 'nullable|string',
            'reference'      => 'string',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'nullable|boolean',
            'placement_offer'  => 'nullable|boolean',
            'pg_offer'         => 'nullable|boolean',
            'is_married'         => 'nullable|boolean',
            'is_online'         => 'nullable|boolean',
        ]);

        if (($validate['reg_fees'] + $validate['paid_fees']) > $validate['total_fees']) {
            return back()
                ->withErrors([
                    'paid_fees' => 'Registration fees + Paid fees cannot be greater than Total fees.',
                ])
                ->withInput();
        }
        $activeSessionId = session('admin_session_id');

        /* =======================================================
           🔴 ONLY NEW LOGIC ADDED — EVERYTHING ELSE SAME
           ======================================================= */

        /** ❌ SESSION-WISE EMAIL DUPLICATE CHECK */
        // $emailExists = Student::withTrashed()
        //     ->where('email_id', $validate['email_id'])
        //     ->where('session', $activeSessionId)
        //     ->exists();

        // if ($emailExists) {
        //     return back()
        //         ->withErrors(['email_id' => 'This email already exists in this session'])
        //         ->withInput();
        // }

         
        // if (!empty($validate['contact'])) {
        //     $contactExists = Student::withTrashed()
        //         ->where('contact', $validate['contact'])
        //         ->where('session', $activeSessionId)
        //         ->exists();

        //     if ($contactExists) {
        //         return back()
        //             ->withErrors(['contact' => 'This contact already exists in this session'])
        //             ->withInput();
        //     }
        // }
        // use Illuminate\Support\Str;

        $validate['student_name'] = Str::of($validate['student_name'])->trim()->lower();
        $validate['f_name']       = Str::of($validate['f_name'])->trim()->lower();

        if (!empty($validate['contact'])) {
            $contactExists = Student::query()
                ->where('student_name', $validate['student_name'])
                ->where('f_name', $validate['f_name'])
                ->where('contact', $validate['contact'])
                ->where('session', $activeSessionId)
                ->exists();

            if ($contactExists) {
                return back()
                    ->withErrors([
                        'contact' => 'This student name with this contact already exists in this session'
                    ])
                    ->withInput();
            }
        }




        /** 🔢 GLOBAL RECEIPT / STUDENT RECORD NUMBER */
        // $lastSno = Student::whereRaw("sno REGEXP '^[0-9]+$'")
        //     ->max(DB::raw('CAST(sno AS UNSIGNED)'));

        // $validate['sno'] = $lastSno ? $lastSno + 1 : 1;

        $lastSno = Student::orderBy('id', 'desc')->value('sno');

        $newSno = is_numeric($lastSno) ? ((int)$lastSno + 1) : 1;

        $validate['sno'] = $newSno;


        /* =================== EXISTING CODE =================== */
        $validate['student_name'] = Str::lower($validate['student_name']);
        $validate['f_name']       = Str::lower($validate['f_name']);
        $validate['pending_fees'] = "0";
        $validate['paid_fees'] = $validate['paid_fees'] ?? 0;

        $validate['pending_fees'] = max(
            $validate['total_fees'] - $validate['reg_fees'] - $validate['paid_fees'],
            0
        );

        $name = strtolower(trim($validate['student_name']));
        $name = preg_replace('/\s+/', '_', $name);

        $validate['plain_password'] = $validate['password'] = $name . $newSno;
        $validate['session'] = $activeSessionId;

        Student::create($validate);

        $activeSession = StudentSession::find($activeSessionId);

        $sessionName = $activeSession
            ? ucwords($activeSession->session_name)
            : 'Unknown Session';

        return redirect()
            ->route('students.index')
            ->with('success', "Student added successfully for session: {$sessionName}");
    }

    // Show edit form
    public function edit(Student $student)
    {   
        $activeSessionId = session('admin_session_id');
        $activeSession = StudentSession::find($activeSessionId);
        $sessions = StudentSession::all();
        $colleges    = College::orderBy('college_display_name', 'asc')->get();
        $courses     = Course::orderBy('course_name', 'asc')->get();
        $batches     = Batch::orderBy('batch_name', 'asc')->get();        
        $references  = Reference::orderBy('name', 'asc')->get();

        $users = User::all();
        $course_duration = Duration::all();
        $student_status = StudentStatus::all();

        return view('students.edit', compact('student','sessions','colleges','courses','batches','references','users','course_duration','student_status','activeSession'));
        // return view('students.edit', compact('student','sessions','colleges','courses','batches','department','references','users'));
    }

    public function update(Request $request, Student $student)
    {
        // dd($request->all());
        $request->merge([
            'f_name' => 'Mr. ' . ucwords(
                trim(preg_replace('/^(mr\.?\s*)+/i', '', $request->f_name))
            )
        ]);

        if ($request->filled('password')) {
            $request->merge([
                'password' => $request->password
            ]);
        }
        
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
            // 'email_id'       => 'required|email|unique:students_detail,email_id,'.$student->id,
            'email_id'       => 'nullable',
            // 'contact'        => 'required|string|max:15',
            'contact' => ['nullable', 'string', new NotBlockedNumber],
            'gender'         => 'required|string',
            // 'college_name'   => 'required|string',   // not college_id
            // 'session'        => 'required|string',   // not session_id
            // 'technology'     => 'required|string',   // not technology_id
            'college_name' => 'required_if:is_place,0|nullable|string',
            'place'        => 'required_if:is_place,1|nullable|string',
            'is_place'    => 'nullable',
            'technology'   => 'required|array',
            'technology.*' => 'string',

            'batch_assign'   => 'required|string',   // not batch_id
            'reference'      => 'nullable|string',   // not reference_user
            'status'         => 'required|string',
            'duration'         => 'nullable|string',
            'total_fees'     => 'required|numeric',
            'reg_fees'       => 'required|numeric',
            'paid_fees'       => 'nullable|numeric',
            // 'pending_fees'   => 'nullable|numeric',
            'next_due_date' => 'nullable|date',
            // 'department'     => 'required|string',
            'join_date'      => 'required|date',
            'start_date'     => 'nullable|date',
            'end_date'       => 'nullable|date',
            'part_time_offer'  => 'required|boolean',
            'placement_offer'  => 'required|boolean',
            'pg_offer'         => 'required|boolean',
            'is_intern'         => 'required|boolean',
            'is_married'         => 'nullable|boolean',
            'is_online'         => 'nullable|boolean',
            'password' => 'nullable|min:6',
        ]);
        // dd('Passed validation', $validates);

        if (($validates['reg_fees'] + $validates['paid_fees']) > $validates['total_fees']) {
            return back()
                ->withErrors([
                    'paid_fees' => 'Registration fees + Paid fees cannot be greater than Total fees.',
                ])
                ->withInput();
        }
        // Force lowercase before saving
        

        $validates['student_name'] = Str::of($validates['student_name'])->trim()->lower();
        $validates['f_name']       = Str::of($validates['f_name'])->trim()->lower();

        $activeSessionId = session('admin_session_id');
        if (!empty($validates['contact'])) {
            $contactExists = Student::query()
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

        
        $validates['paid_fees'] = $validates['paid_fees'] ?? 0;
        $validates['reg_fees'] = $validates['reg_fees'] ?? 0;

        $validates['pending_fees'] = max(
            $validates['total_fees'] - $validates['reg_fees'] - $validates['paid_fees'],
            0
        );
        unset($validates['password']);
        if ($request->filled('password')) {
            $validates['plain_password'] = $validates['password'] = trim($request->password);
        }
        // dd($validates);
        $student->update($validates);

        return redirect()->route('students.index')
                        ->with('success','Student updated successfully');
    }


    // Delete student
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
                         ->with('success','Student deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        // If no ids sent (GET request or empty payload), DO NOTHING
        // If no valid IDs are sent, ignore GET request
        if (!$request->filled('ids') || is_array($request->ids)) {
            return back()->with('error', 'No students selected.');
        }

        // Now decode properly
        $ids = json_decode($request->ids, true);

        Student::whereIn('id', $ids)->delete();

        return back()->with('success', 'Selected students deleted.');
    }



    // public function import(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|mimes:xlsx,xls,csv'
    //     ]);

    //     Excel::import(new StudentsImport, $request->file('file'));

    //     return back()->with('success', 'Students imported successfully!');
    // }

    public function importForm()
    {
        return view('students.import');
    }
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx,xls',
    ]);

    $file = $request->file('file');

    \DB::beginTransaction();

    try {

        $importer = new \App\Imports\StudentsImport();
        \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

        // Validation failures
        $failures = $importer->failures();

        if ($failures->isNotEmpty()) {

            \DB::rollBack();

            $messages = [];

            foreach ($failures as $failure) {
                $messages[] =
                    "Row {$failure->row()} – {$failure->attribute()} – " .
                    implode(', ', $failure->errors());
            }

            return back()->withErrors($messages);
        }

        \DB::commit();

        // Final counts
        $total    = $importer->totalRows;
        $inserted = $importer->insertedRows;
        $skipped  = $importer->skippedRows;

        $message = "From {$total} rows: {$inserted} inserted successfully, {$skipped} skipped.";

        // Warnings
        $warnings = $importer->duplicateContacts;

        if (!empty($warnings)) {
            return back()
                ->with('success', $message)
                ->withErrors($warnings);
        }

        return back()->with('success', $message);

    } catch (\Throwable $e) {

        \DB::rollBack();

        return back()->withErrors([
            'Import failed: Something went wrong while importing the file.'
        ]);
    }
}

    public function importw(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:csv,txt,xlsx,xls',
    ]);

    $file = $request->file('file');

    /** STEP 1: Check file not empty + read headers */
    $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);

    if (empty($data) || empty($data[0]) || empty($data[0][0])) {
        return back()->withErrors(['Uploaded file is empty or unreadable.']);
    }

    // Read headers (first row)
    $headers = array_map('strtolower', $data[0][0]);

    // REQUIRED HEADERS (order NOT required)
    $requiredHeaders = [
        'student_name',
        'f_name',
        'email_id',
        'contact',
        'college_name',
        'status',
        'technology',
        'total_fees',
        'reg_fees',
        'start_date',
    ];

    // Check missing headers
    $missing = array_diff($requiredHeaders, $headers);

    if (!empty($missing)) {
        return back()->withErrors([
            "Missing required column(s): " . implode(', ', $missing)
        ]);
    }

    /** STEP 2: FULL VALIDATION + IMPORT (ALL OR NOTHING) */
    \DB::beginTransaction();

    try {

        $importer = new \App\Imports\StudentsImport();

        \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

        // 🔴 VALIDATION FAILURES (wrong phone, empty name, bad fees, bad date, etc.)
        $failures = $importer->failures();

        if ($failures->isNotEmpty()) {

            \DB::rollBack();

            $messages = [];

            foreach ($failures as $failure) {
                $messages[] =
                    "Row {$failure->row()} – {$failure->attribute()} – " .
                    implode(', ', $failure->errors());
            }

            return back()->withErrors($messages);
        }

        // 🟡 DUPLICATE / BLOCKED WARNINGS (rows skipped)
        $warnings = [];

        if (!empty($importer->duplicateContacts)) {
            foreach ($importer->duplicateContacts as $msg) {
                $warnings[] = $msg;
            }
        }

        if (!empty($importer->duplicateEMail)) {
            foreach ($importer->duplicateEMail as $msg) {
                $warnings[] = $msg;
            }
        }

        // ✅ Everything OK → commit
        \DB::commit();

        if (!empty($warnings)) {
            return back()
                ->with('success', 'Students Imported Successfully (some rows skipped).')
                ->withErrors($warnings);   // show warnings
        }

        return back()->with('success', 'Students Imported Successfully!');

    } catch (\Throwable $e) {

        \DB::rollBack();

        return back()->withErrors([
            'Import failed: ' . $e->getMessage()
        ]);
    }
}

    public function import20jan(Request $request)
    {
        $request->validate([
            // 'file' => 'required|file|mimes:xlsx,xls,csv',
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        /** STEP 1: Read Excel headers BEFORE actual import */
        $data =  \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
         // dd('here');
        if (empty($data) || empty($data[0]) || empty($data[0][0])) {
            return back()->with('error', 'Uploaded file is empty or unreadable.');
        }

        // lowercase header keys
        $headers = array_map('strtolower', $data[0][0]);

        // REQUIRED COLUMNS (in correct order)
        $requiredHeaders = [
            'student_name',
            'f_name',
            // 'sno',
            'email_id',
            'contact',
            // 'gender',
            'college_name',
            // 'session',
            'status',
            'technology',
            'total_fees',
            'reg_fees',
            // 'pending_fees',
            // 'next_due_date',
            // 'join_date',
            // 'duration',
            // 'batch_assign',
            'start_date',
            // 'end_date',
        ];

        /** 1️⃣ Missing headers? */
        $missing = array_diff($requiredHeaders, $headers);
        if (!empty($missing)) {
            return back()->withErrors([
                "Missing required column(s): " . implode(', ', $missing)
            ]);
        }

        /** 2️⃣ Check required columns order (extra columns allowed) */
        $uploadedRequired = array_values(array_intersect($headers, $requiredHeaders));

        if ($uploadedRequired !== $requiredHeaders) {
            return back()->withErrors([
                "Invalid column order! Required order: " . implode(' → ', $requiredHeaders)
            ]);
        }

        /** STEP 2: Run import */
        try {
            $importer = new \App\Imports\StudentsImport();
             \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

             // Collect duplicate contact errors
            $errors = [];

            if (!empty($importer->duplicateContacts)) {
                foreach ($importer->duplicateContacts as $msg) {
                    $errors[] = $msg;
                }
            }

            if (!empty($importer->duplicateEMail)) {
                foreach ($importer->duplicateEMail as $em) {
                    $errors[] = $em;
                }
            }

            // If importer has other errors (optional)
            if (!empty($importer->errors ?? [])) {
                foreach ($importer->errors as $err) {
                    $errors[] = $err;
                }
            }

            // If any errors exist → show them (but still show success)
            if (!empty($errors)) {
                return back()
                    ->with('success', "Students Imported Successfully!")
                    ->withErrors($errors);
            }

            return back()->with('success', "Students Imported Successfully!");
            // Log file if needed
            $logFile = null;
            if (!empty($importer->errors)) {
                $logFile = 'students-import-log-' . time() . '.txt';
                \Storage::put($logFile, implode("\n", $importer->errors));
            }

            return back()->with('success', "Students Imported Successfully!")
                         ->with('logFile', $logFile);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {

            // Collect all row validation errors
            $messages = [];
            foreach ($e->failures() as $failure) {
                $messages[] =
                    "Row {$failure->row()}: " .
                    implode(', ', $failure->errors());
            }

            return back()->withErrors($messages);
        }
        catch (\Throwable $e) {
            return back()->withErrors([
                'Import failed: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmStudent(Request $request, $id)
    {
        // return redirect()->back()->with('success', 'Cannot send email.');
        // $student = Student::findOrFail($id);
        // $student = Student::with('sessionData')->find($id);
        $isInternship = $request->boolean('is_internship');
        // dd($isInternship);
        $student = Student::with(['sessionData', 'durationData','collegeData'])->find($id);

        // 🔥 1. Check if pending fees exists
        $totalPaid = $student->total_fees - $student->pending_fees;

        // If the student has paid nothing
        if ($totalPaid <= 0) {
            return redirect()->back()
                ->with('error', "Cannot Confirm. No fees submitted yet.");
        }

        // 2. Generate PDF dynamically
        // $filePath = $this->generateConfirmationPdf($student,$isInternship);
        // $receiptPath = $this->generatePaymentReceiptPdf($student);

        // 3. Send email with attachment
        // Mail::to($student->email_id)
        //     ->send(new StudentConfirmationMail($student, $filePath,$receiptPath));

        // 4. Increment student's certificate email count
        // $student->increment('email_count_confirmation');
        // $student->increment('count_receipt_download');

        // 5. Increment global email count
        // $emailCount = EmailCount::firstOrCreate(
        //     ['email' => $student->email_id],
        //     ['count' => 0]
        // );
        // $emailCount->increment('count');

        $student->certificate_status = 0; // confirmed
        $student->save();

        return redirect()->back()->with('success', 'Student sent to certification section.');
        // return redirect()->back()->with('success', 'Student confirm and email sent.');
    }

    public function confirmMultiple(Request $request)
    {

        // return back()->with('success', 'Cannot send to selected students.');
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');
        $isInternship = $request->boolean('is_internship');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            // NEW CHECK: Has the student paid anything?
            $totalPaid = $student->total_fees - $student->pending_fees;
             if ($totalPaid <= 0) {
                return back()->with(
                    'error',
                    "Cannot Confirm {$student->student_name}. No payment submitted yet."
                );
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

               // 2. Generate PDF dynamically
            // $filePath = $this->generateConfirmationPdf($student, $isInternship);
            // $receiptPath = $this->generatePaymentReceiptPdf($student);

            // 3. Send email with attachment
            //skip email to student
            // Mail::to($student->email_id)
            //     ->send(new StudentConfirmationMail($student, $filePath, $receiptPath));

            // Increment counters
            // $student->increment('email_count_confirmation');
            // $student->increment('count_receipt_download');
            $student->certificate_status = 0; // confirmed
            $student->save();

            // EmailCount::firstOrCreate(['email' => $student->email_id], ['count' => 0])->increment('count');
        }

        return back()->with('success', 'Selected student send to confirmation section.');
        // return back()->with('success', 'Confimation send to selected students.');
    }

    public function downloadconfirmMultiple(Request $request)
    {
        $ids = json_decode($request->ids, true);
         $isInternship = $request->boolean('is_internship');
         $ismonthly = $request->boolean('is_monthly');
         $is_logo_show = $request->boolean('is_logo_show');
         // dd($request->request);
        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'No students selected.');
        }

        // Ensure IDs are integers
        $ids = array_map('intval', $ids);

        $students = Student::whereIn('id', $ids)->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Selected students not found.');
        }

        // Generate (or reuse) PDFs and collect file paths
        $pdfPaths = [];
        foreach ($students as $student) {
            $pdfPath = $this->generateConfirmationPdf($student, $isInternship, $ismonthly, $is_logo_show);
            $student->increment('download_count_confirmation');
            if (file_exists($pdfPath)) {
                $pdfPaths[] = $pdfPath;
            }
        }

        // If only one PDF, return it directly
        if (count($pdfPaths) === 1) {
            $singlePath = $pdfPaths[0];
            $downloadName = basename($singlePath);

            // Return the single PDF with proper headers
            return response()->download($singlePath, $downloadName, [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // Otherwise create ZIP
        if($isInternship){
            $zipFileName = 'internship_letters_' . time() . '.zip';
        }else{
            $zipFileName = 'confirmation_letters_' . time() . '.zip';
        }
        
        $zipFullPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfPaths as $path) {
                // Use a friendlier name inside zip (only filename)
                $zip->addFile($path, basename($path));
            }
            $zip->close();

            // Return and delete zip after download
            return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    public function downloadMultipleReceipts(Request $request)
    {
        $ids = json_decode($request->ids, true);

        if (!$ids || count($ids) == 0) {
            return back()->with('error', 'No students selected.');
        }

        // Fetch students
        $students = Student::whereIn('id', $ids)->get();

        // -------------------------------------------------------
        // 🔥 If only ONE student is selected → download PDF directly
        // -------------------------------------------------------
        if ($students->count() === 1) {

            $student = $students->first();

            // Generate PDF
            $pdfPath = $this->generatePaymentReceiptPdf($student);

            if (!file_exists($pdfPath)) {
                return back()->with('error', 'Receipt could not be generated.');
            }

            $student->increment('count_receipt_download');
            return response()->download(
                $pdfPath,
                basename($pdfPath),
                ['Content-Type' => 'application/pdf']
            );
        }

        // -------------------------------------------------------
        // 🔥 If MULTIPLE students → ZIP all receipts
        // -------------------------------------------------------
        $zipFileName = 'payment_receipts_' . time() . '.zip';
        $zipPath = storage_path('app/' . $zipFileName);

        $zip = new \ZipArchive;

        if ($zip->open($zipPath, \ZipArchive::CREATE) === TRUE) {

            foreach ($students as $student) {

                $pdfPath = $this->generatePaymentReceiptPdf($student);

                if (file_exists($pdfPath)) {
                    $zip->addFile($pdfPath, basename($pdfPath));
                }
            }

            $zip->close();
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }

    public function downloadCertificateMultiple(Request $request)
    {

        $ids = json_decode($request->ids, true);
        $isPursuing = $request->boolean('is_pursuing');

        // dd($request->request);
        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'No students selected.');
        }

        // Ensure IDs are integers
        $ids = array_map('intval', $ids);

        $students = Student::whereIn('id', $ids)->get();

        if ($students->isEmpty()) {
            return back()->with('error', 'Selected students not found.');
        }

        // Generate (or reuse) PDFs and collect file paths
        $pdfPaths = [];
        foreach ($students as $student) {
            $pdfPath = $this->generatePdf($student, $isPursuing);

            if (file_exists($pdfPath)) {
                $pdfPaths[] = $pdfPath;
            }
        }

        // If only one PDF, return it directly
        if (count($pdfPaths) === 1) {
            $singlePath = $pdfPaths[0];
            $downloadName = basename($singlePath);

            // Return the single PDF with proper headers
            return response()->download($singlePath, $downloadName, [
                'Content-Type' => 'application/pdf'
            ]);
        }

        // Otherwise create ZIP
        $zipFileName = 'certificate_letters_' . time() . '.zip';
        $zipFullPath = storage_path('app/' . $zipFileName);

        $zip = new ZipArchive();
        if ($zip->open($zipFullPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($pdfPaths as $path) {
                // Use a friendlier name inside zip (only filename)
                $zip->addFile($path, basename($path));
            }
            $zip->close();

            // Return and delete zip after download
            return response()->download($zipFullPath, $zipFileName)->deleteFileAfterSend(true);
        }

        return back()->with('error', 'Could not create ZIP file.');
    }

    public function moveMultipleToCertificate(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            // NEW CHECK: Has the student paid anything?
            // $totalPaid = $student->total_fees - $student->pending_fees;
            if ($student->pending_fees > 0) {
                return back()->with(
                    'error',
                    "Cannot Shift {$student->student_name}. Showing pending fee in record."
                );
            }

            // if ($totalPaid <= 0) {
            //     return back()->with(
            //         'error',
            //         "Cannot Shift {$student->student_name}. No payment submitted yet."
            //     );
            // }

            // if($student->count_receipt_download == 0){
            //     return back()->with(
            //         'error',
            //         "Cannot Shift {$student->student_name}. No payment slip downloaded yet."
            //     );
            // }

            // if($student->email_count_confirmation == 0){
            //     return back()->with(
            //         'error',
            //         "Cannot Shift {$student->student_name}. No Confirmation Letter downloaded yet."
            //     );
            // }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

            $student->certificate_status = 1; // confirmed
            $student->save();
        }

        return back()->with('success', 'Student send to Certificate Section.');
    }

    public function moveMultipleToConfirmation(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');
        // dd($idsPayload);
        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

            $student->certificate_status = 0; // confirmation
            $student->save();
        }

        return back()->with('success', 'Student sent back to Confirmation Section.');
    }


    public function issueCertificate($id)
    {
        // $student = Student::findOrFail($id);
        $student = Student::with(['sessionData', 'durationData','collegeData'])->find($id);

        // 🔥 1. Check if pending fees exists
        if ($student->pending_fees > 0) {
            return redirect()->back()->with('error', "Cannot Confirm. Pending fees: ₹{$student->pending_fees}");
        }

        // 2. Generate PDF dynamically
        $filePath = $this->generatePdf($student);

        // 3. Send email with attachment
        // Mail::to($student->email_id)
        //     ->send(new CertificateIssuedMail($student, $filePath));

        // 4. Increment student's certificate email count
        // $student->increment('email_count_certificate');

        // 5. Increment global email count
        // $emailCount = EmailCount::firstOrCreate(
        //     ['email' => $student->email_id],
        //     ['count' => 0]
        // );
        // $emailCount->increment('count');

        $student->certificate_status = 2; // certificate send
        $student->certificate_send_date = now();
        $student->close_date = now();
        $student->save();

        return redirect()->back()->with('success', 'Certificate issued and email sent.');
    }

    public function issueMultiple(Request $request)
    {
        // Expecting JSON string or array in $request->ids
        $idsPayload = $request->input('ids');

        if (empty($idsPayload)) {
            return back()->with('error', 'No students selected.');
        }

        // Decode possible JSON string
        $ids = is_array($idsPayload) ? $idsPayload : json_decode($idsPayload, true);

        if (!is_array($ids) || count($ids) === 0) {
            return back()->with('error', 'Invalid selection.');
        }

        // Validate all selected students exist and pending fees = 0
        foreach ($ids as $studentId) {
            // $student = Student::find($studentId);
            $student = Student::with(['sessionData', 'durationData','collegeData'])->find($studentId);
            if (!$student) {
                return back()->with('error', "Student (ID: {$studentId}) not found.");
            }
            if ($student->pending_fees > 0) {
                return back()->with('error', "Cannot Confirm {$student->student_name}. Pending fees: ₹{$student->pending_fees}");
            }
        }

        // If we reach here, all students are OK — process each
        foreach ($ids as $studentId) {
            $student = Student::find($studentId);
            if (!$student) continue; // defensive

            //return view('pdf.student_certificate', compact('student'));
            $filePath = $this->generatePdf($student);
            //die();
            // Send email
            // Mail::to($student->email_id)->send(new CertificateIssuedMail($student, $filePath));

            // Increment counters
            // $student->increment('email_count_certificate');
            $student->certificate_status = 2; // certificate send
            $student->certificate_send_date = now();
            // Only set close_date if it is currently NULL
            if (is_null($student->close_date)) {
                $student->close_date = now();
            }
            $student->save();

            EmailCount::firstOrCreate(['email' => $student->email_id], ['count' => 0])->increment('count');
        }

        return back()->with('success', 'Certificates issued to selected students.');
    }

    private function generateConfirmationPdf($student, $isInternship = false, $ismonthly = false, $is_logo_show = false)
    {
        // dd($isInternship, $ismonthly, $is_logo_show);
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("studentConfirmation/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        // $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $studentName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->student_name));
        $fatherName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->f_name));
        // $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->CollegeData->FullName));
        $collegeOrPlace = optional($student->CollegeData)->FullName ?: $student->place;

        // sanitize
        $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($collegeOrPlace));

        $dateFormatted = Carbon::now()->format('d_F_Y'); // 26_March_2015

        if($isInternship){
            $fileName = "{$studentName}_{$fatherName}_{$collegeName}_{$dateFormatted}_internship_letter.pdf";
        }else{
            $fileName = "{$studentName}_{$fatherName}_{$collegeName}_{$dateFormatted}_confirmation_letter.pdf";    
        }
        

        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }

        // Generate or overwrite PDF if needed

        if ($regenerate) {
            
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);
        
            $mpdf->SetHTMLHeader($this->getPDFHeader());
            $mpdf->SetHTMLFooter($this->getPDFFooter());

            if($isInternship){
                $view = "pdf.internship_detail";
            }else{
                $view = "pdf.confirmation_detail";
            }

            $html = view($view, compact('student','isInternship','ismonthly','is_logo_show'))->render();
        
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F');
            //return response()->download($filePath);
        }

         return $filePath;
        // if ($regenerate) {
        //     $pdf = Pdf::loadView('pdf.confirmation_detail', ['student' => $student])
        //               ->setPaper('a4', 'portrait')  // or 'portrait'
        //               ->setOption('dpi', 150)        // higher resolution
        //               ->setOption('defaultFont', 'sans-serif');

        //     $pdf->save($filePath);
        // }
        return $filePath;
    }


    function getPDFHeader()
    {
        return '<div style="position: fixed; top: -35px;" class="head-shape">
            <img src="'. public_path('images/confirmation_images/head-shape.png').'"/>
        </div>';
    }



    function getPDFFooter()
    {
        return '<div style="position: fixed; bottom: -35px;" class="ct-footer-shape">
                    <img src="'.public_path('images/confirmation_images/footer-shape-1.png').'"/>
                </div>';
    }


private function generatePdf($student, $isPursuing = false)
    {
         
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("student_certificate/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        // $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $studentName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->student_name));
        $fatherName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->f_name));
        // $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->CollegeData->FullName));
        $collegeOrPlace = optional($student->CollegeData)->FullName ?: $student->place;

        // sanitize
        $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($collegeOrPlace));

        $dateFormatted = Carbon::now()->format('d_F_Y'); // 26_March_2015

        $fileName = "{$studentName}_{$fatherName}_{$collegeName}_{$dateFormatted}_certificate.pdf";
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }
        // echo $filePath;
        // Generate or overwrite PDF if needed
        if ($regenerate) {
            
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_top' => 0,
                'margin_bottom' => 0,
            ]);
        
            $mpdf->SetHTMLHeader($this->getPDFHeader());

            $mpdf->SetHTMLFooter($this->getPDFFooter());

            if($isPursuing){
                $view = "pdf.student_pursuing_certificate";
            }else{
                $view = "pdf.student_certificate";
            }

            $html = view($view, compact('student'))->render();
        
            $mpdf->WriteHTML($html);
            $mpdf->Output($filePath, 'F');
            //return response()->download($filePath);
        }

        return $filePath;
    }
    
    private function gsaeneratePdf_old($student)
    {
     // Create folder path for today
        $date = Carbon::now()->format('Y-m-d');
        $folderPath = public_path("student_certificate/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Create PDF file name
        $fileName = $student->id . '_' . preg_replace('/\s+/', '_', $student->student_name) . '.pdf';
        $filePath = $folderPath . '/' . $fileName;

        $regenerate = true;

        // Check if file exists and whether student data changed
        if (file_exists($filePath)) {
            $fileModified = filemtime($filePath);
            $studentUpdated = strtotime($student->updated_at);

            // Only skip regeneration if PDF is newer than student update
            if ($studentUpdated <= $fileModified) {
                //$regenerate = false;
            }
        }

        // $html = view('pdf.certificate_detail', [
        //     'student' => $student
        // ])->render();
        // echo $html;die;
        // dd($html);
        // Generate or overwrite PDF if needed
        if ($regenerate) {
            // $pdf = Pdf::loadView('pdf.student_certificate', ['student' => $student])
            //           ->setPaper('a4', 'portrait')  // or 'portrait'
            //           ->setOption('dpi', 150)        // higher resolution
            //           ->setOption('defaultFont', 'sans-serif');
            // return $pdf->stream('student-certificate.pdf');
            // return $pdf->download('student-certificate.pdf');
            $pdf->save($filePath);
        }

        return $filePath;
    }


   private function generatePaymentReceiptPdf($student)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $folderPath = public_path("paymentReceipts/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Receipt No.
        $receiptNumber = strtoupper(uniqid("RCT"));

        // Payment amount (you can change this)
        // $amount = $student->reg_fees;
        $amount = $student->reg_fees + ($student->paid_fees ?? 0);

        // Convert amount to words
        $amountInWords = ucwords(
            (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))->format($amount)
        );

        // PDF Name
        // $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->student_name);
        // $fileName = $student->id . '_' . $safeName . '_receipt.pdf';
        
        $studentName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->student_name));
        $fatherName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->f_name));
        // $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($student->CollegeData->FullName));
        $collegeOrPlace = optional($student->CollegeData)->FullName ?: $student->place;

        // sanitize
        $collegeName = preg_replace('/[^A-Za-z0-9]+/', '_', trim($collegeOrPlace));

        $dateFormatted = Carbon::now()->format('d_F_Y'); // 26_March_2015

        $fileName = "{$studentName}_{$fatherName}_{$collegeName}_{$dateFormatted}_receipt.pdf";

        $filePath = $folderPath . '/' . $fileName;

        // Render PDF with ALL dynamic values
        // $pdf = \PDF::loadView('pdf.payment_receipt', [
        //     'student'        => $student,
        //     'receiptNumber'  => $receiptNumber,
        //     'amount'         => $amount,
        //     'amountInWords'  => $amountInWords,
        //     'payment_mode'   => 'Cash',   // default
        //     'transaction_no' => 'N/A',    // default
        // ])
        // ->setPaper('a4')
        // ->setOption('dpi', 150)
        // ->setOption('defaultFont', 'sans-serif');

        // $pdf->save($filePath);


        $mpdf = new Mpdf([
            'mode'           => 'utf-8',
            'format'         => 'A5',
            'orientation'    => 'L',
            'margin_left'    => 10,
            'margin_right'   => 10,
            'margin_top'     => 5,
            'margin_bottom'  => 5,
            'default_font'   => 'sans-serif',
            'dpi'            => 150,
        ]);

        // Render Blade view to HTML
        $html = view('pdf.payment_receipt', [
            'student'        => $student,
            'receiptNumber'  => $receiptNumber,
            'amount'         => $amount,
            'amountInWords'  => $amountInWords,
            'payment_mode'   => 'Cash',
            'transaction_no' => 'N/A',
        ])->render();

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        // Save PDF to file path
        $mpdf->Output($filePath, 'F');


        return $filePath;
    }

    private function generatePaymentReceiptPdf22dec($student)
    {
        $date = \Carbon\Carbon::now()->format('Y-m-d');
        $folderPath = public_path("paymentReceipts/{$date}");

        if (!file_exists($folderPath)) {
            mkdir($folderPath, 0777, true);
        }

        // Receipt No.
        $receiptNumber = strtoupper(uniqid("RCT"));

        // Payment amount (you can change this)
        $amount = $student->reg_fees;

        // Convert amount to words
        $amountInWords = ucwords(
            (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))->format($amount)
        );

        // PDF Name
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $student->student_name);
        $fileName = $student->id . '_' . $safeName . '_receipt.pdf';
        $filePath = $folderPath . '/' . $fileName;

        // Render PDF with ALL dynamic values
        $pdf = \PDF::loadView('pdf.payment_receipt', [
            'student'        => $student,
            'receiptNumber'  => $receiptNumber,
            'amount'         => $amount,
            'amountInWords'  => $amountInWords,
            'payment_mode'   => 'Cash',   // default
            'transaction_no' => 'N/A',    // default
        ])
        ->setPaper('a4')
        ->setOption('dpi', 150)
        ->setOption('defaultFont', 'sans-serif');

        $pdf->save($filePath);

        return $filePath;
    }


    public function managerIndex(Request $request)
    {
        $query = Student::query();

        // Filters
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
            $query->where('session', $request->session); // use 'session' not session_id
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


        // if ($request->filled('department')) {
        //     $query->where('department', $request->department);
        // }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $query->where('pending_fees', '>', 0.00);
        }



        $students = $query->paginate(10);

        return view('manager.student', [
            'students'    => $students, // use pagination
            'colleges'    => College::all(),
            'sessions'    => StudentSession::all(),
            'courses'     => Course::all(),
            'batches'     => Batch::all(),
            'users'       => User::all(),
            'departments' => Department::all(),
            'reference'   => Reference::all(),
        ]);
    }

    public function salesIndex(Request $request)
    {
        $query = Student::query();

        // Filters
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
            $query->where('session', $request->session); // use 'session' not session_id
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


        // if ($request->filled('department')) {
        //     $query->where('department', $request->department);
        // }

        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }
        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $query->where('pending_fees', '>', 0.00);
        }



        $students = $query->paginate(10);

        return view('sales.student', [
            'students'    => $students, // use pagination
            'colleges'    => College::all(),
            'sessions'    => StudentSession::all(),
            'courses'     => Course::all(),
            'batches'     => Batch::all(),
            'users'       => User::all(),
            'departments' => Department::all(),
            'reference'   => Reference::all(),
        ]);
    }

    public function pendingFees()
    {
        $activeSessionId = session('admin_session_id');

        $students = Student::where('pending_fees', '>', 0)
                           
                           ->where('certificate_status', 1)
                           ->where('session', $activeSessionId)
                           ->paginate(50);
                           
        return view('pending_fees', compact('students'));
    }

    public function closingList()
    {
        $activeSessionId = session('admin_session_id');

        $students = Student::where('pending_fees', 0)
                           ->where('certificate_status', 2)
                           ->where('email_count_certificate','>' ,0)
                           ->where('session', $activeSessionId)
                           ->paginate(50);

        return view('closing_list', compact('students'));
    }

    public function pendingFeesold()
    {

        $students = Student::where('pending_fees', '>', 0)
                           ->whereDate('next_due_date', '<=', now())
                           ->paginate(10);

        return view('pending_fees', compact('students'));
    }


    //Pending STudent whose session not added
     public function pendingStudents(Request $request)
    {   
        $notificationMode = $request->notification ?? null;

        $query = Student::query();
        
        $query->where(function ($q) {
            $q->whereNull('session')
              ->orWhere('session', '');
        });
        
        $students = $query->paginate(100);

        $sessions = StudentSession::all();
        $colleges = College::all();
        $courses = Course::all();
        $batches = Batch::all();
        $references = Reference::all();
        $departments = Department::all();
        $users = User::all();
        $student_status = StudentStatus::all();

        //pending fee
        $dismissed = session('dismiss_pending_fee');
        $activeSessionNo = session('admin_session_id');
            
            $pendingStudents = !$dismissed
                ? Student::where('pending_fees', '>', 0)
                    ->whereDate('next_due_date', '<=', now())
                    ->where('session', $activeSessionNo)
                    ->where('certificate_status', 1)
                    ->orderBy('next_due_date', 'asc')
                    ->take(10)
                    ->get()
                : collect();
        

        return view('students.pending_student_index', compact('students','sessions','colleges','courses','batches','references','departments','users','student_status','pendingStudents'));
    }

    private function generateIdCard($student): string
    {
        $mpdf = new Mpdf([
            'format' => [54, 85.6],
            // 'format' => [85.6, 54],
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);

         // Footer HTML
        

        $html = View::make('students.id-card-pdf', compact('student'))->render();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }

    public function downloadIdStudentCard(Student $student)
    {
        $pdf = $this->generateIdCard($student);
        $name = strtoupper(str_replace(' ', '_', $student->student_name));

        // return response(
        //     $this->generateIdCard($student),
        //     200,
        //     [
        //         'Content-Type' => 'application/pdf',
        //         'Content-Disposition' => 'inline; filename="id-card.pdf"',
        //     ]
        // );
        return response($pdf)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="ID_CARD_'.$name.'.pdf"');
    }

    public function verifyStudents()
    {
        $students = Student::latest()->get();
        return view('verify_students.index', compact('students'));
    }

    public function verifyStudentsLink()
    {
        return view('verify_students.link_index');
    }

    public function exportExcel(Request $request)
    {
         
        // $date = strtolower(now()->format('d_F'));

        // default filename
        // $fileName = 'students_list_' . $date;
        // if ($request->filled('technology')) {

        //     $fileName = 'students_technology_' . $date;
        // }
        // if ($request->filled('gender')) {

        //     $fileName = 'students_' . $request->gender . '_' . $date;
        // }
        // // change filename based on fee filter
        // if ($request->filled('fee_filter')) {

        //     $fileName = 'students_' . $request->fee_filter . '_' . $date;
        // }

        $date = strtolower(now()->format('d_F'));

        $parts = ['students'];

        // Basic filters
        if ($request->filled('student_name')) {
            $parts[] = Str::slug($request->student_name, '_');
        }

        if ($request->filled('f_name')) {
            $parts[] = 'fname_' . Str::slug($request->f_name, '_');
        }

        if ($request->filled('sno')) {
            $parts[] = 'sno_' . $request->sno;
        }

        if ($request->filled('gender')) {
            $parts[] = strtolower($request->gender);
        }

        if ($request->filled('session')) {
            $parts[] = 'session_' . $request->session;
        }

        if ($request->filled('college_name')) {
            $parts[] = Str::slug($request->college_name, '_');
        }

        if ($request->filled('email_id')) {
            $parts[] = 'email';
        }

        // Status / flags
        if ($request->filled('status')) {
            $parts[] = strtolower($request->status);
        }

        if ($request->filled('is_intern')) {
            $parts[] = $request->is_intern ? 'intern' : 'non_intern';
        }

        if ($request->filled('is_online')) {
            $parts[] = $request->is_online ? 'online' : 'offline';
        }

        // Fees
        if ($request->filled('registration_fee')) {
            $parts[] = 'fee_' . $request->registration_fee;
        }

        if ($request->filled('fee_filter')) {
            $parts[] = $request->fee_filter;
        }

        if ($request->filled('pending_fees') && $request->pending_fees == 1) {
            $parts[] = 'pendingfees';
        }

        // Technology
        if ($request->filled('technology')) {
            $parts[] = Str::slug($request->technology, '_');
        }

         

        // Offers
        if ($request->filled('part_time_offer')) {
            $parts[] = 'parttime';
        }

        if ($request->filled('placement_offer')) {
            $parts[] = 'placement';
        }

        if ($request->filled('pg_offer')) {
            $parts[] = 'pg';
        }

         

         
        // Final filename
        $fileName = implode('_', $parts) . '_' . $date . '.xlsx';

        // Prevent too long filename
        $fileName = substr($fileName, 0, 150);


        return Excel::download(
            new StudentListExport($request),
             $fileName
        );
    }

    public function copyStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required',
            'session' => 'required|exists:student_sessions,id',
        ]);

        $ids = json_decode($request->student_ids, true);

        $students = Student::whereIn('id', $ids)->get();

        foreach ($students as $student) {

            // OPTIONAL safety: prevent same student + same session duplicate
            $exists = Student::where('contact', $student->contact)
                ->where('session', $request->session)
                ->exists();

            if ($exists) {
                continue;
            }

            // 🔴 PURE COPY
            $newStudent = $student->replicate();

            // 🔴 ONLY CHANGE SESSION
            $newStudent->session = $request->session;

            // 🔴 NOTHING ELSE TOUCHED
            $newStudent->save();
        }

        return redirect()->back()->with('success', 'Students copied successfully.');
    }

    public function makeInterns(Request $request)
    {
        // dd($request);
        $request->validate([
            'ids' => 'required'
        ]);

        // Decode safely
        $ids = json_decode($request->ids, true);
        // dd($ids);
        // If empty / invalid JSON
        if (empty($ids) || !is_array($ids)) {
            return back()->with('error', 'No students selected.');
        }

        // Get only existing IDs
        $validIds = Student::whereIn('id', $ids)->pluck('id')->toArray();

        // If no valid students found
        if (empty($validIds)) {
            return back()->with('error', 'Selected students do not exist.');
        }

        // Update interns
        Student::whereIn('id', $validIds)->update([
            'is_intern' => 1
        ]);

        return back()->with('success', 'Students marked as intern successfully.');
    }


    public function importFeeForm()
    {
        return view('students.import_fee');
    }

    public function importFee(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls',
        ]);

        $file = $request->file('file');

        \DB::beginTransaction();

        try {

            $importer = new \App\Imports\StudentsFeeImport();
            \Maatwebsite\Excel\Facades\Excel::import($importer, $file);

            // Validation failures
            $failures = $importer->failures();
            if ($failures->isNotEmpty()) {

                \DB::rollBack();

                $messages = [];

                foreach ($failures as $failure) {
                    $messages[] =
                        "Row {$failure->row()} – {$failure->attribute()} – " .
                        implode(', ', $failure->errors());
                }

                return back()->withErrors($messages);
            }

            \DB::commit();

            // Final counts
            $total    = $importer->totalRows;
            $inserted = $importer->insertedRows;
            $skipped  = $importer->skippedRows;

            $message = "From {$total} records: {$inserted} records updated successfully, {$skipped} skipped.";

            // Warnings
            $warnings = $importer->duplicateContacts;

            if (!empty($warnings)) {
                return back()
                    ->with('success', $message)
                    ->withErrors($warnings);
            }

            return back()->with('success', $message);

        } catch (\Throwable $e) {
            dd($e);
            \DB::rollBack();

            return back()->withErrors([
                'Import failed: Something went wrong while importing the file.'
            ]);
        }
    }

    // public function downloadActiveSessionStudents()
    // {
    //     $activeSessionNo = session('admin_session_id');

    //     return Excel::download(
    //         new ActiveSessionStudentsExport($activeSessionNo),
    //         'active_session_students_fee_upload.xlsx'
    //     );
    // }
    public function downloadActiveSessionStudents()
    {
        $activeSessionNo = session('admin_session_id');

        if (!$activeSessionNo) {
            abort(403, 'Active session not found.');
        }

        return Excel::download(
            new StudentsFeeTemplateExport($activeSessionNo),
            'active_session_students_fee_upload.xlsx'
        );
    }


    public function moveToPlacement(Request $request)
    {
        $ids = json_decode($request->ids, true);

        if (empty($ids)) {
            return back()->with('error', 'No students selected');
        }

        DB::beginTransaction();

        try {

            $students = Student::whereIn('id', $ids)->get();

            if ($students->isEmpty()) {
                return back()->with('error', 'No valid students found');
            }

            $movedCount = 0; // 🔥 IMPORTANT

            foreach ($students as $student) {

                // Skip already placed
                if ($student->is_placed == 1) {
                    continue;
                }

                // Prevent duplicate placement entry
                $exists = Placement::where('student_id', $student->id)->exists();
                if ($exists) {
                    continue;
                }

                // Handle tech
                $tech = null;

                if (!empty($student->technology)) {
                    if (is_array($student->technology)) {
                        $tech = $student->technology[0] ?? null;
                    } else {
                        $tech = explode(',', $student->technology)[0] ?? null;
                    }
                }

                // Insert placement
                Placement::create([
                    'student_id'     => $student->id,
                    'student_name'   => $student->student_name,
                    'tech'           => $tech,
                    'placement_date' => now(),
                    'college_name'   => $student->college_name,
                    'phone_no'       => $student->contact,
                    'location'       => $student->place,
                    'session_id'     => $student->session,
                ]);

                // Update student
                $student->is_placed = 1;
                $student->save();
                $student->delete();

                $movedCount++; // 🔥 track success
            }

            // ❌ NOTHING MOVED
            if ($movedCount === 0) {
                DB::rollBack();
                return back()->with('error', 'All selected students are already placed');
            }

            DB::commit();

            return back()->with('success', "$movedCount students moved to placement successfully");

        } catch (\Exception $e) {

            DB::rollBack();

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // public function moveToPlacement(Request $request)
    // {
    //     $ids = json_decode($request->ids, true);

    //     if (empty($ids)) {
    //         return back()->with('error', 'No students selected');
    //     }

    //     // dd($ids);

    //     DB::beginTransaction();

    //     try {

    //         // Fetch only not already placed students
    //     $students = Student::whereIn('id', $ids)
    //         ->where('is_placed', 0)
    //         ->get();


    //     if ($students->isEmpty()) {
    //         // dd($students);
    //         return back()->with('error', 'All selected students are already placed');
    //     }

    //         foreach ($students as $student) {

    //             // 🚫 Prevent duplicate placement entry
    //             $exists = Placement::where('student_id', $student->id)->exists();
    //             if ($exists) {
    //                 continue;
    //             }

    //             // ✅ Handle tech (first tech only)
    //             $tech = null;

    //             if (!empty($student->technology)) {
    //                 if (is_array($student->technology)) {
    //                     $tech = $student->technology[0] ?? null;
    //                 } else {
    //                     $tech = explode(',', $student->technology)[0] ?? null;
    //                 }
    //             }

    //             // ✅ Insert into placements
    //             Placement::create([
    //                 'student_id'     => $student->id,
    //                 'student_name'   => $student->student_name,
    //                 'tech'           => $tech,
    //                 'placement_date' => now(),
    //                 'college_name'   => $student->college_name, // already ID
    //                 'phone_no'       => $student->contact,
    //                 'location'       => $student->place,
    //                 'session_id'     => $student->session,
    //                 'created_at'     => now(),
    //                 'updated_at'     => now(),
    //             ]);

    //             // ✅ Update student
    //             $student->update([
    //                 'is_placed' => 1,
    //                 'deleted_at' => now(),
    //             ]);
    //         }

    //         DB::commit();

    //         return back()->with('success', 'Selected students moved to placement successfully');

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return back()->with('error', 'Error: ' . $e->getMessage());
    //     }
    // }
}